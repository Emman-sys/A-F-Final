<?php
// filepath: c:\Users\ceile\A-F-Final\product_carousel.php

function generateProductCarousel($conn) {
    // Get random products for carousel
    $stmt = $conn->prepare("
        SELECT p.product_id, p.name, p.price, p.stock_quantity,
               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
        FROM products p 
        ORDER BY RAND() 
        LIMIT 5
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    
    if (empty($products)) {
        return '<div class="product-carousel"><p>No products available</p></div>';
    }
    
    $html = '<div class="product-carousel">';
    $html .= '<div class="carousel-header">';
    $html .= '<h3>Featured Products</h3>';
    $html .= '<div class="carousel-controls">';
    $html .= '<button class="carousel-btn" onclick="prevSlide()">&#8249;</button>';
    $html .= '<button class="carousel-btn" onclick="nextSlide()">&#8250;</button>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '<div class="carousel-container">';
    $html .= '<div class="carousel-track">';
    
    foreach ($products as $product) {
        $html .= '<div class="carousel-item" data-product-id="' . $product['product_id'] . '">';
        $html .= '<div class="carousel-product-card">';
        
        // Product image
        $html .= '<div class="carousel-product-image">';
        if ($product['has_image']) {
            $html .= '<img src="display_image.php?id=' . $product['product_id'] . '" alt="' . htmlspecialchars($product['name']) . '">';
        } else {
            $html .= '<div class="carousel-placeholder-img">' . strtoupper(substr($product['name'], 0, 1)) . '</div>';
        }
        $html .= '</div>';
        
        // Product info
        $html .= '<div class="carousel-product-info">';
        $html .= '<h4 class="product-name">' . htmlspecialchars($product['name']) . '</h4>';
        $html .= '<div class="carousel-price">$' . number_format($product['price'], 2) . '</div>';
        
        // Stock info
        if ($product['stock_quantity'] <= 0) {
            $html .= '<div class="stock-info">Out of Stock</div>';
        } elseif ($product['stock_quantity'] <= 10) {
            $html .= '<div class="stock-info">Only ' . $product['stock_quantity'] . ' left</div>';
        } else {
            $html .= '<div class="stock-info">In Stock</div>';
        }
        
        // Action buttons
        $html .= '<div class="product-actions">';
        $html .= '<button class="carousel-add-btn" onclick="addToCartFromCarousel(' . $product['product_id'] . ', event)">Add to Cart</button>';
        $html .= '<button class="view-details-btn" onclick="openProductModal(' . $product['product_id'] . ')">View Details</button>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    // Carousel indicators
    $html .= '<div class="carousel-indicators">';
    for ($i = 0; $i < count($products); $i++) {
        $active = $i === 0 ? 'active' : '';
        $html .= '<div class="indicator ' . $active . '" onclick="goToSlide(' . $i . ')"></div>';
    }
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}
?>