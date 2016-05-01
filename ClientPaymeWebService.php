<?php
/**
 * Class is responsible for connecting get data for PAYME
 * @author josafatbusio@gmail.com
 *
 * */
// Include database class
require_once 'connection/Database.php';
require_once 'utils/Constants.php';
require_once 'dao/ClientDao.php';
require_once('utils/GenericResponse.php');
require_once('utils/CodeGenerator.php');
require_once 'utils/PHPMailer-master/PHPMailerAutoload.php';
require_once('utils/UtilsFunctions.php');

require_once 'dao/CronRemainderDao.php';
require_once './CronRemainders.php';


/*
getClient http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClient&email=osjobu@gmail.com&clientid=4
getClientsForUser http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClientsForUser&userid=50
saveClient //http://localhost/PAYME/ClientPaymeWebService.php?methodName=saveClient&userid=50&email=ogascon@iasanet.com.mx&name=Oscar&lastname=Gascon&company=CASA&description=cargo&cost=739&dateReminder=2016-03-30 11:10:07&sendnow=false&idTemplates=1
getClientsWithProjectsAndRemindersForUser http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClientsWithProjectsAndRemindersForUser&userid=50
getRemindersForPojectId http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getRemindersForPojectId&projectId=1

getRemindersSentAndAnsweredByUserId http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getRemindersSentAndAnsweredByUserId&userid=62
setReminderAsRead http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=setReminderAsRead&idreminders=36
setReminderAnweredAsRead http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=setReminderAnweredAsRead&idreminders=36

*/


$controllerObject = new ClientPaymeWebService($_REQUEST['methodName'],
									   isset($_REQUEST['callback']),
									   isset($_REQUEST['callback']) ? $_REQUEST['callback']:"");
class ClientPaymeWebService {
	public $isJSONP = false;
	public $callback = "";
	
	public function ClientPaymeWebService($methodName,$isJSONP=false,$callback=""){
		$this->isJSONP = $isJSONP;
		$this->callback = $callback;
// 		$response = new GenericResponse(true,$isJSONP,$callback);
		call_user_func(array($this,$methodName));
	}

	/**
	 * Recupera un cliente por medio del email y el clientid
	 */
	public function getClient(){
		$email = utf8_encode($_REQUEST['email']);
		$clientid = utf8_encode($_REQUEST['clientid']);
		/*$name = utf8_encode($_REQUEST['name']);*/
	
		$clientDao = ClientDao::Instance();
		$client = $clientDao->getClient($clientid, $email);
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(is_array($client) ){
			$items['client'] = $client;
			$response->setItems($items);
			$response->success = true;
			$response->message = "Se encontro cliente.";
		}else{
			$response->success = false;
			$response->message = "No se encontro cliente.";
		}
		echo $response->getResponseAsJSON();
	}
	
