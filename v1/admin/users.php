<?php
require_once '../includes/functions.php';
require_role(['admin']);
$page_title = 'Manage users';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'role') {
        $role = $_POST['role'] ?? 'client';
        if (in_array($role, ['admin','designer','client'], true) && $id !== (int)current_user()['id']) {
            db()->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role,$id]);
        }
    } elseif ($action === 'delete' && $id !== (int)current_user()['id']) {
        db()->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }
    flash('User record updated.');
    redirect('users.php');
}
$users = db()->query("SELECT id,name,email,role,is_active,created_at FROM users ORDER BY created_at DESC")->fetchAll();
include '../includes/header.php';
?>
<section class="dashboard-shell"><div class="dashboard-heading"><div><p class="eyebrow">ADMINISTRATION</p><h1>Manage users</h1></div><a class="btn btn-outline" href="../dashboard.php">Back to dashboard</a></div>
<div class="dashboard-panel"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead><tbody><?php foreach ($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="action" value="role"><select name="role" onchange="this.form.submit()" <?= (int)$u['id']===(int)current_user()['id']?'disabled':'' ?>><?php foreach (['admin','designer','client'] as $role): ?><option value="<?= $role ?>" <?= $u['role']===$role?'selected':'' ?>><?= ucfirst($role) ?></option><?php endforeach; ?></select></form></td><td><?= e(date('d M Y',strtotime($u['created_at']))) ?></td><td><?php if ((int)$u['id'] !== (int)current_user()['id']): ?><form method="post" data-confirm="Delete this user?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="action" value="delete"><button class="link-danger">Delete</button></form><?php else: ?>Current account<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php include '../includes/footer.php'; ?>
