<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}


if (isset($_POST['add_to_cart'])) {
    $food_id = (int)$_POST['food_id'];
    
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM foods WHERE id = ? AND is_available = 1");
    $stmt->execute([$food_id]);
    $food = $stmt->fetch();

    if ($food) {
        if (isset($_SESSION['cart'][$food_id])) {
            $_SESSION['cart'][$food_id]['quantity']++;
        } else {
            $_SESSION['cart'][$food_id] = [
                'name' => $food['name'],
                'price' => $food['price'],  
                'image' => $food['image'],
                'quantity' => 1
            ];
        }
        
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
        $_SESSION['success'] = "«{$food['name']}» به سبد اضافه شد!";
    } else {
        $_SESSION['error'] = "غذا یافت نشد یا در دسترس نیست.";
    }
}


if (isset($_GET['remove'])) {
    $food_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$food_id])) {
        $food_name = $_SESSION['cart'][$food_id]['name'];
        unset($_SESSION['cart'][$food_id]);
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
        $_SESSION['success'] = "«{$food_name}» از سبد حذف شد.";
    }
    header("Location: cart.php");
    exit;
}


if (isset($_GET['increase'])) {
    $food_id = (int)$_GET['increase'];
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id]['quantity']++;
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    header("Location: cart.php");
    exit;
}


if (isset($_GET['decrease'])) {
    $food_id = (int)$_GET['decrease'];
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id]['quantity']--;
        if ($_SESSION['cart'][$food_id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$food_id]);
        }
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    header("Location: cart.php");
    exit;
}


$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="cart-page">
        <h2 class="page-title">سبد خرید شما</h2>

        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>سبد خرید شما خالی است.</p>
                <a href="menu.php" class="btn-admin">برگشت به منو</a>
            </div>

        <?php else: ?>
            
            <div class="cart-items">
                <?php foreach ($_SESSION['cart'] as $food_id => $item): ?>
                    <div class="cart-item">
                        
                        <?php if ($item['image']): ?>
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-image">
                        <?php else: ?>
                            <div class="no-image-cart">بدون عکس</div>
                        <?php endif; ?>

                        
                        <div class="cart-info">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="cart-price"><?= number_format($item['price']) ?> تومان</div>
                        </div>

                        
                        <div class="cart-quantity">
                            <a href="cart.php?decrease=<?= $food_id ?>" class="qty-btn">-</a>
                            <span class="qty-number"><?= $item['quantity'] ?></span>
                            <a href="cart.php?increase=<?= $food_id ?>" class="qty-btn">+</a>
                        </div>

                        
                        <div class="cart-subtotal">
                            <?= number_format($item['price'] * $item['quantity']) ?> تومان
                        </div>

                        
                        <a href="cart.php?remove=<?= $food_id ?>" class="btn-remove" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                    </div>
                <?php endforeach; ?>
            </div>

            
            <div class="cart-summary">
                <div class="cart-total">
                    <strong>جمع کل:</strong>
                    <span><?= number_format($total) ?> تومان</span>
                </div>
                <div class="cart-actions">
                    <a href="menu.php" class="btn-admin secondary">ادامه خرید</a>
                    <a href="checkout.php" class="btn-admin">تسویه حساب</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>