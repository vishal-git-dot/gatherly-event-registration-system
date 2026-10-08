<?php
require_once 'config.php';
require_once 'auth.php';

if (is_logged_in()) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (mb_strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists. Please sign in instead.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'user')");
        $stmt->execute([$name, $email, $hash]);
        $userId = (int)$pdo->lastInsertId();
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'role' => 'user'
        ];
        flash('success', 'Welcome to Gatherly! Your account is ready.');
        $redirect = $_GET['redirect'] ?? 'index.php';
        if (!is_string($redirect) || preg_match('/^https?:\/\//i', $redirect)) $redirect = 'index.php';
        header('Location: ' . $redirect);
        exit;
    }
}

$page_title = 'Create account';
require 'header.php';
?>
<div class="auth-wrap auth-wrap-register reveal">
    <div class="auth-panel">
        <div class="auth-symbol">✦</div>
        <span class="eyebrow">JOIN GATHERLY</span>
        <h1>Your next<br><em>great experience</em><br>starts here.</h1>
        <p>Create a free account, reserve your place at events, and keep your registration details together.</p>
        <div class="auth-benefits">
            <div><span>✓</span><strong>One-click event registration</strong></div>
            <div><span>✓</span><strong>Unique ticket code for every booking</strong></div>
            <div><span>✓</span><strong>Simple, secure account access</strong></div>
        </div>
    </div>
    <div class="form-card">
        <h2>Create account</h2>
        <p class="muted">It only takes a minute.</p>
        <?php if ($errors): ?>
            <div class="form-alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="post" class="stack-form" data-validate>
            <label>Full name
                <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required maxlength="120" autocomplete="name" placeholder="Your name">
            </label>
            <label>Email address
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required maxlength="190" autocomplete="email" placeholder="you@example.com">
            </label>
            <label>Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
            </label>
            <label>Confirm password
                <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password" placeholder="Repeat your password">
            </label>
            <button class="btn btn-primary btn-block" type="submit">Create my account <span>→</span></button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>">Sign in</a></p>
    </div>
</div>
<?php require 'footer.php'; ?>
