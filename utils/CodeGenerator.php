<?php 
/**
 * 
 * @author Oscar Gascon
 *
 */
class CodeGenerator{	
	/**
	 * Genera un string aleatorio
	 * @param number $length
	 * @return string
	 */
	public static function generateString ($length = 8){
		$string = "";
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";
		$i = 0;
		while ($i < $length) {
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$string .= $char;
			$i++;
		}
		return $string;
	}
	
	/**
	 * Genera un string haciendo uso de una longitud y una semilla
	 * @param int $length
	 * @param string $seed
	 * @return string
	 */
	public static function getUniqueCode($length = 8,$seed){
		$unique = uniqid(rand(), true);
		$code = md5($unique.sha1($seed).time());
		if ($length != ""){
			return substr($code, 0, $length);
		}else {
			return $code;
		}
	}
	
	
	/**
	 * Genera el codigo para la url unica de confirmación de registro o de cambio de password
	 * @param  $model
	 * @return string
	 */
	public static function activationAccountCodeGenerator($emailNameLastname){
		$length = 50;
		$activationCode =  strtoupper ( self::getUniqueCode($length,$emailNameLastname.self::generateString() ) );
		return $activationCode;
	}
	
	
	/**
	 * Genera un codigo unico para el cambio de password
	 * @param string $email
	 * @param int $id
	 * @param string $name
	 * @return unknown
	 */
	public static function generateChangePasswordCode($email,$id,$name){
		$length = 50;
		$seed = $email.$id.$name;
		$activationCode =  strtoupper ( self::getUniqueCode($length,$seed.self::generateString() ) );
		return $activationCode;
	}

}


?>
