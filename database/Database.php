<?php
namespace main\database;
use PDO;
class Database
{

    private string $hostname = "localhost";
    private int $port = 3306;
    private string $username = "root";
    private string $password = "";
    private string $dbName = "eshop";

    private $errors = array();
    private $conn;

    function __construct()
    {

        try {
            $this->conn = new PDO("mysql:charset=utf8;host=" . $this->hostname . ";dbname=" . $this->dbName . ";port=" . $this->port, $this->username, $this->password);
        } catch (\PDOException $exception) {
            echo $exception->getMessage();
            echo $this->hostname . "  " . $this->port . "  " . $this->dbName;
            die();
        }
    }


    public function insertContact($name, $email, $subject, $message)
    {
        $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name, $email, $subject, $message]);
    }

    public function validateUser($fullName, $email, $address, $password, $passwordRepeat)
    {
        if (empty($fullName) or empty($email) or empty($password) or empty($address) or empty($passwordRepeat)) {
            array_push($this->errors, "All fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errors, "Email is not valid");
        }
        if (strlen($password) < 8) {
            array_push($this->errors, "Password must be at least 8 characters long");
        }
        if ($password !== $passwordRepeat) {
            array_push($this->errors, "Password does not match");
        }
    }

    public function checkEmailExists($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            array_push($this->errors, "Email already exists!");
        }
    }

    public function createUser($fullName, $email, $address, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, address, password) VALUES ( ?, ?, ?, ? )";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$fullName, $email, $address, $passwordHash]);
        if ($stmt->rowCount() === 1) {
            echo "<div class='alert alert-success'>You are registered successfully. Please now log in.</div>";
        } else {
            die("Something went wrong");
        }
    }


    public function userInfo($email)
    {
        $sql = "SELECT id,full_name,email,address FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getUserID($email)
    {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }


    public function loginUser($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($password, $user["password"])) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION["user"] = "yes";
                $_SESSION["full_name"] = $user["full_name"]; // Store the full name in the session
                header("Location: index.php");
                die();
            } else {
                echo "<div class='alert alert-danger'>Password does not match</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Email does not exist</div>";
        }
    }

    public function getLoggedInUserName()
    {
        if (isset($_SESSION["user"]) && $_SESSION["user"] === "yes" && isset($_SESSION["full_name"])) {
            return $_SESSION["full_name"];
        } else {
            return false;
        }
    }

    public function getLoggedInUserEmail()
    {
        if (isset($_SESSION["user"]) && $_SESSION["user"] === "yes" && isset($_SESSION["email"])) {
            return $_SESSION["email"];
        } else {
            return false;
        }
    }

    public function verifyUser($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    function getProducts()
    {
        $sql = "SELECT * FROM products ORDER BY id ASC";
        $result = $this->conn->query($sql);
        return $result;

    }

    public function getAllProducts()
    {
        $query = "SELECT * FROM product";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $products = array();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = $row;
            }
        }

        return $products;
    }

    public function addProduct($name, $price, $description, $image)
    {
        $query = "INSERT INTO product (name, price, description, image) VALUES (:name, :price, :description, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }



    public function addCart($image, $name, $quantity, $price, $ProductID)
    {
        $query = "SELECT * FROM cart WHERE ProductID = :ProductID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $ProductID);
        $stmt->execute();

        if ($stmt->rowCount() > 0) { // riadok s danym ProductID uz existuje
            $query = "UPDATE cart SET quantity = quantity + :quantity, price = price + :price WHERE ProductID = :ProductID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':ProductID', $ProductID);
            $stmt->execute();
        } else { // riadok s danym ProductID neexistuje
            $query = "INSERT INTO cart (image, name, quantity, price, ProductID) VALUES (:image, :name, :quantity, :price, :ProductID)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':ProductID', $ProductID);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getCartItems() {
        $query = "SELECT image, name, quantity, price,ProductID FROM cart";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeCart($id)
    {
        $query = "DELETE FROM cart WHERE ProductID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getTotalQuantity() {
        $query = "SELECT SUM(quantity) as totalQuantity FROM cart";
        $result = $this->conn->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return $data['totalQuantity'];

    }

    public function getTotalPrice() {
        $query = "SELECT SUM(price * quantity) as totalPrice FROM cart";
        $result = $this->conn->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return $data['totalPrice'];


    }





    public function clearCart()
    {
        $query = "DELETE FROM cart";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function updateProduct($id, $name, $price, $description, $image)
    {
        $query = "UPDATE product SET name = :name, price = :price, description = :description, image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM product WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteProductOrder($id)
    {
        $query = "DELETE FROM product_orders WHERE IDorder = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }




    public function addOrder($name, $email, $address, $totalProduct, $totalPrice, $userId)
    {
        $sql = "INSERT INTO `order` (name, email, address, total_products, total_price, UserID) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name, $email, $address, $totalProduct, $totalPrice, $userId]);
        return $this->conn->lastInsertId();
    }


    public function editOrder($id, $name, $email, $address)
    {
        $query = "UPDATE `order` SET name = :name, email = :email, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function removeOrder($id)
    {
        $query = "DELETE FROM `order` WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$id]);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function addProductOrder($productId, $orderId, $quantity, $amount)
    {
        $sql = "INSERT INTO product_orders (IDproduct, IDorder, quantity, amount) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId, $orderId, $quantity, $amount]);
    }



    public function getTableOrders($IDuser)
    {
        $sql = "SELECT * FROM `order` WHERE UserID = :IDuser";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':IDuser', $IDuser, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getTableOrderss()
    {
        $sql = "SELECT IDorder,IDproduct, quantity, amount FROM `product_orders`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getMaxOrderID() {
        $sql = "SELECT MAX(ID) as max_id FROM `order`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['max_id'];
    }


    public function getProductNameById($id)
    {
        $sql = "SELECT name FROM `products` WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }



}

?>