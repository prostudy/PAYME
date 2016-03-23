<?php
require_once 'tutorial.php';
require_once('./GenericResponse.php');

/*
http://localhost/PAYME/TravelsWebService.php?methodName=getUserByCredentials&user=oscar&password=asdad
*/

$controllerObject = new TravelsWebService($_REQUEST['methodName'],
									   isset($_REQUEST['callback']),
									   isset($_REQUEST['callback']) ? $_REQUEST['callback']:"");


class TravelsWebService {
	
	public $isJSONP = false;
	
	public $callback = "";
	
	public function TravelsWebService($methodName,$appKey,$isJSONP=false,$callback=""){
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
	public function getUserByCredentials(){
		$user = utf8_encode($_REQUEST['user']);
		$password = utf8_encode($_REQUEST['password']);

		//$travelsDao = new TravelsDao();
		//$userInfo = $travelsDao->getUserByCredentials($user,$password);
		$items = array();

		$userInfo = simpleDataBase();
		/*echo "<pre>";
		print_r($userInfo);
		echo "</pre>";*/
		$response = new GenericResponse(true,$this->isJSONP,$this->callback);
		if(count( $userInfo ) > 0 ){
			//$document = array_values($document);
			$items['content'] = $userInfo;
			$response->setItems($items);
		}else{
			$response->success = false;
			$response->message = "No se encontro usuario.";
		}
		//print_r($userInfo);
		echo $response->getResponseAsJSON();
	}

}
?>