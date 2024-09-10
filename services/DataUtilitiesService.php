<?php
include_once(__DIR__ . '/FileSystemService.php');

class DataUtilitiesService {

    public static $monthRoman = array('I'=>'01','II'=>'02','III'=>'03','IV'=>'04','V'=>'05','VI'=>'06','VII'=>'07','VIII'=>'08','IX'=>'09','X'=>'10','XI'=>'11','XII'=>'12');
    public static $monthNames = array('jan'=>'01','ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','ago'=>'08',
        'aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12','dic'=>'12');

    public static function formatDate($inStr){
        $retDate = '';
        $dateStr = trim($inStr);
        if(!$dateStr) {
            return true;
        }
        $t = '';
        $y = '';
        $m = '00';
        $d = '00';
        if(preg_match('/\d{2}:\d{2}:\d{2}/', $dateStr,$match)){
            $t = $match[0];
        }
        if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
            $d = $match[3];
        }
        elseif(preg_match('/^(\d{4})-(\d{1,2})/', $dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
        }
        elseif(preg_match('/^([\d-]{1,5})\.([IVX]{1,4})\.(\d{2,4})/i', $dateStr,$match)){
            $d = $match[1];
            $mStr = strtoupper($match[2]);
            $y = $match[3];
            if(array_key_exists($mStr, self::$monthRoman)){
                $m = self::$monthRoman[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})[\s\/-](\D{3,})\.*[\s\/-](\d{2,4})/', $dateStr,$match)){
            $d = $match[1];
            $mStr = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr, self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})/', $dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s?(\d{1,2})[,\s]+([1,2][0,5-9]\d{2})$/', $dateStr,$match)){
            $mStr = $match[1];
            $d = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr, self::$monthNames)) {
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2,4})/', $dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s+([1,2][0,5-9]\d{2})/', $dateStr,$match)){
            $mStr = strtolower(substr($match[1],0,3));
            if(array_key_exists($mStr, self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
            $y = $match[2];
        }
        elseif(preg_match('/([1,2][0,5-9]\d{2})/', $dateStr,$match)){
            $y = $match[1];
        }
        if($y && strlen($y) === 4){
            if(strlen($m) === 1) {
                $m = '0' . $m;
            }
            if(strlen($d) === 1) {
                $d = '0' . $d;
            }
            if($m > 12){
                $m = '00';
                $d = '00';
            }
            if($d > 31){
                $d = '00';
            }
            elseif((int)$d === 30 && (int)$m === 2){
                $d = '00';
            }
            elseif((int)$d === 31 && ((int)$m === 4 || (int)$m === 6 || (int)$m === 9 || (int)$m === 11)){
                $d = '00';
            }
            $retDate = $y.'-'.$m.'-'.$d;
        }
        elseif($timestamp = strtotime($retDate)){
            $retDate = date('Y-m-d', $timestamp);
        }
        if($t){
            $retDate .= ' ' . $t;
        }
        return $retDate;
    }
}
