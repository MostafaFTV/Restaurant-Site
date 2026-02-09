<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM foods WHERE id = ?");
    $stmt->execute([$id]);
    $food = $stmt->fetch();

    if ($food && $food['image']) {
        @unlink('../' . $food['image']); 
    }

    $pdo->prepare("DELETE FROM foods WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = "غذا حذف شد.";
    header("Location: manage_foods.php");
    exit; 
}


if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE foods SET is_available = NOT is_available WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "وضعیت غذا تغییر کرد.";
    header("Location: manage_foods.php");
    exit; 
}

$foods = $pdo->query("
    SELECT f.*, c.name as category_name 
    FROM foods f 
    LEFT JOIN categories c ON f.category_id = c.id 
    ORDER BY f.created_at DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-list">
        <h2>مدیریت غذاها</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="table-actions">
            <a href="add_food.php" class="btn-admin">اضافه کردن غذای جدید</a>
        </div>

        <?php if (empty($foods)): ?>
            <p class="no-data">هنوز غذایی اضافه نشده است.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>عکس</th>
                            <th>نام</th>
                            <th>دسته‌بندی</th>
                            <th>قیمت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($foods as $food): ?>
                            <tr>
                                <td>
                                    <?php if ($food['image']): ?>
                                        <img src="../<?= $food['image'] ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-thumb">
                                    <?php else: ?>
                                        <div class="no-image">بدون عکس</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($food['name']) ?></td>
                                <td><?= htmlspecialchars($food['category_name'] ?? 'نامشخص') ?></td>
                                <td><?= number_format($food['price']) ?> تومان</td>
                                <td>
                                    <span class="status <?= $food['is_available'] ? 'available' : 'unavailable' ?>">
                                        <?= $food['is_available'] ? 'موجود' : 'ناموجود' ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="edit_food.php?id=<?= $food['id'] ?>" class="btn-small edit">ویرایش</a>
                                    <a href="manage_foods.php?toggle=<?= $food['id'] ?>" class="btn-small toggle">
                                        <?= $food['is_available'] ? 'ناموجود' : 'موجود' ?> کن
                                    </a>
                                    <a href="manage_foods.php?delete=<?= $food['id'] ?>" 
                                       class="btn-small delete" 
                                       onclick="return confirm('آیا مطمئن هستید؟')">
                                        حذف
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>