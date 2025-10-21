<?php
include_once '../Controller/ProductController.php';
$productController = new ProductController($connection);
$categories = $productController->getAllCategoriesWithProducts();
?>

 
<div style="padding: 40px; margin-top: 50px;">
  <?php
  if ($categories && count($categories) > 0) {
    foreach ($categories as $category) {
      $categoryName = htmlspecialchars($category['category_name']);
      $products = $category['products'];

      if (!empty($products)) {
        echo '<div class="category-section" style="margin-bottom: 60px;">';
        echo '<h2 style="font-size: 26px; font-weight: bold; margin-bottom: 20px;"> ' . $categoryName . '</h2>';
        
        echo '<div class="slider-container" style="position: relative;">';
        echo '<button class="scroll-btn scroll-left" onclick="scrollSlider(this, -1)">⟨</button>';
        
        echo '<div class="product-slider" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth;">';
        
        foreach ($products as $product) {
        //   $imagePath = str_replace('../', '', $product['image_path']);
        //   $finalPath = "employe_management_system/" . $imagePath;

        //   if (!file_exists("../" . $finalPath)) {
        //     $finalPath = "uploads/no-image.png";
        //   }
            //  $imagePath = trim($product['image_path']);
             $imagePath = isset($product['image_path']) ? trim((string)$product['image_path']) : '';

             $finalPath = "../" . $imagePath;

          echo '<div class="product-card" 
                style="flex: 0 0 auto; width: 220px; border: 1px solid #ccc; border-radius: 12px; 
                       text-align: center; padding: 15px; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">';
          
          echo '<img src="' . $finalPath . '" alt="' . htmlspecialchars($product['product_name']) . '" 
                 style="width: 180px; height: 150px; object-fit: cover; border-radius: 10px;">';
          
          echo '<div class="product-name" style="font-weight: 600; margin-top: 10px;">' 
                . htmlspecialchars($product['product_name']) . '</div>';
          
          echo '<div class="product-price" style="font-weight: bold; color: #2a7;">$' 
                . htmlspecialchars($product['product_price']) . '</div>';
          
          // echo '<button class="add-to-cart" 
          //         style="margin-top: 10px; background: #ff6b00; color: white; 
          //                border: none; border-radius: 5px; padding: 8px 12px; cursor: pointer;">
          //       Add to Cart
          //       </button>';


                echo '<a href="../web_layout/product_details.php?id=' . $product['product_id'] . '" 
        style="display:inline-block; margin-top:10px; background:#ff6b00; 
               color:white; text-decoration:none; border-radius:5px; padding:8px 12px;">
        Add to Cart
      </a>';




        

          
          echo '</div>'; // end product-card
        }
        
        echo '</div>'; // end slider
        echo '<button class="scroll-btn scroll-right" onclick="scrollSlider(this, 1)">⟩</button>';
        echo '</div>'; // end container
        echo '</div>'; // end section
      }
    }
  } else {
    echo "<p>No categories or products found.</p>";
  }
  ?>
</div>

<!-- ✅ JS for Slider Scrolling -->
<script>
function scrollSlider(button, direction) {
  const container = button.parentElement;
  const slider = container.querySelector('.product-slider');
  slider.scrollBy({ left: direction * 300, behavior: 'smooth' });
}
</script>

<!-- ✅ CSS (optional for better look) -->
<style>
.scroll-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: black;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 20px;
  z-index: 10;
}
.scroll-left { left: 0; }
.scroll-right { right: 0; }
.product-slider::-webkit-scrollbar { display: none; }
</style>
