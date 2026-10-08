<?php
require_once 'config.php';
require_once 'auth.php';
require_login();

$stmt = $pdo->prepare("SELECT r.*, e.title, e.description, e.event_date, e.location
                       FROM registrations r
                       JOIN events e ON e.id=r.event_id
                       WHERE r.user_id=?
                       ORDER BY e.event_date DESC");
$stmt->execute([$_SESSION['user']['id']]);
$registrations = $stmt->fetchAll();

$page_title = 'My registrations';
require 'header.php';
?>
<div class="dashboard-head reveal">
    <div>
        <a class="back-link" href="index.php">← Discover events</a>
        <span class="eyebrow">YOUR TICKETS</span>
        <h1>My registrations</h1>
        <p>Everything you've reserved, in one place.</p>
    </div>
    <a class="btn btn-primary" href="index.php">Find an event <span>→</span></a>
</div>

<?php if (!$registrations): ?>
    <div class="empty-state reveal">
        <div class="empty-icon">✦</div>
        <h3>No registrations yet</h3>
        <p>Find an event you love and reserve your spot.</p>
        <a class="btn btn-primary" href="index.php">Explore events</a>
    </div>
<?php else: ?>
    <div class="ticket-grid">
    <?php foreach ($registrations as $registration): ?>
        <article class="ticket-card reveal">
            <div class="ticket-top">
                <span class="status-pill <?= e($registration['status']) ?>"><?= e(ucfirst($registration['status'])) ?></span>
                <span class="ticket-star">✦</span>
            </div>
            <span class="eyebrow"><?= strtoupper(date('M d', strtotime($registration['event_date']))) ?></span>
            <h3><?= e($registration['title']) ?></h3>
            <div class="ticket-fact">◷ <?= date('l, d M Y · g:i A', strtotime($registration['event_date'])) ?></div>
            <div class="ticket-fact">⌖ <?= e($registration['location']) ?></div>
            <div class="ticket-code"><small>TICKET CODE</small><code><?= e($registration['ticket_code']) ?></code></div>
            <a class="btn btn-dark btn-block" href="event.php?id=<?= (int)$registration['event_id'] ?>">View event <span>→</span></a>
        </article>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require 'footer.php'; ?>
