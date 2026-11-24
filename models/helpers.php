<?php
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Mengikat parameter agar input pengguna tetap aman dari SQL injection.
function bindQueryParams(mysqli_stmt $stmt, array $params): void
{
    if (!$params) return;
    $types = '';
    foreach ($params as $value) {
        $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
    }
    $values = [];
    foreach ($params as $index => $value) {
        $values[$index] = $params[$index];
    }
    $arguments = [$types];
    foreach ($values as $index => &$value) {
        $arguments[] = &$value;
    }
    call_user_func_array([$stmt, 'bind_param'], $arguments);
}

// Menjalankan SELECT dan mengambil seluruh baris sebagai array.
function queryAll(string $sql, array $params = []): array
{
    global $koneksi;

    if ($params) {
        $stmt = $koneksi->prepare($sql);
        bindQueryParams($stmt, $params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    $result = $koneksi->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Menjalankan SELECT dan mengambil satu baris.
function queryOne(string $sql, array $params = []): ?array
{
    $rows = queryAll($sql, $params);
    return $rows[0] ?? null;
}

// Mengambil satu nilai dari hasil SELECT, misalnya COUNT atau SUM.
function queryValue(string $sql, array $params = [])
{
    $row = queryOne($sql, $params);
    return $row ? reset($row) : null;
}

// Menjalankan INSERT, UPDATE, atau DELETE.
function queryExecute(string $sql, array $params = []): int
{
    global $koneksi;

    if ($params) {
        $stmt = $koneksi->prepare($sql);
        bindQueryParams($stmt, $params);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    $koneksi->query($sql);
    return $koneksi->affected_rows;
}
function url(string $page = 'dashboard', array $params = []): string
{
    return 'index.php?' . http_build_query(array_merge(['page' => $page], $params));
}
function redirect(string $page = 'dashboard', array $params = []): never
{
    header('Location: ' . url($page, $params));
    exit;
}
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function getFlash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
function csrf(): string
{
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function verifyCsrf(): void
{
    if (!hash_equals(csrf(), $_POST['_csrf'] ?? '')) {
        http_response_code(419);
        exit('Permintaan tidak valid. Muat ulang halaman.');
    }
}
function requireLogin(): void
{
    if (empty($_SESSION['user'])) redirect('login');
}
function requireRole(string $role): void
{
    requireLogin();
    if (($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}
function currentUser(): array
{
    return $_SESSION['user'] ?? [];
}
function monthName(int $month): string
{
    return [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'][$month] ?? '-';
}
function grade(float $score): string
{
    return $score >= 90 ? 'Sangat Baik' : ($score >= 80 ? 'Baik' : ($score >= 70 ? 'Cukup' : ($score >= 60 ? 'Kurang' : 'Sangat Kurang')));
}
function calculateResult(int $employeeId, int $periodId): array
{
    $rows = queryAll('SELECT k.id,k.name,k.weight,pr.rating,pr.status FROM kpis k LEFT JOIN performance_records pr ON pr.kpi_id=k.id WHERE k.employee_id=? AND k.period_id=? ORDER BY k.id', [$employeeId, $periodId]);
    if (!$rows) throw new RuntimeException('Karyawan belum memiliki KPI pada periode ini.');
    $total = 0;
    foreach ($rows as $r) $total += (float)$r['weight'];
    if (abs($total - 100) > 0.001) throw new RuntimeException('Total bobot KPI harus tepat 100%. Saat ini ' . number_format($total, 2) . '%.');
    $sum = 0;
    foreach ($rows as &$r) {
        if ($r['rating'] === null) throw new RuntimeException('Semua KPI harus memiliki rating sebelum hasil dihitung.');
        $r['score'] = (int)$r['rating'] * 20;
        $r['contribution'] = $r['score'] * ((float)$r['weight'] / 100);
        $sum += $r['contribution'];
    }
    unset($r);
    $score = round($sum, 2);
    return ['details' => $rows, 'total_weight' => round($total, 2), 'final_score' => $score, 'grade' => grade($score)];
}