	/**
	 *  Recupera todos los clientes de un determinado usuario
	 */
	public function getClientsForUser(){
		$userid = utf8_encode($_REQUEST['userid']);
	
		$clientDao = ClientDao::Instance();
		$clients = $clientDao->getClientsForUserId($userid);
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count($clients) > 0 ){
			$items['clients'] = $clients;
			$response->setItems($items);
			$response->success = true;
			$response->message = "Se encontraron clientes.";
		}else{
			$response->success = false;
			$response->message = "No se encontraron clientes.";
		}
		echo $response->getResponseAsJSON();
	}
	
	/**
	 * Inserta un cliente en la tabla clients
	 */
	public function saveClient(){
		$createdon = date("Y-m-d H:i:s");
		
		$email = utf8_encode($_REQUEST['email']);
		$name = strtoupper( utf8_encode($_REQUEST['name']) );
		$lastname = strtoupper( utf8_encode($_REQUEST['lastname']) );
		$company = strtoupper( utf8_encode($_REQUEST['company']) );
		$userid = utf8_encode($_REQUEST['userid']);
		
		$description = strtoupper ( utf8_encode($_REQUEST['description']) );//Tabla de proyectos
		$cost = utf8_encode($_REQUEST['cost']);
		
		$dateReminder = utf8_encode($_REQUEST['dateReminder']);//Tabla de recordatorios
		$sendnow = $_REQUEST['sendnow'];
		$responseCode = CodeGenerator::activationAccountCodeGenerator($email.$name.$lastname.$userid.$createdon);
		$idTemplates = utf8_encode($_REQUEST['idTemplates']);
					
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		$clientDao = ClientDao::Instance();
			
		$saveClientResult = $clientDao->saveClient($email,$name,$lastname,$company,$userid,$description,$cost,$dateReminder,$sendnow,$idTemplates,$responseCode,$createdon);				
		if($saveClientResult['rowsClient'] > 0 && $saveClientResult['rowsProject'] > 0 && $saveClientResult['rowsReminder'] > 0){
			$response->items = $saveClientResult;
			$response->success = true;
			$response->message = "Se guardo el cliente correctamente.";
			
			$idReminderToSendNow = $saveClientResult['idReminderToSendNow'];
			if(!strcmp($sendnow,"true")){
				CronRemainders::readTableReminders($idReminderToSendNow);
			}
		}else{
			$response->success = false;
			$response->message = $saveClientResult['error'];
		}
		echo $response->getResponseAsJSON();
	}
	
	/**
	 * Recupera todos los clientes con sus respectivos proyectos para un usuario determinado.
	 */
	public function getClientsWithProjectsAndRemindersForUser(){
		$userid = utf8_encode($_REQUEST['userid']);
	
		$clientDao = ClientDao::Instance();
		$clients = $clientDao->getClientsForUserId($userid);
		
		
		$clientsPaidup = array();
		$clientsNotPaidup = array();
		
		$i=0;
		$totalPaidupd = 0;
		foreach ($clients as $client){
			$projectsPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],1);
			$projectsPaidup = self::setRemindersForProjects($projectsPaidup);
				
			
			foreach ($projectsPaidup as $project){
				$clientsPaidup[$i] = $client;
				$clientsPaidup[$i]['project'] = $project;
				$totalPaidupd += $project['cost'];
				$i++;
			}
			$clientsPaidup['total'] = $totalPaidupd;
		}
		
		
		$i=0;
		$totalNotPaidupd = 0;
		foreach ($clients as $client){
			$projectsNotPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],0);
			$projectsNotPaidup = self::setRemindersForProjects($projectsNotPaidup);
		
				
			foreach ($projectsNotPaidup as $project){
				$clientsNotPaidup[$i] = $client;
				$clientsNotPaidup[$i]['project'] = $project;
				$totalNotPaidupd += $project['cost'];
				$i++;
			}
			
			$clientsNotPaidup['total'] = $totalNotPaidupd;
		}
				
				
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count($clients) > 0 ){
			$items['clientsPaidup'] = $clientsPaidup;
			$items['clientsNotPaidup'] = $clientsNotPaidup;
			$response->setItems($items);
			$response->success = true;
			$response->message = "Se encontraron clientes.";
		}else{
			$response->success = true;
			$response->message = "No se encontraron clientes.";
		}
		echo  $response->getResponseAsJSON();
	}
	
	/*
	 public function getClientsWithProjectsAndRemindersForUser(){
		$userid = utf8_encode($_REQUEST['userid']);
	
		$clientDao = ClientDao::Instance();
		$clients = $clientDao->getClientsForUserId($userid);
		$clientsb = $clientDao->getClientsForUserId($userid);
		
		$i=0;
		foreach ($clients as $client){
			$projectsPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],1);
			$projectsPaidup = self::setRemindersForProjects($projectsPaidup);
			
			//$projectsNotPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],0);
			//$projectsNotPaidup = self::setRemindersForProjects($projectsNotPaidup);
			
			$clients[$i]['projectsPaidup'] = $projectsPaidup;
			//$clients[$i]['b']['projectsNotPaidup'] = $projectsNotPaidup;
			$i++;
		}
		
		$i=0;
		foreach ($clientsb as $client){	
			$projectsNotPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],0);
			$projectsNotPaidup = self::setRemindersForProjects($projectsNotPaidup);
				
			//$clients[$i]['a']['projectsPaidup'] = $projectsPaidup;
			$clientsb[$i]['projectsNotPaidup'] = $projectsNotPaidup;
			$i++;
		}
		
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count($clients) > 0 ){
			$items['clients'] = $clients;
			$items['clientsb'] = $clientsb;
			$response->setItems($items);
			$response->success = true;
			$response->message = "Se encontraron clientes.";
		}else{
			$response->success = false;
			$response->message = "No se encontraron clientes.";
		}
		echo $response->getResponseAsJSON();
	}
	 
	 */
	
	
	/**
	 * Recibe un conjunto de proyectos y agrega sus respectivos recordatorios
	 * @param array $projects
	 * @return array projectos con tus recordatorios
	 */
	private static function setRemindersForProjects($projects){
		/*echo "<pre>";
		print_r($projects);
		echo "</pre>";*/
		$clientDao = ClientDao::Instance();
		$i=0;
		$deleted = 0 ;
		foreach ($projects as $project){//Asigna los recordatorios  correspondientes al proyecto
			$reminders = $clientDao->getAllRemindersForProjectId($project['idprojects'],$deleted);
			
			$j=0;
			foreach ($reminders as $reminder){//Asigna el template correspondiente al recordatorio
				$template = $clientDao->getTemplateForReminder($reminder['templates_idtemplates']);
				$reminders[$j]['template'] = $template;
				$j++;
			}
			
			$projects[$i]['reminders'] = $reminders;
			$i++;
		}
		return $projects;
	} 
	
	
	/**
	 * Recupera los recordatorios de un proyecto especificado.
	 */
	public function getRemindersForPojectId(){
		$projectId = utf8_encode($_REQUEST['projectId']);

		$clientDao = ClientDao::Instance();
		$deleted = 0 ;
		$reminders = $clientDao->getAllRemindersForProjectId($projectId,$deleted);
		
		$i=0;
		foreach ($reminders as $reminder){//Asigna el template correspondiente al recordatorio
			$template = $clientDao->getTemplateForReminder($reminder['templates_idtemplates']);
			$reminders[$i]['template'] = $template;
			$i++;
		}

		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count($reminders) > 0 ){
			$response->setItems($reminders);
			$response->success = true;
			$response->message = "Se encontraron recordatorios.";
		}else{
			$response->success = false;
			$response->message = "No se encontraron recordatorios.";
		}
		echo $response->getResponseAsJSON();
	}
	
	/**
	 * Verifica si existe la url de respuesta para un recordatorio dado
	 */
	public function webPageResponseReminderCode(){
		$responseCode = utf8_encode($_REQUEST['responseCode']);
		$clientDao = ClientDao::Instance();
		$reminder = $clientDao->verifyResponseReminderCode($responseCode);
		//$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(is_array($reminder)){
			$response->setItems($reminder);
			$response->success = true;
			$response->message = "El codigo de respuesta se encontro correctamente";
		}else{
			$response->success = false;
			$response->message = "No se encontro codigo de respuesta.";
		}
		echo $response->getResponseAsJSON();
	}
	
	
	/**
	 * Regresa todos los recordatorios que se han enviado.
	 */
	public function getRemindersSentAndAnsweredByUserId(){
		$userid = utf8_encode($_REQUEST['userid']);
		
		$clientDao = ClientDao::Instance();
		$remindersSent = $clientDao->getRemindersSentByUserId($userid);
		$remindersAnswered = $clientDao->getRemindersAnsweredByUserId($userid);
		
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		
		$response->success = false;
		$response->message = "No se encontraron recordatorios.";
		
		//if(is_array($remindersSent) ){
			$items['remindersSent'] = $remindersSent;
			$items['remindersAnswered'] = $remindersAnswered;
			$response->setItems($items);
			$response->success = true;
			$response->message = "Se encontraron recordatorios.";
		/*}else{
			$response->success = false;
			$response->message = "No se encontraron recordatorios.";
		}*/
		echo $response->getResponseAsJSON();
		
	}

	
	
	/**
	 * Actualiza el valor de la bandera isread indicando que ya se leyo la notificación
	 */
	public function setReminderAsRead(){
		$idreminders = utf8_encode($_REQUEST['idreminders']);
		$clientDao = ClientDao::Instance();
		
		$idremindersArray = explode(',', $idreminders);
		for($index=0; $index< count($idremindersArray); $index++){
			$clientDao->setReminderAsRead($idremindersArray[$index]);
		}
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		$response->success = true;
		$response->message = "Se actualizaron los registros.";
		
		echo $response->getResponseAsJSON();
	}
	
	/**
	 * Actualiza el valor de la bandera responseIsRead indicando que ya se leyo la notificación que un usuario contesto
	 * @param id $user
	 */
	public function setReminderAnweredAsRead(){
		$idreminders = utf8_encode($_REQUEST['idreminders']);
		$clientDao = ClientDao::Instance();
	
		$idremindersArray = explode(',', $idreminders);
		for($index=0; $index< count($idremindersArray); $index++){
			$clientDao->setReminderAnweredAsRead($idremindersArray[$index]);
		}
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		$response->success = true;
		$response->message = "Se actualizaron los registros.";
	
		echo $response->getResponseAsJSON();
	}
}
?>