<?php
/**
 * Class is responsible for send reminders
 * @author josafatbusio@gmail.com
 *
 * */
// Include database class
require_once 'connection/Database.php';
require_once 'utils/Constants.php';
require_once 'dao/CronRemindeDao.php';
require_once 'utils/PHPMailer-master/PHPMailerAutoload.php';
require_once('utils/UtilsFunctions.php');

class CronReminders {
	public static function readTableReminders(){
		$cronDao = CronRemindeDao::Instance();
		$reminders = $cronDao->getReminders();

		foreach ($reminders as $reminder){
			if(UtilsFunctions::sendMail($reminder['email'], $reminder['nameUser'], "Recordatorio pago", $reminder['description'], $reminder['text'], "Texto footer")){
				$cronDao->updateRemienderAsSend( $reminder['idreminders']);
				//error_log("\nSe ha enviado un correo al email (".$reminder['email'].") reminder con id:".$reminder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::URL_ERROR_LOGS);
			}else{
				error_log("\nNo se pudo enviar recordatorio por email al (".$reminder['email'].") reminder con id:".$reminder['idreminders']." ". date("Y-m-d H:i:s"), 3,Constants::URL_ERROR_LOGS);
			}
		}
	}
}

CronReminders::readTableReminders();
?>