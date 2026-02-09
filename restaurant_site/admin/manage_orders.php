<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


if (isset($_GET['status']) && isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    $status = $_GET['status'];

    
    $allowed = ['pending', 'processing', 'delivered', 'canceled'];

    if (in_array($status, $allowed)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")
            ->execute([$status, $order_id]);
        $_SESSION['success'] = "وضعیت سفارش بروز شد.";
    }

    header("Location: manage_orders.php");
    exit;
}


$orders = $pdo->query("
    SELECT o.*, u.name AS user_name
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container">
    <div class="admin-list">
        <h2>مدیریت سفارش‌ها</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <p class="no-data">هنوز سفارشی ثبت نشده است.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>شماره</th>
                            <th>مشتری</th>
                            <th>تلفن</th>
                            <th>جمع کل</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>

                                <td>
                                    <?= htmlspecialchars($order['customer_name']) ?>
                                    <br>
                                    <small><?= $order['user_name'] ? "($order[user_name])" : 'مهمان' ?></small>
                                </td>

                                <td><?= htmlspecialchars($order['customer_phone']) ?></td>

                                <td><?= number_format($order['total_amount']) ?> تومان</td>

                                <td>
                                    <span class="status <?= $order['status'] ?>">
                                        <?= [
                                            'pending'    => 'در انتظار',
                                            'processing' => 'در حال پردازش',
                                            'delivered'  => 'تحویل داده شده',
                                            'canceled'   => 'لغو شده'
                                        ][$order['status']] ?? 'نامشخص' ?>
                                    </span>
                                </td>

                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>

                                <td class="actions">
                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn-small edit">جزئیات</a>

                                    <?php if ($order['status'] === 'pending'): ?>
                                        <a href="manage_orders.php?order_id=<?= $order['id'] ?>&status=processing" class="btn-small toggle">تأیید</a>
                                    <?php endif; ?>

                                    <?php if ($order['status'] === 'processing'): ?>
                                        <a href="manage_orders.php?order_id=<?= $order['id'] ?>&status=delivered" class="btn-small toggle">تحویل</a>
                                    <?php endif; ?>

                                    <?php if ($order['status'] !== 'canceled' && $order['status'] !== 'delivered'): ?>
                                        <a href="manage_orders.php?order_id=<?= $order['id'] ?>&status=canceled" class="btn-small delete">لغو</a>
                                    <?php endif; ?>
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
