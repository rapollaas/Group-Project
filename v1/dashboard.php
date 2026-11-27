<?php
require_once 'includes/functions.php';
require_login();
$user = current_user();
$page_title = 'Dashboard';
$role = $user['role'];
if ($role === 'admin') {
    $stats = [
        ['Users', db()->query("SELECT COUNT(*) FROM users")->fetchColumn()],
        ['Projects', db()->query("SELECT COUNT(*) FROM projects")->fetchColumn()],
        ['Requests', db()->query("SELECT COUNT(*) FROM design_requests")->fetchColumn()],
        ['Categories', db()->query("SELECT COUNT(*) FROM categories")->fetchColumn()],
    ];
    $requests = db()->query("SELECT r.*,u.name AS client_name FROM design_requests r JOIN users u ON u.id=r.client_id ORDER BY r.created_at DESC LIMIT 8")->fetchAll();
} elseif ($role === 'designer') {
    $stmt = db()->prepare("SELECT p.*,c.name AS client_name FROM projects p LEFT JOIN users c ON c.id=p.client_id WHERE p.designer_id=? ORDER BY p.updated_at DESC");
    $stmt->execute([$user['id']]); $projects = $stmt->fetchAll();
    $stats = [['Assigned projects', count($projects)],['In progress', count(array_filter($projects, fn($p) => $p['status']==='In Progress'))],['Completed', count(array_filter($projects, fn($p) => $p['status']==='Completed'))]];
} else {
    $stmt = db()->prepare("SELECT * FROM design_requests WHERE client_id=? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]); $requests = $stmt->fetchAll();
    $stmt = db()->prepare("SELECT * FROM projects WHERE client_id=? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]); $projects = $stmt->fetchAll();
    $stats = [['My requests', count($requests)],['My projects', count($projects)],['Completed projects', count(array_filter($projects, fn($p) => $p['status']==='Completed'))]];
}
include 'includes/header.php';
?>
<section class="dashboard-shell">
    <div class="dashboard-heading"><div><p class="eyebrow"><?= strtoupper(e($role)) ?> WORKSPACE</p><h1>Hello, <?= e(explode(' ', $user['name'])[0]) ?>.</h1><p class="muted">Here is what is happening in your studio today.</p></div>
        <?php if ($role === 'client'): ?><a class="btn btn-dark" href="request.php">＋ New design request</a><?php elseif ($role === 'admin'): ?><a class="btn btn-dark" href="admin/project-form.php">＋ Add project</a><?php endif; ?>
    </div>
    <div class="stats-grid"><?php foreach ($stats as $stat): ?><div class="stat-card"><span><?= e($stat[0]) ?></span><strong><?= (int)$stat[1] ?></strong></div><?php endforeach; ?></div>
    <?php if ($role === 'admin'): ?>
        <div class="dashboard-panel"><div class="panel-heading"><h2>Recent design requests</h2><a href="admin/requests.php">Manage all ↗</a></div>
        <?php if ($requests): ?><div class="table-wrap"><table><thead><tr><th>Request</th><th>Client</th><th>Status</th><th>Received</th><th></th></tr></thead><tbody><?php foreach ($requests as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['client_name']) ?></td><td><span class="status"><?= e($r['status']) ?></span></td><td><?= e(date('d M Y', strtotime($r['created_at']))) ?></td><td><a href="admin/requests.php">View</a></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="muted">No design requests yet.</p><?php endif; ?></div>
        <div class="quick-links"><a href="admin/users.php">Manage users ↗</a><a href="admin/projects.php">Manage projects ↗</a><a href="admin/categories.php">Manage categories ↗</a></div>
    <?php elseif ($role === 'designer'): ?>
        <div class="dashboard-panel"><h2>My assigned projects</h2><?php if ($projects): ?><div class="table-wrap"><table><thead><tr><th>Project</th><th>Client</th><th>Status</th><th>Progress</th><th></th></tr></thead><tbody><?php foreach ($projects as $p): ?><tr><td><?= e($p['title']) ?></td><td><?= e($p['client_name'] ?? '—') ?></td><td><?= e($p['status']) ?></td><td><?= (int)$p['progress'] ?>%</td><td><a href="designer/project.php?id=<?= (int)$p['id'] ?>">Update</a></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="muted">No projects have been assigned to you yet.</p><?php endif; ?></div>
    <?php else: ?>
        <div class="dashboard-panel"><div class="panel-heading"><h2>Your design requests</h2><a href="request.php">New request ↗</a></div><?php if ($requests): ?><div class="table-wrap"><table><thead><tr><th>Request</th><th>Status</th><th>Submitted</th></tr></thead><tbody><?php foreach ($requests as $r): ?><tr><td><?= e($r['title']) ?></td><td><span class="status"><?= e($r['status']) ?></span></td><td><?= e(date('d M Y', strtotime($r['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="muted">You have not submitted a request yet.</p><?php endif; ?></div>
        <div class="dashboard-panel"><h2>Your projects</h2><?php if ($projects): ?><div class="project-grid"><?php foreach ($projects as $p): ?><a class="project-card" href="project.php?id=<?= (int)$p['id'] ?>"><img src="<?= e(project_image($p['image'])) ?>" alt=""><div class="project-meta"><div><h3><?= e($p['title']) ?></h3><span><?= e($p['status']) ?> · <?= (int)$p['progress'] ?>% complete</span></div></div></a><?php endforeach; ?></div><?php else: ?><p class="muted">Your approved projects will appear here.</p><?php endif; ?></div>
    <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>
