<?php
include_once(__DIR__ . '/DbConnection.php');

class ConfigurationManager{

    private $conn;

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
        if(!$this->conn || $this->conn->connect_errno) {
            echo '<h1 style="color:red;">Cannot connect to the database</h1>';
            $this->conn = null;
            exit();
        }
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function setDatabaseConfigurations(): void
    {
        /*$retArr = array();
        $sql = 'SELECT configurationname, configurationvalue FROM configurations ';
        $rs = $this->conn->query($sql);
        if($rs->num_rows){
            while($r = $rs->fetch_object()){
                $GLOBALS[$r->configurationname] = $r->configurationvalue;
            }
        }
        else{
            $this->configureFromSymbini();
        }
        $rs->free();*/
    }

    public function configureFromSymbini(): void
    {
        if(file_exists(__DIR__ . '/../config/symbini.php')){
            include(__DIR__ . '/../config/symbini.php');
            $symbiniKeys = array_keys($GLOBALS);
            foreach($symbiniKeys as $key){
                if($key !== 'confManager' && $key !== 'DB_SERVER' && $key !== 'GLOBALS' && $key[0] !== '_'){
                    $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
                        'VALUES("'.$key.'","'.$GLOBALS[$key].'")';
                    $this->conn->query($sql);
                }
            }
        }


    }
}
