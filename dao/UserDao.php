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
			$database->bind(':password', $password);			
			$row = $database->single(); //$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $row;
	}
}

?>