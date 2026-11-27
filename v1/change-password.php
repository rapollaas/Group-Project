<?php
require_once 'includes/functions.php';
require_login();
$page_title = 'Change password';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([current_user()['id']]); $hash = $stmt->fetchColumn();
    $new = $_POST['new_password'] ?? '';
    if (!password_verify($_POST['current_password'] ?? '', $hash)) $error = 'Your current password is not correct.';
    elseif (strlen($new) < 8) $error = 'The new password must contain at least 8 characters.';
    elseif ($new !== ($_POST['confirm_password'] ?? '')) $error = 'The password confirmation does not match.';
    else {
        db()->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new,PASSWORD_DEFAULT),current_user()['id']]);
        flash('Your password has been changed.');
        redirect('dashboard.php');
    }
}
include 'includes/header.php';
?>
<section class="section narrow-section"><p class="eyebrow">ACCOUNT SETTINGS</p><h1>Change password</h1><form class="form-card" method="post"><?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Current password<input type="password" name="current_password" required></label><label>New password<input type="password" name="new_password" minlength="8" required></label><label>Confirm new password<input type="password" name="confirm_password" minlength="8" required></label><button class="btn btn-dark">Update password</button></form></section>
<?php include 'includes/footer.php'; ?>
