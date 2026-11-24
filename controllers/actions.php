<?php
function handlePost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'login':
                $u = queryOne('SELECT * FROM users WHERE email=? AND status="active" LIMIT 1', [trim($_POST['email'] ?? '')]);
                if (!$u || !password_verify($_POST['password'] ?? '', $u['password'])) throw new RuntimeException('Email atau password tidak sesuai.');
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)$u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
                redirect('dashboard');

            case 'logout':
                session_destroy();
                session_start();
                redirect('login');

            case 'employee_save':
                requireRole('admin');
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Nama dan email wajib diisi dengan benar.');
                $employeeNumber = trim($_POST['employee_number'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $department = trim($_POST['department'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $status = $_POST['status'] ?? 'active';
                if ($id) {
                    queryExecute('UPDATE users SET name=?,email=?,employee_number=?,position=?,department=?,phone=?,status=? WHERE id=? AND role="employee"', [$name, $email, $employeeNumber !== '' ? $employeeNumber : null, $position !== '' ? $position : null, $department !== '' ? $department : null, $phone !== '' ? $phone : null, $status, $id]);
                } else {
                    $password = $_POST['password'] ?? '';
                    if (strlen($password) < 8) throw new RuntimeException('Password awal minimal 8 karakter.');
                    queryExecute('INSERT INTO users(name,email,password,role,employee_number,position,department,phone,status,created_at,updated_at) VALUES(?,?,?,"employee",?,?,?,?,?,NOW(),NOW())', [$name, $email, password_hash($password, PASSWORD_DEFAULT), $employeeNumber !== '' ? $employeeNumber : null, $position !== '' ? $position : null, $department !== '' ? $department : null, $phone !== '' ? $phone : null, $status]);
                }
                flash('success', 'Data karyawan berhasil disimpan.');
                redirect('employees');

            case 'employee_delete':
                requireRole('admin');
                queryExecute('DELETE FROM users WHERE id=? AND role="employee"', [(int)$_POST['id']]);
                flash('success', 'Data karyawan dihapus jika tidak memiliki riwayat KPI.');
                redirect('employees');

            case 'period_save':
                requireRole('admin');
                $id = (int)($_POST['id'] ?? 0);
                $m = (int)$_POST['month'];
                $y = (int)$_POST['year'];
                if ($m < 1 || $m > 12 || $y < 2000 || $y > 2100) throw new RuntimeException('Bulan atau tahun tidak valid.');
                $status = $_POST['status'] ?? 'open';
                if (!in_array($status, ['open', 'closed'], true)) throw new RuntimeException('Status periode tidak valid.');
                if ($id) queryExecute('UPDATE periods SET month=?,year=?,status=? WHERE id=?', [$m, $y, $status, $id]);
                else queryExecute('INSERT INTO periods(month,year,status,created_at,updated_at) VALUES(?,?,?,NOW(),NOW())', [$m, $y, $status]);
                flash('success', 'Periode berhasil disimpan.');
                redirect('periods');

            case 'period_delete':
                requireRole('admin');
                queryExecute('DELETE FROM periods WHERE id=?', [(int)$_POST['id']]);
                flash('success', 'Periode dihapus jika belum digunakan.');
                redirect('periods');

            case 'kpi_save':
                requireRole('admin');
                $id = (int)($_POST['id'] ?? 0);
                $employeeId = (int)($_POST['employee_id'] ?? 0);
                $periodId = (int)($_POST['period_id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $target = $_POST['target'] ?? '';
                $unit = trim($_POST['unit'] ?? '');
                $weightInput = $_POST['weight'] ?? '';
                $weight = (float)$weightInput;
                if ($employeeId <= 0 || $periodId <= 0 || $name === '' || $target === '' || $unit === '' || !is_numeric($weightInput) || $weight <= 0 || $weight > 100) throw new RuntimeException('Data KPI tidak valid. Bobot harus lebih dari 0 dan maksimal 100%.');
                $sumSql = 'SELECT COALESCE(SUM(weight),0) AS total FROM kpis WHERE employee_id=? AND period_id=?' . ($id ? ' AND id<>?' : '');
                $sumParams = [$employeeId, $periodId];
                if ($id) $sumParams[] = $id;
                $existingWeight = (float)queryValue($sumSql, $sumParams);
                if ($existingWeight + $weight > 100.000001) throw new RuntimeException('Total bobot KPI untuk karyawan dan periode ini akan menjadi ' . number_format($existingWeight + $weight, 2, ',', '.') . '%. Maksimal total bobot adalah 100%.');
                if ($id) queryExecute('UPDATE kpis SET employee_id=?,period_id=?,name=?,target=?,unit=?,weight=?,updated_at=NOW() WHERE id=?', [$employeeId, $periodId, $name, $target, $unit, $weight, $id]);
                else queryExecute('INSERT INTO kpis(employee_id,period_id,name,target,unit,weight,created_at,updated_at) VALUES(?,?,?,?,?,?,NOW(),NOW())', [$employeeId, $periodId, $name, $target, $unit, $weight]);
                flash('success', 'KPI berhasil disimpan. Total bobot KPI karyawan pada periode ini tidak melebihi 100%.');
                redirect('kpis');

            case 'kpi_delete':
                requireRole('admin');
                queryExecute('DELETE FROM kpis WHERE id=?', [(int)$_POST['id']]);
                flash('success', 'KPI dihapus jika belum memiliki catatan realisasi.');
                redirect('kpis');

            case 'employee_kpi':
                requireRole('employee');
                $id = (int)($_POST['kpi_id'] ?? 0);
                if ($id <= 0) throw new RuntimeException('ID KPI tidak valid. Buka kembali KPI dari menu KPI Saya.');
                $k = queryOne('SELECT k.id,p.status AS period_status FROM kpis k JOIN periods p ON p.id=k.period_id WHERE k.id=? AND k.employee_id=?', [$id, (int)currentUser()['id']]);
                if (!$k) throw new RuntimeException('KPI tidak ditemukan atau bukan milik akun yang sedang login.');
                if ($k['period_status'] !== 'open') throw new RuntimeException('Periode KPI ini sudah ditutup sehingga pencapaian tidak dapat diubah.');
                $old = queryOne('SELECT * FROM performance_records WHERE kpi_id=?', [$id]);
                if ($old && in_array($old['status'], ['submitted', 'published'], true)) throw new RuntimeException('Pencapaian yang sudah dikirim tidak dapat diubah.');
                $actual = trim((string)($_POST['actual'] ?? ''));
                $act = $_POST['submit_action'] ?? 'draft';
                if (!in_array($act, ['draft', 'submit'], true)) throw new RuntimeException('Aksi pengisian KPI tidak valid.');
                if ($act === 'submit' && $actual === '') throw new RuntimeException('Isi realisasi sebelum mengirim KPI.');
                if ($actual !== '' && (!is_numeric($actual) || (float)$actual < 0)) throw new RuntimeException('Realisasi harus berupa angka positif.');
                $evidence = $old['evidence_path'] ?? null;
                if (!empty($_FILES['evidence']['name'])) {
                    $f = $_FILES['evidence'];
                    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) throw new RuntimeException('File bukti maksimal 2 MB.');
                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
                    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
                    if (!$ext) throw new RuntimeException('Bukti harus berupa JPG, PNG, atau WEBP.');
                    $file = bin2hex(random_bytes(12)) . '.' . $ext;
                    $uploadDir = __DIR__ . '/../public/uploads/';
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) throw new RuntimeException('Folder upload tidak dapat dibuat.');
                    if (!move_uploaded_file($f['tmp_name'], $uploadDir . $file)) throw new RuntimeException('Gagal menyimpan bukti.');
                    $evidence = $file;
                }
                $status = $act === 'submit' ? 'submitted' : 'draft';
                if ($old) queryExecute('UPDATE performance_records SET actual=?,description=?,evidence_path=?,status=?,updated_at=NOW() WHERE id=?', [$actual === '' ? null : $actual, $_POST['description'] ?? null, $evidence, $status, (int)$old['id']]);
                else queryExecute('INSERT INTO performance_records(kpi_id,actual,description,evidence_path,status,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())', [$id, $actual === '' ? null : $actual, $_POST['description'] ?? null, $evidence, $status]);
                flash('success', $status === 'submitted' ? 'Pencapaian berhasil dikirim.' : 'Draft berhasil disimpan.');
                redirect('my-kpis');

            case 'assessment_rate':
                requireRole('admin');
                $rating = (int)($_POST['rating'] ?? 0);
                if ($rating < 1 || $rating > 5) throw new RuntimeException('Rating harus 1 sampai 5.');
                queryExecute('UPDATE performance_records SET rating=?,admin_notes=?,updated_at=NOW() WHERE id=? AND status IN ("submitted","published")', [$rating, $_POST['admin_notes'] ?? null, (int)$_POST['record_id']]);
                flash('success', 'Penilaian berhasil disimpan.');
                redirect('assessments');

            case 'publish':
                requireRole('admin');
                $emp = (int)$_POST['employee_id'];
                $period = (int)$_POST['period_id'];
                $result = calculateResult($emp, $period);
                $count = (int)queryValue('SELECT COUNT(*) AS total FROM kpis k JOIN performance_records r ON r.kpi_id=k.id WHERE k.employee_id=? AND k.period_id=? AND r.status IN ("submitted","published")', [$emp, $period]);
                if ($count !== count($result['details'])) throw new RuntimeException('Semua KPI harus dikirim dan dinilai terlebih dahulu.');
                queryExecute('UPDATE performance_records r JOIN kpis k ON k.id=r.kpi_id SET r.status="published",r.updated_at=NOW() WHERE k.employee_id=? AND k.period_id=? AND r.status="submitted"', [$emp, $period]);
                flash('success', 'Hasil penilaian berhasil dipublikasikan.');
                redirect('assessments');

            default:
                throw new RuntimeException('Aksi tidak dikenal.');
        }
    } catch (Throwable $e) {
        $message = $e instanceof mysqli_sql_exception ? 'Data tidak dapat disimpan. Periksa duplikasi atau relasi data.' : $e->getMessage();
        flash('danger', $message);
        $back = $_POST['_back'] ?? 'dashboard';
        redirect($back);
    }
}
