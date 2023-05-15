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

    private PDO $conn;

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


    public function insertContact(string $name, string $email, string $subject, string $message): void
    {
        try {
            $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $email, $subject, $message]);
        } catch (Exception $e) {
            throw new Exception("Error in insertContact function: " . $e->getMessage());
        }
    }



    public function checkEmailExists(string $email): bool
    {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Error in checkEmailExists function: " . $e->getMessage());
        }
    }

    public function createUser(string $fullName, string $email, string $address, string $password): bool
    {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, address, password) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$fullName, $email, $address, $passwordHash]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in createUser function: " . $e->getMessage());
        }
    }



    public function userInfo(string $email): ?array
    {
        try {
            $sql = "SELECT id, full_name, email, address FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in userInfo function: " . $e->getMessage());
        }
    }

    public function getUserID(string $email): ?array
    {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in checkEmailExists function: " . $e->getMessage());
        }
    }

    public function loginUser(string $email, string $password, string &$errorMessage): bool
    {
        try {
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
                    return true;
                } else {
                    $errorMessage = "Password does not match";
                    return false;
                }
            } else {
                $errorMessage = "Email does not exist";
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Error in loginUser function: " . $e->getMessage());

        }
    }



    public function getLoggedInUserEmail(): ?string
    {
        try {
            if (isset($_SESSION["user"]) && $_SESSION["user"] === "yes" && isset($_SESSION["email"])) {
                return $_SESSION["email"];
            } else {
                return null;
            }
        } catch (Exception $e) {
            throw new Exception("Error in getLoggedInUserEmail function: " . $e->getMessage());

        }
    }



    public function getProducts()
    {
        try {
            $sql = "SELECT * FROM products ORDER BY id ASC";
            $result = $this->conn->query($sql);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in getProducts function: " . $e->getMessage());


        }
    }

    public function getItemCount(): int
    {
        try {
            $query = "SELECT sum(quantity) as count FROM cart";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = isset($row['count']) ? $row['count'] : 0;
            return $count;
        } catch (Exception $e) {
            throw new Exception("Error in getItemCount function: " . $e->getMessage());
        }
    }




    public function addCart(string $image, string $name, int $quantity, float $price, int $ProductID): void
    {
        try {
            $sql = "SELECT * FROM cart WHERE ProductID = :ProductID";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ProductID', $ProductID);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $sql = "UPDATE cart SET quantity = quantity + :quantity, price = price + :price WHERE ProductID = :ProductID";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':ProductID', $ProductID);
                $stmt->execute();
            } else {
                $query = "INSERT INTO cart (image, name, quantity, price, ProductID) VALUES (:image, :name, :quantity, :price, :ProductID)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':image', $image);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':ProductID', $ProductID);
                $stmt->execute();

            }
        } catch (Exception $e) {
            throw new Exception("Error in addCart function: " . $e->getMessage());

        }
    }

    public function getCartItems(): array
    {
        try {
            $sql = "SELECT image, name, quantity, price, ProductID FROM cart";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error in getCartItems function: " . $e->getMessage());

        }
    }

    public function removeCart(int $id): bool
    {
        try {
            $sql = "DELETE FROM cart WHERE ProductID = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error in removeCart function: " . $e->getMessage());

        }
    }

    public function getTotalQuantity()
    {
        try {
            $sql = "SELECT SUM(quantity) as totalQuantity FROM cart";
            $result = $this->conn->query($sql);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            return $data['totalQuantity'];
        } catch (Exception $e) {
            throw new Exception("Error in  getTotalQuantity function: " . $e->getMessage());
        }
    }

    public function getTotalPrice()
    {
        try {
            $sql = "SELECT SUM(price * quantity) as totalPrice FROM cart";
            $result = $this->conn->query($sql);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            return $data['totalPrice'];
        } catch (Exception $e) {
            throw new Exception("getTotalPrice " . $e->getMessage());
        }
    }

    public function clearCart(): bool
    {
        try {
            $sql = "DELETE FROM cart";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error in clearCart function: " . $e->getMessage());

        }
    }





    public function deleteProductOrder(int $id): bool
    {
        try {
            $sql = "DELETE FROM product_orders WHERE IDorder = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error in deleteProductOrder function: " . $e->getMessage());

        }
    }

    public function addOrder(string $name, string $email, string $address, int $totalProduct, float $totalPrice, int $userId): int
    {
        try {
            $sql = "INSERT INTO `order` (name, email, address, total_products, total_price, UserID) VALUES (?, ?, ?, ?, ?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $email, $address, $totalProduct, $totalPrice, $userId]);
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error in addOrder function: " . $e->getMessage());

        }
    }

    public function editOrder(int $id, string $name, string $email, string $address): bool
    {
        try {
            $sql = "UPDATE `order` SET name = :name, email = :email, address = :address WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':address', $address);

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error in editOrder function: " . $e->getMessage());

        }
    }

    public function removeOrder(int $id): bool
    {
        try {
            $sql = "DELETE FROM `order` WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in removeOrder function: " . $e->getMessage());

        }
    }

    public function addProductOrder(string $productId, string $orderId, int $quantity, int $amount): void
    {
        try {
            $sql = "INSERT INTO product_orders (IDproduct, IDorder, quantity, amount) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$productId, $orderId, $quantity, $amount]);
        } catch (Exception $e) {
            throw new Exception("Error in addProductOrder function: " . $e->getMessage());
        }
    }

    public function getOrders(int $IDuser): array
    {
        try {
            $sql = "SELECT * FROM `order` WHERE UserID = :IDuser";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':IDuser', $IDuser, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in getOrders function: " . $e->getMessage());
        }
    }

    public function getProductsOrders(): array
    {
        try {
            $sql = "SELECT IDorder, IDproduct, quantity, amount FROM `product_orders`";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in getProductsOrders function: " . $e->getMessage());

        }
    }

    public function getMaxOrderID(): int
    {
        try {
            $sql = "SELECT MAX(ID) as max_id FROM `order`";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['max_id'];
        } catch (Exception $e) {
            throw new Exception("Error in getMaxOrderID function: " . $e->getMessage());

        }
    }


    public function getProductNameById(int $id): string
    {
        try {
            $sql = "SELECT name FROM `products` WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error in getProductNameById function: " . $e->getMessage());

        }
    }



    public function getMenuItemName(string $path): string
    {
        try {
            $sql = "SELECT user_name FROM menu WHERE path = '" . $path . "'";
            $query = $this->conn->query($sql);
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if ($data && isset($data['user_name'])) {
                return $data['user_name'];
            } else {
                return "";
            }
        } catch (\Exception $exception) {
            throw new Exception("Error in getMenuItemName function: " . $e->getMessage());

        }
    }


    public function getMenu(string $type): array
    {
        $menu = [];
        try {
            $sql = "SELECT * FROM menu";
            $query = $this->conn->query($sql);
            $menuItems = $query->fetchAll(PDO::FETCH_ASSOC);


            foreach ($menuItems as $menuItem) {
                $menu[$menuItem['sys_name']] = [
                    'path' => $menuItem['path'],
                    'name' => $menuItem['user_name'],
                    'id' => $menuItem['id']
                ];
            }

        } catch (\Exception $exception) {
            throw new Exception("Error in checkEmailExists function: " . $e->getMessage());


        }

        return $menu;
    }





}

?>