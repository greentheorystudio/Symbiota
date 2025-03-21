<?php
/*
SuperAdmin			Edit all data and assign new permissions
RareSppAdmin		Add or remove species from rare species list
RareSppReadAll		View and map rare species collection data for all collections
RareSppReader		View and map rare species collecton data for specific collections
CollAdmin			Upload records; modify metadata
CollEditor  		Edit collection records
ClAdmin 			Checklist write access
ProjAdmin			Project admin access
KeyAdmin			Edit identification key characters and character states
KeyEditor			Edit identification key data
TaxonProfile		Modify decriptions; add images;
Taxonomy			Add names; edit name; change taxonomy
*/
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class Permissions{

	private $conn;

    private $fields = array(
        "userroleid" => array("dataType" => "number", "length" => 10),
        "uid" => array("dataType" => "number", "length" => 10),
        "role" => array("dataType" => "string", "length" => 45),
        "tablename" => array("dataType" => "string", "length" => 45),
        "tablepk" => array("dataType" => "number", "length" => 11),
        "uidassignedby" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function addPermission($uid, $role, $tablePk = null): void
    {
        if((int)$uid > 0){
            $sql = 'INSERT IGNORE INTO userroles(uid, role, tablepk, uidassignedby) VALUES('.
                (int)$uid . ','.
                '"' . SanitizerService::cleanInStr($this->conn, $role) . '", '.
                (($tablePk && (int)$tablePk > 0) ? (int)$tablePk : 'NULL') . ','.
                $GLOBALS['SYMB_UID'] . ') ';
            $this->conn->query($sql);
        }
    }

    public function deletePermission($uid, $role, $tablePk = null): void
    {
        if((int)$uid > 0){
            $sql = 'DELETE FROM userroles '.
                'WHERE uid = ' . (int)$uid . ' AND role = "' . SanitizerService::cleanInStr($this->conn, $role) . '" '.
                'AND (tablepk '.($tablePk?' = '.$tablePk:' IS NULL').') ';
            if($tablePk && (int)$tablePk > 0){
                $sql .= 'AND tablepk = ' . (int)$tablePk . ' ';
            }
            $this->conn->query($sql);
        }
    }

    public function getUserRareSpCollidAccessArr(): array
    {
        $returnArr = array();
        if($GLOBALS['VALID_USER']){
            $sql = 'SELECT collid FROM omcollections ';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if($GLOBALS['IS_ADMIN'] || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
                        $returnArr[] = (int)$row['collid'];
                    }
                    elseif(
                        (array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array((int)$row['collid'], $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) ||
                        (array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array((int)$row['collid'], $GLOBALS['USER_RIGHTS']['CollEditor'], true)) ||
                        (array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS']) && in_array((int)$row['collid'], $GLOBALS['USER_RIGHTS']['RareSppReader'], true))
                    ){
                        $returnArr[] = (int)$row['collid'];
                    }


                    unset($rows[$index]);
                }
            }
        }
        return $returnArr;
    }

    public function setUserPermissions(): void
    {
        if(isset($_SESSION['PARAMS_ARR']['uid']) && (int)$_SESSION['PARAMS_ARR']['uid'] > 0){
            $permittedCollections = array();
            $userrights = array();
            $sql = 'SELECT role, tablepk FROM userroles WHERE uid = ' . (int)$_SESSION['PARAMS_ARR']['uid'] . ' ';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if($row['tablepk']){
                        $userrights[$row['role']][] = (int)$row['tablepk'];
                        if(($row['role'] === 'CollAdmin' || $row['role'] === 'CollEditor' || $row['role'] === 'CollTaxon') && !in_array((int)$row['tablepk'], $permittedCollections, true)){
                            $permittedCollections[] = (int)$row['tablepk'];
                        }
                    }
                    else{
                        $userrights[$row['role']] = true;
                    }
                    unset($rows[$index]);
                }
            }
            $_SESSION['USER_RIGHTS'] = $userrights;
            $GLOBALS['USER_RIGHTS'] = $userrights;
            $GLOBALS['PERMITTED_COLLECTIONS'] = $permittedCollections;
        }
    }

    public function validatePermission($permissions, $key): array
    {
        $returnArr = array();
        if(is_array($permissions)){
            if($GLOBALS['IS_ADMIN']){
                $returnArr = $permissions;
            }
            else{
                foreach($permissions as $permission){
                    if(array_key_exists($permission, $GLOBALS['USER_RIGHTS']) && (!$key || !is_array($GLOBALS['USER_RIGHTS'][$permission]) || in_array((int)$key, $GLOBALS['USER_RIGHTS'][$permission], true))){
                        $returnArr[] = $permission;
                    }
                }
            }
        }
        elseif($GLOBALS['IS_ADMIN'] || (array_key_exists($permissions, $GLOBALS['USER_RIGHTS']) && (!$key || !is_array($GLOBALS['USER_RIGHTS'][$permissions]) || in_array((int)$key, $GLOBALS['USER_RIGHTS'][$permissions], true)))){
            $returnArr[] = $permissions;
        }
        return $returnArr;
    }
}
