<!DOCTYPE html>
<html lang="en">

<!-- <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>jQuery Carousel with Fade & Pagination</title>
  <style>
    .carousel {
      width: 1300px;
      height: 600px;
      overflow: hidden;
      position: relative;
      margin: auto;
      /* border-radius: 20px; */
      border: 3px solid black;
     top: 100px;
    }

    .carousel-inner img {
      width: 100%;
      height: 100%;
      display: none;
      position: absolute;
      top: 0;
      left: 0;
    }

    .carousel-inner img:first-child {
  display: block;
}

    .controls {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
    }

    .btn {
      background: black;
      color: white;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
    }

    .pagination {
      position: absolute;
      bottom: 10px;
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: gray;
      cursor: pointer;
      border: 2px solid black;
    }

    .dot.active {
      background: white;
    }
  </style>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

 
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head> -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>jQuery Carousel with Fade & Pagination</title>

  <style>
    .carousel {
      width: 1500px;
      height: 600px;
      overflow: hidden;
      position: relative;
      margin: auto;
      border: 3px solid black;
      top: 100px;
    }

    .carousel-inner img {
      width: 100%;
      height: 100%;
      display: none;
      object-fit: cover;
    }

    .carousel-inner img:first-child {
      display: block;
    }

    .controls {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
    }

    .btn {
      background: black;
      color: white;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
    }

    .pagination {
      position: absolute;
      bottom: 10px;
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: gray;
      cursor: pointer;
      border: 2px solid black;
    }

    .dot.active {
      background: white;
    }
  </style>

  <!-- ✅ Correct Script Links -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

  <div class="carousel">
    <div class="carousel-inner">
      <img src="../uploads/categories_image/beautyproduct.jpg" alt="" >
      <img src="../uploads/categories_image/book.jpg" alt="" >
      <img src="../uploads/categories_image/clothing.jpg" alt="" >
      <img src="../uploads/categories_image/electronic.jpg" alt="" >
      <img src="../uploads/categories_image/furniture.jpg" alt="" >
      <img src="../uploads/categories_image/groceries.webp" alt="" >
      <img src="../uploads/categories_image/sportsitems.webp" alt="" >
      <img src="../uploads/categories_image/toys.jpg" alt="" class="active" >
 

    </div>

    <div class="controls">
      <button class="btn prev">⟨</button>
      <button class="btn next">⟩</button>
    </div>

    <div class="pagination"></div>
  </div>
   <!-- <img src="../uploads/categories_image/beautyproduct.jpg" alt="" class="active"> -->
      <!-- <img src="../uploads/categories_image/book.jpg" alt="" > -->
      <!-- <img src="../uploads/categories_image/clothing.jpg" alt="" > -->
      <!-- <img src="../uploads/categories_image/electronic.jpg" alt="" > -->
      <!-- <img src="../uploads/categories_image/furniture.jpg" alt="" > -->
      <!-- <img src="../uploads/categories_image/groceries.webp" alt="" > -->
      <!-- <img src="../uploads/categories_image/sports items.webp" alt="" > -->
      <!-- <img src="../uploads/categories_image/toys.jpg" alt="" > -->
  

  <script  >
    $(document).ready(function () {
    let index = 0;
    const slides = $(".carousel-inner img");
    const total = slides.length;


    function showSlide(i) {
        slides.fadeOut();
        slides.eq(i).fadeIn();


        $(".dot").removeClass("active");
        $(".dot").eq(i).addClass("active");

    }
    $(".next").click(function () {
        index = (index + 1) % total;
        showSlide(index);
    });

    $(".prev").click(function () {
        index = (index - 1 + total) % total;
        showSlide(index);
    });

    for (let i = 0; i < total; i++) {
        $(".pagination").append(`<div class="dot ${i == 0 ? 'active' : ''}" ></div>`);
    }

    $(".dot").click(function () {
        index = $(this).index() ;
        showSlide(index);
    });















});

  </script>

</body>

</html>