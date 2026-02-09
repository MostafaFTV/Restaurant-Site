<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
$success = $error = '';

if ($_POST) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = (int)$_POST['category_id'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $upload_dir = '../assets/images/foods/';
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) { 
            $filename = uniqid('food_') . '.' . $ext;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_path = 'assets/images/foods/' . $filename;
            } else {
                $error = "خطا در آپلود عکس.";
            }
        } else {
            $error = "فرمت یا حجم عکس مجاز نیست (JPG, PNG, WEBP - حداکثر ۵ مگابایت).";
        }
    } else {
        $error = "لطفاً عکس غذا را انتخاب کنید.";
    }

    if (!$error) {
        $sql = "INSERT INTO foods (name, description, price, category_id, image, is_available) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $description, $price, $category_id, $image_path, $is_available])) {
            $success = "غذا با موفقیت اضافه شد!";
        } else {
            $error = "خطا در ذخیره‌سازی.";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>اضافه کردن غذای جدید</h2>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>نام غذا</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>توضیحات</label>
                <textarea name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>قیمت (تومان)</label>
                <input type="number" name="price" min="0" step="1000" required>
            </div>

            <div class="form-group">
                <label>دسته‌بندی</label>
                <select name="category_id" required>
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>عکس غذا</label>
                <input type="file" name="image" accept="image/*" required>
                <small>فرمت: JPG, PNG, WEBP - حداکثر ۵ مگابایت</small>
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="is_available" checked>
                    موجود در منو
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-admin">اضافه کردن غذا</button>
                <a href="index.php" class="btn-admin secondary">بازگشت به پنل</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>