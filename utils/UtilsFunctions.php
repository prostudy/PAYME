<?php 
//include_once('PHPMailer/class.phpmailer.php');
//include("PHPMailer/class.smtp.php");

class UtilsFunctions{
	
	// found at http://www.denbag.us/2013/09/perfect-php-email-regex.html
	public static function validEMail($email) {
		$regex = '/([a-z0-9_]+|[a-z0-9_]+\.[a-z0-9_]+)@(([a-z0-9]|[a-z0-9]+\.[a-z0-9]+)+\.([a-z]{2,4}))/i';
		return preg_match($regex, $email);
	}
	
	
	public static function validUserData($name,$lastname,$password) {
		return    strlen(trim($name)) > 0 ? strlen(trim($lastname)) > 0 ? strlen(trim($password)) > 0 ? true : false : false : false ;
	}
	
	public static function validUserDataForUpdate($name,$lastname) {
		return    strlen(trim($name)) > 0 ? strlen(trim($lastname)) > 0 ?  true : false : false ;
	}
	
	
	
	public static function sendMail($to,$Bcc,$name,$subject,$headMessage,$urlActivacion,$footerMessage){
		try{
			
			$mail = new PHPMailer;
			//$mail->SMTPDebug = 3;                               // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = Constants::EMAIL_HOST;              	  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = Constants::ADMIN_EMAILS_FROM;       // SMTP username
			$mail->Password = Constants::EMAIL_PASSWORD;          // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;  
	
			$mail->setFrom(Constants::ADMIN_EMAILS_FROM, Constants::SET_FROM_NAME);
			$mail->addAddress($to, $name);     // Add a recipient
			if($Bcc){
				$mail->addBCC($Bcc);
			}
			$mail->FromName = utf8_decode( Constants::SET_FROM_NAME);
			$mail->isHTML(true);                                  // Set email format to HTML
	
			//$mail->AltBody = 'Favor de activa tu cuenta con el siguiente enlace ';
			$mail->Subject = utf8_decode($subject);
			$mail->Body    = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
								<html>
								  <head>
								    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
								    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
								    <title>Bienvenido a Payme</title>
								    <style type='text/css'>
								      body {margin: 10px 0; padding: 0 10px; background: #DCDFE0; font-size: 13px;}
								      table {border-collapse: collapse;}
								      td {font-family: arial, sans-serif; color: #333333;}
								
								      @media only screen and (max-width: 480px) {
								        body,table,td,p,a,li,blockquote {
								          -webkit-text-size-adjust:none !important;
								        }
								        table {width: 100% !important;}
								
								        .responsive-image img {
								          height: auto !important;
								          max-width: 100% !important;
								          width: 100% !important;
								        }
								      }
								    </style>
								  </head>
								  <body>
								    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
								      <tr>
								        <td>
								          <table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF'>
								            <tr>
								              <td bgcolor='#F8F9F9' style='font-size: 0; line-height: 0; padding: 0 10px;' height='140' align='center' class='responsive-image'>
								                <img src='http://getsir.mx/payme/logopayme.png' alt='' />
								              </td>
								            </tr>
								            <tr><td style='font-size: 0; line-height: 0;' height='30'>&nbsp;</td></tr>
								            <tr>
								              <td style='padding: 10px 10px 20px 10px;'>
								                <div style='font-size: 20px; text-align: center;'>$subject</div>
								                <br />
								                <div>
								                  <h2 style='text-align: center; padding: 10px 10px 10px 10px'>$headMessage</h2>
								                  <p style='text-align: center; font-size: 20px;'></p>
								
								                  <div style='margin-bottom:20px' style='text-align: center;'>

								                 <p style='text-align: center;'>$urlActivacion<br><br><br>
								                 <p>$footerMessage</p>
								                 </div>
								                </div>
								              </td>
								            </tr>
								              <td bgcolor='#083D5F'>
								                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
								                  <tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
								                  <tr>
								                    <td style='padding: 0 10px; color: #FFFFFF;     text-align: center;'>
								                     <a href='http://paymeapp.com.mx/' style='color: white;'>Payme</a> 
								                    </td>
								                  </tr>
								                  <tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
								                </table>
								              </td>
								            </tr>
								          </table>
								        </td>
								      </tr>
								    </table>
								  </body>
								</html>";
	
				                   	if(!$mail->send()) {
				                   	//echo 'Message could not be sent.';
				                   	//echo 'Mailer Error: ' . $mail->ErrorInfo;
				return false;
			} else {
				//echo 'Message has been sent';
				return true;
			}
			} catch (phpmailerException $e) {
				echo $e->errorMessage(); //error messages from PHPMailer
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		
		
		public static function sendMailReminder($to,$name,$subject,$headMessage,$body,$urlResponse){
			try{
					
				$mail = new PHPMailer;
				//$mail->SMTPDebug = 3;                               // Enable verbose debug output
				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = Constants::EMAIL_HOST;              	  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = Constants::ADMIN_EMAILS_FROM;       // SMTP username
				$mail->Password = Constants::EMAIL_PASSWORD;          // SMTP password
				$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 587;
		
				$mail->setFrom(Constants::ADMIN_EMAILS_FROM, Constants::SET_FROM_NAME);
				$mail->addAddress($to, $name);     // Add a recipient
				
				$mail->FromName = utf8_decode( Constants::SET_FROM_NAME);
				$mail->isHTML(true);                                  // Set email format to HTML
		
				//$mail->AltBody = 'Favor de activa tu cuenta con el siguiente enlace ';
				$mail->Subject = utf8_decode($subject);
				$mail->Body    = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
								<html>
								  <head>
								    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
								    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
								    <title>Deuda en espera de pago</title>
								    <style type='text/css'>
								      body {margin: 10px 0; padding: 0 10px; background: #DCDFE0; font-size: 13px;}
								      table {border-collapse: collapse;}
								      td {font-family: arial, sans-serif; color: #333333;}
								
								      @media only screen and (max-width: 480px) {
								        body,table,td,p,a,li,blockquote {
								          -webkit-text-size-adjust:none !important;
								        }
								        table {width: 100% !important;}
								
								        .responsive-image img {
								          height: auto !important;
								          max-width: 100% !important;
								          width: 100% !important;
								        }
								      }
								    </style>
								  </head>
								  <body>
								    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
								      <tr>
								        <td>
								          <table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF'>
								            <tr>
								              <td bgcolor='#F8F9F9' style='font-size: 0; line-height: 0; padding: 0 10px;' height='140' align='center' class='responsive-image'>
								                <img src='http://getsir.mx/payme/logopayme.png' alt='' />
								              </td>
								            </tr>
								            <tr><td style='font-size: 0; line-height: 0;' height='30'>&nbsp;</td></tr>
								            <tr>
								              <td style='padding: 10px 10px 20px 10px;'>
								                <div style='font-size: 20px; text-align: center;'>$headMessage</div>
								                <br />
								                <div>
								                  <!--<p style='text-align: center; padding: 10px 10px 10px 10px font-size: 30px;'>Tienes una deuda pendiente con el usuario <b>Nombre del Usuario</b></p>-->
								                  <p style='text-align: center; font-size: 20px;'></p>
								
								                  <div style='margin-bottom:20px' style='text-align: center;'>

								                 <p style='text-align: center;margin: 10px 40px 10px 40px;padding: 20px;background: #E8E8E8;'>
								                 $body<br>
								                 </div>
								                 <p style='text-align: center;'>Responde a este mensaje dando click en el siguiente enlace:</p>
								                 <table class='body-action' align='center' width='100%' cellpadding='0' cellspacing='0'>
							                      <tr>
							                        <td align='center'>
							                          <div>
							                            <a href='$urlResponse' class='button button--green'>Responder a esta deuda</a>
							                          </div>
							                        </td>
							                      </tr>
							                    </table>
								                </div>
								              </td>
								            </tr>
								              <td bgcolor='#083D5F'>
								                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
								                  <tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
								                  <tr>
								                    <td style='padding: 0 10px; color: #FFFFFF;     text-align: center;'>
								                     <p style='font-size: 18px;'><strong>'Que te pague quien te deba'</strong></p>
								                     <p> Payme es una plataforma gratuita para cobrar deudas a tus clientes y amigos</p>
								                     <a href='http://paymeapp.com.mx/' style='color: white;'>Conoce Payme</a>
								                    </td>
								                  </tr>
								                  <tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
								                </table>
								              </td>
								            </tr>
								          </table>
								        </td>
								      </tr>
								    </table>
								  </body>
								</html>";
		
			if(!$mail->send()) {
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
			return false;
			} else {
			//echo 'Message has been sent';
			return true;
			}
			} catch (phpmailerException $e) {
			echo $e->errorMessage(); //error messages from PHPMailer
			} catch (Exception $e) {
			echo $e->getMessage();
			}
			}
			
			
			public static function sendMailReminderUser($to,$name,$subject,$headMessage,$body){
				try{
						
					$mail = new PHPMailer;
					//$mail->SMTPDebug = 3;                               // Enable verbose debug output
					$mail->isSMTP();                                      // Set mailer to use SMTP
					$mail->Host = Constants::EMAIL_HOST;              	  // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = Constants::ADMIN_EMAILS_FROM;       // SMTP username
					$mail->Password = Constants::EMAIL_PASSWORD;          // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;
			
					$mail->setFrom(Constants::ADMIN_EMAILS_FROM, Constants::SET_FROM_NAME);
					$mail->addAddress($to, $name);     // Add a recipient
			
					$mail->FromName = utf8_decode( Constants::SET_FROM_NAME);
					$mail->isHTML(true);                                  // Set email format to HTML
			
					//$mail->AltBody = 'Favor de activa tu cuenta con el siguiente enlace ';
					$mail->Subject = utf8_decode($subject);
					$mail->Body    = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
					<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
					<title>Deuda en espera de pago</title>
					<style type='text/css'>
					body {margin: 10px 0; padding: 0 10px; background: #DCDFE0; font-size: 13px;}
					table {border-collapse: collapse;}
					td {font-family: arial, sans-serif; color: #333333;}
					
					@media only screen and (max-width: 480px) {
					body,table,td,p,a,li,blockquote {
					-webkit-text-size-adjust:none !important;
					}
					table {width: 100% !important;}
					
					.responsive-image img {
					height: auto !important;
					max-width: 100% !important;
					width: 100% !important;
					}
					}
					</style>
					</head>
					<body>
					<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td>
					<table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF'>
					<tr>
					<td bgcolor='#F8F9F9' style='font-size: 0; line-height: 0; padding: 0 10px;' height='140' align='center' class='responsive-image'>
					<img src='http://getsir.mx/payme/logopayme.png' alt='' />
					</td>
					</tr>
					<tr><td style='font-size: 0; line-height: 0;' height='30'>&nbsp;</td></tr>
					<tr>
					<td style='padding: 10px 10px 20px 10px;'>
					<div style='font-size: 20px; text-align: center;'>$headMessage</div>
					<br />
					<div>
					<!--<p style='text-align: center; padding: 10px 10px 10px 10px font-size: 30px;'>Tienes una deuda pendiente con el usuario <b>Nombre del Usuario</b></p>-->
					<p style='text-align: center; font-size: 20px;'></p>
					
					<div style='margin-bottom:20px' style='text-align: center;'>
					
					<p style='text-align: center;margin: 10px 40px 10px 40px;padding: 20px;background: #E8E8E8;'>
					$body<br>
					</div>
					<p style='text-align: center;'></p>
					<table class='body-action' align='center' width='100%' cellpadding='0' cellspacing='0'>
					<tr>
					<td align='center'>
					<div>
					
					</div>
					</td>
					</tr>
					</table>
					</div>
					</td>
					</tr>
					<td bgcolor='#083D5F'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
					<tr>
					<td style='padding: 0 10px; color: #FFFFFF;     text-align: center;'>
					<p style='font-size: 18px;'><strong>'Que te pague quien te deba'</strong></p>
					<p> Payme es una plataforma gratuita para cobrar deudas a tus clientes y amigos</p>
					<a href='http://paymeapp.com.mx/' style='color: white;'>Conoce Payme</a>
					</td>
					</tr>
					<tr><td style='font-size: 0; line-height: 0;' height='15'>&nbsp;</td></tr>
					</table>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>
					</body>
					</html>";
			
				if(!$mail->send()) {
				//echo 'Message could not be sent.';
				//echo 'Mailer Error: ' . $mail->ErrorInfo;
				return false;
				} else {
				//echo 'Message has been sent';
				return true;
				}
				} catch (phpmailerException $e) {
				echo $e->errorMessage(); //error messages from PHPMailer
				} catch (Exception $e) {
				echo $e->getMessage();
				}
				}
}
?>
