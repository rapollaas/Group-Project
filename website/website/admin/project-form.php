<?php
require_once '../includes/functions.php';
require_role(['admin']);
$id = (int)($_GET['id'] ?? 0);
$project = ['title'=>'','description'=>'','location'=>'','category_id'=>'','client_id'=>'','designer_id'=>'','status'=>'Planning','progress'=>0,'is_published'=>1,'image'=>''];
if ($id) {
    $stmt = db()->prepare("SELECT * FROM projects WHERE id=?"); $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) exit('Project not found.');
}
$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$people = db()->query("SELECT id,name,role FROM users WHERE role IN ('client','designer') ORDER BY name")->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $client_id = (int)($_POST['client_id'] ?? 0) ?: null;
    $designer_id = (int)($_POST['designer_id'] ?? 0) ?: null;
    $status = $_POST['status'] ?? 'Planning';
    $progress = max(0,min(100,(int)($_POST['progress'] ?? 0)));
    $location = trim($_POST['location'] ?? '');
    $published = isset($_POST['is_published']) ? 1 : 0;
    $image = $project['image'];
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if (!isset($allowed[$mime]) || $_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Upload a JPG, PNG or WebP image smaller than 5MB.';
        } else {
            $image = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $image);
        }
    }
    if ($title === '' || $description === '') $errors[] = 'Title and description are required.';
    if (!$errors) {
        $values = [$title,$description,$location,$category_id,$client_id,$designer_id,$status,$progress,$published,$image];
        if ($id) {
            $values[] = $id;
            db()->prepare("UPDATE projects SET title=?,description=?,location=?,category_id=?,client_id=?,designer_id=?,status=?,progress=?,is_published=?,image=? WHERE id=?")->execute($values);
        } else {
            db()->prepare("INSERT INTO projects (title,description,location,category_id,client_id,designer_id,status,progress,is_published,image) VALUES (?,?,?,?,?,?,?,?,?,?)")->execute($values);
        }
        flash('Project saved successfully.');
        redirect('projects.php');
    }
    $project = array_merge($project, compact('title','description','location','category_id','client_id','designer_id','status','progress','published','image'));
}
$page_title = $id ? 'Edit project' : 'Add project';
include '../includes/header.php';
?>
<section class="section narrow-section"><p class="eyebrow">PROJECT MANAGEMENT</p><h1><?= $id ? 'Edit project' : 'Create a project' ?></h1>
<form class="form-card" method="post" enctype="multipart/form-data">
    <?php foreach ($errors as $error): ?><p class="form-error"><?= e($error) ?></p><?php endforeach; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Project title<input name="title" required value="<?= e($project['title']) ?>"></label>
    <label>Description<textarea name="description" rows="5" required><?= e($project['description']) ?></textarea></label>
    <label>Location<input name="location" value="<?= e($project['location']) ?>"></label>
    <label>Category<select name="category_id"><option value="">Select category</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$project['category_id']===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
    <label>Client<select name="client_id"><option value="">No client linked</option><?php foreach ($people as $person): if ($person['role']==='client'): ?><option value="<?= (int)$person['id'] ?>" <?= (int)$project['client_id']===(int)$person['id']?'selected':'' ?>><?= e($person['name']) ?></option><?php endif; endforeach; ?></select></label>
    <label>Designer<select name="designer_id"><option value="">Unassigned</option><?php foreach ($people as $person): if ($person['role']==='designer'): ?><option value="<?= (int)$person['id'] ?>" <?= (int)$project['designer_id']===(int)$person['id']?'selected':'' ?>><?= e($person['name']) ?></option><?php endif; endforeach; ?></select></label>
    <div class="form-row"><label>Status<select name="status"><?php foreach (['Planning','In Progress','On Hold','Completed'] as $s): ?><option <?= $project['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></label><label>Progress (%)<input type="number" name="progress" min="0" max="100" value="<?= (int)$project['progress'] ?>"></label></div>
    <label>Project image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP, maximum 5MB.</small></label>
    <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" <?= $project['is_published']?'checked':'' ?>> Publish in portfolio</label>
    <button class="btn btn-dark" type="submit">Save project</button><a class="text-link" href="projects.php">Cancel</a>
</form></section>
<?php include '../includes/footer.php'; ?>
