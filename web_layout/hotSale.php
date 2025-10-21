<?php
include_once '../Controller/ProductController.php';
$productController = new ProductController($connection);
$hotSaleProducts = $productController->showHotSaleProducts();
?>

<!-- Hot Sale Products Section -->
<div class="hot-selling-container" 
     style="position: relative; padding: 40px; margin-top: 100px; background: #fafafa; border-radius: 12px;">

  <div class="hot-selling-title" 
       style="text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 30px; color: #333;">
        Hot Sale Products
  </div>

  <!-- Left Scroll Button -->
  <button class="scroll-btn scroll-left"
          style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
                 background: black; color: white; border: none; padding: 10px 15px;
                 border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 2;">⟨</button>

  <!-- Product Slider -->
  <div class="product-slider" id="hot-sale-slider"
       style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding: 10px;" style='overflow-x: auto !important;'>

    <?php
    if ($hotSaleProducts && $hotSaleProducts->num_rows > 0) {
        while ($product = $hotSaleProducts->fetch_assoc()) {
            // $imagePath = trim($product['image_path']);
             $imagePath = isset($product['image_path']) ? trim((string)$product['image_path']) : '';

            $finalPath = "../" . $imagePath;
            ?>
            
            <div class="product-card" 
                 style="flex: 0 0 auto; width: 220px; border: 1px solid #ccc; border-radius: 12px; 
                        text-align: center; padding: 15px; background: white; 
                        box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
              
              <img src="<?php echo htmlspecialchars($finalPath); ?>" 
                   alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                   style="width: 180px; height: 150px; object-fit: cover; border-radius: 10px;">
              
              <div class="product-name" style="font-weight: 600; margin-top: 10px; color: #222;">
                <?php echo htmlspecialchars($product['product_name']); ?>
              </div>
              
              <div class="product-category" style="color: gray;">
                <?php echo htmlspecialchars($product['category_name']); ?>
              </div>
              
              <div class="product-price" style="font-weight: bold; color: #2a7;">
                $<?php echo htmlspecialchars($product['product_price']); ?>
              </div>
              
             <button class="add-to-cart"
  style="margin-top: 10px; background: #ff6b00; color: white;
         border: none; border-radius: 5px; padding: 8px 12px; cursor: pointer;">
  <a href="../web_layout/product_details.php?id=<?= $product['product_id']; ?>"
     style="color: white; text-decoration: none;">
     Add to Cart
  </a>
</button>



                    <!-- echo '<a href="../web_layout/product_details.php?id=' . $product['product_id'] . '" 
        style="display:inline-block; margin-top:10px; background:#ff6b00; 
               color:white; text-decoration:none; border-radius:5px; padding:8px 12px;">
        Add to Cart
      </a>'; -->
            </div>
    <?php
        }
    } else {
        echo "<p style='text-align:center; width:100%;'>No hot-sale products found.</p>";
    }
    ?>
  </div>

  <!-- Right Scroll Button -->
  <button class="scroll-btn scroll-right"
          style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
                 background: black; color: white; border: none; padding: 10px 15px;
                 border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 2;">⟩</button>
</div>

<!-- Scroll Script -->
<script>
  function initSlider(containerId, leftBtn, rightBtn) {
    const container = document.getElementById(containerId);
    const scrollLeftBtn = document.querySelector(leftBtn);
    const scrollRightBtn = document.querySelector(rightBtn);

    const scrollAmount = 300;

    scrollLeftBtn.addEventListener("click", () => {
      container.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    });

    scrollRightBtn.addEventListener("click", () => {
      container.scrollBy({ left: scrollAmount, behavior: "smooth" });
    });
  }

  // Initialize the slider
  initSlider("hot-sale-slider", ".scroll-left", ".scroll-right");
</script>
