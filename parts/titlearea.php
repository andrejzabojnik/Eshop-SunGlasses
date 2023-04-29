<?php
include_once "shop/Navigation.php";

use main\shop\Navigation;

$menuObj = new Navigation();

$path = basename($_SERVER['PHP_SELF']);
$name = $menuObj->getMenuItemName($path);
?>

<div class="slider-area">
    <div class="single-slider slider-height2 d-flex align-items-center h-auto">
        <div class="container container-sm">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center py-2 text-sm-center">
                        <h2><?= $name ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>