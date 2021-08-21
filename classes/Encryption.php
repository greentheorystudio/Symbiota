<?php

class Encryption{

	public const METHOD = 'aes-256-cbc';

	public static function encrypt($plainText): string
	{
		$returnStr = '';
	    $key = self::getKey();
		if((!isset($GLOBALS['SECURITY_KEY']) || !$GLOBALS['SECURITY_KEY']) || !function_exists('openssl_encrypt') || mb_strlen($key, '8bit') !== 32){
            $returnStr = $plainText;
        }
		else{
            $secure = false;
            $ivSize = openssl_cipher_iv_length(self::METHOD);
            do {
                $iv = openssl_random_pseudo_bytes($ivSize, $secure);
            } while(!$iv || !$secure);
            $ciphertext = openssl_encrypt($plainText, self::METHOD, $key, 1, $iv);
            if($iv && $ciphertext){
                $returnStr = $iv . $ciphertext;
            }
        }
		return $returnStr;
	}

	public static function decrypt($cipherTextIn) {
		$returnStr = '';
        $key = self::getKey();
	    if(!isset($GLOBALS['SECURITY_KEY']) ||
            !$GLOBALS['SECURITY_KEY'] ||
            !function_exists('openssl_decrypt') ||
            strpos($cipherTextIn,'CollEditor') !== false ||
            strpos($cipherTextIn,'CollAdmin') !== false ||
            strpos($cipherTextIn,'uid=') !== false ||
            mb_strlen($key, '8bit') !== 32) {
            $returnStr = $cipherTextIn;
		}
	    else{
            $ivSize = openssl_cipher_iv_length(self::METHOD);
            $iv = mb_substr($cipherTextIn, 0, $ivSize, '8bit');
            $cipherText = mb_substr($cipherTextIn, $ivSize, null, '8bit');
            if(!$cipherText){
                $cipherText = mb_substr($cipherTextIn, $ivSize);
            }
            $returnStr = openssl_decrypt($cipherText, self::METHOD, $key, 1, $iv);
        }
		return $returnStr;
	}

	public static function getKey(){
		$returnStr = '';
	    if (strlen($GLOBALS['SECURITY_KEY']) > 32) {
            $returnStr = substr($GLOBALS['SECURITY_KEY'],0,32);
		}
        elseif(strlen($GLOBALS['SECURITY_KEY']) < 32) {
            $returnStr = str_pad($GLOBALS['SECURITY_KEY'], 32, $GLOBALS['SECURITY_KEY'][0], STR_PAD_BOTH);
		}
		return $returnStr;
	}
}
