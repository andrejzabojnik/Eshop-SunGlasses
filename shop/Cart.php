<?php

class Cart {

    private $cartItems = array();

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['shopping_cart'])) {
            $_SESSION['shopping_cart'] = array();
        }
        $this->cartItems = &$_SESSION['shopping_cart'];
    }

    public function addItem($item_id, $item_name, $item_price, $item_quantity) {
        $item_array_id = array_column($this->cartItems, 'item_id');
        if (!in_array($item_id, $item_array_id)) {
            $count = count($this->cartItems);
            $item_array = array(
                'item_id'           =>  $item_id,
                'item_name'         =>  $item_name,
                'item_price'        =>  $item_price,
                'item_quantity'     =>  $item_quantity
            );
            $this->cartItems[$count] = $item_array;
        }
        else {
            foreach($this->cartItems as &$item){
                if($item["item_id"] == $item_id){
                    $item["item_quantity"] += $item_quantity;
                }
            }
        }
    }

    public function removeItem($item_id) {
        foreach($this->cartItems as $keys => $values) {
            if($values["item_id"] == $item_id) {
                unset($this->cartItems[$keys]);
                return true;
            }
        }
        return false;
    }

    public function clearCart() {
        unset($_SESSION["shopping_cart"]);
        return true;
    }

    public function getItems() {
        return $this->cartItems;
    }

    public function getItemCount() {
        return count($this->cartItems);
    }

    public function getTotalPrice() {
        $total_price = 0;
        foreach($this->cartItems as $item) {
            $total_price += ($item["item_quantity"] * $item["item_price"]);
        }
        return $total_price;
    }

    public function getTotalQuantity() {
        $total_quantity = 0;
        foreach($this->cartItems as $item) {
            $total_quantity += $item["item_quantity"];
        }
        return $total_quantity;
    }

}

?>