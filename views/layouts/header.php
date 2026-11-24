<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sistem Penilaian Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/css/custom.css" rel="stylesheet">
    <style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        height: 100vh;
        padding: 24px 16px;
        color: #fff;
        overflow: auto
    }

    .sidebar a {
        display: block;
        color: #dbeafe;
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 8px;
        margin: 4px 0
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #263b78;
        color: #fff
    }

    .main-content {
        margin-left: 250px;
        min-height: 100vh
    }

    .topbar {
        background: #fff;
        padding: 16px 28px;
        border-bottom: 1px solid #e5e7eb
    }

    .content-wrap {
        padding: 28px
    }

    .brand {
        font-weight: 700;
        font-size: 18px;
        margin: 4px 10px 26px
    }

    .card {
        border: 0;
        box-shadow: 0 3px 14px rgba(15, 23, 42, .06);
        border-radius: 12px
    }

    .table th {
        white-space: nowrap;
        background: #f8fafc
    }

    .form-label {
        font-weight: 600
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700
    }

    @media(max-width:768px) {
        .sidebar {
            position: relative;
            width: 100%;
            height: auto
        }

        .main-content {
            margin-left: 0
        }

        .content-wrap {
            padding: 16px
        }
    }
    </style>
</head>

<body>
    <?php $user=currentUser(); $isAdmin=$user['role']==='admin'; $active=$_GET['page']??'dashboard'; $flash=getFlash(); ?>
    <aside class="sidebar" style="background:#172554">
        <div class="brand">SISTEM PENILAIAN<br><small class="fw-normal text-info">KINERJA KARYAWAN</small></div>
        <a class="<?= $active==='dashboard'?'active':'' ?>" href="<?= url('dashboard') ?>">▦ &nbsp; Dashboard</a>
        <?php if($isAdmin): ?><div class="text-uppercase small text-secondary mt-4 mb-2 px-2">Manajemen</div><a
            href="<?= url('employees') ?>">♙ &nbsp; Data Karyawan</a><a href="<?= url('periods') ?>">▣ &nbsp; Periode
            Penilaian</a><a href="<?= url('kpis') ?>">◎ &nbsp; Data KPI</a>
        <div class="text-uppercase small text-secondary mt-4 mb-2 px-2">Penilaian</div><a
            href="<?= url('assessments') ?>">✓ &nbsp; Penilaian Kinerja</a><a href="<?= url('reports') ?>">▤ &nbsp;
            Laporan</a><?php else: ?><div class="text-uppercase small text-secondary mt-4 mb-2 px-2">Aktivitas</div><a
            href="<?= url('my-kpis') ?>">◎ &nbsp; KPI Saya</a><a href="<?= url('history') ?>">◷ &nbsp; Riwayat
            Penilaian</a><a href="<?= url('results') ?>">▤ &nbsp; Hasil Penilaian</a><?php endif; ?>
        <div class="mt-5 pt-4 border-top border-secondary">
            <div class="small text-white-50">Masuk sebagai</div>
            <div class="fw-semibold"><?= e($user['name']) ?></div>
            <div class="small text-info mb-3"><?= $isAdmin?'Administrator':'Karyawan' ?></div>
            <form method="post"><input type="hidden" name="_csrf" value="<?= csrf() ?>"><button
                    class="btn btn-sm btn-outline-light w-100" name="action" value="logout">Keluar</button></form>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar d-flex justify-content-between align-items-center">
            <div><strong><?= $isAdmin?'Panel Administrator':'Panel Karyawan' ?></strong></div>
            <div class="text-secondary small"><?= date('d/m/Y') ?></div>
        </header>
        <div class="content-wrap">
            <?php if($flash): ?><div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                <?= e($flash['message']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>