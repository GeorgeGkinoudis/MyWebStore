// Add to Cart functionality
function addToCart(productId) {
    // Get existing cart cookie
    let cart = getCookie('MyWebStoreCart');
    let cartItems = [];

    if (cart) {
        // Parse existing cart
        cartItems = cart.split(',');
    }

    // Check if product already exists in cart
    let productFound = false;
    let newCartItems = [];

    for (let i = 0; i < cartItems.length; i++) {
        if (cartItems[i]) {
            let parts = cartItems[i].split(':');
            let id = parts[0];
            let quantity = parseInt(parts[1]);

            if (id === String(productId)) {
                // Product exists, increase quantity
                quantity++;
                productFound = true;
            }

            newCartItems.push(id + ':' + quantity);
        }
    }

    // If product not found, add it with quantity 1
    if (!productFound) {
        newCartItems.push(productId + ':1');
    }

    // Save updated cart to cookie
    let cookieValue = newCartItems.join(',');
    document.cookie = 'MyWebStoreCart=' + cookieValue + '; path=/; max-age=86400'; // 24 hours

    // Show confirmation
    alert('Product added to cart!');
}

// Get cookie value
function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}