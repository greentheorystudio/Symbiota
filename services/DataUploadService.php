<?php
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/SanitizerService.php');

class DataUploadService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getUploadTableFieldData($tableArr): array
    {
        $retArr = array();
        foreach($tableArr as $table){
            if(strpos($table, 'upload') === 0){
                $retArr[$table] = array();
                $sql = 'SHOW COLUMNS FROM ' . $table . ' ';
                //echo '<div>'.$sql.'</div>';
                if($result = $this->conn->query($sql)){
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    $result->free();
                    foreach($rows as $rIndex => $row){
                        $retArr[$table][] = strtolower($row['Field']);
                        unset($rows[$rIndex]);
                    }
                }
            }
        }
        return $retArr;
    }

    public function processExternalDwcaTransfer($collid, $uploadType, $dwcaPath): string
    {
        $retVal = '';
        $transferSuccess = false;
        $targetPath = FileSystemService::getTempDwcaUploadPath($collid);
        if($targetPath && $dwcaPath){
            $fileName = 'dwca.zip';
            $fullTargetPath = $targetPath . '/' . $fileName;
            if((int)$uploadType === 8){
                $transferSuccess = FileSystemService::transferDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            elseif((int)$uploadType === 10){
                $transferSuccess = FileSystemService::transferSymbiotaDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            if($transferSuccess){
                $retVal = $fullTargetPath;
            }
        }
        return $retVal;
    }
}
