<?php $r = $data['row'];
if (!$r): ?>
    <div class="alert alert-warning">KPI tidak ditemukan atau bukan milik akun Anda.</div>
<?php else: ?>
    <h4 class="fw-bold mb-3">Detail KPI</h4>
    <div class="card p-4 mb-3">
        <h5><?= e($r['name']) ?></h5>
        <p class="text-secondary mb-1">Periode: <?= monthName((int)$r['month']) . ' ' . $r['year'] ?></p>
        <div>Target: <strong><?= e($r['target'] . ' ' . $r['unit']) ?></strong> · Bobot:
            <strong><?= e($r['weight']) ?>%</strong>
        </div>
        <div>Status: <strong><?= e($r['record_status'] ?? 'Belum diisi') ?></strong></div>
    </div>
    <div class="card p-4">
        <h5>Isi Pencapaian</h5>
        <?php if ($r['period_status'] !== 'open' || in_array($r['record_status'] ?? '', ['submitted', 'published'], true)): ?>
            <div class="alert alert-info mb-0">
                <?php if ($r['period_status'] !== 'open'): ?>Periode penilaian ini sudah ditutup. Form tidak dapat diubah.
                <?php else: ?>Pencapaian sudah dikirim dan tidak dapat diubah.<?php endif; ?>
            </div>
        <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= csrf() ?>">
                <input type="hidden" name="action" value="employee_kpi">
                <input type="hidden" name="kpi_id" value="<?= (int)$r['kpi_id'] ?>">
                <input type="hidden" name="_back" value="my-kpis">
                <div class="mb-3"><label class="form-label">Realisasi Aktual</label><input type="number" min="0" step="0.01"
                        class="form-control" name="actual" value="<?= e($r['actual'] ?? '') ?>"></div>
                <div class="mb-3"><label class="form-label">Deskripsi Pekerjaan</label><textarea class="form-control"
                        name="description" rows="4"><?= e($r['description'] ?? '') ?></textarea></div>
                <div class="mb-3"><label class="form-label">Bukti Pekerjaan (JPG/PNG/WEBP, maks. 2 MB)</label><input type="file"
                        class="form-control" name="evidence" accept="image/jpeg,image/png,image/webp"></div>
                <button class="btn btn-outline-primary" name="submit_action" value="draft">Simpan Draft</button>
                <button class="btn btn-primary" name="submit_action" value="submit"
                    onclick="return confirm('Kirim pencapaian KPI? Setelah dikirim tidak dapat diubah.')">Kirim
                    Pencapaian</button>
            </form>
        <?php endif; ?>
    </div>
    <a class="btn btn-light mt-3" href="<?= url('my-kpis') ?>">Kembali</a>
<?php endif; ?>