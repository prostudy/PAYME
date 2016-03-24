<?php
/*
 * Class is responsible for connecting get data for PAYME
 * Autor: josafatbusio@gmail.com
 *
 * */
// Include database class
require_once 'connection/Database.php';
require_once 'dao/tutorial.php';
require_once 'dao/UserDao.php';
require_once('utils/GenericResponse.php');
/*
http://localhost:8888/PAYME/PaymeWebService.php?methodName=getUser&email=osjobu@gmail.com&password=12345
*/
$controllerObject = new PaymeWebService($_REQUEST['methodName'],
									   isset($_REQUEST['callback']),
									   isset($_REQUEST['callback']) ? $_REQUEST['callback']:"");
class PaymeWebService {
	public $isJSONP = false;
	public $callback = "";
	
	public function PaymeWebService($methodName,$appKey,$isJSONP=false,$callback=""){
		$this->isJSONP = $isJSONP;
		$this->callback = $callback;
// 		$response = new GenericResponse(true,$isJSONP,$callback);
		call_user_func(array($this,$methodName));
	}

	/**
	 * * Realiza la validación del login
	 *  @param string  email
	 *  @param string  password
	 */
	public function getUser(){
		$email = utf8_encode($_REQUEST['email']);
		$password = utf8_encode($_REQUEST['password']);

		$userDao = UserDao::Instance();
		$user = $userDao->getUser($email,$password);
		$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(is_array($user) ){
			//$document = array_values($document);
			$items['user'] = $user;
			$response->setItems($items);
		}else{
			$response->success = false;
			$response->message = "No se encontro usuario.";
		}
		echo $response->getResponseAsJSON();
	}
}
?>