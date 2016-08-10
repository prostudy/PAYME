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
			$urlResponseReminderCode = Constants::URL_RESPONSE_REMINDER_CODE.$remainder['response_code'];
			$body = '';
			if( strlen( trim($remainder['customtext'] ) ) > 0  ) {
				$body = $remainder['customtext'];
				$body .= "<br/>You can reply to this message on the following link:<br/>".$urlResponseReminderCode."<br/><br/>Datos:<br/><b>CLABE:</b>".$remainder['clabe']."<br/><b>CARD:</b>".$remainder['card']."<br/><b>PAYPAL:</b>".$remainder['paypal']."<br/><b>PHONE:</b>".$remainder['phone']."<br/><b>MORE:</b>".$remainder['text_account'];
			}else{
				$body = $remainder['text']." <b>$".$remainder['cost']."</b> to <b>".$remainder['userName']."</b> the reason is <b>".$remainder['description']."</b><br/>You can reply to this message on the following link:<br/>".$urlResponseReminderCode."<br/><br/>Datos:<br/><b>CLABE:</b>".$remainder['clabe']."<br/><b>CARD:</b>".$remainder['card']."<br/><b>PAYPAL:</b>".$remainder['paypal']."<br/><b>PHONE:</b>".$remainder['phone']."<br/><b>MORE:</b>".$remainder['text_account'];
			}
			
			if(UtilsFunctions::sendMail($remainder['email'],$remainder['emailuser'], $remainder['clientName'], Constants::SUBJECT_EMAIL_REMAINDER, "Hello: ".$remainder['clientName'], $body, Constants::FOOTER_EMAIL_REMAINDER)){
				$cronDao->updateRemainderAsSend( $remainder['idreminders']);
				error_log("\nSe ha enviado un correo al email (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}else{
				error_log("\nNo se pudo enviar recordatorio por email al (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}
		}
	}
}

CronRemainders::readTableReminders(false);
?>