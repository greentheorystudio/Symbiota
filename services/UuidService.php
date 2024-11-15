<?php
class UuidService {

	public const METHOD = 'aes-256-cbc';

	public static function getUuidV4(): string
	{
		$returnStr = '';
	    $data = null;
		if(function_exists('openssl_random_pseudo_bytes')){
			$secure = false;
			$dataSize = openssl_cipher_iv_length(self::METHOD);
			do {
				$data = openssl_random_pseudo_bytes($dataSize, $secure);
			} while(!$data || !$secure);
		}
		if(!$data && file_exists('/dev/urandom')){
			$data = file_get_contents('/dev/urandom', NULL, NULL, 0, 16);
		}
		if(!$data && file_exists('/dev/random')){
			$data = file_get_contents('/dev/random', NULL, NULL, 0, 16);
		}
		if(!$data){
			for($cnt = 0; $cnt < 16; $cnt ++) {
				try {
					$data .= chr(random_int(0, 255));
				} catch (Exception $e) {
                    $returnStr = $e;
                }
			}
		}
		if($data) {
            if($data){
                $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            }
            if($data){
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            }

            $returnStr = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}
        return $returnStr;
	}

}
