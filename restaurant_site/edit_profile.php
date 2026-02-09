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
$success = $error = '';


$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    
    if (!$name || !$email) {
        $error = "نام و ایمیل الزامی هستند.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "ایمیل معتبر نیست.";
    } elseif ($password && $password !== $confirm_password) {
        $error = "رمز عبور و تکرار آن مطابقت ندارند.";
    } else {
        try {
            $updates = ['name' => $name, 'email' => $email];
            $sql = "UPDATE users SET name = ?, email = ?";

            if ($password) {
                $updates['password'] = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", password = ?";
            }

            $sql .= " WHERE id = ?";
            $updates['id'] = $user_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($updates));

            
            $_SESSION['user_name'] = $name;

            $success = "پروفایل با موفقیت بروز شد!";
        } catch (Exception $e) {
            $error = "خطا در بروزرسانی. لطفاً دوباره تلاش کنید.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-form">
        <h2>ویرایش پروفایل</h2>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>نام و نام خانوادگی</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>رمز عبور جدید (خالی بگذارید اگر نمی‌خواهید تغییر کند)</label>
                <input type="password" name="password" placeholder="رمز جدید">
            </div>

            <div class="form-group">
                <label>تکرار رمز عبور</label>
                <input type="password" name="confirm_password" placeholder="تکرار رمز">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-admin">ذخیره تغییرات</button>
                <a href="dashboard.php" class="btn-admin secondary">بازگشت</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>