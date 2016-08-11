<?php
/**
 * Class is responsible for send reminders
 * @author josafatbusio@gmail.com
 *
 * */
final class CronRemainderDao
{
	/**
	 * Call this method to get singleton
	 *
	 * @return CronRemainderDao
	 */
	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new CronRemainderDao();
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
	function getRemainders($idReminderToSendNow){
		$database = new Database();
		$rows = array();
		try{
			$queryString = "SELECT clients.email
						   ,concat(clients.name,' ',clients.`lastname`) as clientName
						   ,clients.`company`
						   ,projects.`description`
						   ,projects.`cost`
						   ,projects.`logo_image`
						   ,templates.text
						   ,reminders.`idreminders`
						   ,reminders.response_code
						   ,concat(users.name,' ',users.`lastname`) as userName
						   ,users.text_account
						   ,users.email as emailuser
						   ,users.name as  nameuser
						   ,users.clabe
					       ,users.card
					       ,users.paypal
					       ,users.phone
						   ,reminders.customtext
							FROM reminders, `projects`, clients, templates,users
							WHERE 1 = 1
							AND projects.`paidup` = 0 AND projects.`deleted` = 0 /*solo proyectos sin pagar y sin estar eliminados*/
							AND reminders.send = 0 AND reminders.deleted = 0  /*solo recordatorios no enviados y que no esten eliminados*/
							AND reminders.`projects_idprojects` = projects.`idprojects` 
							AND clients.`idclients` = projects.`clients_idclients`
							AND users.idusers = clients.users_idusers					
							AND templates.`idtemplates` = reminders.`templates_idtemplates`";
			
			if($idReminderToSendNow){
				$queryString .= " AND reminders.idreminders = $idReminderToSendNow limit 1";
			}else{
				$queryString .= " AND (TIMESTAMPDIFF(MINUTE,  now(),date) < 0 ) /*diferencia de minutos de la fecha programada y la actual*/";
			}
			$database->query($queryString);
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
	public function updateRemainderAsSend($remainderId){
		$database = new Database();
		$database->beginTransaction();
		$updateUserResult = array();
		$updateUserResult['rowsUpdated']  = 0;
		$updateUserResult['error'] = '';
	
		try{
			$database->query("UPDATE reminders set send = 1 WHERE idreminders = :idreminders");
			$database->bind(':idreminders',  $remainderId);
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