<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<div class="container">
    <div class="about-page">
        <div class="about-header">
            <h2 class="page-title">درباره رستوران خوشمزه</h2>
            <p class="page-subtitle">طعم اصالت، عشق به غذا، تجربه‌ای متفاوت</p>
        </div>

        <div class="about-content">
            
            <div class="about-section">
                <div class="about-image">
                    <img src="assets/images/about-chef.jpg" alt="سرآشپز رستوران" class="img-responsive">
                </div>
                <div class="about-text">
                    <h3>داستان ما</h3>
                    <p>
                        رستوران <strong>خوشمزه</strong> در سال ۱۳۹۵ با هدف آوردن طعم‌های اصیل ایرانی و غذاهای بین‌المللی با کیفیت بالا به سفره شما تأسیس شد. ما باور داریم که غذا فقط یک وعده نیست، بلکه یک <strong>تجربه عاطفی و فرهنگی</strong> است.
                    </p>
                    <p>
                        از همان روز اول، با تیمی از سرآشپزهای حرفه‌ای و مواد اولیه تازه و محلی، تلاش کردیم تا هر لقمه‌ای که می‌خورید، خاطره‌ای دلنشین بسازد.
                    </p>
                </div>
            </div>

            
            <div class="about-values">
                <h3 class="section-title">ارزش‌های ما</h3>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">Fresh Ingredients</div>
                        <h4>مواد اولیه تازه</h4>
                        <p>هر روز از بازارهای محلی بهترین مواد را انتخاب می‌کنیم.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">Handmade</div>
                        <h4>دست‌پخت خانگی</h4>
                        <p>غذاها با عشق و دقت، مثل خانه پخته می‌شوند.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">Eco-Friendly</div>
                        <h4>حمایت از محیط زیست</h4>
                        <p>از بسته‌بندی قابل بازیافت و کاهش ضایعات استفاده می‌کنیم.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">24/7 Support</div>
                        <h4>پشتیبانی شبانه‌روزی</h4>
                        <p>تیم ما همیشه آماده پاسخگویی به شماست.</p>
                    </div>
                </div>
            </div>

            
            <div class="about-team">
                <h3 class="section-title">تیم حرفه‌ای ما</h3>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="assets/images/team1.jpg" alt="علی رضایی" class="team-img">
                        <h4>علی رضایی</h4>
                        <p>سرآشپز ارشد</p>
                    </div>
                    <div class="team-member">
                        <img src="assets/images/team2.jpg" alt="سارا محمدی" class="team-img">
                        <h4>سارا محمدی</h4>
                        <p>مدیر رستوران</p>
                    </div>
                    <div class="team-member">
                        <img src="assets/images/team3.jpg" alt="رضا حسینی" class="team-img">
                        <h4>رضا حسینی</h4>
                        <p>آشپز شیرینی‌پز</p>
                    </div>
                </div>
            </div>

            
            <div class="about-contact">
                <h3 class="section-title">با ما در ارتباط باشید</h3>
                <div class="contact-info">
                    <p><strong>آدرس:</strong> تهران، خیابان ولیعصر، پلاک ۱۲۳۴</p>
                    <p><strong>تلفن:</strong> ۰۲۱-۱۲۳۴۵۶۷۸</p>
                    <p><strong>ایمیل:</strong> info@khoshamzeh.com</p>
                    <p><strong>ساعات کاری:</strong> همه روزه ۱۱ صبح تا ۱۱ شب</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>