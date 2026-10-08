<?php
require_once 'config.php';
$page_title = 'Discover events';

$stmt = $pdo->query("
    SELECT e.*, COUNT(r.id) AS registered
    FROM events e
    LEFT JOIN registrations r ON r.event_id = e.id AND r.status = 'confirmed'
    WHERE e.event_date >= NOW()
    GROUP BY e.id
    ORDER BY e.event_date ASC
");
$events = $stmt->fetchAll();
require 'header.php';
?>
<section class="hero reveal">
    <div class="hero-copy">
        <span class="eyebrow">EVENTS, WITHOUT THE FRICTION</span>
        <h1>Find your next <em>moment.</em></h1>
        <p>Discover local meetups, workshops and community experiences. Register in seconds and keep your ticket close.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="#events">Explore events <span>→</span></a>
            <?php if (!is_logged_in()): ?><a class="btn btn-ghost" href="login.php">Create an account</a><?php endif; ?>
        </div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="art-card art-main">
            <div class="art-date">OCT<br><strong>18</strong></div>
            <div><span class="mini-label">UP NEXT</span><strong>Design & Tech<br>Meetup</strong><small>● Kochi Innovation Hub</small></div>
        </div>
        <div class="art-card art-float one">✦ 120 seats</div>
        <div class="art-card art-float two">✓ Easy registration</div>
    </div>
</section>

<section class="section-head reveal" id="events">
    <div>
        <span class="eyebrow">WHAT'S HAPPENING</span>
        <h2>Upcoming events</h2>
    </div>
    <span class="result-count"><?= count($events) ?> <?= count($events) === 1 ? 'event' : 'events' ?></span>
</section>

<?php if (!$events): ?>
    <div class="empty-state reveal"><div class="empty-icon">✦</div><h3>No upcoming events yet</h3><p>Check back soon for new experiences.</p></div>
<?php else: ?>
<div class="event-grid">
<?php foreach ($events as $event):
    $registered = (int)$event['registered'];
    $capacity = max(1, (int)$event['capacity']);
    $percent = min(100, round($registered / $capacity * 100));
    $full = $registered >= $capacity;
?>
<article class="event-card reveal">
    <div class="event-visual">
        <span class="event-category"><?= $full ? 'FULL' : 'OPEN' ?></span>
        <div class="event-date-block">
            <span><?= strtoupper(date('M', strtotime($event['event_date']))) ?></span>
            <strong><?= date('d', strtotime($event['event_date'])) ?></strong>
        </div>
        <span class="visual-symbol">✦</span>
    </div>
    <div class="event-content">
        <div class="event-meta"><?= date('l, d M · g:i A', strtotime($event['event_date'])) ?></div>
        <h3><?= e($event['title']) ?></h3>
        <p><?= e(mb_strimwidth($event['description'], 0, 125, '…')) ?></p>
        <div class="location">⌖ <?= e($event['location']) ?></div>
        <div class="capacity-row">
            <span><?= $registered ?> / <?= $capacity ?> registered</span>
            <span><?= $percent ?>%</span>
        </div>
        <div class="progress"><span style="width:<?= $percent ?>%"></span></div>
        <a class="btn <?= $full ? 'btn-disabled' : 'btn-dark' ?>" href="event.php?id=<?= (int)$event['id'] ?>"><?= $full ? 'View details' : 'View & register' ?> <span>→</span></a>
    </div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require 'footer.php'; ?>