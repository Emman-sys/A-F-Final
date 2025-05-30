<?php
// filepath: c:\Users\ceile\A-F-Final\includes\CategoryManager.php
class CategoryManager {
    private $conn;
    private $categories;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
        $this->loadCategoriesFromDatabase();
    }
    
    private function loadCategoriesFromDatabase() {
        $this->categories = [];
        
        try {
            $stmt = $this->conn->prepare("SELECT category_id, category_name FROM categories ORDER BY category_id");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $gradients = [
                1 => 'linear-gradient(to bottom, #55361A, #CDACB1)',
                2 => 'linear-gradient(to bottom, #BFB886, #F3E794)', 
                3 => 'linear-gradient(to bottom, #D97272, #F8DDDD)',
                4 => 'linear-gradient(to bottom, #71EEEC, #C1ACAC)',
                5 => 'linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D)',
                6 => 'linear-gradient(to bottom, #E6E6FA, #DDA0DD)',
                7 => 'linear-gradient(to bottom, #FFE4E1, #FFC0CB)',
                8 => 'linear-gradient(to bottom, #F0E68C, #BDB76B)',
            ];
            
            while ($row = $result->fetch_assoc()) {
                $category_id = $row['category_id'];
                $category_name = $row['category_name'];
                
                $this->categories[$category_id] = [
                    'name' => $category_name,
                    'gradient' => isset($gradients[$category_id]) ? $gradients[$category_id] : 'linear-gradient(to bottom, #C647CC, #ECC7ED)',
                    'placeholder_letter' => strtoupper(substr($category_name, 0, 1))
                ];
            }
        } catch (Exception $e) {
            error_log("CategoryManager: Failed to load categories from database - " . $e->getMessage());
        }
    }
    
    public function getCategoryImage($category_id) {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE category_id = ? AND product_image IS NOT NULL LIMIT 1");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        return null;
    }
    
    public function getAllProductsImage() {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE product_image IS NOT NULL ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        return null;
    }
    
    public function generateCategoryHTML() {
        $html = "";
        
        // Add "All Products" category first
        $all_products_image = $this->getAllProductsImage();
        
        $html .= "<div class='category-item category-item-all' data-category=''>";
        
        if ($all_products_image) {
            $html .= "<img src='{$all_products_image}' class='category-image' alt='All Products'>";
        } else {
            $html .= "<div class='product-placeholder'>";
            $html .= "ALL";
            $html .= "</div>";
        }
        
        $html .= "<div class='category-label'>All Products</div>";
        $html .= "</div>";
        
        // Add existing categories
        foreach ($this->categories as $id => $category) {
            $image_src = $this->getCategoryImage($id);
            
            $html .= "<div class='category-item category-item-{$id}' data-category='{$id}'>";
            
            if ($image_src) {
                $html .= "<img src='{$image_src}' class='category-image' alt='{$category['name']}'>";
            } else {
                $html .= "<div class='product-placeholder'>";
                $html .= $category['placeholder_letter'];
                $html .= "</div>";
            }
            
            $html .= "<div class='category-label'>{$category['name']}</div>";
            $html .= "</div>";
        }
        return $html;
    }
    
    public function categoryExists($id) {
        return isset($this->categories[$id]);
    }
}
?>