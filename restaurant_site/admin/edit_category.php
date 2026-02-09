<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: categories.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: categories.php");
    exit;
}

$success = $error = '';

if ($_POST) {
    $name = trim($_POST['name']);
    if ($name) {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
            $success = "دسته‌بندی بروز شد.";
            $category['name'] = $name;
        } catch (Exception $e) {
            $error = "خطا در بروزرسانی.";
        }
    } else {
        $error = "نام الزامی است.";
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>ویرایش دسته‌بندی</h2>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>نام دسته‌بندی</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-admin">ذخیره</button>
                <a href="categories.php" class="btn-admin secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>