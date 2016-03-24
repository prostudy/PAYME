<?php
/*
LAS FUNCIONES DE ESTE SCRIPT SON INVOCADAS UNICAMENTE POR EL SCRIPT PRINCIPAL TravelsWebService.PHP
*/

class GenericResponse{
	
	public $sucess;
	public $message;
	public $items = null;
	public $isJSONP = false;
	
	public $callback ="";
	
	
	public function GenericResponse($success,$isJSONP=false,$callback="",$message=""){
		$this->success = $success;
		$this->message = $message;
		$this->isJSONP = $isJSONP;
		$this->callback = $callback;
	}
	
	public function setItems($itms){
		$this->items = $itms;
	}
	
	public function getResponseAsJSON(){
		//print_r($this->items);
		$response = ($this->isJSONP) ? $this->callback."(" : "" ;
		$response .= json_encode(array('success' => $this->success,
									'message'=>$this->message,
									'items'=>$this->items,
									/*'town'=>$this->town,*/
									/*'rankingInfo' => array('ranking'=>$this->ranking, 'facebookId'=>$this->facebookid , 'comment'=>$this->comment,'townId'=>$this->town))
									 */ 
									 ));
		$response.=($this->isJSONP) ? ")" :"";
		return $response;
	}
	
} 
?>