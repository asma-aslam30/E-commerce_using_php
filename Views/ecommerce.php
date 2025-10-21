<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
   <?php 
      include_once '../config/connection.php';
      include_once '../config/session_check.php';
      include_once '../layout/header_links.php';
    ?>

</head>
<body>
    <?php 
      include_once '../web_layout/web_header.php';
      include_once '../web_layout/web_main_carousel.php';
      include_once '../web_layout/hotSale.php';
      include_once '../web_layout/productbycategory.php';
    ?>





       
 <?php 
      include_once '../layout/footer_links.php';
      include_once '../web_layout/web_footer.php';
    ?>
</body>
</html>