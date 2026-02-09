<?php require 'includes/header.php'; ?>

<div class="container">
    <div class="auth-form">
        <h2>ثبت‌نام در رستوران خوشمزه</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>نام کامل</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>رمز عبور</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>تلفن (اختیاری)</label>
                <input type="text" name="phone">
            </div>
            <button type="submit" class="btn-primary">ثبت‌نام</button>
        </form>
        <p>قبلاً ثبت‌نام کردید؟ <a href="login.php">وارد شوید</a></p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>