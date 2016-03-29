<?php
/**
 * @author josafatbusio@gmail.com
 *
 */
final class ClientDao
{
	/**
	 * Call this method to get singleton
	 *
	 * @return ClientDao
	 */
	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new ClientDao();
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
	
	/**
	 * Recupera un cliente por medio del email y el clientid
	 * @param int $clientid
	 * @param string $email
	 * @return array client 
	 */
	function getClient($clientid,$email){
		$database = new Database();
		$row = array();
		try{
			$database->query('SELECT * FROM clients where idclients = :idclients AND email = :email  limit 1');
			$database->bind(':idclients', $clientid);
			$database->bind(':email', $email);
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
	 * Recupera todos los clientes de un determinado usuario
	 * @param int $userid
	 * @return array clientes:
	 */
	function getClientsForUserId($userid){
		$database = new Database();
		$rows = array();
		try{
			$database->query('SELECT * FROM clients where users_idusers = :userid limit 50');
			$database->bind(':userid', $userid);
			$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $rows;
	}
	
	
	
	/**
	 * Inserta un cliente en la tabla clients
	 * @param string $email
	 * @param string $name
	 * @param string $lastname
	 * @param string $company
	 * @param string $userid
	 * @param date $createdon
	 * @return multitype:number string NULL
	 */
	function saveClient($email,$name,$lastname,$company,$userid,$createdon){
		$database = new Database();
		$database->beginTransaction();
		$saveClientResult = array();
		$saveClientResult['rowsInserted']  = 0;
		$saveClientResult['error'] = '';
		try{
			$database->query("INSERT INTO clients (`email`, `name`, `lastname`, `company`, `users_idusers`, `createdon`) VALUES (:email, :name, :lastname, :company, :userid, :createdon)");
			$database->bind(':email',  $email);
			$database->bind(':name', $name);
			$database->bind(':lastname', $lastname);
			$database->bind(':company', $company);
			$database->bind(':userid', $userid );
			$database->bind(':createdon', $createdon );
			$database->execute();
			$saveClientResult['rowsInserted'] = $database->rowCount();
			$saveClientResult['lastInsertId'] = $database->lastInsertId();
	
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$saveClientResult['error'] = $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $saveClientResult;
	}
	
	/**
	 * Recupera todos los proyectos de un determinado cliente, 
	 * Separando los proyectos pagados de los no pagados.
	 * @param int $clientId
	 * @param int $paidup
	 * @return array proyectos del cliente:
	 */
	function getAllProjectsForClientId($clientId,$paidup){
		$database = new Database();
		$rows = array();
		try{
			$database->query('SELECT * FROM `projects` WHERE clients_idclients = :clientid AND deleted is NULL AND paidup = :paidup limit 50');
			$database->bind(':clientid', $clientId);
			$database->bind(':paidup', $paidup);
			$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $rows;
	}
	
	/**
	 * Regresa todos los recordatorios de un proyecto especificado.
	 * @param int $projectId
	 * @param int $deleted
	 * @return array recordatorios del proyecto::
	 */
	function getAllRemindersForProjectId($projectId, $deleted){
		$database = new Database();
		$rows = array();
		try{
			
			$database->query('SELECT * FROM reminders WHERE projects_idprojects	= :projects_idprojects	 AND deleted = :deleted  limit 50');
			$database->bind(':projects_idprojects', $projectId);
			$database->bind(':deleted', $deleted);
			$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
		}finally{
			$database->closeConnection();
			$database = null;
		}
		return $rows;
	}	
	
	
	/**
	 * Recupera el template de un recordatorio
	 * @param int $templateId
	 * @return array row
	 */
	function getTemplateForReminder($templateId){
		$database = new Database();
		$row = array();
		try{
			$database->query('SELECT * FROM templates WHERE idtemplates = :idtemplates limit 1');
			$database->bind(':idtemplates', $templateId);
			$row = $database->single();
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