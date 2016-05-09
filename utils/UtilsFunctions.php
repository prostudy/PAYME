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
	
	
	
	public static function sendMail($to,$name,$subject,$headMessage,$urlActivacion,$footerMessage){
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
			$mail->Body    = "<!doctype html>
			     <html>
			      <head>
			         <meta name='viewport' content='width=device-width, initial-scale=1.0'>
			      </head>
			      <body>
			        <div id='section1'>
			          <div style='font-family:verdana;font-size:12px;color:#555555;line-height:14pt'>
			              <div style='width:590px'>
			                <div style='background:url(http://dps.grupoiasa.com/iasaEntitlement/utils/img/topMail.png) no-repeat;width:100%;min-height:75px;display:block'>
			                  <div style='background-repeat-y:no-repeat;background-size:contain;margin: 0px 14px 0px 16px;background-size:contain;overflow:hidden;background-image:url(http://pmstudykit.com/kitsgdp/images/pleca.png);min-height:75px;'>
			                  <a href='https://www.facebook.com/maestrosdelmedia/?fref=nf' target='_blank'>
			                    <img src='https://scontent-dfw1-1.xx.fbcdn.net/hprofile-xpf1/v/t1.0-1/p160x160/1914912_506480059531666_6066849200975198148_n.png?_nc_eui=ARill5Cymg2sVEH8siD_hTY3IcFwsQ8Bcbrsi-oMDTOQ-K_iuld5og&oh=af288ae28f7173faba5cb1da585bc732&oe=577B0A5A' alt='nyce' style='border: none;
padding: 1em;' target=_blank></a>
	
								<!--<a href='http://www.nyce.org.mx/' target='_blank'>
				                    <img src='http://www.nyce.org.mx/templates/theme_full/images/header-object.png' alt='nyce' style='border: none;
padding: 1.5em;
float: right;' target=_blank>
								</a>-->
			                  </div>
			                </div>
		
			                <div style='background:url(http://dps.grupoiasa.com/iasaEntitlement/utils/img/contentMail.png) repeat-y;width:100%;display:block'>
			                  <div style='padding-left:50px;padding-right:50px;padding-bottom:1px'>
			                  <div style='border-bottom:1px solid #ededed'>
			                  </div>
			                  <div style='margin:20px 0px;font-size:20px;line-height:30px'>
			                   ".$subject."
				                   </div>
				                   <div style='margin-bottom:30px'>
				                   <div>
				                   $headMessage
				                   </div>
				                   <br>
				                   <div style='margin-bottom:20px'>
				                   $urlActivacion<br>
				                   $footerMessage
				                   </div>
				                   </div>
				                   <div>
				                   <span></span>
				                   <div style='border-bottom:1px solid #ededed'>
				                   </div>
				                   </div>
				                   	
				                   <div style='margin:20px 0'>
				                   Plataforma digital para el aprendizaje de Marketing Digital, SEO, Multimedia, Diseño Web, Apps y mucho mas.</b> <a href='https://www.facebook.com/maestrosdelmedia/?fref=nf'>https://www.facebook.com/maestrosdelmedia/?fref=nf</a>
				                   </div>
				                   <div style='margin:10px 5px;display:inline-block'></div>
				                   	
				                   <div style='border-bottom:1px solid #ededed'></div>
				                    
				                   	
				                   <div style='font-size:9px;color:#707070'>
				                   <br>No respondas a este mensaje.<br>
				                   Fundación en diciembre de 2015
				                   <br>Ver la <a href=http://www.maestrosdelmedia.com target=_blank>Política de privacidad</a>
				                   </div></div></div><div class='yj6qo'></div>
				                   	
				                   <div style='background:url(http://dps.grupoiasa.com/iasaEntitlement/utils/img/footerMail.png) no-repeat;width:100%;min-height:50px;display:block' class='adL'></div></div>
				                   </div>
				                   </div>
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
