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

/*
getClient http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClient&email=osjobu@gmail.com&clientid=4
getClientsForUser http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClientsForUser&userid=50
saveClient http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=saveClient&email=osjobu@gmail.com&name=Oscar&lastname=Busio&company=compania&userid=50
getClientsWithProjectsAndRemindersForUser http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getClientsWithProjectsAndRemindersForUser&userid=50
getRemindersForPojectId http://localhost:8888/PAYME/ClientPaymeWebService.php?methodName=getRemindersForPojectId&projectId=1
*/

$controllerObject = new ClientPaymeWebService($_REQUEST['methodName'],
									   isset($_REQUEST['callback']),
									   isset($_REQUEST['callback']) ? $_REQUEST['callback']:"");
class ClientPaymeWebService {
	public $isJSONP = false;
	public $callback = "";
	
	public function ClientPaymeWebService($methodName,$appKey,$isJSONP=false,$callback=""){
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
		$email = utf8_encode($_REQUEST['email']);
		$name = utf8_encode($_REQUEST['name']);
		$lastname = utf8_encode($_REQUEST['lastname']);
		$company = utf8_encode($_REQUEST['company']);
		$userid = utf8_encode($_REQUEST['userid']);
		$createdon = date("Y-m-d H:i:s");
			
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		$clientDao = ClientDao::Instance();
			
		$saveClientResult = $clientDao->saveClient($email,$name,$lastname,$company,$userid,$createdon);				
		if($saveClientResult['rowsInserted'] > 0){
			$response->items = $saveClientResult;
			$response->success = true;
			$response->message = "Se guardo el cliente correctamente.";
		}else{
			$response->success = false;
			$response->message = $saveUserResult['error'];
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
		
		$i=0;
		foreach ($clients as $client){
			$projectsPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],1);
			$projectsPaidup = self::setRemindersForProjects($projectsPaidup);
			
			$projectsNotPaidup = $clientDao->getAllProjectsForClientId($client['idclients'],0);
			$projectsNotPaidup = self::setRemindersForProjects($projectsNotPaidup);
			
			$clients[$i]['projectsPaidup'] = $projectsPaidup;
			$clients[$i]['projectsNotPaidup'] = $projectsNotPaidup;
			$i++;
		}
		
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
}
?>