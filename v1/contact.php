<?php
require_once 'includes/functions.php';
$page_title = 'Contact';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $errors[] = 'Please enter your name, a valid email and a message.';
    } else {
        $stmt = db()->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$subject,$message]);
        flash('Thank you. Your message has been sent.');
        redirect('contact.php');
    }
}
include 'includes/header.php';
?>
<section class="page-hero compact"><p class="eyebrow">GET IN TOUCH</p><h1>Let's make room<br><em>for something lovely.</em></h1><p>Tell us a little about what you have in mind. We would love to hear from you.</p></section>
<section class="section contact-layout"><div><div class="section-kicker">START A CONVERSATION</div><h2>Every great space<br><em>starts somewhere.</em></h2><p>Share your ideas, questions or project details and our studio will be in touch.</p><p><b>Email</b><br>hello@interiorstudio.example</p><p><b>Studio hours</b><br>Monday – Friday, 9:00 – 17:00</p></div>
<form class="form-card" method="post">
    <?php foreach ($errors as $error): ?><p class="form-error"><?= e($error) ?></p><?php endforeach; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Your name<input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
    <label>Email address<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
    <label>Subject<input name="subject" value="<?= e($_POST['subject'] ?? '') ?>"></label>
    <label>Your message<textarea name="message" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea></label>
    <button class="btn btn-dark" type="submit">Send message</button>
</form></section>
<?php include 'includes/footer.php'; ?>
