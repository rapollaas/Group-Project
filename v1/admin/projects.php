<?php
require_once '../includes/functions.php';
require_role(['admin']);
$page_title = 'Manage projects';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    db()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
    flash('Project deleted.');
    redirect('projects.php');
}
$projects = db()->query("SELECT p.*,u.name AS designer_name FROM projects p LEFT JOIN users u ON u.id=p.designer_id ORDER BY p.created_at DESC")->fetchAll();
include '../includes/header.php';
?>
<section class="dashboard-shell"><div class="dashboard-heading"><div><p class="eyebrow">ADMINISTRATION</p><h1>Manage projects</h1></div><a class="btn btn-dark" href="project-form.php">＋ Add project</a></div>
<div class="dashboard-panel"><div class="table-wrap"><table><thead><tr><th>Project</th><th>Designer</th><th>Status</th><th>Progress</th><th>Actions</th></tr></thead><tbody><?php foreach ($projects as $p): ?><tr><td><?= e($p['title']) ?></td><td><?= e($p['designer_name'] ?? 'Unassigned') ?></td><td><?= e($p['status']) ?></td><td><?= (int)$p['progress'] ?>%</td><td class="action-cell"><a href="project-form.php?id=<?= (int)$p['id'] ?>">Edit</a><form method="post" data-confirm="Delete this project?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="link-danger">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php include '../includes/footer.php'; ?>
