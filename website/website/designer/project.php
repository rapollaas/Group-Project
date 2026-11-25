<?php
require_once '../includes/functions.php';
require_role(['designer']);
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM projects WHERE id=? AND designer_id=?");
$stmt->execute([$id,current_user()['id']]); $project = $stmt->fetch();
if (!$project) { http_response_code(404); exit('Project not found or not assigned to you.'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $status = $_POST['status'] ?? $project['status'];
    $progress = max(0,min(100,(int)($_POST['progress'] ?? 0)));
    $note = trim($_POST['note'] ?? '');
    if (!in_array($status,['Planning','In Progress','On Hold','Completed'],true)) $errors[] = 'Invalid project status.';
    if (!$errors) {
        db()->prepare("UPDATE projects SET status=?,progress=? WHERE id=? AND designer_id=?")->execute([$status,$progress,$id,current_user()['id']]);
        if ($note !== '') db()->prepare("INSERT INTO project_updates (project_id,user_id,note) VALUES (?,?,?)")->execute([$id,current_user()['id'],$note]);
        flash('Project progress updated.');
        redirect('project.php?id=' . $id);
    }
}
$updates = db()->prepare("SELECT pu.*,u.name FROM project_updates pu JOIN users u ON u.id=pu.user_id WHERE pu.project_id=? ORDER BY pu.created_at DESC");
$updates->execute([$id]); $updates = $updates->fetchAll();
$page_title = 'Update project';
include '../includes/header.php';
?>
<section class="section narrow-section"><p class="eyebrow">DESIGNER WORKSPACE</p><h1><?= e($project['title']) ?></h1><p class="muted">Update project progress and share a note with the studio.</p>
<form class="form-card" method="post"><?php foreach ($errors as $error): ?><p class="form-error"><?= e($error) ?></p><?php endforeach; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Status<select name="status"><?php foreach (['Planning','In Progress','On Hold','Completed'] as $s): ?><option <?= $project['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></label>
<label>Progress <strong id="progress-value"><?= (int)$project['progress'] ?>%</strong><input type="range" name="progress" min="0" max="100" value="<?= (int)$project['progress'] ?>" data-progress></label>
<label>Progress note<textarea name="note" rows="4" placeholder="What has been completed or what happens next?"></textarea></label><button class="btn btn-dark">Save progress</button><a class="text-link" href="../dashboard.php">Back to dashboard</a></form>
<div class="dashboard-panel"><h2>Project update history</h2><?php foreach ($updates as $update): ?><div class="update-item"><strong><?= e($update['name']) ?></strong><small><?= e(date('d M Y, H:i',strtotime($update['created_at']))) ?></small><p><?= nl2br(e($update['note'])) ?></p></div><?php endforeach; ?><?php if (!$updates): ?><p class="muted">No updates have been added.</p><?php endif; ?></div></section>
<?php include '../includes/footer.php'; ?>
