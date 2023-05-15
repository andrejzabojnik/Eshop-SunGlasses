<?php
session_start();

use main\database\Database;

require_once 'database/Database.php';


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}



$db = new Database();


if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        $item_id = $_GET["id"];

        echo "<div id='alert' class='alert alert-danger'>Item remove.</div>";
        $db->removeCart($item_id);


    }
}


if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete_order") {
        $order_id = $_GET["id"];
        $db->deleteProductOrder($order_id);
        $db->removeOrder($order_id);
        echo "<div id='alert' class='alert alert-danger'>Order canceled.</div>";


    }
}

if (isset($_POST["action"]) && $_POST["action"] == "clear_cart") {
    if (!empty($db->getCartItems())) {
        $db->clearCart();
        echo "<div id='alert' class='alert alert-danger'>All product remove.</div>";
    }
}

if (isset($_POST["action"]) && $_POST["action"] == "submit_order") {
    $email = $db->getLoggedInUserEmail();
    $userInfo = $db->userInfo($email);
    $userId = $userInfo["id"];
    $name = $userInfo["full_name"];
    $address = $userInfo["address"];

    $totalProduct = $db->getTotalQuantity();
    $totalPrice = $db->getTotalPrice();


    if ($totalProduct == 0 || $totalPrice == 0) {
        echo "<div id='alert' class='alert alert-danger'>Cannot submit order with total price or total product equal to 0.</div>";
    } else {
        $orderId = $db->addOrder($name, $email,$address, $totalProduct, $totalPrice, $userId);
        foreach ($db->getCartItems() as $item) {
            $productId = $item["ProductID"];
            $quantity = $item["quantity"];
            $amount = $item["price"];
            $db->addProductOrder($productId,$orderId, $quantity, $amount * $quantity);
        }
        $db->clearCart();
        echo "<div id='alert' class='alert alert-success'>Order submitted.</div>";
    }
}

?>


<!doctype html>
<html lang="zxx">
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
<?php include_once "parts/header.php"; ?>




<div class="container">
    <div style="clear:both"></div>
    <br />
    <h1>Order Details</h1>
    <br>
    <div class="container-fluid" style="height: 400px;">
    <?php

    if (!empty($db->getCartItems())) {
    $total = 0;
    ?>
    <!--================Cart Area =================-->

        <form method="post" class="d-flex justify-content-end">
            <input type="hidden" name="action" value="clear_cart">
            <input type="submit" value="Clear Cart" class="btn btn-danger">
        </form>
        <table class="table table-bordered">
            <tr>
                <th width="25%">Item image</th>
                <th width="20%">Item Name</th>
                <th width="20%">Quantity</th>
                <th width="20%">Price</th>
                <th width="20%">Action</th>
            </tr>
            <?php
            foreach ($db->getCartItems() as $keys => $values) {
                $total += ($values["quantity"] * $values["price"]);
                ?>
                <tr>
                    <td><img src="images/<?php echo $values["image"]; ?>" alt="<?php echo $values["name"]; ?>" width="100"></td>
                    <td><?php echo $values["name"]; ?></td>
                    <td><?php echo $values["quantity"]; ?></td>
                    <td>$ <?php echo $values["price"]; ?></td>
                    <td><a href="shopping-cart.php?action=delete&id=<?php echo $values["ProductID"]; ?>"><span class="text-danger">Remove</span></a></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td colspan="4" align="right">Total</td>
                <td align="right">$ <?php echo $total ?></td>
                <td></td>
            </tr>
        </table>
        <?php
        } else {
            echo "<h3>Your cart is empty!</h3>";
        }
        ?>
        <?php $email = $db->getLoggedInUserEmail(); ?>
        <?php $userID = $db->getUserID($email)["id"]; ?>
        <form method="post" class="mb-4">
            <?php if (!empty($db->getCartItems())): ?>
                <input type="hidden" name="action" value="submit_order">
                <input type="submit" name="submit_order" value="Send Order" class="btn btn-block" style="background-color: green;">
            <?php endif; ?>
        </form>
        <br>
    </div>
    <?php
    for ($i = 0; $i < 10; $i++) {
        echo "<br>";
    }
    ?>
    <br>
    <h1 >Your orders</h1>
        <?php
        $orderID = $db->getMaxOrderID();
        $orders = $db->getOrders($userID);
        $products = $db->getProductsOrders();
        ?>


        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
            <tr>
                <th>Order number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Total Products</th>
                <th>Products</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['total_products']; ?></td>
                    <td>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $filtered_orders = array();
                            $search_id = $row['id'];
                            foreach ($products as $order) {
                                if ($order["IDorder"] == $search_id) {
                                    $filtered_orders[] = $order;
                                }
                            }
                            foreach ($filtered_orders as $detail):
                                $productname = $db->getProductNameById($detail['IDproduct']);
                                ?>
                                <tr>
                                    <td><?php echo $productname ?></td>
                                    <td><?php echo $detail['quantity']; ?></td>
                                    <td>$<?php echo $detail['amount']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                    <td>$<?php echo $row['total_price']; ?></td>
                    <td>
                        <a href="edit-order.php?action=edit_order&id=<?php echo $row['id'] ?>" class="text-danger d-block">Edit</a>
                        <br>
                        <a href="shopping-cart.php?action=delete_order&id=<?php echo $row['id'] ?>" class="text-danger d-block">Cancel</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php include_once "parts/footer.php"; ?>
    </div>

</div>



</body>
</html>


</html>

<!--================End Cart Area =================-->
</main>>
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
<script>
    setTimeout(function() {
        var alerts = document.querySelectorAll("#alert");
        alerts.forEach(function(alert) {
            alert.style.display = "none";
        });
    }, 5000);
</script>

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

<!-- Scrollup, nice-select, sticky -->
<script src="./assets/js/jquery.scrollUp.min.js"></script>
<script src="./assets/js/jquery.nice-select.min.js"></script>
<script src="./assets/js/jquery.sticky.js"></script>
<script src="./assets/js/jquery.magnific-popup.js"></script>

<!-- contact js -->
<script src="./assets/js/contact.js"></script>
<script src="./assets/js/jquery.form.js"></script>
<script src="./assets/js/jquery.validate.min.js"></script>
<script src="./assets/js/mail-script.js"></script>
<script src="./assets/js/jquery.ajaxchimp.min.js"></script>


<!-- Jquery Plugins, main Jquery -->
<script src="./assets/js/plugins.js"></script>
<script src="./assets/js/main.js"></script>

</body>
</html>