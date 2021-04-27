<?php

class Encryption{

	public const METHOD = 'aes-256-cbc';

	public static function encrypt($plainText): string
	{
		if(!isset($GLOBALS['SECURITY_KEY']) || !$GLOBALS['SECURITY_KEY']) {
			return $plainText;
		}
		if(!function_exists('openssl_encrypt')) {
			return $plainText;
		}
		$key = self::getKey();
		if (mb_strlen($key, '8bit') !== 32) {
			return $plainText;
		}
		$secure = false;
        $ivSize = openssl_cipher_iv_length(self::METHOD);
        do {
            $iv = openssl_random_pseudo_bytes($ivSize, $secure);
        } while(!$iv || !$secure);
        $ciphertext = openssl_encrypt($plainText, self::METHOD, $key, 1, $iv);

		return $iv . $ciphertext;
	}

	public static function decrypt($cipherTextIn) {
		if(!isset($GLOBALS['SECURITY_KEY']) ||
            !$GLOBALS['SECURITY_KEY'] ||
            !function_exists('openssl_decrypt') ||
            strpos($cipherTextIn,'CollEditor') !== false ||
            strpos($cipherTextIn,'CollAdmin') !== false ||
            strpos($cipherTextIn,'uid=') !== false) {
			return $cipherTextIn;
		}
		$key = self::getKey();
		if (mb_strlen($key, '8bit') !== 32) {
			return $cipherTextIn;
		}
		$ivSize = openssl_cipher_iv_length(self::METHOD);
		$iv = mb_substr($cipherTextIn, 0, $ivSize, '8bit');
		$cipherText = mb_substr($cipherTextIn, $ivSize, null, '8bit');
		if(!$cipherText){
			$cipherText = mb_substr($cipherTextIn, $ivSize);
		}
		return openssl_decrypt($cipherText, self::METHOD, $key, 1, $iv);
	}

	public static function getKey(){
		if (strlen($GLOBALS['SECURITY_KEY']) > 32) {
			return substr($GLOBALS['SECURITY_KEY'],0,32);
		}

		if(strlen($GLOBALS['SECURITY_KEY']) < 32) {
			return str_pad($GLOBALS['SECURITY_KEY'], 32, $GLOBALS['SECURITY_KEY'][0], STR_PAD_BOTH);
		}
		return '';
	}
}
