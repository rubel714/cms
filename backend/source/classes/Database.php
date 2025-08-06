<?php

require_once(__DIR__.'../../../env.php');

class Database{
    
    private $db_host = DB_SERVER;
    private $db_name = DB_NAME;
    private $db_username = DB_USER;
    private $db_password = DB_PASSWORD;
    
    public function dbConnection(){
        
        try{
            $conn = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name,$this->db_username,$this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e){
            echo "Connection error ".$e->getMessage(); 
            exit;
        }
          
    }
}