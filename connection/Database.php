<?php
/*
 * Class is responsible for connecting to the database
 * Autor: josafatbusio@gmail.com
 * 
 * */
define("DB_HOST", "localhost");

class Database{
	private $host      = DB_HOST;
	private $user      = null;
	private $pass      = null;
	private $dbname    = null;

	private $dbh;
	private $stmt;	
	private $error;
	
	public function __construct(){
		// Create a new PDO instanace
		try{
			if(!isset($this->dbh)) {
				// Set DSN
				$config = parse_ini_file('config.ini');//TODO:CAMBIAR LOS PERMISOS DEL ARCHIVO PARA QUE NO PUEDA SER VISTO
				$this->user = $config['username'];
				$this->pass = $config['password'];
				$this->dbname = $config['dbname'];
				
				$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
				// Set options
				$options = array(
						PDO::ATTR_PERSISTENT    => true,
						PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
						'charset'=>'utf8'
				);
				$this->dbh = new PDO($dsn, $this->user, $this->pass, $options );
			}
		}
		// Catch any errors
		catch(PDOException $e){
			$this->error = $e->getMessage();
			$this->dbh = null;
			$this->stmt = null;
		}
	}
	
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	
	public function execute(){
		return $this->stmt->execute();
	}
	

	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	
	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}
	
	public function endTransaction(){
		return $this->dbh->commit();
	}
	
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
	
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
	
	public function closeConnection(){
		$this->dbh = null;
		$this->stmt = null;
		$this->host = null;
		$this->user = null;
		$this->pass = null;
		$this->dbname = null;
		$this->error = null;
		//echo "<br>ya cerre";
	}
}
?>