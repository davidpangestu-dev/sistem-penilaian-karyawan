<h4 class="fw-bold">KPI Saya</h4>
<p class="text-secondary">Daftar indikator kinerja dan status pengisian Anda.</p>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Nama KPI</th>
                    <th>Target</th>
                    <th>Bobot</th>
                    <th>Realisasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                        <td><?= monthName((int)$r['month']) . ' ' . $r['year'] ?></td>
                        <td><?= e($r['name']) ?></td>
                        <td><?= e($r['target'] . ' ' . $r['unit']) ?></td>
                        <td><?= e($r['weight']) ?>%</td>
                        <td><?= e($r['actual'] ?? '-') ?></td>
                        <td><?= e($r['record_status'] ?? 'Belum diisi') ?></td>
                        <td><a class="btn btn-sm btn-outline-primary"
                                href="<?= url('my-kpi-detail', ['id' => $r['id']]) ?>">Detail / Isi</a></td>
                    </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                        <td colspan="7" class="text-center">Belum ada KPI yang ditugaskan.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>