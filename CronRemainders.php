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
	public static function readTableReminders(){
		$cronDao = CronRemainderDao::Instance();
		$remainders = $cronDao->getRemainders();

		foreach ($remainders as $remainder){
			$body = $remainder['text']." <b>$".$remainder['cost']."</b> to <b>".$remainder['userName']."</b> the reason is <b>".$remainder['description']."</b>";
			if(UtilsFunctions::sendMail($remainder['email'], $remainder['clientName'], Constants::SUBJECT_EMAIL_REMAINDER, "Hello: ".$remainder['clientName'], $body, Constants::FOOTER_EMAIL_REMAINDER)){
				$cronDao->updateRemainderAsSend( $remainder['idreminders']);
				error_log("\nSe ha enviado un correo al email (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}else{
				error_log("\nNo se pudo enviar recordatorio por email al (".$remainder['email'].") reminder con id:".$remainder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::FILE_CRON_REMAINDERS_ERRORS);
			}
		}
	}
}

CronRemainders::readTableReminders();
?>