<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}


$base_path = '/restaurant_site/';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رستوران خوشمزه</title>
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <h1><a href="<?= $base_path ?>index.php" style="color:#f9f5f0;text-decoration:none;">رستوران خوشمزه</a></h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?= $base_path ?>index.php">خانه</a></li>
                    <li><a href="<?= $base_path ?>menu.php">منو</a></li>
                    <li><a href="<?= $base_path ?>about.php">درباره ما</a></li>

                    
                    <li>
                        <a href="<?= $base_path ?>cart.php">
                            سبد خرید 
                            <span class="cart-count">(<?= $_SESSION['cart_count'] ?>)</span>
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        
                        <li><a href="<?= $base_path ?>logout.php">خروج</a></li>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            
                            <li>
                                <a href="<?= $base_path ?>admin/index.php" 
                                   style="color:#ff8a65; font-weight:600;">
                                    پنل ادمین
                                </a>
                            </li>
                        <?php else: ?>
                            
                            <li>
                                <a href="<?= $base_path ?>dashboard.php" 
                                   style="color:#8d6e63; font-weight:600;">
                                    پنل من
                                </a>
                            </li>
                        <?php endif; ?>

                    <?php else: ?>
                        
                        <li><a href="<?= $base_path ?>login.php">ورود</a></li>
                        <li><a href="<?= $base_path ?>register.php">ثبت نام</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>