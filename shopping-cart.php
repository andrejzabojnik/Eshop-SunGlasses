<?php



require_once 'Cart.php';
require_once 'Database.php';

$cart = new Cart();


$db = new Database();



if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        $item_id = $_GET["id"];
        $cart->removeItem($item_id);


    }
}

if (isset($_POST["action"]) && $_POST["action"] == "clear_cart") {
    if (!empty($_SESSION["shopping_cart"])) {
        $cart->clearCart();
        echo "<div id='alert' class='alert alert-danger'>All product remove.</div>";
    }
}

if (isset($_POST["action"]) && $_POST["action"] == "submit_order") {
    $email = $db->getLoggedInUserEmail();
    $userInfo = $db->userInfo($email);
    $userId = $userInfo["id"];
    $name = $userInfo["full_name"];
    $address = $userInfo["address"];

    $totalProduct = $cart->getTotalQuantity();
    $totalPrice = $cart->getTotalPrice();

    if ($totalProduct == 0 || $totalPrice == 0) {
        echo "<div id='alert' class='alert alert-danger'>Cannot submit order with total price or total product equal to 0.</div>";
    } else {
        $orderId = $db->addOrder($name, $email,$address, $totalProduct, $totalPrice, $userId);
        foreach ($_SESSION["shopping_cart"] as $item) {
            $productId = $item["item_id"];
            $quantity = $item["item_quantity"];
            $amount = $item["item_price"];
            $db->addProductOrder($productId,$orderId, $quantity, $amount);
        }
        $cart->clearCart();
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







      <!--================Cart Area =================-->
      <div class="container">





          <div style="clear:both"></div>
          <br />
          <h3>Order Details</h3>
          <div class="table-responsive">
              <form method="post" class="d-flex justify-content-end">
                  <?php if (!empty($_SESSION["shopping_cart"])): ?>
                      <input type="hidden" name="action" value="clear_cart">
                      <input type="submit" value="Clear Cart" class="btn btn-danger">

                  <?php endif; ?>
              </form>
              <table class="table table-bordered">
                  <tr>
                      <th width="40%">Item Name</th>
                      <th width="10%">Quantity</th>
                      <th width="20%">Price</th>
                      <th width="15%">Total</th>
                      <th width="5%">Action</th>
                  </tr>
                  <?php
                  if(!empty($_SESSION["shopping_cart"]))
                  {
                      $total = 0;
                      foreach($_SESSION["shopping_cart"] as $keys => $values)
                      {
                          ?>

                          <tr>
                              <td><?php echo $values["item_name"]; ?></td>
                              <td><?php echo $values["item_quantity"]; ?></td>
                              <td>$ <?php echo $values["item_price"]; ?></td>
                              <td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2);?></td>
                              <td><a href="shopping-cart.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Remove</span></a></td>
                          </tr>
                          <?php
                          $total = $total + ($values["item_quantity"] * $values["item_price"]);
                      }
                      ?>
                      <tr>
                          <td colspan="3" align="right">Total</td>
                          <td align="right">$ <?php echo number_format($total, 2); ?></td>
                          <td></td>
                      </tr>
                      <?php
                  }
                  else{
                      echo "<div class='alert alert-danger'>Cart is empty.</div>";

                  }
                  ?>
                  <?php $email = $db->getLoggedInUserEmail()  ?>


                  <?php $userID = $db->getUserID($email)["id"]  ?>



              </table>
          </div>
          <form method="post">
              <?php if (!empty($_SESSION["shopping_cart"])): ?>
                  <input type="hidden" name="action" value="submit_order">
                  <input type="submit" name="submit_order" value="Send Order" class="btn btn-block" style="background-color: green;">
              <?php endif; ?>
          </form>
          <br>
          <h1>Your orders</h1>

          <?php $array = $db->getTableOrders($userID ); ?>
          <table class="table table-bordered table-hover">
              <thead class="thead-dark">
              <tr>
                  <th>Order number</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Address</th>
                  <th>Total Products</th>
                  <th>Total Price</th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($array as $row) { ?>

                  <tr>
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo $row['name']; ?></td>
                      <td><?php echo $row['email']; ?></td>
                      <td><?php echo $row['address']; ?></td>
                      <td><?php echo $row['total_products']; ?></td>
                      <td><?php echo $row['total_price'] . " euro"; ?></td>

                  </tr>
              <?php } ?>
              </tbody>
          </table>

      </div>



      </div>
      <br />


</body>
</html>

<?php
//If you have use Older PHP Version, Please Uncomment this function for removing error

/*function array_column($array, $column_name)
{
	$output = array();
	foreach($array as $keys => $values)
	{
		$output[] = $values[$column_name];
	}
	return $output;
}*/
?>
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
    // Získajte odkaz na element správy
    var alert = document.getElementById("alert");

    // Skryte správu po 5 sekundách
    setTimeout(function() {
        alert.style.display = "none";
    }, 2000);
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