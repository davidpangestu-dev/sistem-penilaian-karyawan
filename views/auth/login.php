<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Sistem Penilaian Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', sans-serif
        }

        .login-card {
            max-width: 430px;
            margin: 8vh auto;
            background: white;
            padding: 36px;
            border-radius: 16px;
            box-shadow: 0 8px 30px #17255418
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h4 class="fw-bold text-center">Sistem Penilaian Karyawan</h4>
        <p class="text-secondary text-center mb-4">Silakan masuk ke akun Anda</p><?php $f = getFlash();
                                                                                    if ($f): ?><div
                class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div><?php endif; ?><form method="post">
            <input type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="login">
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control"
                    name="email" required autofocus></div>
            <div class="mb-4"><label class="form-label">Password</label><input type="password" class="form-control"
                    name="password" required></div><button class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</body>

</html>