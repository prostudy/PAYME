<?php
/**
 * Class is responsible for send reminders
 * @author josafatbusio@gmail.com
 *
 * */
final class CronRemindeDao
{
	/**
	 * Call this method to get singleton
	 *
	 * @return CronRemindeDao
	 */
	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new CronRemindeDao();
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
	function getReminders(){
		$database = new Database();
		$rows = array();
		try{
			$database->query("SELECT clients.email
						   ,concat(clients.name,' ',clients.`lastname`) as nameUser
						   ,clients.`company`
						   ,projects.`description`
						   ,projects.`cost`
						   ,projects.`logo_image`
						   ,templates.text
						   ,reminders.`idreminders`
							FROM reminders, `projects`, clients, templates
							WHERE 1 = 1
							AND projects.`paidup` = 0 AND projects.`deleted` = 0 /*solo proyectos sin pagar y sin estar eliminados*/
							AND reminders.send = 0 AND reminders.deleted = 0  /*solo recordatorios no enviados y que no esten eliminados*/
							AND reminders.`projects_idprojects` = projects.`idprojects` 
							AND clients.`idclients` = projects.`clients_idclients`
							AND templates.`idtemplates` = reminders.`templates_idtemplates`
							AND (TIMESTAMPDIFF(MINUTE,  now(),date) > 0 AND TIMESTAMPDIFF(MINUTE,  now(),date) <= 1 ) /*diferencia de minutos de la fecha programada y la actual*/");
			/*$database->bind(':send', 0);
			$database->bind(':deleted', 0);*/
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
	 * Actualiza el campo send de la tabla reminders para indicar que el recordatorio ya ha sido enviado
	 * @param unknown $reminderId
	 * @return multitype:number string NULL
	 */
	public function updateRemienderAsSend($reminderId){
		$database = new Database();
		$database->beginTransaction();
		$updateUserResult = array();
		$updateUserResult['rowsUpdated']  = 0;
		$updateUserResult['error'] = '';
	
		try{
			$database->query("UPDATE reminders set send = 1 WHERE idreminders = :idreminders");
			$database->bind(':idreminders',  $reminderId);
			$database->execute();
			$updateUserResult['rowsUpdated'] = $database->rowCount();
			$database->endTransaction();
		}catch(PDOException $e){
			$database->cancelTransaction();
			$updateUserResult['error'] = $e->getMessage();
			$database->closeConnection();
		}
		$database->closeConnection();
		$database = null;
	
		return $updateUserResult;
	}
}

?>