<?php
require_once '../includes/functions.php';
require_role(['admin']);
$page_title = 'Manage categories';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $name = trim($_POST['name'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        if ($name !== '') {
            if ($id) db()->prepare("UPDATE categories SET name=? WHERE id=?")->execute([$name,$id]);
            else db()->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
        }
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]);
    }
    flash('Category list updated.');
    redirect('categories.php');
}
$categories = db()->query("SELECT c.*,COUNT(p.id) AS project_count FROM categories c LEFT JOIN projects p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name")->fetchAll();
include '../includes/header.php';
?>
<section class="dashboard-shell"><div class="dashboard-heading"><div><p class="eyebrow">ADMINISTRATION</p><h1>Project categories</h1></div><a class="btn btn-outline" href="../dashboard.php">Dashboard</a></div>
<div class="dashboard-panel"><h2>Add a category</h2><form class="inline-add" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input name="name" placeholder="Category name" required><button class="btn btn-dark">Add category</button></form>
<div class="table-wrap"><table><thead><tr><th>Category</th><th>Projects</th><th>Rename</th><th></th></tr></thead><tbody><?php foreach ($categories as $c): ?><tr><td><?= e($c['name']) ?></td><td><?= (int)$c['project_count'] ?></td><td><form class="inline-add" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input name="name" value="<?= e($c['name']) ?>" required><button class="link-button">Save</button></form></td><td><form method="post" data-confirm="Delete category? Projects will become uncategorised."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="link-danger">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php include '../includes/footer.php'; ?>
