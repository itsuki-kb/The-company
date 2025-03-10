<?php

    class Database
    {
        private $server_name    = 'localhost';
        private $username       = 'root';
        private $password       = 'root';
        private $db_name        = 'the_company';
        protected $conn;

        public function __construct()
        {
            //mysqli Represents a connection between PHP and a MySQL database.
            $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name);
            //$this->conn is now the object that holds the connection to the db.
            //$conn - object


            if($this->conn->connect_error){
                die("Unable to connect to the database: " . $this->conn->connect_error);
            }
        }
    }


?>