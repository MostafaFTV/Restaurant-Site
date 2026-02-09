<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-dashboard">
        <h2>پنل مدیریت رستوران خوشمزه</h2>
        <p>خوش آمدید، <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>

        <div class="admin-actions">
            <a href="add_food.php" class="btn-admin">اضافه کردن غذا</a>
            <a href="manage_foods.php" class="btn-admin">مدیریت غذاها</a>
            <a href="categories.php" class="btn-admin">مدیریت دسته‌بندی‌ها</a>
            <a href="manage_orders.php" class="btn-admin">مدیریت سفارش‌ها</a>
            <a href="../index.php" class="btn-admin secondary">بازگشت به سایت</a>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <h3>تعداد غذاها</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM foods");
                $count = $stmt->fetchColumn();
                ?>
                <p class="stat-number"><?= $count ?></p>
            </div>
            <div class="stat-card">
                <h3>تعداد دسته‌بندی‌ها</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
                $count = $stmt->fetchColumn();
                ?>
                <p class="stat-number"><?= $count ?></p>
            </div>
            <div class="stat-card">
                <h3>سفارش‌های امروز</h3>
                <?php
                $today = date('Y-m-d');
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = ?");
                $stmt->execute([$today]);
                $count = $stmt->fetchColumn();
                ?>
                <p class="stat-number"><?= $count ?></p>
            </div>
            <div class="stat-card">
                <h3>کل سفارش‌ها</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
                $count = $stmt->fetchColumn();
                ?>
                <p class="stat-number"><?= $count ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>