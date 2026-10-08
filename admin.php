<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalRegistrations = (int)$pdo->query("SELECT COUNT(*) FROM registrations WHERE status='confirmed'")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$upcoming = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= NOW()")->fetchColumn();

$events = $pdo->query("
 SELECT e.*, COUNT(r.id) registered
 FROM events e LEFT JOIN registrations r ON r.event_id=e.id AND r.status='confirmed'
 GROUP BY e.id ORDER BY e.event_date ASC
")->fetchAll();

$page_title = 'Admin dashboard';
require 'header.php';
?>
<div class="dashboard-head reveal">
    <div><span class="eyebrow">ADMIN CONSOLE</span><h1>Good to see you, <?= e(explode(' ', $_SESSION['user']['name'])[0]) ?>.</h1><p>Manage your events and keep registrations moving.</p></div>
    <a class="btn btn-primary" href="create_event.php">＋ Create event</a>
</div>
<div class="stats-grid reveal">
    <div class="stat-card"><span>UPCOMING EVENTS</span><strong><?= $upcoming ?></strong><small>Live and scheduled</small></div>
    <div class="stat-card"><span>REGISTRATIONS</span><strong><?= $totalRegistrations ?></strong><small>Confirmed spots</small></div>
    <div class="stat-card"><span>ATTENDEES</span><strong><?= $totalUsers ?></strong><small>Registered users</small></div>
    <div class="stat-card"><span>ALL EVENTS</span><strong><?= $totalEvents ?></strong><small>Across your workspace</small></div>
</div>
<section class="dashboard-section reveal">
    <div class="section-head"><div><span class="eyebrow">EVENT MANAGEMENT</span><h2>Your events</h2></div><a href="create_event.php" class="text-link">New event →</a></div>
    <div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Event</th><th>Date</th><th>Location</th><th>Registrations</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach($events as $event):
            $reg=(int)$event['registered']; $cap=(int)$event['capacity'];
        ?>
        <tr>
            <td><strong><?= e($event['title']) ?></strong><small><?= e(mb_strimwidth($event['description'],0,55,'…')) ?></small></td>
            <td><?= date('d M Y',strtotime($event['event_date'])) ?><small><?= date('g:i A',strtotime($event['event_date'])) ?></small></td>
            <td><?= e($event['location']) ?></td>
            <td><strong><?= $reg ?></strong> / <?= $cap ?><div class="mini-progress"><span style="width:<?= min(100,$reg/max(1,$cap)*100) ?>%"></span></div></td>
            <td><span class="status-pill <?= strtotime($event['event_date']) < time() ? 'past' : ($reg >= $cap ? 'full' : 'open') ?>"><?= strtotime($event['event_date']) < time() ? 'Past' : ($reg >= $cap ? 'Full' : 'Open') ?></span></td>
            <td><a class="icon-link" href="participants.php?id=<?= (int)$event['id'] ?>" title="Participants">→</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</section>
<?php require 'footer.php'; ?>