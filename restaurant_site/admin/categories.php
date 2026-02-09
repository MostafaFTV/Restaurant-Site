<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$success = $error = '';

if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if ($name) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "دسته‌بندی «{$name}» با موفقیت اضافه شد.";
        } catch (Exception $e) {
            $error = "خطا در افزودن دسته‌بندی.";
        }
    } else {
        $error = "نام دسته‌بندی الزامی است.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $success = "دسته‌بندی با موفقیت حذف شد.";
    } catch (Exception $e) {
        $error = "خطا در حذف. ممکن است غذا به این دسته متصل باشد.";
    }
    header("Location: categories.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>مدیریت دسته‌بندی‌ها</h2>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="category-form">
            <div class="form-group inline">
                <input type="text" name="name" placeholder="نام دسته‌بندی جدید" required>
                <button type="submit" name="add_category" class="btn-admin">افزودن</button>
            </div>
        </form>

        <?php if (empty($categories)): ?>
            <p class="no-data">هنوز هیچ دسته‌بندی ثبت نشده است.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام دسته‌بندی</th>
                            <th>تعداد غذاها</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM foods WHERE category_id = ?");
                            $stmt->execute([$cat['id']]);
                            $food_count = $stmt->fetchColumn();
                            ?>
                            <tr>
                                <td>#<?= $cat['id'] ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= $food_count ?> غذا</td>
                                <td>
                                    <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn-small edit">ویرایش</a>
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" 
                                       class="btn-small delete" 
                                       onclick="return confirm('آیا از حذف «<?= htmlspecialchars($cat['name']) ?>» مطمئن هستید؟')">
                                       حذف
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="form-actions" style="margin-top:2rem;">
            <a href="index.php" class="btn-admin secondary">بازگشت به پنل</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>