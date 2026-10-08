<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT e.*, COUNT(r.id) AS registered FROM events e LEFT JOIN registrations r ON r.event_id=e.id AND r.status='confirmed' WHERE e.id=? GROUP BY e.id");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) { http_response_code(404); die('Event not found.'); }
$registered = (int)$event['registered'];
$capacity = (int)$event['capacity'];
$remaining = max(0, $capacity - $registered);
$page_title = $event['title'];
require 'header.php';
?>
<div class="detail-layout reveal">
    <section class="detail-main">
        <a class="back-link" href="index.php">← Back to events</a>
        <div class="detail-visual">
            <span class="event-category"><?= $remaining > 0 ? 'REGISTRATION OPEN' : 'SOLD OUT' ?></span>
            <div class="detail-date"><span><?= strtoupper(date('M', strtotime($event['event_date']))) ?></span><strong><?= date('d', strtotime($event['event_date'])) ?></strong></div>
            <div class="detail-symbol">✦</div>
        </div>
        <span class="eyebrow">UPCOMING EVENT</span>
        <h1><?= e($event['title']) ?></h1>
        <p class="lead"><?= e($event['description']) ?></p>
        <div class="detail-facts">
            <div><span>DATE & TIME</span><strong><?= date('l, d F Y · g:i A', strtotime($event['event_date'])) ?></strong></div>
            <div><span>LOCATION</span><strong>⌖ <?= e($event['location']) ?></strong></div>
        </div>
    </section>
    <aside class="register-card">
        <div class="card-label">YOUR SPOT</div>
        <h2><?= $remaining ?> <small>spots left</small></h2>
        <div class="progress large"><span style="width:<?= min(100, round($registered / max(1,$capacity)*100)) ?>%"></span></div>
        <p><?= $registered ?> of <?= $capacity ?> seats are currently registered.</p>
        <?php if ($remaining <= 0): ?>
            <button class="btn btn-disabled btn-block" disabled>Registration full</button>
        <?php elseif (!is_logged_in()): ?>
            <a class="btn btn-primary btn-block" href="login.php?redirect=<?= urlencode('event.php?id='.$id) ?>">Sign in to register <span>→</span></a>
            <a class="btn btn-ghost btn-block" href="register.php?redirect=<?= urlencode('event.php?id='.$id) ?>">Create account</a>
            <p class="register-hint">New to Gatherly? Create an account first, then your registration will be saved automatically.</p>
        <?php else: ?>
            <?php
            $check = $pdo->prepare("SELECT * FROM registrations WHERE event_id=? AND user_id=? AND status='confirmed'");
            $check->execute([$id, $_SESSION['user']['id']]);
            $myReg = $check->fetch();
            ?>
            <?php if ($myReg): ?>
                <div class="registered-state"><span>✓</span><div><strong>You're registered!</strong><small>Ticket <?= e($myReg['ticket_code']) ?></small></div></div>
            <?php else: ?>
                <form action="register_event.php" method="post">
                    <input type="hidden" name="event_id" value="<?= $id ?>">
                    <button class="btn btn-primary btn-block" type="submit">Reserve my spot <span>→</span></button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        <div class="secure-note">🔒 Your registration is securely stored.</div>
    </aside>
</div>
<?php require 'footer.php'; ?>