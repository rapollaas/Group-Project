<?php
$page_title = $page_title ?? 'Interior Studio';
$user = current_user();
$notice = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Interior Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark">IS</span><span>INTERIOR <b>STUDIO</b></span></a>
    <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="services.php">Services</a>
        <a href="portfolio.php">Portfolio</a>
        <a href="contact.php">Contact</a>
        <?php if ($user): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" class="nav-cta">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="nav-cta">Get Started</a>
        <?php endif; ?>
    </nav>
</header>
<?php if ($notice): ?>
    <div class="flash flash-<?= e($notice['type']) ?>"><?= e($notice['message']) ?></div>
<?php endif; ?>
<main>
