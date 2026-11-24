<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold">Data Karyawan</h4>
        <p class="text-secondary">Kelola akun dan informasi karyawan.</p>
    </div><a class="btn btn-primary" href="<?= url('employee-form') ?>">+ Tambah Karyawan</a>
</div>
<div class="card p-3"><input id="searchEmployee" class="form-control mb-3" placeholder="Cari karyawan...">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody"><?php foreach ($data['rows'] as $i => $r): ?><tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($r['employee_number']) ?></td>
                        <td><?= e($r['name']) ?></td>
                        <td><?= e($r['email']) ?></td>
                        <td><?= e($r['position']) ?></td>
                        <td><?= e($r['department']) ?></td>
                        <td><?= e($r['status']) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary"
                                href="<?= url('employee-form', ['id' => $r['id']]) ?>">Edit</a>
                            <form class="d-inline" method="post" onsubmit="return confirm('PERINGATAN: Menghapus karyawan ini juga akan menghapus seluruh KPI dan riwayat penilaiannya. Lanjutkan?')"><input
                                    type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
                                    value="employee_delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button
                                    class="btn btn-sm btn-outline-danger">Hapus</button></form>
                        </td>
                    </tr><?php endforeach;
                                            if (!$data['rows']): ?><tr>
                        <td colspan="8" class="text-center">Belum ada karyawan.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>