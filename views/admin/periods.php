<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold">Periode Penilaian</h4>
        <p class="text-secondary">Kelola periode penilaian bulanan.</p>
    </div><a class="btn btn-primary" href="<?= url('period-form') ?>">+ Tambah Periode</a>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                        <td><?= monthName((int)$r['month']) . ' ' . $r['year'] ?></td>
                        <td><span
                                class="badge text-bg-<?= $r['status'] === 'open' ? 'success' : 'secondary' ?>"><?= e($r['status']) ?></span>
                        </td>
                        <td><a class="btn btn-sm btn-outline-primary"
                                href="<?= url('period-form', ['id' => $r['id']]) ?>">Edit</a>
                            <form class="d-inline" method="post" onsubmit="return confirm('PERINGATAN: Menghapus periode ini juga akan menghapus seluruh KPI dan riwayat penilaian di dalamnya. Lanjutkan?')"><input
                                    type="hidden" name="_csrf" value="<?= csrf() ?>"><input type="hidden" name="action"
                                    value="period_delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button
                                    class="btn btn-sm btn-outline-danger">Hapus</button></form>
                        </td>
                    </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                        <td colspan="3" class="text-center">Belum ada periode.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>