<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="fw-bold">Data KPI</h4>
        <p class="text-secondary">Indikator, target, dan bobot karyawan.</p>
    </div><a class="btn btn-primary" href="<?= url('kpi-form') ?>">+ Tambah KPI</a>
</div>
<div class="card p-3 mb-3">
    <form method="get" action="index.php" class="row g-2"><input type="hidden" name="page" value="kpis">
        <div class="col-md-8"><input type="search" class="form-control" name="q" value="<?= e($data['search'] ?? '') ?>"
                placeholder="Cari nama KPI atau nama karyawan..."></div>
        <div class="col-md-auto"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i>
                Cari</button> <a class="btn btn-light" href="<?= url('kpis') ?>">Reset</a></div>
    </form>
</div>
<?php if (!empty($data['weight_totals'])): ?>

<?php endif; ?>
<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-2"><span
            class="text-secondary small"><?php if (($data['search'] ?? '') !== ''): ?>Hasil pencarian:
            <strong><?= e($data['search']) ?></strong> · <?php endif; ?><?= count($data['rows']) ?> KPI ditemukan</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Periode</th>
                    <th>Nama KPI</th>
                    <th>Target</th>
                    <th>Bobot</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                        <td><?= e($r['employee']) ?></td>
                        <td><?= monthName((int)$r['month']) . ' ' . $r['year'] ?></td>
                        <td><?= e($r['name']) ?></td>
                        <td><?= e($r['target'] . ' ' . $r['unit']) ?></td>
                        <td><?= e($r['weight']) ?>%</td>
                        <td><a class="btn btn-sm btn-outline-primary"
                                href="<?= url('kpi-form', ['id' => $r['id']]) ?>">Edit</a>
                            <form class="d-inline" method="post" onsubmit="return confirm('Hapus KPI?')"><input
                                    type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
                                    value="kpi_delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button
                                    class="btn btn-sm btn-outline-danger">Hapus</button></form>
                        </td>
                    </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            <?php if (($data['search'] ?? '') !== ''): ?>Tidak ada KPI atau karyawan yang cocok dengan
                            pencarian
                            tersebut.<?php else: ?>Belum ada KPI.<?php endif; ?></td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>