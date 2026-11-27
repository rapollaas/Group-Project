<?php
require_once 'includes/functions.php';
$page_title = 'Beautiful spaces, thoughtfully designed';
$featured = db()->query("SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_published=1 ORDER BY p.created_at DESC LIMIT 3")->fetchAll();
include 'includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">INTERIORS WITH INTENTION</p>
        <h1>Spaces that feel<br><em>beautifully yours.</em></h1>
        <p class="hero-copy">We create considered, characterful interiors that bring ease, warmth and understated luxury to everyday life.</p>
        <div class="button-row">
            <a class="btn btn-gold" href="portfolio.php">Explore our work</a>
            <a class="text-link light-link" href="about.php">Discover our studio <span>↗</span></a>
        </div>
    </div>
    <div class="hero-note">DESIGNING THE ART OF LIVING <span>— EST. 2016</span></div>
</section>
<section class="section intro-section">
    <div class="section-kicker">THE STUDIO</div>
    <div class="intro-grid">
        <h2>Quietly confident.<br><em>Distinctly personal.</em></h2>
        <div><p class="lead">Good design is more than how a room looks. It is how it welcomes you, supports your rituals and makes the everyday feel exceptional.</p><a class="text-link" href="about.php">Meet Interior Studio <span>↗</span></a></div>
    </div>
</section>
<section class="section portfolio-preview">
    <div class="section-heading"><div><div class="section-kicker">SELECTED WORK</div><h2>Spaces we love.</h2></div><a class="text-link" href="portfolio.php">View all projects ↗</a></div>
    <div class="project-grid">
        <?php foreach ($featured as $project): ?>
            <a class="project-card" href="project.php?id=<?= (int)$project['id'] ?>">
                <img src="<?= e(project_image($project['image'])) ?>" alt="<?= e($project['title']) ?>">
                <div class="project-meta"><div><h3><?= e($project['title']) ?></h3><span><?= e($project['category_name'] ?? 'Interior design') ?></span></div><span class="arrow">↗</span></div>
            </a>
        <?php endforeach; ?>
        <?php if (!$featured): ?>
            <p class="muted">Our latest projects will appear here soon.</p>
        <?php endif; ?>
    </div>
</section>
<section class="statement-band"><p>“A home should tell the story of the people who live in it.”</p><span>OUR DESIGN PHILOSOPHY</span></section>
<section class="section cta-section"><div><div class="section-kicker">LET'S CREATE SOMETHING MEANINGFUL</div><h2>Your next chapter<br><em>starts at home.</em></h2></div><a class="btn btn-dark" href="contact.php">Start a conversation</a></section>
<?php include 'includes/footer.php'; ?>
