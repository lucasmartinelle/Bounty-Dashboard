<?php
    namespace Utils;

    require 'PHPMailer/PHPMailerAutoload.php';
    use PHPMailer;

    class Sender {
        private $_username;
        private $_email;
        private $_url;

        public function __construct($username, $email, $url){
            $this->setUsername($username);
            $this->setEmail($email);
            $this->setUrl($url);
        }

        private function setUsername($username){
            if(is_string($username)){
                $this->_username = $username;
            }
        }

        private function setEmail($email){
            if(is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $this->_email = $email;
            }
        }

        private function setUrl($url){
            if(is_string($url)){
                $this->_url = $url;
            }
        }

        private function username(){
            return $this->_username;
        }

        private function email(){
            return $this->_email;
        }

        private function url(){
            return $this->_url;
        }

        public function validationCompte(){
            $mail = new PHPMailer(True);
    
            $mail->isSMTP();
            $mail->SMTPAuth = EMAIL_SMTP_ACTIVE_AUTH;
            if(EMAIL_DEBUG != false){
                $mail->SMTPDebug = EMAIL_DEBUG;
            }
            $mail->SMTPSecure = EMAIL_SMTPSECURE;
            $mail->Host = EMAIL_HOST;
            $mail->Port = EMAIL_PORT;
    
            $mail->Username = EMAIL;
            $mail->Password = EMAIL_PASSWORD;
    
            $mail->SetFrom(EMAIL, EMAIL_FROM);
            $mail->addAddress($this->email());
    
            $mail->IsHTML(true);
            $mail->Subject = 'Registration';

            $BodyFinal = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            <title>Confirm Email</title>
                            <style type="text/css">
                                table { 
                                    border-collapse: collapse !important;
                                }
                                
                                #outlook a { 
                                    padding:0; 
                                }

                                .ReadMsgBody { 
                                    width: 100%; 
                                }

                                .ExternalClass { 
                                    width: 100%; 
                                }

                                .backgroundTable { 
                                    margin: 0 auto; 
                                    padding: 0; 
                                    width: 100% !important; 
                                }

                                table td { 
                                    border-collapse: collapse; 
                                }

                                .ExternalClass * { 
                                    line-height: 115%; 
                                }

                                .container-for-gmail-android { 
                                    min-width: 600px; 
                                }

                                * {
                                    font-family: Helvetica, Arial, sans-serif;
                                }

                                body {
                                    -webkit-font-smoothing: antialiased;
                                    -webkit-text-size-adjust: none;
                                    width: 100% !important;
                                    margin: 0 !important;
                                    height: 100%;
                                    color: #676767;
                                }

                                td {
                                    font-family: Helvetica, Arial, sans-serif;
                                    font-size: 14px;
                                    color: #777777;
                                    text-align: center;
                                    line-height: 21px;
                                }

                                .important {
                                    color: #ff6f6f;
                                    font-weight: bold;
                                    text-decoration: none !important;
                                }

                                .header-lg,
                                .header-md,
                                .header-sm {
                                    font-size: 32px;
                                    font-weight: 700;
                                    line-height: normal;
                                    padding: 35px 0 0;
                                    color: #4d4d4d;
                                }

                                .header-md {
                                    font-size: 24px;
                                }

                                .header-sm {
                                    padding: 5px 0;
                                    font-size: 18px;
                                    line-height: 1.3;
                                }

                                .content-padding {
                                    padding: 20px 0 30px;
                                }

                                .block-rounded {
                                    border-radius: 5px;
                                    border: 1px solid #e5e5e5;
                                    vertical-align: top;
                                }

                                .button {
                                    padding: 30px 0 0;
                                }

                                .info-block {
                                    padding: 0 20px;
                                    width: 260px;
                                }

                                .mini-block-container {
                                    padding: 30px 50px;
                                    width: 500px;
                                }

                                .mini-block {
                                    background-color: #ffffff;
                                    width: 498px;
                                    border: 1px solid #cccccc;
                                    border-radius: 5px;
                                    padding: 45px 75px;
                                }

                                .block-rounded {
                                    width: 260px;
                                }

                                .info-img {
                                    width: 258px;
                                    border-radius: 5px 5px 0 0;
                                }

                                .force-width-img {
                                    width: 480px;
                                    height: 1px !important;
                                }

                                .force-width-full {
                                    width: 600px;
                                    height: 1px !important;
                                }

                                .user-img img {
                                    width: 130px;
                                    border-radius: 5px;
                                    border: 1px solid #cccccc;
                                }

                                .user-img {
                                    text-align: center;
                                    border-radius: 100px;
                                    color: #ff6f6f;
                                    font-weight: 700;
                                }

                                .user-msg {
                                    padding-top: 10px;
                                    font-size: 14px;
                                    text-align: center;
                                    font-style: italic;
                                }

                                .mini-img {
                                    padding: 5px;
                                    width: 140px;
                                }

                                .mini-img img {
                                    border-radius: 5px;
                                    width: 140px;
                                }

                                .force-width-gmail {
                                    min-width:600px;
                                    height: 0px !important;
                                    line-height: 1px !important;
                                    font-size: 1px !important;
                                }

                                .mini-imgs {
                                    padding: 25px 0 30px;
                                }

                                .link {
                                    color: #48aaad;
                                    text-decoration: underline;
                                }
                            </style>

                            <style type="text/css" media="screen">
                                @import url(http://fonts.googleapis.com/css?family=Oxygen:400,700);
                            </style>

                            <style type="text/css" media="screen">
                                @media screen {
                                    * {
                                        font-family: \'Oxygen\', \'Helvetica Neue\', \'Arial\', \'sans-serif\' !important;
                                    }
                                }
                            </style>

                            <style type="text/css" media="only screen and (max-width: 480px)">
                                @media only screen and (max-width: 480px) {

                                table[class*="container-for-gmail-android"] {
                                    min-width: 290px !important;
                                    width: 100% !important;
                                }

                                table[class="w320"] {
                                    width: 320px !important;
                                }

                                img[class="force-width-gmail"] {
                                    display: none !important;
                                    width: 0 !important;
                                    height: 0 !important;
                                }

                                td[class*="mobile-header-padding-left"] {
                                    width: 160px !important;
                                    padding-left: 0 !important;
                                }

                                td[class*="mobile-header-padding-right"] {
                                    width: 160px !important;
                                    padding-right: 0 !important;
                                }

                                td[class="mobile-block"] {
                                    display: block !important;
                                }

                                td[class="mini-img"],
                                td[class="mini-img"] img{
                                    width: 150px !important;
                                }

                                td[class="header-lg"] {
                                    font-size: 24px !important;
                                    padding-bottom: 5px !important;
                                }

                                td[class="header-md"] {
                                    font-size: 18px !important;
                                    padding-bottom: 5px !important;
                                }

                                td[class="content-padding"] {
                                    padding: 5px 0 30px !important;
                                }

                                td[class="button"] {
                                    padding: 5px !important;
                                }

                                td[class*="free-text"] {
                                    padding: 10px 18px 30px !important;
                                }

                                img[class="force-width-img"],
                                img[class="force-width-full"] {
                                    display: none !important;
                                }

                                td[class="info-block"] {
                                    display: block !important;
                                    width: 280px !important;
                                    padding-bottom: 40px !important;
                                }

                                td[class="info-img"],
                                img[class="info-img"] {
                                    width: 278px !important;
                                }

                                td[class="mini-block-container"] {
                                    padding: 8px 20px !important;
                                    width: 280px !important;
                                }

                                td[class="mini-block"] {
                                    padding: 20px !important;
                                }

                                td[class="user-img"] {
                                    display: block !important;
                                    text-align: center !important;
                                    width: 100% !important;
                                    padding-bottom: 10px;
                                }

                                td[class="user-msg"] {
                                    display: block !important;
                                    padding-bottom: 20px;
                                }
                            }
                        </style>
                    </head>

                    <body bgcolor="#f7f7f7">
                        <table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%">
                            <tr>
                                <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;" class="content-padding">
                                    <center>
                                        <table cellspacing="0" cellpadding="0" width="600" class="w320">
                                            <tr>
                                                <td class="header-lg">
                                                    Validate Your email
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mini-block-container">
                                                    <table cellspacing="0" cellpadding="0" width="100%"  style="border-collapse:separate !important;">
                                                        <tr>
                                                            <td class="mini-block">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                                                <tr>
                                                                                    <td class="user-msg">
                                                                                        Please, '.$this->username().' Confirm your email to finish your registration
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>  
                                                                    <tr>
                                                                        <td class="button">
                                                                            <div><!--[if mso]>
                                                                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.$this->url().'" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                                                                                    <w:anchorlock/>
                                                                                    <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">Confirm Email</center>
                                                                                </v:roundrect>
                                                                                <![endif]-->
                                                                                <a href="'.$this->url().'" style="background-color:#ff6f6f;border-radius:5px;color:#ffffff;display:inline-block;font-family:\'Cabin\', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">Confirm Email</a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>    
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" width="100%" style="background-color: #f7f7f7; height: 100px;">
                                    <center>
                                        <table cellspacing="0" cellpadding="0" width="600" class="w320">
                                            <tr>
                                                <td style="padding: 25px 0 25px">
                                                    if the button don\'t work, copy and past this link in your browser : <a href="" class="link">'.$this->url().'</a> <br /><br />
                                                    <strong><a class="important">Lucas Martinelle</a></strong><br />
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </body>
                </html>
            ';

            $mail->Body = $BodyFinal;
                
            if($mail->send()){
                return true;
            } else {
                return false;
            }
        }

        function resetpassword(){
            $mail = new PHPMailer(True);
    
            $mail->isSMTP();
            $mail->SMTPAuth = EMAIL_SMTP_ACTIVE_AUTH;
            if(EMAIL_DEBUG != false){
                $mail->SMTPDebug = EMAIL_DEBUG;
            }
            $mail->SMTPSecure = EMAIL_SMTPSECURE;
            $mail->Host = EMAIL_HOST;
            $mail->Port = EMAIL_PORT;
    
            $mail->Username = EMAIL;
            $mail->Password = EMAIL_PASSWORD;
    
            $mail->SetFrom(EMAIL, EMAIL_FROM);
            $mail->addAddress($this->email());
    
            $mail->IsHTML(true);
            $mail->Subject = 'Reset Password';
    
            $BodyFinal = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            <title>Confirm Email</title>
                            <style type="text/css">
                                table { 
                                    border-collapse: collapse !important;
                                }
                                
                                #outlook a { 
                                    padding:0; 
                                }

                                .ReadMsgBody { 
                                    width: 100%; 
                                }

                                .ExternalClass { 
                                    width: 100%; 
                                }

                                .backgroundTable { 
                                    margin: 0 auto; 
                                    padding: 0; 
                                    width: 100% !important; 
                                }

                                table td { 
                                    border-collapse: collapse; 
                                }

                                .ExternalClass * { 
                                    line-height: 115%; 
                                }

                                .container-for-gmail-android { 
                                    min-width: 600px; 
                                }

                                * {
                                    font-family: Helvetica, Arial, sans-serif;
                                }

                                body {
                                    -webkit-font-smoothing: antialiased;
                                    -webkit-text-size-adjust: none;
                                    width: 100% !important;
                                    margin: 0 !important;
                                    height: 100%;
                                    color: #676767;
                                }

                                td {
                                    font-family: Helvetica, Arial, sans-serif;
                                    font-size: 14px;
                                    color: #777777;
                                    text-align: center;
                                    line-height: 21px;
                                }

                                .important {
                                    color: #ff6f6f;
                                    font-weight: bold;
                                    text-decoration: none !important;
                                }

                                .header-lg,
                                .header-md,
                                .header-sm {
                                    font-size: 32px;
                                    font-weight: 700;
                                    line-height: normal;
                                    padding: 35px 0 0;
                                    color: #4d4d4d;
                                }

                                .header-md {
                                    font-size: 24px;
                                }

                                .header-sm {
                                    padding: 5px 0;
                                    font-size: 18px;
                                    line-height: 1.3;
                                }

                                .content-padding {
                                    padding: 20px 0 30px;
                                }

                                .block-rounded {
                                    border-radius: 5px;
                                    border: 1px solid #e5e5e5;
                                    vertical-align: top;
                                }

                                .button {
                                    padding: 30px 0 0;
                                }

                                .info-block {
                                    padding: 0 20px;
                                    width: 260px;
                                }

                                .mini-block-container {
                                    padding: 30px 50px;
                                    width: 500px;
                                }

                                .mini-block {
                                    background-color: #ffffff;
                                    width: 498px;
                                    border: 1px solid #cccccc;
                                    border-radius: 5px;
                                    padding: 45px 75px;
                                }

                                .block-rounded {
                                    width: 260px;
                                }

                                .info-img {
                                    width: 258px;
                                    border-radius: 5px 5px 0 0;
                                }

                                .force-width-img {
                                    width: 480px;
                                    height: 1px !important;
                                }

                                .force-width-full {
                                    width: 600px;
                                    height: 1px !important;
                                }

                                .user-img img {
                                    width: 130px;
                                    border-radius: 5px;
                                    border: 1px solid #cccccc;
                                }

                                .user-img {
                                    text-align: center;
                                    border-radius: 100px;
                                    color: #ff6f6f;
                                    font-weight: 700;
                                }

                                .user-msg {
                                    padding-top: 10px;
                                    font-size: 14px;
                                    text-align: center;
                                    font-style: italic;
                                }

                                .mini-img {
                                    padding: 5px;
                                    width: 140px;
                                }

                                .mini-img img {
                                    border-radius: 5px;
                                    width: 140px;
                                }

                                .force-width-gmail {
                                    min-width:600px;
                                    height: 0px !important;
                                    line-height: 1px !important;
                                    font-size: 1px !important;
                                }

                                .mini-imgs {
                                    padding: 25px 0 30px;
                                }

                                .link {
                                    color: #48aaad;
                                    text-decoration: underline;
                                }
                            </style>

                            <style type="text/css" media="screen">
                                @import url(http://fonts.googleapis.com/css?family=Oxygen:400,700);
                            </style>

                            <style type="text/css" media="screen">
                                @media screen {
                                    * {
                                        font-family: \'Oxygen\', \'Helvetica Neue\', \'Arial\', \'sans-serif\' !important;
                                    }
                                }
                            </style>

                            <style type="text/css" media="only screen and (max-width: 480px)">
                                @media only screen and (max-width: 480px) {

                                table[class*="container-for-gmail-android"] {
                                    min-width: 290px !important;
                                    width: 100% !important;
                                }

                                table[class="w320"] {
                                    width: 320px !important;
                                }

                                img[class="force-width-gmail"] {
                                    display: none !important;
                                    width: 0 !important;
                                    height: 0 !important;
                                }

                                td[class*="mobile-header-padding-left"] {
                                    width: 160px !important;
                                    padding-left: 0 !important;
                                }

                                td[class*="mobile-header-padding-right"] {
                                    width: 160px !important;
                                    padding-right: 0 !important;
                                }

                                td[class="mobile-block"] {
                                    display: block !important;
                                }

                                td[class="mini-img"],
                                td[class="mini-img"] img{
                                    width: 150px !important;
                                }

                                td[class="header-lg"] {
                                    font-size: 24px !important;
                                    padding-bottom: 5px !important;
                                }

                                td[class="header-md"] {
                                    font-size: 18px !important;
                                    padding-bottom: 5px !important;
                                }

                                td[class="content-padding"] {
                                    padding: 5px 0 30px !important;
                                }

                                td[class="button"] {
                                    padding: 5px !important;
                                }

                                td[class*="free-text"] {
                                    padding: 10px 18px 30px !important;
                                }

                                img[class="force-width-img"],
                                img[class="force-width-full"] {
                                    display: none !important;
                                }

                                td[class="info-block"] {
                                    display: block !important;
                                    width: 280px !important;
                                    padding-bottom: 40px !important;
                                }

                                td[class="info-img"],
                                img[class="info-img"] {
                                    width: 278px !important;
                                }

                                td[class="mini-block-container"] {
                                    padding: 8px 20px !important;
                                    width: 280px !important;
                                }

                                td[class="mini-block"] {
                                    padding: 20px !important;
                                }

                                td[class="user-img"] {
                                    display: block !important;
                                    text-align: center !important;
                                    width: 100% !important;
                                    padding-bottom: 10px;
                                }

                                td[class="user-msg"] {
                                    display: block !important;
                                    padding-bottom: 20px;
                                }
                            }
                        </style>
                    </head>

                    <body bgcolor="#f7f7f7">
                        <table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%">
                            <tr>
                                <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;" class="content-padding">
                                    <center>
                                        <table cellspacing="0" cellpadding="0" width="600" class="w320">
                                            <tr>
                                                <td class="header-lg">
                                                    Reset your password
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mini-block-container">
                                                    <table cellspacing="0" cellpadding="0" width="100%"  style="border-collapse:separate !important;">
                                                        <tr>
                                                            <td class="mini-block">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                                                <tr>
                                                                                    <td class="user-msg">
                                                                                        A request was made to change your password. Click on the button below to proceed. If you have not made this request, you can ignore this message by deleting it.
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>  
                                                                    <tr>
                                                                        <td class="button">
                                                                            <div><!--[if mso]>
                                                                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.$this->url().'" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                                                                                    <w:anchorlock/>
                                                                                    <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">Confirm Email</center>
                                                                                </v:roundrect>
                                                                                <![endif]-->
                                                                                <a href="'.$this->url().'" style="background-color:#ff6f6f;border-radius:5px;color:#ffffff;display:inline-block;font-family:\'Cabin\', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">Confirm Email</a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>    
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" width="100%" style="background-color: #f7f7f7; height: 100px;">
                                    <center>
                                        <table cellspacing="0" cellpadding="0" width="600" class="w320">
                                            <tr>
                                                <td style="padding: 25px 0 25px">
                                                    if the button don\'t work, copy and past this link in your browser : <a href="" class="link">'.$this->url().'</a> <br /><br />
                                                    <strong><a class="important">Lucas Martinelle</a></strong><br />
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </body>
                </html>
            ';
            $mail->Body = $BodyFinal;
                        
            if($mail->send()){
                return true;
            } else {
                return false;
            }
        }
    }
?>