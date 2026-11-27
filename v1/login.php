
<?php
require_once 'includes/functions.php';

$page_title = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Check whether the login attempt limit has been reached.
    if (login_attempts_exceeded($email)) {
        $error = 'Too many login attempts. Please try again in 15 minutes.';
    } else {
        $stmt = db()->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ? AND is_active = 1"
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Perform password verification even when the account does not exist.
        $dummy_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $password_hash = $user ? $user['password'] : $dummy_hash;
        $password_valid = password_verify($password, $password_hash);

        if ($user && $password_valid) {
            // Clear previous failed attempts after successful authentication.
            clear_login_attempts($email);

            session_regenerate_id(true);

            unset($user['password']);
            $_SESSION['user'] = $user;

            redirect('dashboard.php');
        }

        // Record unsuccessful login attempts.
        record_login_attempt($email);

        $error = 'The email or password you entered is incorrect.';
    }
}

include 'includes/header.php';
?>

<section class="auth-wrap">
    <form class="form-card auth-card" method="post">
        <p class="eyebrow">WELCOME BACK</p>

        <h1>Sign in to your<br><em>studio space.</em></h1>

        <?php if ($error): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Email address
            <input type="email" name="email" required autocomplete="email">
        </label>

        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button class="btn btn-dark btn-full" type="submit">Login</button>

        <p class="form-foot">
            New to Interior Studio? <a href="register.php">Create an account</a>
        </p>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
