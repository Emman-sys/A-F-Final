<?php
// Product Carousel Component
function getRandomProducts($conn, $limit = 5, $category_id = null) {
    $sql = "
        SELECT p.product_id, p.name, p.price, p.stock_quantity, p.description,
               c.category_name,
               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.stock_quantity > 0";
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
    }
    
    $sql .= " ORDER BY RAND() LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($category_id) {
        $stmt->bind_param("ii", $category_id, $limit);
    } else {
        $stmt->bind_param("i", $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function generateProductCarousel($conn, $category_id = null, $title = "Featured Products") {
    $products = getRandomProducts($conn, 5, $category_id);
    
    if (empty($products)) {
        return '<div class="carousel-placeholder">No products available</div>';
    }
    
    $html = '
    <div class="product-carousel">
        <div class="carousel-header">
            <h3>' . htmlspecialchars($title) . '</h3>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn" onclick="moveCarousel(-1)" aria-label="Previous">‹</button>
                <button class="carousel-btn next-btn" onclick="moveCarousel(1)" aria-label="Next">›</button>
            </div>
        </div>
        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">';
    
    foreach ($products as $index => $product) {
        $html .= '
            <div class="carousel-item" data-index="' . $index . '" data-product-id="' . $product['product_id'] . '">
                <div class="carousel-product-card">
                    <div class="carousel-product-image">';
        
        if ($product['has_image']) {
            $html .= '<img src="display_image.php?id=' . $product['product_id'] . '" 
                          alt="' . htmlspecialchars($product['name']) . '" 
                          loading="lazy">';
        } else {
            $html .= '<div class="carousel-placeholder-img">' . 
                     htmlspecialchars(strtoupper(substr($product['name'], 0, 1))) . '</div>';
        }
        
        $html .= '
                    </div>
                    <div class="carousel-product-info">
                        <div class="product-category">' . 
                        htmlspecialchars($product['category_name'] ?? 'Uncategorized') . '</div>
                        <h4 class="product-name">' . htmlspecialchars($product['name']) . '</h4>';
        
        if (!empty($product['description'])) {
            $html .= '<p class="product-description">' . 
                     htmlspecialchars(substr($product['description'], 0, 100)) . 
                     (strlen($product['description']) > 100 ? '...' : '') . '</p>';
        }
        
        $html .= '
                        <div class="product-details">
                            <p class="carousel-price">$' . number_format($product['price'], 2) . '</p>
                            <p class="stock-info">Stock: ' . $product['stock_quantity'] . '</p>
                        </div>
                        <div class="product-actions">
                            <button class="carousel-add-btn" onclick="addToCart(' . $product['product_id'] . ', event)">
                                Add to Cart
                            </button>
                            <button class="view-details-btn" onclick="viewProduct(' . $product['product_id'] . ')">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>';
    }
    
    $html .= '
            </div>
        </div>
        <div class="carousel-indicators">';
    
    for ($i = 0; $i < count($products); $i++) {
        $html .= '<span class="indicator' . ($i === 0 ? ' active' : '') . '" onclick="goToSlide(' . $i . ')"></span>';
    }
    
    $html .= '
        </div>
    </div>';
    
    return $html;
}

function getProductsByCategory($conn, $category_id, $limit = 10) {
    $stmt = $conn->prepare("
        SELECT p.product_id, p.name, p.price, p.stock_quantity, p.description,
               c.category_name,
               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.category_id = ? AND p.stock_quantity > 0
        ORDER BY p.name ASC 
        LIMIT ?
    ");
    $stmt->bind_param("ii", $category_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>