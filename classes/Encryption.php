<?php

class Encryption{

	public const METHOD = 'aes-256-cbc';

	public static function encrypt($plainText): string
	{
		global $SECURITY_KEY;
		if(!isset($SECURITY_KEY) || !$SECURITY_KEY) {
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
		global $SECURITY_KEY;
		if(!isset($SECURITY_KEY) || !$SECURITY_KEY) {
			return $cipherTextIn;
		}
		if(!function_exists('openssl_decrypt')) {
			return $cipherTextIn;
		}
		if(strpos($cipherTextIn,'CollEditor') !== false || strpos($cipherTextIn,'CollAdmin') !== false) {
			return $cipherTextIn;
		}
		if(strpos($cipherTextIn,'uid=') !== false) {
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
		global $SECURITY_KEY;
		if (strlen($SECURITY_KEY) > 32) {
			return substr($SECURITY_KEY,0,32);
		}

		if(strlen($SECURITY_KEY) < 32) {
			return str_pad($SECURITY_KEY, 32, $SECURITY_KEY[0], STR_PAD_BOTH);
		}
		return '';
	}
}
