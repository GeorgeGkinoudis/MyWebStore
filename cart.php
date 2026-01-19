<?php
// database connection
require_once 'db_connect.php';

// Site configuration
$siteName = "TechHub";

// get categories from database for navigation menu
$categoriesQuery = "SELECT category_id, category_description, category_image FROM categories ORDER BY category_description";
$categoriesResult = $conn->query($categoriesQuery);

// Store categories in array for multiple use
$categories = [];
if ($categoriesResult && $categoriesResult->num_rows > 0) {
    while($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// cart cookie
$cartItems = [];
$cartTotal = 0;

if (isset($_COOKIE['MyWebStoreCart'])) {
    $cookieData = $_COOKIE['MyWebStoreCart'];
    $items = explode(',', $cookieData);
    
    foreach ($items as $item) {
        if (!empty($item)) {
            $parts = explode(':', $item);
            if (count($parts) == 2) {
                $productId = intval($parts[0]);
                $quantity = intval($parts[1]);
                
                // get product details from database
                $productQuery = "SELECT * FROM products WHERE product_id = ?";
                $stmt = $conn->prepare($productQuery);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    $product['quantity'] = $quantity;
                    $product['subtotal'] = $product['product_price'] * $quantity;
                    $cartItems[] = $product;
                    $cartTotal += $product['subtotal'];
                }
                $stmt->close();
            }
        }
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
    <title>Shopping Cart - <?php echo $siteName; ?></title>
    <link rel="stylesheet" href="style.css">
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

    <!-- Row 3 Cart -->
    <section class="cart-content">
        <h1 class="cart-title">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart alert -->
            <div class="empty-cart">
                <p>The shopping cart is empty</p>
                <a href="index.php" class="continue-shopping">← Continue Shopping</a>
            </div>
        <?php else: ?>
            <!-- Cart Table -->
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <a href="#" class="cart-item-title">
                                    <?php echo htmlspecialchars($item['product_description']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($item['product_image'])): ?>
                                    <?php
                                    $imagePath = $item['product_image'];
                                    if (strpos($imagePath, 'http') !== 0) {
                                        $imagePath = 'https://mywork.gr/projects/courses/web/' . $imagePath;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_description']); ?>"
                                         class="cart-item-image">
                                <?php else: ?>
                                    <div class="cart-item-image" style="background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #636e72; font-size: 12px;">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="cart-item-price">
                                    $<?php echo number_format($item['product_price'], 2); ?>
                                </span>
                            </td>
                            <td>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" onclick="return false;">-</button>
                                    <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                    <button class="quantity-btn" onclick="return false;">+</button>
                                </div>
                            </td>
                            <td>
                                <span class="cart-subtotal">
                                    $<?php echo number_format($item['subtotal'], 2); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="cart-total">
                    <span class="cart-total-label">Total:</span>
                    <span class="cart-total-amount">$<?php echo number_format($cartTotal, 2); ?></span>
                </div>
                <button class="checkout-btn" onclick="return false;">Proceed to Checkout</button>
                <br>
                <a href="index.php" class="continue-shopping">← Continue Shopping</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Row 4 Footer -->
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