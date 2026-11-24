<h4 class="fw-bold">Penilaian Kinerja</h4>
<p class="text-secondary">Tinjau realisasi KPI dan berikan rating kepada karyawan.</p>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>KPI</th>
                    <th>Periode</th>
                    <th>Realisasi</th>
                    <th>Bobot</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                        <td><?= e($r['employee']) ?></td>
                        <td><?= e($r['kpi']) ?></td>
                        <td><?= monthName((int)$r['month']) . ' ' . $r['year'] ?></td>
                        <td><?= e($r['actual']) ?></td>
                        <td><?= e($r['weight']) ?>%</td>
                        <td><?= e($r['status']) ?></td>
                        <td><?= e($r['rating'] ?? '-') ?></td>
                        <td><a class="btn btn-sm btn-outline-primary"
                                href="<?= url('assessment-detail', ['id' => $r['id']]) ?>">Detail / Nilai</a></td>
                    </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                        <td colspan="8" class="text-center">Belum ada pengajuan penilaian.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>