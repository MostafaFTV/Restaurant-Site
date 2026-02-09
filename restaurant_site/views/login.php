<?php require 'includes/header.php'; ?>

<div class="container">
    <div class="auth-form">
        <h2>ورود به حساب کاربری</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>رمز عبور</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">ورود</button>
        </form>
        <p>حساب ندارید؟ <a href="register.php">ثبت‌نام کنید</a></p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>