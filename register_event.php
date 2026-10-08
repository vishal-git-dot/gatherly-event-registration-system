<?php
require_once 'config.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

$event_id = (int)($_POST['event_id'] ?? 0);
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT capacity FROM events WHERE id=? FOR UPDATE");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    if (!$event) throw new Exception('Event not found.');

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id=? AND status='confirmed'");
    $stmt->execute([$event_id]);
    if ((int)$stmt->fetchColumn() >= (int)$event['capacity']) throw new Exception('Sorry, this event is now full.');

    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE event_id=? AND user_id=? AND status='confirmed'");
    $stmt->execute([$event_id, $_SESSION['user']['id']]);
    if ($stmt->fetch()) throw new Exception('You are already registered for this event.');

    $ticket = 'GTH-' . strtoupper(bin2hex(random_bytes(4)));
    $stmt = $pdo->prepare("INSERT INTO registrations (event_id,user_id,ticket_code) VALUES (?,?,?)");
    $stmt->execute([$event_id, $_SESSION['user']['id'], $ticket]);
    $pdo->commit();

    flash('success', "Registration confirmed! Your ticket is $ticket.");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('error', $e->getMessage());
}
header('Location: event.php?id=' . $event_id);
exit;
?>