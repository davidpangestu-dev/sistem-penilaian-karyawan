<h4 class="fw-bold">Hasil Penilaian</h4>
<p class="text-secondary">Hasil penilaian yang telah dipublikasikan oleh Admin.</p>
<form class="row mb-3"><input type="hidden" name="page" value="results">
    <div class="col-md-4"><select class="form-select" name="period_id"
            onchange="this.form.submit()"><?php foreach ($data['periods'] as $p): ?><option value="<?= $p['id'] ?>"
                    <?= (int)$data['period_id'] === (int)$p['id'] ? 'selected' : '' ?>>
                    <?= monthName((int)$p['month']) . ' ' . $p['year'] ?></option><?php endforeach; ?></select></div>
</form><?php if ($data['result']): ?><div class="card p-4 mb-3">
        <div class="text-secondary">Nilai Akhir</div>
        <div class="display-4 fw-bold text-primary"><?= number_format($data['result']['final_score'], 2) ?></div>
        <h5><?= e($data['result']['grade']) ?></h5>
        <div class="text-secondary">Total bobot: <?= e($data['result']['total_weight']) ?>%</div>
    </div>
    <div class="card p-3">
        <h5>Rincian Penilaian</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>KPI</th>
                        <th>Rating</th>
                        <th>Skor</th>
                        <th>Bobot</th>
                        <th>Kontribusi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ($data['result']['details'] as $r): ?><tr>
                            <td><?= e($r['name']) ?></td>
                            <td><?= e($r['rating']) ?></td>
                            <td><?= e($r['score']) ?></td>
                            <td><?= e($r['weight']) ?>%</td>
                            <td><?= number_format($r['contribution'], 2) ?></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div><?php else: ?><div class="alert alert-info">Belum ada hasil penilaian lengkap yang dipublikasikan untuk periode
        ini.</div><?php endif; ?>