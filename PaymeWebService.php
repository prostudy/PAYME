<?php
/**
 * Class is responsible for connecting get data for PAYME
 * @author josafatbusio@gmail.com
 *
 * */
// Include database class
require_once 'connection/Database.php';
require_once 'utils/Constants.php';
require_once 'dao/tutorial.php';
require_once 'dao/UserDao.php';
require_once('utils/GenericResponse.php');
require_once('utils/CodeGenerator.php');
require_once 'utils/PHPMailer-master/PHPMailerAutoload.php';
require_once('utils/UtilsFunctions.php');

/*
getUser http://localhost:8888/PAYME/PaymeWebService.php?methodName=getUser&email=osjobu@gmail.com&password=12345
saveUser http://localhost:8888/PAYME/PaymeWebService.php?methodName=saveUser&email=osjobu@gmail.com&name=Oscar&lastname=Busio&password=12345
verifyUrlActivation http://localhost:8888/PAYME/PaymeWebService.php?methodName=verifyUrlActivation&activationCode=45E3BEE1A5C06426C8BB87F15ECA6788
requestChangePassword http://localhost:8888/PAYME/PaymeWebService.php?methodName=requestChangePassword&email=osjobu@gmail.com

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
			$response->success = true;
			$response->message = "Se encontro usuario.";
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
				$urlActivation = Constants::URL_REGISTER_CONFIRMATION_CODE.$activation_code;
				UtilsFunctions::sendMail($email,$name." ".$lastname, "Activación cuenta", "headMessage", $urlActivation, "footerMessage");
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
	
	
	/**
	 * Busca en la base de datos el codigo de activación
	 * @param string $activationCode
	 * @return user Regresa el usuario encontrado con dicho codigo de activación
	 */
	public function verifyUrlActivation(){
			$activationCode = utf8_encode($_REQUEST['activationCode']);
			$userDao = UserDao::Instance();
			$user = $userDao->verifyUrlActivation($activationCode);
			$items = array();
			$response = new GenericResponse(true,$this->isJSONP,$this->callback);
			if(is_array($user)){
				$activation_date = date("Y-m-d H:i:s");
				$user['updateResult'] = $userDao->setActiveAccount($user,$activation_date);
				$items['user'] = $user;
				$response->setItems($items);
				$response->success = true;
				$response->message = "El codigo de activación se encontro y se activo correctamente la cuenta de usuario:".$user['name'];
			}else{
				$response->success = false;
				$response->message = "No se encontro codigo de activación.";
		}
		echo $response->getResponseAsJSON();
	}
	
	
	/**
	 * Recibe la petición de cambio de contraseña y envia correo electronico en caso de que el usuario sea valido
	 * @param email
	 */
	public function requestChangePassword(){
		$email = utf8_encode($_REQUEST['email']);
		$resetPasswordCode = CodeGenerator::activationAccountCodeGenerator($email.date("Y-m-d H:i:s"));
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		
		if(UtilsFunctions::validEMail($email)){
			$userDao = UserDao::Instance();
			$result = $userDao->updateResetPasswordCodeforValidUserActive($email,$resetPasswordCode);
			if($result['rowsUpdated'] > 0){
				$urlResetPassword = Constants::URL_CHANGE_PASSWORD_CODE.$resetPasswordCode;
				UtilsFunctions::sendMail($email,"", "Cambio de Password", "Para cambiar tu contraseña utiliza a la siguiente url", $urlResetPassword, "Si no solicitaste cambio de contraseña, omite este mensaje");
				$response->success = true;
				$response->message = "Se solicito el cambio de password.";
			}else{
				$response->success = false;
				$response->message = "No se encontro correo electrónico valido para cambio de password.";
			}
		}else{
			$response->success = false;
			$response->message = "El correo electrónico no tiene un formato valido.";
		}
		echo $response->getResponseAsJSON();
	}
	
}
?>