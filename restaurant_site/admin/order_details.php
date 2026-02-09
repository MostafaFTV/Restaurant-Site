<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header("Location: manage_orders.php");
    exit;
}


if (isset($_POST['status'])) {
    $status = $_POST['status'];
    $allowed = ['pending', 'confirmed', 'delivered', 'canceled'];
    if (in_array($status, $allowed)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);
        $_SESSION['success'] = "وضعیت سفارش بروز شد.";
        header("Location: order_details.php?id=$order_id");
        exit;
    }
}


$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(); 

if (!$order) {
    $_SESSION['error'] = "سفارش پیدا نشد.";
    header("Location: manage_orders.php");
    exit;
}


$stmt = $pdo->prepare("
    SELECT oi.*, f.name as food_name 
    FROM order_items oi 
    LEFT JOIN foods f ON oi.food_id = f.id 
    WHERE oi.order_id = ?
    ORDER BY oi.id
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(); 

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-form">
        <h2>جزئیات سفارش #<?= $order['id'] ?></h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="order-info">
            <div class="info-row">
                <strong>مشتری:</strong> 
                <?= htmlspecialchars($order['customer_name']) ?>
                <?= $order['user_name'] ? " <small>({$order['user_name']})</small>" : '' ?>
            </div>
            <div class="info-row">
                <strong>تلفن:</strong> <?= htmlspecialchars($order['customer_phone']) ?>
            </div>
            <div class="info-row">
                <strong>ایمیل:</strong> <?= $order['email'] ?? '—' ?>
            </div>
            <div class="info-row">
                <strong>آدرس:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?>
            </div>
            <div class="info-row">
                <strong>تاریخ:</strong> <?= date('d F Y - H:i', strtotime($order['created_at'])) ?>
            </div>
            <div class="info-row">
                <strong>وضعیت:</strong>
                <form method="POST" style="display:inline;">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>تأیید شده</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>تحویل داده شده</option>
                        <option value="canceled" <?= $order['status'] === 'canceled' ? 'selected' : '' ?>>لغو شده</option>
                    </select>
                </form>
            </div>
        </div>

        <h3>لیست غذاها</h3>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>غذا</th>
                        <th>تعداد</th>
                        <th>قیمت واحد</th>
                        <th>جمع</th>
                    </tr>
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
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <a href="manage_orders.php" class="btn-admin secondary">بازگشت به لیست</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>