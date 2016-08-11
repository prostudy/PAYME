<?php
/**
 * Class is responsible for send reminders 
 * @author josafatbusio@gmail.com
 *
 * */
// Include database class
require_once 'connection/Database.php';
require_once 'utils/Constants.php';
require_once 'dao/CronRemainderDao.php';
require_once 'utils/PHPMailer-master/PHPMailerAutoload.php';
require_once('utils/UtilsFunctions.php');

class CronRemainders {
	public static function readTableReminders($idReminderToSendNow){
		$cronDao = CronRemainderDao::Instance();
		$remainders = $cronDao->getRemainders($idReminderToSendNow);

		foreach ($remainders as $remainder){
			$headClient = "Hola: <b>".$remainder['clientName']."</b> tienes una deuda por pagar."; //Encabezado para el cliente deudor
			$headUser = "Envio de recordatorio de adeudo exitoso a <b>".$remainder['clientName']."</b>."; //Encabezado para el usuario al que deben
			$urlResponseReminderCode = Constants::URL_RESPONSE_REMINDER_CODE.$remainder['response_code'];
			
			$body = '';
			if( strlen( trim($remainder['customtext'] ) ) > 0  ) {
				$body = $remainder['customtext'];
			}else{
				$body = $remainder['text']." <b>$".$remainder['cost']."</b> to <b>".$remainder['userName']."</b> the reason is <b>".$remainder['description']."</b>";
			}
			
			if(UtilsFunctions::sendMailReminder($remainder['email'], $remainder['clientName'], Constants::SUBJECT_EMAIL_REMAINDER, $headClient, $body,$urlResponseReminderCode)){
				$cronDao->updateRemainderAsSend( $remainder['idreminders']);
				error_log("\nSe ha enviado un correo al email (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}else{
				error_log("\nNo se pudo enviar recordatorio por email al (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}
			
			if(UtilsFunctions::sendMailReminderUser($remainder['emailuser'], $remainder['nameuser'], Constants::SUBJECT_EMAIL_REMAINDER, $headUser, $body)){
				$cronDao->updateRemainderAsSend( $remainder['idreminders']);
				error_log("\nSe ha enviado copia de un correo al email (".$remainder['emailuser'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}else{
				error_log("\nNo se pudo enviar copia de recordatorio por email al (".$remainder['emailuser'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}
		}
	}
}

CronRemainders::readTableReminders(false);
?>