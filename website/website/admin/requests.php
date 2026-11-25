<?php
require_once '../includes/functions.php';
require_role(['admin']);
$page_title = 'Design requests';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'status') {
        $status = $_POST['status'] ?? 'Submitted';
        if (in_array($status,['Submitted','Reviewing','Approved','Declined','Completed'],true)) {
            db()->prepare("UPDATE design_requests SET status=? WHERE id=?")->execute([$status,$id]);
        }
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM design_requests WHERE id=?")->execute([$id]);
    }
    flash('Design request updated.');
    redirect('requests.php');
}
$requests = db()->query("SELECT r.*,u.name AS client_name,u.email FROM design_requests r JOIN users u ON u.id=r.client_id ORDER BY r.created_at DESC")->fetchAll();
include '../includes/header.php';
?>
<section class="dashboard-shell"><div class="dashboard-heading"><div><p class="eyebrow">ADMINISTRATION</p><h1>Design requests</h1></div><a class="btn btn-outline" href="../dashboard.php">Dashboard</a></div>
<div class="dashboard-panel"><div class="table-wrap"><table><thead><tr><th>Request</th><th>Client</th><th>Budget</th><th>Status</th><th>Submitted</th><th></th></tr></thead><tbody><?php foreach ($requests as $r): ?><tr><td><strong><?= e($r['title']) ?></strong><details><summary>Read brief</summary><p><?= nl2br(e($r['description'])) ?></p></details></td><td><?= e($r['client_name']) ?><br><small><?= e($r['email']) ?></small></td><td><?= e($r['budget'] ?? '—') ?></td><td><form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="status"><select name="status" onchange="this.form.submit()"><?php foreach (['Submitted','Reviewing','Approved','Declined','Completed'] as $s): ?><option <?= $r['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></form></td><td><?= e(date('d M Y',strtotime($r['created_at']))) ?></td><td><form method="post" data-confirm="Delete this request?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="delete"><button class="link-danger">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php include '../includes/footer.php'; ?>
