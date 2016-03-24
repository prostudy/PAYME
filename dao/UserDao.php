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
	
	public function getUser(){
		$database = new Database();
		try{
			$database->query('select pagetitle,introtext from modx_site_content order by id desc limit 10');
			$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $rows;
	}
}

?>