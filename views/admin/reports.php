<h4 class="fw-bold">Laporan Penilaian</h4>
<p class="text-secondary">Rekap hasil penilaian yang sudah dipublikasikan.</p>
<form class="row g-2 mb-3"><input type="hidden" name="page" value="reports">
    <div class="col-md-4"><select class="form-select" name="period_id"
            onchange="this.form.submit()"><?php foreach ($data['periods'] as $p): ?><option value="<?= $p['id'] ?>"
                <?= (int)$data['period_id'] === (int)$p['id'] ? 'selected' : '' ?>>
                <?= monthName((int)$p['month']) . ' ' . $p['year'] ?></option><?php endforeach; ?></select></div>
</form>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>NIP</th>
                    <th>Total Bobot</th>
                    <th>Nilai Akhir</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                    <td><?= e($r['name']) ?></td>
                    <td><?= e($r['employee_number']) ?></td>
                    <td><?= e($r['total_weight']) ?>%</td>
                    <td class="fw-bold"><?= number_format($r['final_score'], 2) ?></td>
                    <td><?= e($r['grade']) ?></td>
                </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                    <td colspan="5" class="text-center">Belum ada hasil lengkap yang dipublikasikan pada periode ini.
                    </td>
                </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>