<?php $r = $data['row'];
if (!$r): ?><div class="alert alert-warning">Data penilaian tidak ditemukan.</div><?php else: ?>
    <h4 class="fw-bold mb-3">Detail Penilaian</h4>
    <div class="card p-4 mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-secondary small">Karyawan</div><strong><?= e($r['employee']) ?></strong>
            </div>
            <div class="col-md-6">
                <div class="text-secondary small">KPI</div><strong><?= e($r['kpi']) ?></strong>
            </div>
            <div class="col-md-6">
                <div class="text-secondary small">Periode</div><?= monthName((int)$r['month']) . ' ' . $r['year'] ?>
            </div>
            <div class="col-md-4">
                <div class="text-secondary small">Target</div><strong><?= e($r['target']) ?> <?= e($r['unit']) ?></strong>
            </div>
            <div class="col-md-4">
                <div class="text-secondary small">Realisasi Karyawan</div><strong><?= e($r['actual']) ?>
                    <?= e($r['unit']) ?></strong>
            </div>
            <div class="col-md-4">
                <div class="text-secondary small">Bobot KPI</div><strong><?= e($r['weight']) ?>%</strong>
            </div>
            <div class="col-12">
                <div class="text-secondary small">Deskripsi</div><?= nl2br(e($r['description'])) ?>
            </div><?php if ($r['evidence_path']): ?><div class="col-12"><a target="_blank"
                        href="public/uploads/<?= e($r['evidence_path']) ?>">Lihat bukti pekerjaan</a></div><?php endif; ?>
        </div>
    </div>
    <div class="card p-4">
        <h5>Berikan Penilaian</h5>
        <form method="post"><input type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
                value="assessment_rate"><input type="hidden" name="record_id" value="<?= $r['id'] ?>"><input type="hidden"
                name="_back" value="assessments">
            <div class="mb-3"><label class="form-label">Rating (1–5)</label><select class="form-select" name="rating"
                    required><?php for ($i = 1; $i <= 5; $i++): ?><option value="<?= $i ?>"
                            <?= (int)$r['rating'] === $i ? 'selected' : '' ?>><?= $i ?> -
                            <?= [1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik'][$i] ?></option>
                    <?php endfor; ?></select></div>
            <div class="mb-3"><label class="form-label">Catatan Admin</label><textarea class="form-control"
                    name="admin_notes" rows="3"><?= e($r['admin_notes']) ?></textarea></div><button class="btn btn-primary"
                <?= $r['status'] === 'published' ? 'disabled' : '' ?>>Simpan Rating</button>
        </form>
        <hr>
        <form method="post" onsubmit="return confirm('Publikasikan seluruh hasil KPI karyawan untuk periode ini?')"><input
                type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="publish"><input
                type="hidden" name="employee_id" value="<?= $r['employee_id'] ?>"><input type="hidden" name="period_id"
                value="<?= $r['period_id'] ?>"><input type="hidden" name="_back" value="assessments"><button
                class="btn btn-success">Publikasikan Hasil Karyawan untuk Periode Ini</button></form>
    </div><a class="btn btn-light mt-3" href="<?= url('assessments') ?>">Kembali</a><?php endif; ?>