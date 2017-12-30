<?php
/**
 * Created by PhpStorm.
 * User: charlespalmer
 * Date: 28/12/2017
 * Time: 17:04
 *
 * Class to handle the database connection
 */

class DBAdapter
{
    private $connection;
    private $hostName;
    private $userName;
    private $database;
    private $password;

    public function __construct($host, $user, $db, $pass=''){
        $this->hostName = $host;
        $this->userName = $user;
        $this->database = $db;
        $this->password = $pass;

        $this->connect();
    }

    /**
     * Connect to the database
     */
    private function connect(){
        $this->connection = new mysqli(
            $this->hostName,
            $this->userName,
            $this->password,
            $this->database
        );
    }

    /**
     * @param $id
     * @return bool
     *
     * Returns true or false, if the order has already been processed
     */
    public function findOrder($id){
        $order = $this->connection->query("SELECT * FROM `interflora_orders` WHERE `order_id`='$id'");

        if($order->num_rows > 0)
            return true;
        else
            return false;
    }

    /**
     * @param $name
     * @param $postcode
     * @param $orderId
     *
     * Saves an order to the database
     */
    public function saveOrder($name, $postcode, $orderId){
        $sql = "INSERT INTO `interflora_orders` (customer_name, customer_postcode, order_id)
                VALUES ('$name', '$postcode', '$orderId')";

        $this->connection->query($sql);
    }
}
