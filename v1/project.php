<?php
require_once 'includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_published=1");
$stmt->execute([$id]); $project = $stmt->fetch();
if (!$project) { http_response_code(404); exit('Project not found.'); }
$page_title = $project['title'];
include 'includes/header.php';
?>
<section class="project-detail-hero"><img src="<?= e(project_image($project['image'])) ?>" alt="<?= e($project['title']) ?>"><div><p class="eyebrow"><?= e($project['category_name'] ?? 'INTERIOR DESIGN') ?></p><h1><?= e($project['title']) ?></h1><p><?= e($project['location'] ?? 'A considered interior') ?></p></div></section>
<section class="section detail-copy"><div class="section-kicker">THE PROJECT</div><div><h2>Designed around<br><em>the way you live.</em></h2><p><?= nl2br(e($project['description'])) ?></p><a class="btn btn-dark" href="contact.php">Discuss a similar project</a></div></section>
<?php include 'includes/footer.php'; ?>
