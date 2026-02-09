<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


$stmt = $pdo->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as items_count
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="user-dashboard">
        <h2>پنل کاربری</h2>
        <p>خوش آمدید، <strong><?= htmlspecialchars($user['name']) ?></strong></p>
        <p class="user-email">ایمیل: <?= htmlspecialchars($user['email']) ?></p>

        <div class="user-actions">
            <a href="edit_profile.php" class="btn-admin">ویرایش پروفایل</a>
            <a href="logout.php" class="btn-admin secondary">خروج</a>
        </div>

        <h3>سفارش‌های شما</h3>

        <?php if (empty($orders)): ?>
            <p class="no-data">هنوز سفارشی ثبت نکرده‌اید.</p>
            <a href="menu.php" class="btn-admin">شروع خرید</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>شماره</th>
                            <th>تاریخ</th>
                            <th>تعداد آیتم</th>
                            <th>جمع کل</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['items_count'] ?></td>
                                <td><?= number_format($order['total_amount']) ?> تومان</td>
                                <td>
                                    <span class="status <?= $order['status'] ?>">
                                        <?= [
                                            'pending' => 'در انتظار',
                                            'confirmed' => 'تأیید شده',
                                            'delivered' => 'تحویل داده شده',
                                            'canceled' => 'لغو شده'
                                        ][$order['status']] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn-small edit">جزئیات</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>    