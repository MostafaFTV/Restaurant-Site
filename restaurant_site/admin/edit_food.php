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
    header("Location: manage_foods.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM foods WHERE id = ?");
$stmt->execute([$id]);
$food = $stmt->fetch(); 

if (!$food) {
    $_SESSION['error'] = "غذا پیدا نشد.";
    header("Location: manage_foods.php");
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

    $image_path = $food['image']; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $filename = uniqid('food_') . '.' . $ext;
            $destination = '../assets/images/foods/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                if ($food['image'] && file_exists('../' . $food['image'])) {
                    @unlink('../' . $food['image']);
                }
                $image_path = 'assets/images/foods/' . $filename;
            } else {
                $error = "خطا در آپلود عکس جدید.";
            }
        } else {
            $error = "فرمت یا حجم عکس مجاز نیست.";
        }
    }

    if (!$error) {
        $sql = "UPDATE foods SET 
                name = ?, description = ?, price = ?, category_id = ?, image = ?, is_available = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $description, $price, $category_id, $image_path, $is_available, $id])) {
            $success = "غذا با موفقیت ویرایش شد!";
            $food = array_merge($food, [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category_id,
                'image' => $image_path,
                'is_available' => $is_available
            ]);
        } else {
            $error = "خطا در به‌روزرسانی.";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>ویرایش غذا: <?= htmlspecialchars($food['name']) ?></h2>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>نام غذا</label>
                <input type="text" name="name" value="<?= htmlspecialchars($food['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>توضیحات</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($food['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>قیمت (تومان)</label>
                <input type="number" name="price" value="<?= $food['price'] ?>" min="0" step="1000" required>
            </div>

            <div class="form-group">
                <label>دسته‌بندی</label>
                <select name="category_id" required>
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $food['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>عکس فعلی</label>
                <?php if ($food['image']): ?>
                    <img src="../<?= $food['image'] ?>" alt="عکس فعلی" class="current-image">
                <?php else: ?>
                    <p>بدون عکس</p>
                <?php endif; ?>
                <br><br>
                <label>عکس جدید (اختیاری)</label>
                <input type="file" name="image" accept="image/*">
                <small>فرمت: JPG, PNG, WEBP - حداکثر ۵ مگابایت</small>
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="is_available" <?= $food['is_available'] ? 'checked' : '' ?>>
                    موجود در منو
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-admin">به‌روزرسانی</button>
                <a href="manage_foods.php" class="btn-admin secondary">بازگشت</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>