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
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
		
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
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
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
	
	function saveClient($email,$name,$lastname,$company,$userid,$description,$cost,$remindersArray,$sendnow,$idTemplates,$createdon,$mode){
		$database = new Database();
		$database->beginTransaction();
		$saveClientResult = array();
		$saveClientResult['rowsClient']  = 0;
		$saveClientResult['rowsProject']  = 0;
		$saveClientResult['rowsReminder']  = 0;
		
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
			
			$saveClientResult['rowsClient'] = $database->rowCount();
			$saveClientResult['lastInsertId'] = $database->lastInsertId();
			
			
			$database->query("INSERT INTO projects (`description`, `cost`, `paidup`, `logo_image`, `createdon`, `deleted`, `deleteon`, `clients_idclients`) VALUES (:description, :cost, '0', NULL, :createdon, '0', NULL, :clients_idclients)");
			$database->bind(':description',  $description);
			$database->bind(':cost',  $cost);
			$database->bind(':createdon', $createdon );
			$database->bind(':clients_idclients', $database->lastInsertId() );
			$database->execute();
			$database->lastInsertId();
			$saveClientResult['rowsProject'] = $database->rowCount();
			
			$idProyecto = $database->lastInsertId();
			
			for($index=0; $index< count($remindersArray); $index++){
				$responseCode = CodeGenerator::activationAccountCodeGenerator($index.$email.$name.$lastname.$userid.$createdon);
				
				$database->query("INSERT INTO reminders (`date`, `send`, `createdon`, `deleted`, `deleteon`, `isread`, `responseByClient`, `projects_idprojects`, `templates_idtemplates`,response_code) VALUES ( :dateReminder, '0', :createdon, '0', NULL, '0', NULL, :projects_idprojects, :templates_idtemplates,:response_code)");
				$database->bind(':dateReminder',  $remindersArray[$index]);
				$database->bind(':createdon', $createdon );
				$database->bind(':projects_idprojects', $idProyecto);
				$database->bind(':templates_idtemplates',  $idTemplates);
				$database->bind(':response_code',  $responseCode);
				$database->execute();
				$saveClientResult['rowsReminder'] +=  $database->rowCount();
					
			}
			$saveClientResult['idReminderToSendNow'] =  $database->lastInsertId();
			
	
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$saveClientResult['error'] = $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
		
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
			$database->query('SELECT * FROM `projects` WHERE clients_idclients = :clientid AND deleted = 0 AND paidup = :paidup limit 50');
			$database->bind(':clientid', $clientId);
			$database->bind(':paidup', $paidup);
			$rows = $database->resultset(); //$row = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;

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
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
		
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
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;

		return $row;
	}
	
	
	/**
	 * Verifica si existe la url de respuesta para un recordatorio dado
	 * @param string $responseCode
	 * @return array row
	 */
	public function verifyResponseReminderCode($responseCode){
		$database = new Database();
		$user = array();
		try{
			$database->query('SELECT * FROM reminders where response_code = :response_code  limit 1');
			$database->bind(':response_code', $responseCode);
			$user = $database->single();
		}catch(PDOException $e){
			echo $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	
		return $user;
	}
	
	
	/**
	 * Regresa todos los recordatorios que se han enviado.
	 */
	function getRemindersSentByUserId($userid){
		$database = new Database();
		$rows = array();
		try{
			$database->query("SELECT clients.email
						   ,concat(clients.name,' ',clients.`lastname`) as clientName
						   ,clients.`company`
						   ,projects.`description`
						   ,projects.`cost`
						   ,projects.`logo_image`
						   ,templates.text
						   ,reminders.`idreminders`
						   ,reminders.response_code
						   ,concat(users.name,' ',users.`lastname`) as userName					
							FROM reminders, `projects`, clients, templates,users
							WHERE 1 = 1
							AND projects.`paidup` = 0 AND projects.`deleted` = 0 /*solo proyectos sin pagar y sin estar eliminados*/
							AND reminders.send = 1 AND reminders.deleted = 0  /*solo recordatorios no enviados y que no esten eliminados*/
							AND reminders.isread = 0
							AND reminders.`projects_idprojects` = projects.`idprojects` 
							AND clients.`idclients` = projects.`clients_idclients`
							AND users.idusers = clients.users_idusers					
							AND templates.`idtemplates` = reminders.`templates_idtemplates`
							AND users.idusers = :userId");
			$database->bind(':userId', $userid);
			$rows = $database->resultset(); 
		}catch(PDOException $e){
			echo $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	
		return $rows;
	}
	
	
	/**
	 * Regresa todos los recordatorios que se han contestado.
	 */
	function getRemindersAnsweredByUserId($userid){
		$database = new Database();
		$rows = array();
		try{
			$database->query("SELECT clients.email
						   ,concat(clients.name,' ',clients.`lastname`) as clientName
						   ,clients.`company`
						   ,projects.`description`
						   ,projects.`cost`
						   ,projects.`logo_image`
						   ,templates.text
						   ,reminders.`idreminders`
						   ,reminders.response_code
						   ,concat(users.name,' ',users.`lastname`) as userName					
							FROM reminders, `projects`, clients, templates,users
							WHERE 1 = 1
							AND projects.`paidup` = 0 AND projects.`deleted` = 0 /*solo proyectos sin pagar y sin estar eliminados*/
							AND reminders.send = 1 AND reminders.deleted = 0  /*solo recordatorios no enviados y que no esten eliminados*/
							AND reminders.responseByClient is NOT NULL /*no es nula la respuesta*/
							AND reminders.responseIsRead = 0
							AND reminders.`projects_idprojects` = projects.`idprojects` 
							AND clients.`idclients` = projects.`clients_idclients`
							AND users.idusers = clients.users_idusers					
							AND templates.`idtemplates` = reminders.`templates_idtemplates`
							AND users.idusers = :userId");
			$database->bind(':userId', $userid);
			$rows = $database->resultset();
		}catch(PDOException $e){
			echo $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	
		return $rows;
	}
	
	
	/**
	 * Actualiza el valor de la bandera isread indicando que ya se leyo la notificación
	 * @param id $user
	 */
	public function setReminderAsRead($idreminder){
		$database = new Database();
		$database->beginTransaction();	
		try{
			$database->query("UPDATE reminders SET `isread` = '1' WHERE `reminders`.`idreminders` = :idreminders");
			$database->bind(':idreminders',  $idreminder);
			$database->execute();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	}
	
	/**
	 * Actualiza el valor de la bandera responseIsRead indicando que ya se leyo la notificación que un usuario contesto
	 * @param id $user
	 */
	public function setReminderAnweredAsRead($idreminder){
		$database = new Database();
		$database->beginTransaction();
		try{
			$database->query("UPDATE reminders SET `responseIsRead` = '1' WHERE `reminders`.`idreminders` = :idreminders");
			$database->bind(':idreminders',  $idreminder);
			$database->execute();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	}
	
	
	/**
	 * Marca un proyecto como pagado o no pagado
	 * @param int $idproject
	 * @param int $idclient
	 * @param int $paid
	 * @return multitype:number string NULL
	 */
	public function setProjectAsPaidup($idproject,$idclient,$paid){
		$database = new Database();
		$database->beginTransaction();
		$updateProjectResult = array();
		$updateProjectResult['rowsUpdated']  = 0;
		$updateProjectResult['error'] = '';
		
		try{
			$database->query("UPDATE projects SET `paidup` = :paid WHERE `idprojects` = :idproject and clients_idclients = :idclient" );
			$database->bind(':idproject',  $idproject);
			$database->bind(':idclient',  $idclient);
			$database->bind(':paid',  $paid);
			
			$database->execute();
			$updateProjectResult['rowsUpdated'] = $database->rowCount();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$updateProjectResult['error'] = $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
		
		return $updateProjectResult;
	}
	
	
	public function setProjectAsArchived($idproject,$idclient,$deleted){
		$database = new Database();
		$database->beginTransaction();
		$updateProjectResult = array();
		$updateProjectResult['rowsUpdated']  = 0;
		$updateProjectResult['error'] = '';
	
		try{
			$database->query("UPDATE projects SET `deleted` = :deleted WHERE `idprojects` = :idproject and clients_idclients = :idclient" );
			$database->bind(':idproject',  $idproject);
			$database->bind(':idclient',  $idclient);
			$database->bind(':deleted',  $deleted);
				
			$database->execute();
			$updateProjectResult['rowsUpdated'] = $database->rowCount();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$updateProjectResult['error'] = $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	
		return $updateProjectResult;
	}
	
	
	
}

?>