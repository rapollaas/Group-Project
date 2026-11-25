<?php
require_once 'includes/functions.php';
$page_title = 'Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare("SELECT id,name,email,password,role FROM users WHERE email=? AND is_active=1");
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('dashboard.php');
    }
    $error = 'The email or password you entered is incorrect.';
}
include 'includes/header.php';
?>
<section class="auth-wrap"><form class="form-card auth-card" method="post"><p class="eyebrow">WELCOME BACK</p><h1>Sign in to your<br><em>studio space.</em></h1>
    <?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Email address<input type="email" name="email" required autocomplete="email"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn btn-dark btn-full" type="submit">Login</button>
    <p class="form-foot">New to Interior Studio? <a href="register.php">Create an account</a></p>
</form></section>
<?php include 'includes/footer.php'; ?>
