<?php
// Database connection
require_once 'db_connect.php';

// Category id
$categoryId = isset($_GET['c']) ? intval($_GET['c']) : 0;

// category information
$categoryQuery = "SELECT category_id, category_description, category_image FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($categoryQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categoryResult = $stmt->get_result();
$category = $categoryResult->fetch_assoc();
$stmt->close();

// If category not found, redirect to home
if (!$category) {
    header("Location: index.php");
    exit();
}

// get products for this category
$productsQuery = "SELECT p.*, c.category_description 
                  FROM products p 
                  JOIN categories c ON p.product_category = c.category_id 
                  WHERE p.product_category = ? 
                  ORDER BY p.product_description";
$stmt = $conn->prepare($productsQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$productsResult = $stmt->get_result();
$stmt->close();

// Site structure
$siteName = "TechHub";
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($category['category_description']); ?> - <?php echo $siteName; ?></title>
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

    <!-- Row 2 Navigation braedcrumb -->
    <nav class="navigation">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>›</span>
            <span><?php echo htmlspecialchars($category['category_description']); ?></span>
        </div>
    </nav>

    <!-- main content -->
    <main class="main-content">
        <!-- category header -->
        <div class="category-header">
            <h1 class="category-title"><?php echo htmlspecialchars($category['category_description']); ?></h1>
            <p class="product-count">
                <?php echo $productsResult->num_rows; ?> product(s) found
            </p>
        </div>

        <!-- Products -->
        <?php if ($productsResult->num_rows > 0): ?>
            <div class="products-grid">
                <?php while($product = $productsResult->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['product_image'])): ?>
                                <?php
                                // Image path
                                $imagePath = $product['product_image'];
                                if (strpos($imagePath, 'http') !== 0) {
                                    // alternate
                                    $imagePath = 'https://mywork.gr/projects/courses/web/' . $imagePath;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>"
                                     alt="<?php echo htmlspecialchars($product['product_description']); ?>">
                            <?php else: ?>
                                <span style="color: #636e72;">No Image</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-title">
                            <?php echo htmlspecialchars($product['product_description']); ?>
                        </div>
                        <?php if (!empty($product['product_brand'])): ?>
                            <div style="color: #636e72; font-size: 12px; margin-bottom: 8px;">
                                Brand: <?php echo htmlspecialchars($product['product_brand']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="product-price">
                            $<?php echo number_format($product['product_price'], 2); ?>
                        </div>
                        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <p>No products found in this category.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- footer -->
    <footer class="footer">
        <div class="footer-links">
            <a href="#">Terms of Service</a>
            <span class="separator">•</span>
            <a href="#">Privacy Policy</a>
            <span class="separator">•</span>
            <a href="#">About Us</a>
            <span class="separator">•</span>
            <a href="#">Shipping Methods</a>
            <span class="separator">•</span>
            <a href="#">Payment Methods</a>
            <span class="separator">•</span>
            <a href="#">Return Policy</a>
        </div>
    </footer>
    </body>
    </html>
<?php
// Close database connection
$conn->close();
?>