<h4 class="fw-bold mb-1">Dashboard Karyawan</h4>
<p class="text-secondary">Selamat datang, <?= e(currentUser()['name']) ?>.</p>
<div class="row g-3">
    <div class="col-md-4"><a class="text-decoration-none" href="<?= url('my-kpis') ?>">
            <div class="card p-4">
                <div class="text-secondary">Aktivitas</div>
                <h5 class="mt-2">KPI Saya</h5>
                <p class="text-secondary mb-0">Lihat target dan isi realisasi KPI.</p>
            </div>
        </a></div>
    <div class="col-md-4"><a class="text-decoration-none" href="<?= url('history') ?>">
            <div class="card p-4">
                <div class="text-secondary">Riwayat</div>
                <h5 class="mt-2">Riwayat Penilaian</h5>
                <p class="text-secondary mb-0">Lihat status pengajuan KPI.</p>
            </div>
        </a></div>
    <div class="col-md-4"><a class="text-decoration-none" href="<?= url('results') ?>">
            <div class="card p-4">
                <div class="text-secondary">Hasil</div>
                <h5 class="mt-2">Hasil Penilaian</h5>
                <p class="text-secondary mb-0">Lihat hasil yang sudah dipublikasikan.</p>
            </div>
        </a></div>
</div>