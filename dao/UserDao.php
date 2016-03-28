<?php
/**
 * @author josafatbusio@gmail.com
 *
 */
final class UserDao
{
	/**
	 * Call this method to get singleton
	 *
	 * @return UserDao
	 */
	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new UserDao();
		}
		return $inst;
	}

	/**
	 * Private ctor so nobody else can instance it
	 *
	 */
	private function __construct()
	{

	}
	
	public function getUser($email,$password){
		$database = new Database();
		$row = array();
		try{
			$database->query('SELECT * FROM users where email = :email and password = :password and active = 1 limit 1');
			$database->bind(':email', $email);
			$database->bind(':password', self::krypPassword($password));			
			$row = $database->single(); //$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $row;
	}
	
	/**
	 * * Inserta un usuario en la tabla users
	 *  @param string  email
	 *  @param string  name
	 *  @param string  lastname
	 *  @param string  password
	 *  @return array rowsInserted,error
	 */
	function saveUser($email,$name,$lastname,$password,$activation_code,$createdon,$active){
		$database = new Database();
		$database->beginTransaction();
		$saveUserResult = array();
		$saveUserResult['rowsInserted']  = 0;
		$saveUserResult['error'] = '';
		try{
			$database->query("INSERT INTO `users` (`email`, `name`, `lastname`, `password`, `activation_code`, `createdon`, `active`) VALUES ( :email, :name, :lastname, :password, :activation_code, :createdon,:active)");
			$database->bind(':email',  $email);
			$database->bind(':name', $name);
			$database->bind(':lastname', $lastname);
			$database->bind(':password', self::krypPassword($password));
			$database->bind(':activation_code', $activation_code );
			$database->bind(':createdon', $createdon );
			$database->bind(':active', $active);
			
			$database->execute();
			$saveUserResult['rowsInserted'] = $database->rowCount();
	
			//echo $database->lastInsertId();
			//$database->debugDumpParams();
	
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$saveUserResult['error'] = $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $saveUserResult;
	}
	
	/**
	 * Devuelve un password cifrado
	 * @param string $password
	 * @return string
	 */
	private static function krypPassword($password){
		return md5('payme'.sha1('payme&#'.$password ));
	}
	
	
	/**
	 * Busca en la base de datos el codigo de activación
	 * @param string $activationCode
	 * @return user Regresa el usuario encontrado con dicho codigo de activación
	 */
	public function verifyUrlActivation($activationCode){
		$database = new Database();
		$user = array();
		try{
			$database->query('SELECT idusers,email,name,activation_code FROM users where activation_code = :activation_code  limit 1');
			$database->bind(':activation_code', $activationCode);
			$user = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $user;
	}
	
	/**
	 * Activa la cuenta del usuario que se envia como parametro
	 * @param array $user
	 * @param date $activatedon
	 * @return multitype:number string NULL
	 */
	public function setActiveAccount($user,$activation_date){
		$database = new Database();
		$database->beginTransaction();
		$updateUserResult = array();
		$updateUserResult['rowsUpdated']  = 0;
		$updateUserResult['error'] = '';
		
		try{
			$database->query("UPDATE users set active = 1, activation_date = :activation_date, activation_code = :activation_codeUpdate WHERE activation_code = :activation_code AND email = :email AND active = 0");
			$database->bind(':activation_date',  $activation_date);
			$database->bind(':activation_code',  $user['activation_code']);
			$database->bind(':activation_codeUpdate',  $user['activation_code']."_ACTIVATED");
			$database->bind(':email',  $user['email']);
			$database->execute();
			$updateUserResult['rowsUpdated'] = $database->rowCount();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$updateUserResult['error'] = $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $updateUserResult;
	}
	
	
	/**
	 * Actualiza el codigo de cambio de password
	 * @param unknown $email
	 * @param unknown $resetPasswordCode
	 * @return multitype:number string NULL
	 */
	public function updateResetPasswordCodeforValidUserActive($email,$resetPasswordCode){
		$database = new Database();
		$database->beginTransaction();
		$updateUserResult = array();
		$updateUserResult['rowsUpdated']  = 0;
		$updateUserResult['error'] = '';
	
		try{
			$database->query("UPDATE users set reset_password_code = :reset_password_code WHERE email = :email AND active = 1");
			$database->bind(':reset_password_code',  $resetPasswordCode);
			$database->bind(':email', $email );
			$database->execute();
			$updateUserResult['rowsUpdated'] = $database->rowCount();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$updateUserResult['error'] = $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $updateUserResult;
	}
}

?>