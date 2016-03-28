<?php
/**
 *
 * @author Oscar Gascon
 *
 */
class Constants{
	//URLS
	const DOMAIN = "";
	const URL_REGISTER_CONFIRMATION_CODE = "http://localhost/PAYME/PaymeWebService.php?methodName=verifyUrlActivation&activationCode=";
	const URL_CHANGE_PASSWORD_CODE = "http://localhost/PAYME/PaymeWebService.php?methodName=ChangePassword&changePasswordCode=";
	const URL_AVISO_PRIVACIDAD = "";
	
	//Email
	const ADMIN_EMAILS_FROM = "ogascon@getsir.mx";
	const EMAIL_HOST = "ozer.artehosting.com.mx";
	const EMAIL_PASSWORD = '$GmG3ts1mx';
	const SET_FROM_NAME = 'Oscar Gascon';
	
	
	
	const SUBJECT_EMAIL = "Confirmación de registro Kit SGDP";
	const SUBJECT_EMAIL_CHANGE_PASSWORD = "Solicitud de cambio de contraseña Kit SGDP";
	const HEAD_TEXT = "Para completar el registro al Kit SGDP, por favor visite la siguiente URL.<br/><b>Nota</b>: Asegúrese de no agregar espacios adicionales:";
	const HEAD_TEXT_CHANGE_PASSWORD = "Para cambiar tu contraseña para el Kit SGDP, por favor visita la siguiente URL.<br/><b>Nota</b>: Asegúrese de no agregar espacios adicionales:";
	const FOOTER_TEXT = "<br/>Si tiene algún problema por favor póngase en contacto con un miembro de nuestro personal de apoyo nyce@nyce.org.mx o al número telefónico 5395 0777.";

}