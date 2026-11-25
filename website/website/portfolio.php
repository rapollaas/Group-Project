<?php
require_once 'includes/functions.php';
$page_title = 'Portfolio';
$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$search = trim($_GET['q'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$sql = "SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_published=1";
$params = [];
if ($search !== '') { $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($category > 0) { $sql .= " AND p.category_id=?"; $params[] = $category; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = db()->prepare($sql); $stmt->execute($params); $projects = $stmt->fetchAll();
include 'includes/header.php';
?>
<section class="page-hero compact"><p class="eyebrow">THE PORTFOLIO</p><h1>Spaces with a story<br><em>to tell.</em></h1><p>A selection of residential and lifestyle spaces shaped around the people who call them home.</p></section>
<section class="section">
    <form class="filter-bar" method="get">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search projects...">
        <select name="category"><option value="0">All categories</option><?php foreach ($categories as $item): ?><option value="<?= (int)$item['id'] ?>" <?= $category === (int)$item['id'] ? 'selected' : '' ?>><?= e($item['name']) ?></option><?php endforeach; ?></select>
        <button class="btn btn-dark" type="submit">Filter projects</button>
        <a class="text-link" href="portfolio.php">Reset</a>
    </form>
    <div class="project-grid portfolio-grid">
        <?php foreach ($projects as $project): ?>
            <a class="project-card" href="project.php?id=<?= (int)$project['id'] ?>">
                <img src="<?= e(project_image($project['image'])) ?>" alt="<?= e($project['title']) ?>">
                <div class="project-meta"><div><h3><?= e($project['title']) ?></h3><span><?= e($project['category_name'] ?? 'Interior design') ?></span></div><span class="arrow">↗</span></div>
            </a>
        <?php endforeach; ?>
        <?php if (!$projects): ?><p class="muted">No projects match those filters.</p><?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
