<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';

$category_id = $_GET['category'] ?? '';
$where = '';
$params = [];

if ($category_id && is_numeric($category_id)) {
    $where = "WHERE f.category_id = ? AND f.is_available = 1";
    $params = [$category_id];
} else {
    $where = "WHERE f.is_available = 1";
}

$sql = "
    SELECT f.*, c.name as category_name 
    FROM foods f 
    LEFT JOIN categories c ON f.category_id = c.id 
    $where 
    ORDER BY f.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$foods = $stmt->fetchAll();


$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="menu-page">
        <h2 class="page-title">منوی رستوران خوشمزه</h2>

        
        <div class="category-filter">
            <a href="menu.php" class="<?= !$category_id ? 'active' : '' ?>">همه غذاها</a>
            <?php foreach ($categories as $cat): ?>
                <a href="menu.php?category=<?= $cat['id'] ?>" 
                   class="<?= $category_id == $cat['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        
        <?php if (empty($foods)): ?>
            <p class="no-foods">غذایی در این دسته موجود نیست.</p>
        <?php else: ?>
            <div class="foods-grid">
                <?php foreach ($foods as $food): ?>
                    <div class="food-card">
                        <?php if ($food['image']): ?>
                            <img src="<?= $food['image'] ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-image">
                        <?php else: ?>
                            <div class="no-image-card">بدون عکس</div>
                        <?php endif; ?>

                        <div class="food-info">
                            <h3><?= htmlspecialchars($food['name']) ?></h3>
                            <p class="food-desc"><?= htmlspecialchars($food['description']) ?></p>
                            <div class="food-price"><?= number_format($food['price']) ?> تومان</div>
                        </div>

                        <div class="food-actions">
                            <form method="POST" action="cart.php" class="add-to-cart-form">
                                <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                                <button type="submit" name="add_to_cart" class="btn-add-cart">
                                    اضافه به سبد
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>