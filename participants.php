<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM events WHERE id=?"); $stmt->execute([$id]); $event=$stmt->fetch();
if(!$event){http_response_code(404);die('Event not found.');}
$stmt=$pdo->prepare("SELECT r.*,u.name,u.email FROM registrations r JOIN users u ON u.id=r.user_id WHERE r.event_id=? ORDER BY r.registered_at DESC");
$stmt->execute([$id]); $participants=$stmt->fetchAll();
$page_title='Participants · '.$event['title'];
require 'header.php';
?>
<div class="dashboard-head reveal">
 <div class="dashboard-heading-copy">
  <a class="back-link" href="admin.php">← Dashboard</a>
  <span class="eyebrow">ATTENDEES</span>
  <h1><?= e($event['title']) ?></h1>
  <p><?= count($participants) ?> confirmed registration(s).</p>
 </div>
 <a class="btn btn-ghost" href="event.php?id=<?= $id ?>">View event</a>
</div>
<section class="dashboard-section reveal">
<div class="table-wrap"><table class="data-table">
<thead><tr><th>Participant</th><th>Email</th><th>Ticket</th><th>Registered</th><th>Status</th></tr></thead>
<tbody>
<?php if(!$participants): ?><tr><td colspan="5" class="table-empty">No participants yet.</td></tr>
<?php else: foreach($participants as $p): ?>
<tr><td><div class="person"><span class="avatar"><?= strtoupper(substr($p['name'],0,1)) ?></span><strong><?= e($p['name']) ?></strong></div></td><td><?= e($p['email']) ?></td><td><code><?= e($p['ticket_code']) ?></code></td><td><?= date('d M Y · g:i A',strtotime($p['registered_at'])) ?></td><td><span class="status-pill open"><?= e(ucfirst($p['status'])) ?></span></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div>
</section>
<?php require 'footer.php'; ?>