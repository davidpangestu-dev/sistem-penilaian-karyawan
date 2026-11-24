<?php $r = $data['row']; ?><h4 class="fw-bold mb-3"><?= $r ? 'Edit' : 'Tambah' ?> Karyawan</h4>
<div class="card p-4">
    <form method="post"><input type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
            value="employee_save"><input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>"><input type="hidden"
            name="_back" value="employees">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="name"
                    required value="<?= e($r['name'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control"
                    name="email" required value="<?= e($r['email'] ?? '') ?>"></div><?php if (!$r): ?><div
                    class="col-md-6"><label class="form-label">Password Awal</label><input type="password"
                        class="form-control" name="password" minlength="8" required></div><?php endif; ?><div
                class="col-md-6"><label class="form-label">NIP</label><input class="form-control" name="employee_number"
                    value="<?= e($r['employee_number'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Jabatan</label><input class="form-control" name="position"
                    value="<?= e($r['position'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Departemen</label><input class="form-control"
                    name="department" value="<?= e($r['department'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Telepon</label><input class="form-control" name="phone"
                    value="<?= e($r['phone'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status">
                    <option value="active" <?= ($r['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= ($r['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                </select></div>
        </div>
        <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a class="btn btn-light"
                href="<?= url('employees') ?>">Batal</a></div>
    </form>
</div>