<?php
require_once __DIR__ . '/auth.php';
$page_title = $page_title ?? 'Gatherly';
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gatherly — modern event registration and ticket management.">
    <title><?= e($page_title) ?> · Gatherly</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <link rel="apple-touch-icon" href="assets/favicon.svg">
    <meta name="theme-color" content="#8a5cf6">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Gatherly home">
        <span class="brand-mark">✦</span>
        <span>Gatherly</span>
    </a>
    <nav class="main-nav" id="mainNav">
        <a href="index.php">Discover</a>
        <?php if (is_logged_in() && !is_admin()): ?><a href="my_registrations.php">My registrations</a><?php endif; ?>
        <?php if (is_logged_in() && is_admin()): ?>
            <a href="admin.php">Admin</a>
            <a href="create_event.php">Create event</a>
        <?php endif; ?>
    </nav>
    <div class="header-actions">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Switch theme">
            <span id="themeIcon">☀</span>
            <span id="themeText">Light Mode</span>
        </button>
        <?php if (is_logged_in()): ?>
            <a class="user-chip" href="logout.php">
                <span class="avatar"><?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?></span>
                <span class="hide-mobile"><?= e($_SESSION['user']['name']) ?></span>
                <small>Logout</small>
            </a>
        <?php else: ?>
            <a class="btn btn-ghost btn-small" href="register.php">Create account</a>
            <a class="btn btn-primary btn-small" href="login.php">Sign in</a>
        <?php endif; ?>
    </div>
    <button class="mobile-menu" id="mobileMenu" type="button" aria-label="Open navigation">☰</button>
</header>
<main class="page">
<?php foreach (get_flashes() as $flash): ?>
    <div class="toast toast-<?= e($flash['type']) ?>" data-toast>
        <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
        <div><?= e($flash['message']) ?></div>
        <button type="button" onclick="this.parentElement.remove()">×</button>
    </div>
<?php endforeach; ?>