<?php
session_start();
require __DIR__ . '/koneksi.php';
require __DIR__ . '/models/helpers.php';
require __DIR__ . '/controllers/actions.php';
handlePost();
$page = $_GET['page'] ?? (empty($_SESSION['user']) ? 'login' : 'dashboard');
if ($page === 'login' && !empty($_SESSION['user'])) redirect('dashboard');
if ($page !== 'login' && $page !== 'dashboard') requireLogin();
$adminPages = ['employees', 'employee-form', 'periods', 'period-form', 'kpis', 'kpi-form', 'assessments', 'assessment-detail', 'reports'];
$employeePages = ['my-kpis', 'my-kpi-detail', 'history', 'results'];
if (in_array($page, $adminPages, true)) requireRole('admin');
if (in_array($page, $employeePages, true)) requireRole('employee');
if ($page === 'dashboard' && empty($_SESSION['user'])) redirect('login');
if ($page === 'login') {
    require __DIR__ . '/views/auth/login.php';
    exit;
}
$data = [];
switch ($page) {
    case 'dashboard':
        if (currentUser()['role'] === 'admin') {
            $data['employees'] = (int)queryValue("SELECT COUNT(*) FROM users WHERE role='employee'");
            $data['periods'] = (int)queryValue('SELECT COUNT(*) FROM periods');
            $data['completed'] = (int)queryValue("SELECT COUNT(*) FROM performance_records WHERE status='published'");
            $data['pending'] = (int)queryValue("SELECT COUNT(*) FROM performance_records WHERE status='submitted'");
            $data['recent'] = queryAll("SELECT u.name employee,p.month,p.year,k.name kpi,COALESCE(r.status,'Belum diisi') status FROM kpis k JOIN users u ON u.id=k.employee_id JOIN periods p ON p.id=k.period_id LEFT JOIN performance_records r ON r.kpi_id=k.id ORDER BY COALESCE(r.updated_at,k.updated_at) DESC,k.id DESC LIMIT 10");
        }
        break;
    case 'employees':
        $data['rows'] = queryAll("SELECT * FROM users WHERE role='employee' ORDER BY created_at DESC");
        break;
    case 'employee-form':
        $data['row'] = queryOne('SELECT * FROM users WHERE id=? AND role="employee"', [(int)($_GET['id'] ?? 0)]) ?? [];
        break;
    case 'periods':
        $data['rows'] = queryAll('SELECT * FROM periods ORDER BY year DESC,month DESC');
        break;
    case 'period-form':
        $data['row'] = queryOne('SELECT * FROM periods WHERE id=?', [(int)($_GET['id'] ?? 0)]) ?? [];
        break;
    case 'kpis':
        $data['search'] = trim($_GET['q'] ?? '');
        $sql = 'SELECT k.*,u.name employee,p.month,p.year FROM kpis k JOIN users u ON u.id=k.employee_id JOIN periods p ON p.id=k.period_id';
        if ($data['search'] !== '') {
            $term = '%' . $data['search'] . '%';
            $data['rows'] = queryAll($sql . ' WHERE k.name LIKE ? OR u.name LIKE ? ORDER BY p.year DESC,p.month DESC,u.name,k.name', [$term, $term]);
        } else {
            $data['rows'] = queryAll($sql . ' ORDER BY p.year DESC,p.month DESC,u.name,k.name');
        }
        $data['weight_totals'] = queryAll('SELECT u.name employee,p.month,p.year,SUM(k.weight) total_weight FROM kpis k JOIN users u ON u.id=k.employee_id JOIN periods p ON p.id=k.period_id GROUP BY k.employee_id,k.period_id,u.name,p.month,p.year ORDER BY p.year DESC,p.month DESC,u.name');
        break;
    case 'kpi-form':
        $data['row'] = queryOne('SELECT * FROM kpis WHERE id=?', [(int)($_GET['id'] ?? 0)]) ?? [];
        $data['employees'] = queryAll("SELECT id,name FROM users WHERE role='employee' AND status='active' ORDER BY name");
        $data['periods'] = queryAll('SELECT * FROM periods ORDER BY year DESC,month DESC');
        break;
    case 'assessments':
        $data['rows'] = queryAll("SELECT r.*,k.name kpi,k.target,k.unit,k.weight,k.employee_id,k.period_id,u.name employee,p.month,p.year FROM performance_records r JOIN kpis k ON k.id=r.kpi_id JOIN users u ON u.id=k.employee_id JOIN periods p ON p.id=k.period_id WHERE r.status IN ('submitted','published') ORDER BY r.updated_at DESC");
        break;
    case 'assessment-detail':
        $data['row'] = queryOne('SELECT r.*,k.name kpi,k.target,k.unit,k.weight,k.employee_id,k.period_id,u.name employee,p.month,p.year FROM performance_records r JOIN kpis k ON k.id=r.kpi_id JOIN users u ON u.id=k.employee_id JOIN periods p ON p.id=k.period_id WHERE r.id=?', [(int)($_GET['id'] ?? 0)]) ?? [];
        break;
    case 'reports':
        $data['periods'] = queryAll('SELECT * FROM periods ORDER BY year DESC,month DESC');
        $pid = (int)($_GET['period_id'] ?? ($data['periods'][0]['id'] ?? 0));
        $data['period_id'] = $pid;
        $data['rows'] = [];
        if ($pid) {
            $employees = queryAll("SELECT DISTINCT u.id,u.name,u.employee_number FROM users u JOIN kpis k ON k.employee_id=u.id WHERE k.period_id=? AND u.role='employee' ORDER BY u.name", [$pid]);
            foreach ($employees as $emp) {
                try {
                    $res = calculateResult((int)$emp['id'], $pid);
                    $published = (int)queryValue("SELECT COUNT(*) FROM kpis k JOIN performance_records r ON r.kpi_id=k.id WHERE k.employee_id=? AND k.period_id=? AND r.status='published'", [$emp['id'], $pid]);
                    if ($published === count($res['details'])) $data['rows'][] = $emp + $res;
                } catch (Throwable $e) {
                }
            }
        }
        break;
    case 'my-kpis':
        $data['rows'] = queryAll('SELECT k.*,p.month,p.year,p.status period_status,r.actual,r.status record_status,r.id record_id FROM kpis k JOIN periods p ON p.id=k.period_id LEFT JOIN performance_records r ON r.kpi_id=k.id WHERE k.employee_id=? ORDER BY p.year DESC,p.month DESC', [currentUser()['id']]);
        break;
    case 'my-kpi-detail':
        $data['row'] = queryOne('SELECT k.id AS kpi_id,k.employee_id,k.period_id,k.name,k.target,k.unit,k.weight,p.month,p.year,p.status AS period_status,pr.id AS record_id,pr.actual,pr.description,pr.evidence_path,pr.rating,pr.admin_notes,pr.status AS record_status FROM kpis k JOIN periods p ON p.id=k.period_id LEFT JOIN performance_records pr ON pr.kpi_id=k.id WHERE k.id=? AND k.employee_id=?', [(int)($_GET['id'] ?? 0), currentUser()['id']]) ?? [];
        break;
    case 'history':
        $data['rows'] = queryAll('SELECT r.*,k.name kpi,p.month,p.year FROM performance_records r JOIN kpis k ON k.id=r.kpi_id JOIN periods p ON p.id=k.period_id WHERE k.employee_id=? ORDER BY r.updated_at DESC', [currentUser()['id']]);
        break;
    case 'results':
        $data['periods'] = queryAll("SELECT DISTINCT p.* FROM periods p JOIN kpis k ON k.period_id=p.id JOIN performance_records r ON r.kpi_id=k.id WHERE k.employee_id=? AND r.status='published' ORDER BY p.year DESC,p.month DESC", [currentUser()['id']]);
        $pid = (int)($_GET['period_id'] ?? ($data['periods'][0]['id'] ?? 0));
        $data['period_id'] = $pid;
        $data['result'] = null;
        if ($pid) {
            try {
                $res = calculateResult((int)currentUser()['id'], $pid);
                $published = (int)queryValue("SELECT COUNT(*) FROM kpis k JOIN performance_records r ON r.kpi_id=k.id WHERE k.employee_id=? AND k.period_id=? AND r.status='published'", [currentUser()['id'], $pid]);
                if ($published === count($res['details'])) $data['result'] = $res;
            } catch (Throwable $e) {
            }
        }
        break;
    default:
        http_response_code(404);
        $page = 'not-found';
}
require __DIR__ . '/views/layouts/header.php';
$file = __DIR__ . '/views/' . (currentUser()['role'] === 'admin' ? 'admin' : 'employee') . '/' . $page . '.php';
if (!is_file($file)) {
    $map = ['dashboard' => __DIR__ . '/views/' . currentUser()['role'] . '/dashboard.php'];
    $file = $map[$page] ?? '';
}
if ($file && is_file($file)) require $file;
else echo '<div class="alert alert-warning">Halaman tidak ditemukan.</div>';
require __DIR__ . '/views/layouts/footer.php';
