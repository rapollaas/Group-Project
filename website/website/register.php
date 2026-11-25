<?php
require_once 'includes/functions.php';
$page_title = 'Registration';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter your name and a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Your password must contain at least 8 characters.';
    $check = db()->prepare("SELECT id FROM users WHERE email=?"); $check->execute([$email]);
    if ($check->fetch()) $errors[] = 'An account already exists with that email address.';
    if (!$errors) {
        $stmt = db()->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'client')");
        $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT)]);
        flash('Your client account has been created. Please log in.');
        redirect('login.php');
    }
}
include 'includes/header.php';
?>
<section class="auth-wrap"><form class="form-card auth-card" method="post"><p class="eyebrow">JOIN THE STUDIO</p><h1>Create your<br><em>client account.</em></h1>
    <?php foreach ($errors as $error): ?><p class="form-error"><?= e($error) ?></p><?php endforeach; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Full name<input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
    <label>Email address<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
    <label>Password <small>(at least 8 characters)</small><input type="password" name="password" minlength="8" required></label>
    <button class="btn btn-dark btn-full" type="submit">Create account</button>
    <p class="form-foot">Already registered? <a href="login.php">Login</a></p>
</form></section>
<?php include 'includes/footer.php'; ?>
