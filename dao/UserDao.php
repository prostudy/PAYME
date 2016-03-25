<?php
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
			$database->query('SELECT * FROM users where email = :email and password = :password limit 1');
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
}

?>