<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header("Location: dashboard.php");
    exit;
}


$stmt = $pdo->prepare("
    SELECT o.* FROM orders o 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = "سفارش پیدا نشد.";
    header("Location: dashboard.php");
    exit;
}


$stmt = $pdo->prepare("
    SELECT oi.*, f.name as food_name 
    FROM order_items oi 
    LEFT JOIN foods f ON oi.food_id = f.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>جزئیات سفارش #<?= $order['id'] ?></h2>

        <div class="order-info">
            <div class="info-row"><strong>مشتری:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
            <div class="info-row"><strong>تلفن:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
            <div class="info-row"><strong>آدرس:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></div>
            <div class="info-row"><strong>تاریخ:</strong> <?= date('d F Y - H:i', strtotime($order['created_at'])) ?></div>
            <div class="info-row"><strong>وضعیت:</strong>
                <span class="status <?= $order['status'] ?>">
                    <?= [
                        'pending' => 'در انتظار',
                        'confirmed' => 'تأیید شده',
                        'delivered' => 'تحویل داده شده',
                        'canceled' => 'لغو شده'
                    ][$order['status']] ?>
                </span>
            </div>
        </div>

        <h3>لیست غذاها</h3>
        <table class="admin-table">
            <thead>
                <tr><th>غذا</th><th>تعداد</th><th>قیمت</th><th>جمع</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['food_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price']) ?> تومان</td>
                        <td><?= number_format($item['price'] * $item['quantity']) ?> تومان</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">جمع کل:</th>
                    <th><?= number_format($order['total_amount']) ?> تومان</th>
                </tr>
            </tfoot>
        </table>

        <div class="form-actions" style="margin-top:2rem;">
            <a href="dashboard.php" class="btn-admin secondary">بازگشت</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>