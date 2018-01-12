<?php
/**
 * Created by PhpStorm.
 * User: charlespalmer
 * Date: 28/12/2017
 * Time: 15:48
 *
 * Class to handle conversion of orders from CSV
 * To a QR code image
 */

require 'DBAdapter.php';

class CSVToImage
{
    /**
     * @var $db object DBAdapter
     */
    private $db;

    /**
     * @var $imagePath String
     */
    private $imagePath = './images/';
    
    /**
     * @var $pattern String
     */
    private $pattern = "/((GIR 0AA)|((([A-PR-UWYZ][0-9][0-9]?)|(([A-PR-UWYZ][A-HK-Y][0-9][0-9]?)|(([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY])))) [0-9][ABD-HJLNP-UW-Z]{2}))/i";
    
    /**
     * Runs the sequence of events for the script
     */
    function run(){
        $this->db = new DBAdapter('localhost', 'root', 'all_occasions', 'user');

        $this->readCsv();
    }

    /**
     * Reads the latest CSV file, and handles the orders. 
     * Then generates the images based on the file.
     */
    private function readCsv(){

        if ($file = fopen("actual.csv", "r")) {
            while (!feof($file)) {
                $line = fgets($file);
                
                // Postcode
                preg_match($this->pattern, $line, $matches);
                $postcode = $matches[0];

                // OrderID
                $line = explode(",", $line);
                $orderId = $line[1];
                
                // Name
                $name = $line[2];
                
                if(!$this->orderExists($orderId)) {
                    $this->saveOrder($name, $postcode, $orderId);
                    $this->generateImage($name, $orderId);
                }
            }
            fclose($file);
        }

    }

    /**
     * @param $name
     * @param $postcode
     * @param $orderId
     *
     * Saves an order to the database
     */
    private function saveOrder($name, $postcode, $orderId){
        $this->db->saveOrder($name, $postcode, $orderId);
    }

    /**
     * @param $name
     * @param $orderId
     *
     * Saves the image from i-nigma
     */
    private function generateImage($name, $orderId){

        $remove = array('E', ' ', '#', '.');
        $orderId = str_replace($remove, '', $orderId);
        $name = str_replace($remove, '', $name);
        
        $img = "./images/$orderId.png";
        
        $url = 'http://encode.i-nigma.com/QRCode/img.php?d=SMSTO%3A07786207206%3ADel+'.$orderId.'&c='.$name.'&s=3';
        
        file_put_contents($img, file_get_contents($url));
    }

    /**
     * @param $orderId
     * @return bool
     *
     * Returns true or false if the order exists or not
     */
    private function orderExists($orderId){
        return $this->db->findOrder($orderId);
    }

}

$converter = new CSVToImage();

$converter->run();
