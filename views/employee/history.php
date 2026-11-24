<h4 class="fw-bold">Riwayat Penilaian</h4>
<p class="text-secondary">Riwayat pengisian dan status KPI Anda.</p>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>KPI</th>
                    <th>Realisasi</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Catatan Admin</th>
                </tr>
            </thead>
            <tbody><?php foreach ($data['rows'] as $r): ?><tr>
                        <td><?= monthName((int)$r['month']) . ' ' . $r['year'] ?></td>
                        <td><?= e($r['kpi']) ?></td>
                        <td><?= e($r['actual'] ?? '-') ?></td>
                        <td><?= e($r['rating'] ?? '-') ?></td>
                        <td><?= e($r['status']) ?></td>
                        <td><?= e($r['admin_notes'] ?? '-') ?></td>
                    </tr><?php endforeach;
                        if (!$data['rows']): ?><tr>
                        <td colspan="6" class="text-center">Belum ada riwayat.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div>