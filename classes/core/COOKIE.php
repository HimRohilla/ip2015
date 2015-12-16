<?php
	class Cookie{
		private static $salt = array(9, 5, 8, 2, 4, 5, 7, 8, 1, 3);		
		private static $salt_string = "";

		private static function getHashed($key){
			$salt_string = self::$salt_string;
			$newKey = hash_hmac("md5", $key, $salt_string);
			return $newKey;
		}
		private static function is_json($value){
			json_decode($value);
			return (json_last_error() == JSON_ERROR_NONE);
		}
		public static function exists($key){
			return isset($_COOKIE[self::getHashed($key)]);
		}
		public static function getCookie($key){
			try {
				if(self::exists($key)){
					$value = $_COOKIE[self::getHashed($key)];
					if(self::is_json($value))
						return json_decode($value);
					else
						return self::decode($value);
				}
				throw new CustomException("Cookie ".$key." does not exists");
			}
			catch(CustomException $e){
				echo $e->printStackTrace();
				return null;
			}
		}
		public static function setCookie($key, $value, $expiry=0, $domainName=""){
			try{
				if(is_numeric($value) || is_string($value))
					$value = self::encode($value);
				else
					$value = json_encode($value);
				if(setcookie(self::getHashed($key), $value, time() + $expiry, '/', $domainName, false, true))
					return true;
				throw new CustomException("Cookie <b>".$key."</b> cannot be set");
			}
			catch(CustomException $e){
				echo $e->printStackTrace();
				return false;
			}
		}
		public static function deleteCookie($key){
			try{
				if(self::exists($key))
					if(self::setCookie($key, '', time()-1))
						return true;
					else
						throw new CustomException("Cookie <b>".$key."</b> cannot be deleted");
				throw new CustomException("No cookie with name <b>".$key."</b> exists");
				//return false;
			}
			catch(CustomException $e){
				echo $e->printStackTrace();
				return false;
			}
		}
		private static function encode($value){
			$newValue = "";
			$salt = self::$salt;
			for ($i=0; $i <strlen($value) ; $i++) { 
				$ascii = ord($value[$i]);
				if(isset($salt[$i]))
					$ind = $i;
				else
					$ind = $i - 10;

				$ascii += $salt[$ind];
				$newValue .= chr($ascii);
			}
			return base64_encode(strrev($newValue));
		}
		private static function decode($value){
			$value = strrev(base64_decode($value));
			$newValue = "";
			$salt = self::$salt;
			for ($i=0; $i <strlen($value) ; $i++) { 
				$ascii = ord($value[$i]);
				if(isset($salt[$i]))
					$ind = $i;
				else
					$ind = $i - 10;

				$ascii -= $salt[$ind];
				$newValue .= chr($ascii);				
			}
			return $newValue;
		}				
	}
?>