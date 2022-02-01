<html>
	<head>
		<title>Spirthday Birthday!</title>		
	</head>

	<body>

		<?php 

		// Exit if not being accessed via cron
		if (php_sapi_name() !='cli') exit;

		// Get PHPMailer stuff
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;
		require 'includes/PHPMailer/src/Exception.php';
		require 'includes/PHPMailer/src/PHPMailer.php';
		require 'includes/PHPMailer/src/SMTP.php';

		// Curl to get JSON data
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'INSERT_URL');
		$result = curl_exec($ch);
		curl_close($ch);
		$brc = json_decode($result);
		
		// Variables
		$spirthday_birthday_list = array();
		$birthday_list = array();
		$spirthday_list = array();
		$all_emails = array();
		$recipient_emails = array();

		// Date Variables
		date_default_timezone_set('America/New_York');
		$today = date("n/j");
		//$today = '2/21'; // Testing date
		$pretty_date = date("F jS");

		// Create allEmails 
		foreach ($brc as $user) {
			if (isset($user->Email)) {
				$all_emails[] = $user->Email;
			}
		}

		// Create spirthday_list and birthday_list
		$c = 0; // Counter
		foreach ($brc as $user) {
			// Add Spirthdays
			// Have to remove the last 3 char of 'Baptism Date' so it matches the 'Birthday' and 'Today' date format
			$baptism_short = substr($user->Baptism, 0, -3);
			if ($baptism_short == $today) {
				$name = explode(", ", $user->Name);
				$pretty_name = $name[1] . " " . $name[0];
				$spirthday_list[$c]['name'] = $pretty_name;
				$spirthday_list[$c]['email'] = $user->Email;
				$spirthday_list[$c]['phone'] = $user->Phone;
				$c++;
			}
		}
		$c = 0; // Counter
		foreach ($brc as $user) {
			// Add Birthdays
			if ($user->Birthday == $today) {
				$name = explode(", ", $user->Name);
				$pretty_name = $name[1] . " " . $name[0];
				$birthday_list[$c]['name'] = $pretty_name;
				$birthday_list[$c]['email'] = $user->Email;
				$birthday_list[$c]['phone'] = $user->Phone;
				$c++;
			}
		}

		// Get Recipient email list
		$no_send_emails = array();

		for ($i = 0; $i < count($spirthday_list); $i++) {
			if (isset($spirthday_list[$i]['email'])) {
				$no_send_emails[] = $spirthday_list[$i]['email'];
			}
		}
		for ($i = 0; $i < count($birthday_list); $i++) {
			if (isset($birthday_list[$i]['email'])) {
				$no_send_emails[] = $birthday_list[$i]['email'];
			}
		}
		if ($no_send_emails === NULL) {
			$recipient_emails = $all_emails;
		}
		else {
			$recipient_emails = array_diff($all_emails, $no_send_emails);
		}


		// Output Email msg
		$msg_spirthday = '';
		$msg_birthday = '';

		// Begin assembling Spirthday msg
		$msg_spirthday .= '<tr class="email-section" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
		<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #fff; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; padding: 16px 16px 0em;">
			<div class="heading-section spirthday" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #ECFBFF; padding: 0; border-radius: 8px; border: 1px solid #D4F6FF;">
			<h2 class="subheading" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; display: inline-block; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 22px; color: #00A3AD; font-weight: 700; margin: 0 0 16px !important; position: relative; background: #D4F6FF;
			display: block;	padding: 12px 16px; border-radius: 8px 8px 0 0;">&#128519; Happy Spirthday!</h2>';

		// IF ANY Spirthdays
		if (count($spirthday_list) > 0) {
			
			for ($i = 0; $i < count($spirthday_list); $i++) {

				$msg_spirthday .= '<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; font-size: 18px; font-weight: bold; line-height: 24px; padding: 0 16px; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif;">' . $spirthday_list[$i]['name'];

				if (!empty($spirthday_list[$i]['phone'])) {

					$msg_spirthday .= '<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"><span class="phone" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 18px; font-weight: 400;">' . $spirthday_list[$i]['phone'] . '</span>';
				}
				else {
					$msg_spirthday .= '<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"><span class="phone" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 18px; font-weight: 400;">No phone number listed</span>';
				}
				$msg_spirthday .= '</p>';
			}
		}
		// ELSE NO Spirthdays
		else {
			$msg_spirthday .= '<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; font-size: 18px; line-height: 24px; padding: 0 16px; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif">No spirthdays today.</p>';
		}
		$msg_spirthday .= '</div></td></tr>';

		
		// Begin assembling birthday msg
		$msg_birthday .= '<tr class="email-section" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
		<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #fff; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; padding: 16px 16px 0em;">
			<div class="heading-section birthday" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #FFF2EC; padding: 0; border-radius: 8px; border: 1px solid #FFE2D4;">
			<h2 class="subheading" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; display: inline-block; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 22px; color: #DC6849; font-weight: 700; margin: 0 0 16px !important; position: relative; background: #FFE2D4;
			display: block;	padding: 12px 16px; border-radius: 8px 8px 0 0;">&#127881; Happy Birthday!</h2>';

		// IF ANY Birthdays
		if (count($birthday_list) > 0) {

			for ($i = 0; $i < count($birthday_list); $i++) {

				$msg_birthday .= '<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; font-size: 18px; font-weight: bold; line-height: 24px; padding: 0 16px; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif;">' . $birthday_list[$i]['name'];

				if (!empty($birthday_list[$i]['email'])) {

					$msg_birthday .= '<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"><span class="phone" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 18px; font-weight: 400;">' . $birthday_list[$i]['phone'] . '</span>';
				}
				else {
					$msg_birthday .= '<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"><span class="phone" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 18px; font-weight: 400;">No phone number listed</span>';
				}
				$msg_birthday .= '</p>';
			}
		}
		// ELSE NO Birthdays
		else {
			$msg_birthday .= '<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #104263; font-size: 18px; line-height: 24px; padding: 0 16px; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif;">No birthdays today.</p>';
		}
		$msg_birthday .= '</div></td></tr>';



		$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		
		<head></head>
		
		<body width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #f1f1f1; color: #104263; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 15px; font-weight: 400; height: 100% !important; line-height: 1.8; margin: 0 auto !important; mso-line-height-rule: exactly; padding: 0 !important; width: 100% !important;">
		<span style="display:none !important;">Send some encouragment with a call or text.</span>
		  <center style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f1f1f1; width: 100%;">
			<div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0 auto; padding: 16px; max-width: 600px;" class="email-container">
			  <!-- BEGIN BODY -->
			  <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; border-spacing: 0 !important; margin: 16px auto 0 auto !important; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; table-layout: fixed !important;">
				<tbody>
					<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
						<td style="background: #008B94; border-radius: 3px 3px 0 0;">
							<p style="padding: 8px 16px; margin: 0; color: #f3e7cc; font-size: 18px; font-weight: 700; text-align: center;">BRCOC</p>
						</td>
					</tr>
				  <tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td valign="middle" class="hero" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-radius: 0; background-color: #00a3ad; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; position: relative;">
					  <table style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; border-spacing: 0 !important; margin: 0 !important; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; table-layout: fixed !important;">
						<tbody>
						  <tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
							<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important;">
							  <div class="text" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 0 16px;">
								<h1 style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #f3e7cc; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 26px; font-weight: 800; line-height: 28px; margin-bottom: 0; margin-top: 0; padding: 16px 0; text-transform: uppercase;">Spirthday<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">Birthday<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">Alert!</h1>
							  </div>
							</td>
							<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; text-align: right; width: 100%;">
							  <div class="text" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 10px 12px 12px; text-align: center; background: #008B94; border-radius: 8px; display: inline-block;  margin-right: 16px;">
								<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #f3e7cc; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 22px; font-weight: 400; line-height: 28px; margin-bottom: 0; margin-top: 0;">Today<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">'. $today . '</9>
							  </div>
							</td>
						  </tr>
						</tbody>
					  </table>
					</td>
				  </tr>
				  ' . $msg_spirthday . '
				  ' . $msg_birthday . '
				  <tr class="spacer-section" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #fff; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; border-radius: 0 0 3px 3px; text-align: center; padding: 32px 16px; font-size: 18px; line-height: 28px;"><strong>Send some encouragement</strong> <br>with a call or text &#128330;&#65039;</td>
				  </tr>
				  <tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td class="footer logo" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; padding: 2.5em 16px 0em 16px; text-align: center;">
					  <h3 style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-weight: normal; line-height: 16px; margin: 0; margin-top: 0; color: #888;"><a href="#" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #888; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif; font-size: 14px; font-weight: normal; text-decoration: none; text-transform: none;">&copy; Blue Ridge Church of Christ</a></h3>
					</td>
				  </tr>
				  <tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td class="footer inquire" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; padding: 4px 16px 16px 16px; text-align: center;">
					  <p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #888; font-size: 14px; font-weight: normal; line-height: 21px; margin: 0; font-family: system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen,Ubuntu,Cantarell,\'Droid Sans\',\'Helvetica Neue\',\'Fira Sans\',sans-serif;">If you have any questions about this service please email landon.dorrier@gmail.com</p>
					</td>
				  </tr>
				</tbody>
			  </table>
			</div>
		  </center>
		</body>
		
		</html>';


		echo $msg;


		// Send Mail notification
		if (!empty($spirthday_list) || !empty($birthday_list)) {

			// Instantiation and passing `true` enables exceptions
			$mail = new PHPMailer(true);

			try {
				//Server settings
				$mail->SMTPDebug = 2; // Enable verbose debug output
				$mail->isSMTP(); // Set mailer to use SMTP
				$mail->Host       = 'INSERT_HOST'; // Specify main and backup SMTP servers
				$mail->SMTPAuth   = true; // Enable SMTP authentication

				$mail->Username   = 'INSERT_EMAIL_ADDRESS'; // SMTP username
				$mail->Password   = 'INSERT_PASSWORD'; // SMTP password

				$mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
				$mail->Port       = PORT; // TCP port to connect to

				//Recipients
				$mail->setFrom('INSERT_EMAIL_ADDRESS', 'INSERT_SENDER_NAME'); // Name is optional
				
				foreach ($recipient_emails as $address) {
					if ($address !== '' && $address !== null) {
						$mail->addBCC($address);
					}
				}

				// Content
				$mail->isHTML(true); // Set email format to HTML
				$mail->Subject = '=?utf-8?Q?=F0=9F=98=87_Spirthday_Birthday_Alert_=F0=9F=8E=89?=';
				$mail->Body    = $msg;
				$mail->AltBody = strip_tags($msg);

				$mail->send();
				echo 'Message has been sent';
			} catch (Exception $e) {
				echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
		}
	
		?>

    </body>
</html>
