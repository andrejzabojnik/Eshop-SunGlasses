<?php
namespace main;

use \PDO;

class nepouzite
{

    private string $hostname = "localhost";
    private int $port = 3306;
    private string $username = "root";
    private string $password = "";
    private string $dbName = "eshop";
    private $conn;



    public function __construct()
    {


        try {
            $this->conn = new PDO("mysql:charset=utf8;host=".$this->hostname.";dbname=".$this->dbName.";port=".$this->port, $this->username, $this->password);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            echo $this->hostname."  ".$this->port."  ".$this->dbName;

            die();
        }
    }

    function check_duplicate_email($email) {
        $email = $this->conn->quote($email);
        $sql = "SELECT COUNT(*) FROM users WHERE email = $email";
        $result = $this->conn->query($sql);
        $count = $result->fetchColumn();
        return $count > 0;
    }

    //funkcia na pridavanie zaregistrovaneho do databazy
    function register_user($name, $email, $password) {
        $name = $this->conn->quote($name);
        $email = $this->conn->quote($email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = $this->conn->quote($hashed_password);
        $sql = "INSERT INTO users (name, email, password) VALUES ($name, $email, $hashed_password)";
        if ($this->conn->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

    function loginUser($email, $password) {
        $email = $this->conn->quote($email);
        $password = $this->conn->quote($password);

        // Vytvorenie hesla hash
        $hashed_password = md5($password);

        // Overenie používateľa v databáze
        $sql = "SELECT * FROM users WHERE email=$email AND password=$hashed_password";
        $result = $this->conn->query($sql);

        if ($result->rowCount() == 1) {
            // Prihlásenie používateľa
            $user = $result->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            // Chyba prihlásenia
            return false;
        }
    }


    //autorizacia
    function authenticate_user($email, $password) {
        $email = $this->conn->quote($email);
        $sql = "SELECT * FROM users WHERE email=$email";
        $result = $this->conn->query($sql);
        $user = $result->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }




    function get_users() {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
        $users = $result->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }





}
