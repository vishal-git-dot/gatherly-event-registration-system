<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    if ($title === '') $errors[] = 'Event title is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if (!$date) $errors[] = 'Date and time are required.';
    if ($location === '') $errors[] = 'Location is required.';
    if ($capacity < 1) $errors[] = 'Capacity must be at least 1.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO events(title,description,event_date,location,capacity) VALUES(?,?,?,?,?)");
        $stmt->execute([$title,$description,$date,$location,$capacity]);
        flash('success','Event created successfully.');
        header('Location: admin.php'); exit;
    }
}
$page_title = 'Create event';
require 'header.php';
?>
<div class="form-page reveal">
    <a class="back-link" href="admin.php">← Back to dashboard</a>
    <div class="form-heading"><span class="eyebrow">NEW EXPERIENCE</span><h1>Create an event</h1><p>Give your audience a clear reason to show up.</p></div>
    <?php if ($errors): ?><div class="form-alert error"><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="event-form" data-validate>
        <div class="form-card">
            <div class="field-section"><h3>Event basics</h3><p>These details appear on the public event page.</p></div>
            <label>Event title<input name="title" value="<?= e($_POST['title'] ?? '') ?>" required maxlength="180" placeholder="e.g. Product Design Meetup"></label>
            <label>Description<textarea name="description" rows="6" required placeholder="What will attendees experience?"><?= e($_POST['description'] ?? '') ?></textarea></label>
            <div class="two-col">
                <label>Date & time<input type="datetime-local" name="event_date" value="<?= e($_POST['event_date'] ?? '') ?>" required></label>
                <label>Capacity<input type="number" name="capacity" min="1" value="<?= e($_POST['capacity'] ?? 50) ?>" required></label>
            </div>
            <label>Location<input name="location" value="<?= e($_POST['location'] ?? '') ?>" required placeholder="Venue name or address"></label>
        </div>
        <div class="form-actions"><a class="btn btn-ghost" href="admin.php">Cancel</a><button class="btn btn-primary" type="submit">Publish event <span>→</span></button></div>
    </form>
</div>
<?php require 'footer.php'; ?>