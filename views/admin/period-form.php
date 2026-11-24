<?php $r = $data['row']; ?><h4 class="fw-bold mb-3"><?= $r ? 'Edit' : 'Tambah' ?> Periode</h4>
<div class="card p-4">
        <form method="post"><input type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
                        value="period_save"><input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>"><input type="hidden"
                        name="_back" value="periods">
                <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Bulan</label><select class="form-select"
                                        name="month"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>"
                                                        <?= (int)($r['month'] ?? date('n')) === $i ? 'selected' : '' ?>><?= monthName($i) ?></option>
                                        <?php endfor; ?></select></div>
                        <div class="col-md-4"><label class="form-label">Tahun</label><input type="number" class="form-control"
                                        name="year" value="<?= e($r['year'] ?? date('Y')) ?>" min="2000" max="2100" required></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status">
                                        <option value="open" <?= ($r['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
                                        <option value="closed" <?= ($r['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                                </select></div>
                </div>
                <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a class="btn btn-light"
                                href="<?= url('periods') ?>">Batal</a></div>
        </form>
</div>