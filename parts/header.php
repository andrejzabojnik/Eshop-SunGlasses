<?php


include_once "shop/Navigation.php";
require_once "database/Database.php";
require_once "shop/Cart.php";

use main\database\Database;
use main\shop\Cart;
use main\shop\Navigation;





$menuObj = new Navigation();
$cart = new Cart();

$menu = $menuObj->getMenu("header");
$itemCount = $cart->getItemCount();
?>

<header>
    <!-- Header Start -->
    <div class="header-area">
        <div class="main-header header-sticky">
            <div class="container-fluid">
                <div class="menu-wrapper">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="index.php"><img src="assets/img/logo/logo.png" alt=""></a>
                    </div>
                    <!-- Main-menu -->
                    <div class="main-menu d-none d-lg-block">
                        <nav>
                            <ul id="navigation">
                                <?php
                                $menuObj->printMenu($menu);
                                ?>
                            </ul>
                        </nav>
                    </div>
                    <!-- Header Right -->
                    <div class="header-right">
                        <ul>
                            <li>
                                <div class="nav-search search-switch">
                                    <span class="flaticon-search"></span>
                                </div>
                            </li>
                            <?php if(isset($_SESSION["user"])) { ?>
                                <?php $userInfo = $_SESSION['full_name'];?>
                                <li> Welcome, <?php echo $userInfo ?> <a href="logout.php" class="text-danger">Logout </li>
                            <?php } else { ?>
                                <li> <a href="login.php"><span class="flaticon-user"></span></a></li>
                            <?php } ?>
                            <li><a href="shopping-cart.php"><span class="flaticon-shopping-cart"> <?php if ($itemCount > 0) {echo "(".$itemCount.")";} ?> </span></a> </li>
                        </ul>
                    </div>
                </div>
                <!-- Mobile Menu -->
                <div class="col-12">
                    <div class="mobile_menu d-block d-lg-none"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
</header>