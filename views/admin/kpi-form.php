<?php $r = $data['row']; ?><h4 class="fw-bold mb-3"><?= $r ? 'Edit' : 'Tambah' ?> KPI</h4>
<div class="card p-4">
    <form method="post"><input type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
            value="kpi_save"><input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>"><input type="hidden"
            name="_back" value="kpis">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Karyawan</label><select class="form-select"
                    name="employee_id" required><?php foreach ($data['employees'] as $u): ?><option
                        value="<?= $u['id'] ?>"
                        <?= (int)($r['employee_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>>
                        <?= e($u['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Periode</label><select class="form-select" name="period_id"
                    required><?php foreach ($data['periods'] as $p): ?><option value="<?= $p['id'] ?>"
                        <?= (int)($r['period_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= monthName((int)$p['month']) . ' ' . $p['year'] ?></option><?php endforeach; ?></select>
            </div>
            <div class="col-md-6"><label class="form-label">Nama KPI</label><input class="form-control" name="name"
                    value="<?= e($r['name'] ?? '') ?>" required></div>
            <div class="col-md-3"><label class="form-label">Target</label><input type="number" step="0.01" min="0"
                    class="form-control" name="target" value="<?= e($r['target'] ?? '') ?>" required></div>
            <div class="col-md-3"><label class="form-label">Satuan</label><input class="form-control" name="unit"
                    value="<?= e($r['unit'] ?? '') ?>" required></div>
            <div class="col-md-3"><label class="form-label">Bobot (%)</label>
                <div class="form-text mb-2">Bobot dapat diisi bertahap. Total untuk karyawan dan periode yang sama tidak
                    boleh melebihi 100%, dan harus tepat 100% sebelum hasil penilaian dipublikasikan.</div><input
                    type="number" step="0.01" min="0.01" max="100" class="form-control" name="weight"
                    value="<?= e($r['weight'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a class="btn btn-light"
                href="<?= url('kpis') ?>">Batal</a></div>
    </form>
</div>