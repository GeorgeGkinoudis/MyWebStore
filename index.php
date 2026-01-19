<?php
// Database connection
global $conn;
require_once 'db_connect.php';

// Site configuration
$siteName = "TechHub";
$welcomeMessage = "Welcome to TechHub";

// get categories from database
$categoriesQuery = "SELECT category_id, category_description, category_image FROM categories ORDER BY category_description";
$categoriesResult = $conn->query($categoriesQuery);

// Store categories in array for multiple use
$categories = [];
if ($categoriesResult && $categoriesResult->num_rows > 0) {
    while($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Footer links
$footerLinks = [
        "Terms of Service" => "#",
        "Privacy Policy" => "#",
        "About Us" => "#",
        "Shipping Methods" => "#",
        "Payment Methods" => "#",
        "Return Policy" => "#"
];
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $siteName; ?> TechHub</title>
        <link rel="stylesheet" href="style.css">
        <script src="cart.js"></script>
    </head>
    <body>
    <!-- Row 1 Header -->
    <header class="header">
        <a href="index.php" class="logo-link">
            <img src="LogoTechHub.svg" alt="TechHub Logo" class="logo">
        </a>
        <a href="cart.php" class="cart-link">
            <img src="cart.svg" alt="Cart Image" class="cart-icon">
        </a>
    </header>

     <!-- Row 2 Navigation -->
    <nav class="navigation">
        <ul class="nav-menu">
            <?php foreach ($categories as $navCategory): ?>
                <li>
                    <a href="search.php?c=<?php echo $navCategory['category_id']; ?>">
                        <?php echo htmlspecialchars($navCategory['category_description']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Row 3 categories -->
    <section class="categories-section">
        <h2 class="section-title"><?php echo $welcomeMessage; ?></h2>
        <div class="categories-grid">
            <?php
            if (!empty($categories)) {
                foreach($categories as $category):
                    ?>
                    <a href="search.php?c=<?php echo $category['category_id']; ?>" style="text-decoration: none;">
                        <div class="category-card">
                            <div class="category-image">
                                <?php if (!empty($category['category_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($category['category_image']); ?>"
                                         alt="<?php echo htmlspecialchars($category['category_description']); ?>">
                                <?php else: ?>
                                    <div class="image-placeholder">
                                        <img src="LogoTechHub.svg" alt="TechHub Logo" class="placeholder-logo">
                                        <p>No Image</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="category-name">
                                <?php echo htmlspecialchars($category['category_description']); ?>
                            </div>
                        </div>
                    </a>
                <?php
                endforeach;
            } else {
                echo "<p>No categories found.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Row 4 footer -->
    <footer class="footer">
        <div class="footer-links">
            <?php
            $linkCount = count($footerLinks);
            $currentIndex = 0;
            foreach ($footerLinks as $linkText => $linkUrl):
                $currentIndex++;
                ?>
                <a href="<?php echo htmlspecialchars($linkUrl); ?>">
                    <?php echo htmlspecialchars($linkText); ?>
                </a>
                <?php if ($currentIndex < $linkCount): ?>
                <span class="separator">•</span>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </footer>
    </body>
    </html>
<?php
// Close database connection
$conn->close();
?>