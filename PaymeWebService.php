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
http://localhost/PAYME/PaymeWebService.php?methodName=getUser&user=oscar&password=asdad
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
	 * * Realiza la validación del login cuando no se utiliza facebook
	 *  @param string  usuario
	 *  @param string  password
	 */
	public function getUser(){
		$user = utf8_encode($_REQUEST['user']);
		$password = utf8_encode($_REQUEST['password']);

		$userDao = UserDao::Instance();
		$users = $userDao->getUser();
		//$items = array();
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count( $users ) > 0 ){
			//$document = array_values($document);
			//$items['users'] = $users;
			$response->setItems($users);
		}else{
			$response->success = false;
			$response->message = "No se encontro usuario.";
		}
		//print_r($userInfo);
		echo $response->getResponseAsJSON();
	}
}
?>