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
require_once('utils/CodeGenerator.php');
require_once 'utils/PHPMailer-master/PHPMailerAutoload.php';
require_once('utils/UtilsFunctions.php');

/*
getUser http://localhost:8888/PAYME/PaymeWebService.php?methodName=getUser&email=osjobu@gmail.com&password=12345
saveUser http://localhost:8888/PAYME/PaymeWebService.php?methodName=saveUser&email=osjobu@gmail.com&name=Oscar&lastname=Busio&password=12345
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
	
	/**
	 * * Inserta un usuario en la tabla users.
	 * Valida que los campos sean correctos.
	 *  @param string  email
	 *  @param string  name
	 *  @param string  lastname
	 *  @param string  password
	 */
	public function saveUser(){
		$email = utf8_encode($_REQUEST['email']);
		$name = utf8_encode($_REQUEST['name']);
		$lastname = utf8_encode($_REQUEST['lastname']);
		$password = utf8_encode($_REQUEST['password']);
		$createdon = date("Y-m-d H:i:s");
		$activation_code = CodeGenerator::activationAccountCodeGenerator($email.$name.$lastname.$createdon);
		$active = 0;			
		
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(UtilsFunctions::validEMail($email) && UtilsFunctions::validUserData($name, $lastname, $password)){
			$userDao = UserDao::Instance();
			$saveUserResult = $userDao->saveUser($email,$name,$lastname,$password,$activation_code,$createdon,$active);
			
			if($saveUserResult['rowsInserted'] > 0){
				UtilsFunctions::sendMail($email,$name." ".$lastname, "Activación cuenta", "headMessage", $activation_code, "footerMessage");
				$response->success = true;
				$response->message = "Se guardo el usuario correctamente.";
			}else{
				$response->success = false;
				$response->message = $saveUserResult['error'];
			}
			
		}else{
			$response->success = false;
			$response->message = "Los datos del usuario no son correctos.";
		}
		echo $response->getResponseAsJSON();
	}
	
}
?>