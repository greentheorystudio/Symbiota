<?php
include_once(__DIR__ . '/DbConnection.php');

class ConfigurationManager{

    private $conn;

    public $coreConfigurations = array(
        'DEFAULT_LANG',
        'DEFAULT_PROJ_ID',
        'DEFAULTCATID',
        'DEFAULT_TITLE',
        'TID_FOCUS',
        'ADMIN_EMAIL',
        'CHARSET',
        'MAX_UPLOAD_FILESIZE',
        'PORTAL_GUID',
        'SECURITY_KEY',
        'CLIENT_ROOT',
        'SERVER_ROOT',
        'LOG_PATH',
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_ENCRYPTION',
        'SMTP_ENCRYPTION_MECHANISM',
        'SMTP_USERNAME',
        'SMTP_PASSWORD',
        'IMAGE_DOMAIN',
        'IMAGE_ROOT_URL',
        'IMAGE_ROOT_PATH',
        'IMG_WEB_WIDTH',
        'IMG_TN_WIDTH',
        'IMG_LG_WIDTH',
        'IMG_FILE_SIZE_LIMIT',
        'SOLR_URL',
        'SOLR_FULL_IMPORT_INTERVAL',
        'GBIF_USERNAME',
        'GBIF_PASSWORD',
        'GBIF_ORG_KEY',
        'SPATIAL_INITIAL_CENTER',
        'SPATIAL_INITIAL_ZOOM',
        'GOOGLE_ANALYTICS_KEY',
        'RIGHTS_TERMS',
        'GOOGLE_ANALYTICS_KEY',
        'CSS_VERSION'
    );

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

    public function setGlobalArr(): void
    {
        $retArr = array();
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
        $rs->free();
    }

    public function configureFromSymbini(): void
    {
        if(file_exists(__DIR__ . '/../config/symbini.php')){
            include(__DIR__ . '/../config/symbini.php');
            $symbiniKeys = array_keys($GLOBALS);
            foreach($symbiniKeys as $key){
                if($GLOBALS[$key] && $key !== 'confManager' && $key !== 'DB_SERVER' && $key !== 'GLOBALS' && $key[0] !== '_'){
                    $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
                        'VALUES("'.$key.'","'.$GLOBALS[$key].'")';
                    $this->conn->query($sql);
                }
            }
        }


    }
}
