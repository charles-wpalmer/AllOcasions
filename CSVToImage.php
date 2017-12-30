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
    private $db;
    private $imagePath = './images/';

    /**
     * Runs the sequence of events for the script
     */
    function run(){
        $this->db = new DBAdapter('localhost', 'root', 'all_occasions', 'user');

        $this->readCsv();

    }

    /**
     * Reads the latest CSV file
     */
    private function readCsv(){

        if ($file = fopen("today.csv", "r")) {
            while (!feof($file)) {
                $line = fgets($file);
                $line = explode(",", $line);

                //TODO:
                //  -   Pull this data from the file
                
                $name = 'Test123';
                $postcode = 'Test';
                $orderId = '#12346789123';

                $orderId = str_replace("#","",$orderId);
                
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
        $img = $this->imagePath . $orderId . '.png';
        
        $url = "http://encode.i-nigma.com/QRCode/img.php?d=SMSTO%3A07786207206%3ADel+$orderId&c=$name&s=3";
        
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
