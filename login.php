<?php
require_once 'config.php';
require_once 'auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // Refresh the hash when PHP's password algorithm/cost changes.
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE users SET password=? WHERE id=?');
            $update->execute([$newHash, $user['id']]);
            $user['password'] = $newHash;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? 'admin.php' : 'index.php');
        // Only allow local redirects.
        if (!is_string($redirect) || preg_match('/^https?:\/\//i', $redirect)) {
            $redirect = $user['role'] === 'admin' ? 'admin.php' : 'index.php';
        }
        header('Location: ' . $redirect); exit;
    }
    $error = 'The email or password is incorrect. Please check the demo credentials shown below.';
}
$page_title = 'Sign in';
require 'header.php';
?>
<div class="auth-wrap reveal">
    <div class="auth-panel">
        <div class="auth-symbol">✦</div>
        <span class="eyebrow">WELCOME BACK</span>
        <h1>Make room for<br><em>something memorable.</em></h1>
        <p>Sign in to register for events and keep your tickets in one place.</p>
    </div>
    <div class="form-card">
        <h2>Sign in</h2><p class="muted">Use your Gatherly account.</p>
        <?php if ($error): ?><div class="form-alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="stack-form" data-validate>
            <label>Email<input type="email" name="email" required autocomplete="email" placeholder="you@example.com"></label>
            <label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="••••••••"></label>
            <button class="btn btn-primary btn-block" type="submit">Sign in <span>→</span></button>
        </form>
        <div class="demo-note"><strong>Demo admin</strong><br>admin@gatherly.test · admin123</div>
        <p class="auth-switch">New to Gatherly? <a href="register.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>">Create a free account</a></p>
    </div>
</div>
<?php require 'footer.php'; ?>