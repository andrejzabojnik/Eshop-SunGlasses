<?php
use main\database\Database;

require_once 'database\Database.php';


$db = new Database();
if (isset($_POST["add_to_cart"])) {
    // Check if user is logged in
    if (!isset($_SESSION["user"])) {
        // Redirect user to login page
        echo "<script>window.location.replace('login.php');</script>";
        exit;

    }

    $item_id = $_GET["id"];
    $item_name = $_POST["hidden_name"];
    $item_price = $_POST["hidden_price"];
    $item_quantity = $_POST["quantity"];
    $item_image = $_POST["hidden_image"];
    $db->addCart($item_image,$item_name, $item_quantity,$item_price * $item_quantity, $item_id);
    echo "<div id='alert' class='alert alert-success'>Item $item_name added $item_quantity pieces to cart</div>";
}

?>
<div class="row">
    <?php
    $result = $db->getProducts();
    if ($result->rowCount() > 0) {
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="col-md-4">
                <form method="post" action="<?php echo basename($_SERVER['PHP_SELF']) ?>?action=add&id=<?php  echo $row["id"]; ?>">
                    <div style="border:1px solid #333; background-color:#FFFFFF; border-radius:5px; padding:16px;" align="center">
                        <img src="images/<?php echo $row["image"]; ?>" class="img-responsive" /><br />

                        <h4 class="text-info"><?php echo $row["name"]; ?></h4>
                        <h4 class="text-danger">$ <?php echo $row["price"]; ?></h4>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" />
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


    <script>
        var alert = document.getElementById("alert");

        setTimeout(function() {
            alert.style.display = "none";
        }, 5000);
    </script>
</div>
</div>