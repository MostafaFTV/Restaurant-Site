<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';


if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$success = $error = '';


$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_POST) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (!$name || !$phone || !$address) {
        $error = "همه فیلدها الزامی هستند.";
    } else {
        try {
            $pdo->beginTransaction();

            
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $phone, $address, $total]);
            $order_id = $pdo->lastInsertId();

            
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $food_id => $item) {
                $stmt->execute([$order_id, $food_id, $item['quantity'], $item['price']]);
            }

            $pdo->commit();

            
            unset($_SESSION['cart']);
            $_SESSION['cart_count'] = 0;
            $success = "سفارش شما با موفقیت ثبت شد! شماره سفارش: #$order_id";

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "خطا در ثبت سفارش. لطفاً دوباره تلاش کنید.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="checkout-page">
        <h2 class="page-title">تسویه حساب</h2>

        <?php if ($success): ?>
            <div class="alert success">
                <?= $success ?>
                <br><br>
                <a href="menu.php" class="btn-admin">برگشت به منو</a>
            </div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php else: ?>

            <div class="checkout-summary">
                <h3>خلاصه سفارش</h3>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-item">
                        <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                        <span><?= number_format($item['price'] * $item['quantity']) ?> تومان</span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-total">
                    <strong>جمع کل:</strong>
                    <strong><?= number_format($total) ?> تومان</strong>
                </div>
            </div>

            <form method="POST" class="checkout-form">
                <div class="form-group">
                    <label>نام و نام خانوادگی</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>شماره تماس</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>آدرس تحویل</label>
                    <textarea name="address" rows="4" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="cart.php" class="btn-admin secondary">بازگشت به سبد</a>
                    <button type="submit" class="btn-admin">ثبت سفارش</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>