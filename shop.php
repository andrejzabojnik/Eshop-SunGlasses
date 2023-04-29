<?php

use main\database\Database;
use main\shop\Cart;

require_once 'shop\Cart.php';
require_once 'database\Database.php';

$cart = new Cart();
$db = new Database();

if (isset($_POST["add_to_cart"])) {
    // Check if user is logged in
    if (!isset($_SESSION["user"])) {
        // Redirect user to login page
        header("Location: login.php");
        exit(); // Terminate the current script
    }

    $item_id = $_GET["id"];
    $item_name = $_POST["hidden_name"];
    $item_price = $_POST["hidden_price"];
    $item_quantity = $_POST["quantity"];
    $item_image = $_POST["hidden_image"];
    $cart->addItem($item_image,$item_id, $item_name, $item_price, $item_quantity);
    echo "<div id='alert' class='alert alert-success'>Item $item_name added $item_quantity pieces to cart</div>";
}

?>
<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Watch shop | eCommers</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="assets/css/flaticon.css">
        <link rel="stylesheet" href="assets/css/slicknav.css">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/magnific-popup.css">
        <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
        <link rel="stylesheet" href="assets/css/themify-icons.css">
        <link rel="stylesheet" href="assets/css/slick.css">
        <link rel="stylesheet" href="assets/css/nice-select.css">
        <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!--? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/logo.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
    <?php include_once "parts/header.php"; ?>
    <main>
        <!-- Hero Area Start-->
        <?php include_once "parts/titlearea.php"; ?>



        <!-- Hero Area End-->
        <!-- Latest Products Start -->



        <br />
        <div class="container">
            <br />
            <br />
            <br />
            <br /><br />

            <div class="row">
                <?php
                $result = $db->getProducts();
                if ($result->rowCount() > 0) {
                    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <div class="col-md-4">
                            <form method="post" action="shop.php?action=add&id=<?php echo $row["id"]; ?>">
                                <div style="border:1px solid #333; background-color:#FFFFFF; border-radius:5px; padding:16px;" align="center">
                                    <img src="images/<?php echo $row["image"]; ?>" class="img-responsive" /><br />

                                    <h4 class="text-info"><?php echo $row["name"]; ?></h4>
                                    <h4 class="text-danger">$ <?php echo $row["price"]; ?></h4>
                                    <input type="number" name="quantity" value="1" class="form-control" />
                                    <input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />
                                    <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />
                                    <input type="hidden" name="hidden_image" value="<?php echo $row["image"]; ?>" />
                                    <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-success" value="Add to Cart" />
                                </div>
                            </form>
                            <br>
                        </div>

                        <?php
                    }
                }
                ?>
            </div>
        </div>



        <!-- Latest Products End -->
        <!--? Shop Method Start-->
        <?php include_once "parts/commercy_panel.php"; ?>
        <!-- Shop Method End-->
    </main>
    <?php include_once "parts/footer.php"; ?>
    <!--? Search model Begin -->
    <div class="search-model-box">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-btn">+</div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Searching key.....">
            </form>
        </div>
    </div>
    <!-- Search model end -->

<!-- JS here -->
    <!-- All JS Custom Plugins Link Here here -->
    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="./assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>

    <!-- One Page, Animated-HeadLin -->
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/animated.headline.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>

    <!-- Scroll up, nice-select, sticky -->
    <script src="./assets/js/jquery.scrollUp.min.js"></script>
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>

    <!-- contact js -->
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>

    <script>
        // Získajte odkaz na element správy
        var alert = document.getElementById("alert");

        // Skryte správu po 5 sekundách
        setTimeout(function() {
            alert.style.display = "none";
        }, 2000);
    </script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>

</body>
</html>