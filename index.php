<?php
## created date 06 nov 2019
## This code created by neoistone
## don't share file url anyone 
## if share this link any one take own risk ony for admin
## nearly I will  develop whm mangement system using php whmapi freely available with payment integration (https://www.neoistone.com/whm-mangement/)
## www.neoistone.in/whm-mangement-system/
## any issue this code repaly it hashtag #neoistone-whmacct or send mail with error screenshot to this email manikantasripadi@neoistone.com
## Enter your company name here because cpanel user send email  credentials on down dispaly company  like this Thankyou choosing your company name
## for example Thankyour choosing neoistone hosting like this
$company = "You company name ";
## whm login credentials
$user = 'root'; // Reseller or owner username
$pass = 'password'; // Reseller or owner password
$host = 'host.example.com';  //without a http or https example : cpanel.neoistone.com or your server ip addresh (123.45.678.90)
$user_account = ""; // leave it empty
$user_pass = ""; // leave it empty
#database credentials
$dbname = "whmapi"; // your database name
$db_host="localhost"; //leave it by defult
$table = "users"; // your table name //
$db_username ="root"; // acess database username
$db_password = ""; // database password

#smtp credentials
$Host = 'mail.server.hostname.here.com'; // smtp hostname for example mail.gmail.com
$smtp_Username = 'username'; // smtp username  for auth 
$smtp_Password = 'password'; // smtp passwoord for auth
$smtp_Port = "port"; // smtp port (example port 25)

echo '<!DOCTYPE HTML>';
echo '<html>';
echo "<head>";
echo "<title>cpanel account create</title>";
echo "<!-- Fav Icon -->";
echo "<link rel='shortcut icon' href='favicon.ico' type='image/x-icon' />";
echo "<!-- Meta Data -->";
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
echo "<meta name='referrer' content='origin'>";
echo "</head>";

#sendmail function using smtp
function Nmail($Host,$smtp_Username,$smtp_Password,$smtp_Port,$to,$xsubject,$body){ // this function requires class  call a phpmailer class downlaad for this url https://www.neoistone.com/phpmailer.zip
include_once("./mail/class/class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host = $Host; // SMTP server
$mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
// 1 = errors and messages
// 2 = messages only
$mail->SMTPAuth = true; // enable SMTP authentication
//$mail->SMTPSecure = 'ssl'; //or tsl -> switched off
$mail->Port = $smtp_Port; // set the SMTP port for the service server
$mail->Username = $smtp_Username; // account username
$mail->Password = $smtp_Password; // account password

$mail->SetFrom($smtp_Username);
$mail->Subject = $subject;
$mail->MsgHTML($body);
$mail->AddAddress($to);

if(!$mail->Send()) {
    $mensagemRetorno = 'Error: '. print($mail->ErrorInfo);
} else {
    $mensagemRetorno = 'E-mail sent!';
}
 return $mensagemRetorno;
}
##getting current url
function url(){
  $requestprotocal = '';
  if ($_SERVER['SERVER_PORT'] == 80) {
    $requestprotocal = 'http://';
  } elseif ($_SERVER['SERVER_PORT'] == 443) {
    $requestprotocal = 'https://';
  }
  $uri = $requestprotocal.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    return $uri;
 }
##Generating username
function get_username(){
    $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrestuvwxyz";
    $name = ""; // leave it empty
    $length = "8"; // username length
    for($i = 0; $i < $length; $i++){
        $name .= $characters[mt_rand(0,strlen($characters) - 1)];

    }
   return $name;
 }
 #Generating password
 function get_password(){
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $p_length = "16"; // password length
   $r_password = substr( str_shuffle( $chars ), 0, $p_length);

   return $r_password;
 }
   ##calling username and password
   $user_account = get_username();
   $user_pass = get_password();
 ## whene submit woking this code
if(isset($_POST['acct']))
{
   echo '<body>';
   $user_domain = $_POST['domain'];
   $package = $_POST['pkgname'];
   $email = $_POST['email'];
  #Don't touch this stuff   
 $url = "https://$host:2087/json-api/createacct?username=$user_account&domain=";
 $url .= urlencode($user_domain) . 
 "&password=" . urlencode($user_pass) . 
 "&pkgname=" . urlencode($package) .
 "&contactemail=" . urlencode($email);

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
  curl_setopt($curl, CURLOPT_HEADER,0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($curl, CURLOPT_USERPWD, $user.":".$pass);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($curl, CURLOPT_URL, $url);

 $response = curl_exec($curl);
  #check it response first time 
  #print_r($response);


  #sending mail user login credentials
 $body = "Your username : ". $user_account ."<br>".
 "Your password : ". $user_pass ."<br>".
 "Login url : https://" .$host .":2083 <br>".
 "<p style='color: #F32013;'>Don't share anyone this credentials</p>".
 "Thank you choosing " .$company." hosting <br>";

  try {
    $conn = new PDO("mysql:host=$db_host;dbname=$dbname", $db_username, $db_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO $table (username, password,domain,pack,email)
    VALUES ('$user_account','$user_pass','$user_domain','$package','$email')";
    // use exec() because no results are returned
    $conn->exec($sql);
      ## sending email to cpanel user login credentials
      $mail_response = Nmail($Host,$smtp_Username,$smtp_Password,$smtp_Port,$email, $company." Hosting", $body);
      ## email response displaying
       if ($mail_response == 'E-mail sent!') {
         $url = url();
         echo "<p>cpanel login credentials sent email to user</p><br>";
         echo "<a href=\"".$url. "\">GO Back</a>";
       } else {
        echo $mail_response;
       }
    }
catch(PDOException $e)
    {
      ## sending email to cpanel user login credentials
      $mail_response = Nmail($Host,$smtp_Username,$smtp_Password,$smtp_Port,$email, $company." Hosting", $body);
      ## email response displaying
      echo $mail_response.'<br>';
    echo "insert data into database  insert manully error is the: <br>".$sql . "<br>" . $e->getMessage();
    }
    echo '</body>';
    echo "</html>";
} else {
  ## by defult this page template
   echo "<body>";
   echo "<form method='POST'>";
   echo "<p>Domain   : <input type='text' name='domain' placeholder='Domain'></p>";
   echo "<p>Packge   : <input type='text' name='pkgname' placeholder='Packgename'></p>";
   echo "<p>email id : <input type='text' name='email' placeholder='Email Id'></p>";
   echo "<input type='submit' name='acct' value='Create a cPanel account'>";
   echo "</form>";
   echo "</body>";
   echo "</html>";
}
?>