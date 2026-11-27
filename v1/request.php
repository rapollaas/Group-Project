
<?php
require_once 'includes/functions.php';

require_role(['client']);

$page_title = 'New design request';
$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $budget = trim($_POST['budget'] ?? '');
    $date = $_POST['preferred_date'] ?? null;

    if ($title === '' || $description === '') {
        $errors[] = 'Please provide a project title and description.';
    }

    if (!$errors) {
        $client_id = (int) current_user()['id'];

        // Check how many requests this client submitted
        // during the previous 60 minutes.
        $limit_stmt = db()->prepare(
            "SELECT COUNT(*)
             FROM design_requests
             WHERE client_id = ?
             AND created_at >= DATE_SUB(NOW(), INTERVAL 60 MINUTE)"
        );

        $limit_stmt->execute([$client_id]);
        $request_count = (int) $limit_stmt->fetchColumn();

        // Allow a maximum of three requests per hour.
        if ($request_count >= 3) {
            $errors[] = 'You have reached the limit of 3 design requests per hour. Please try again later.';
        } else {
            $stmt = db()->prepare(
                "INSERT INTO design_requests
                (client_id, category_id, title, description, budget, preferred_date, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Submitted')"
            );

            $stmt->execute([
                $client_id,
                $category_id ?: null,
                $title,
                $description,
                $budget ?: null,
                $date ?: null
            ]);

            flash('Your design request has been submitted.');
            redirect('dashboard.php');
        }
    }
}

include 'includes/header.php';
?>

<section class="section narrow-section">
    <p class="eyebrow">YOUR NEXT PROJECT</p>
    <h1>Tell us what<br><em>you are imagining.</em></h1>

    <form class="form-card" method="post">
        <?php foreach ($errors as $error): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endforeach; ?>

        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Project title
            <input name="title" required>
        </label>

        <label>
            Design category
            <select name="category_id">
                <option value="0">Choose a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>">
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Approximate budget
            <input name="budget" placeholder="e.g. €10,000 – €15,000">
        </label>

        <label>
            Preferred completion date
            <input type="date" name="preferred_date">
        </label>

        <label>
            Project details
            <textarea name="description" rows="6" required placeholder="Tell us about the space, your goals and any ideas you have..."></textarea>
        </label>

        <button class="btn btn-dark" type="submit">
            Submit design request
        </button>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
