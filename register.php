<?php
include 'func.php';
session_start();
$db=dbconnect ();

getpost_ifset(array('login','password','submit','email','e','a'));

$banned_email=array('drdrb.net','dropmail.me','mailforspam.com','mailspeed.ru','coieo.com','spambox.us','unmail.ru','mt2014.com','coldemail.info','sharklasers.com','guerrillamailblock.com','guerrillamail.com','guerrillamail.net','guerrillamail.org','guerrillamail.biz','spam4.me','grr.la','guerrillamail.de','tempinbox.com','mailinator.com','reallymymail.com','trbvm.com','mailspeed.ru','yopmail.com','lackmail.ru','incognitomail.org','discard.email','discardmail.com','discardmail.de','spambog.com','spambog.de','spambog.ru','0815.ru','hulapla.de','s0ny.net','teewars.org','yapped.net','sweetxxx.de','jobbikszimpatizans.hu','zeta-telecom.com','zaktouni.fr','freelance-france.eu','webcontact-france.eu','fast-mail.fr','mail-easy.fr','instantmail.fr','dfghj.ml','skrx.tk','sraka.xyz','servermaps.net','lovesea.gq','btcmail.pw','kanker.website','knol-power.nl','hoer.pw','belastingdienst.pw','everytg.ml','freemeil.gq','info-radio.ml','liveradio.tk','resgedvgfed.tk','susi.ml','muehlacker.tk','hartbot.de','lovefall.ml','keinpardon.de','savelife.ml','blutig.me','u14269.ml','freundin.ru','breadtimes.press','cyber-phone.eu','premium-mail.fr','disign-concept.eu','ecolo-online.fr','photo-impact.eu','web-ideal.fr','used-product.fr','cyber-innovation.club','reality-concept.club','last-chance.pro','disign-revelation.com','art-en-ligne.pro','solar-impact.pro','smashmail.de','social-mailer.tk','teamspeak3.ga','gamno.config.work','smap.4nmv.ru','spam.2012-2016.ru','chechnya.conf.work','hmamail.com','mintemail.com','mytempemail.com');


$error=array("Please, use Latin chars in Your login only","Your login is less than 3 or big than 30 chars ","Email not found","This Login in use",'Wrong email');
$err = array();

if (isset($a))
{
	$sql='select * from users where md5(md5(cid))="'.$a.'"';
	$r=mq($sql);
	if (mysqli_num_rows($r)==0) quit ('/?e=4');

	$l=mysqli_fetch_array($r);
	mq('update users set active=1 where cid="'.$l['cid'].'"');
		$_SESSION['user_id']=$l['cid'];
		$_SESSION['user']=$l['user'];
		$_SESSION['user_type']=$l['user_type'];
		$_SESSION['ban']=$l['ban'];
		$_SESSION['avatar']=$l['avatar'];
		setcookie('retrobbb',md5($data['cid']),time()+60*60*24*3);
		logging ($log_type['activate'],$l['cid'],$l['user']);
		quit("/");
}

if(isset($login))
{

	# проверям логин
	if(!preg_match("/^[a-zA-Z0-9]+$/",$login)) $err[] = 0;
	if(strlen($login) < 2 or strlen($login) > 30) $err[] = 1;
	if($email=='') $err[] = 2;

	$ch_mail=explode('@',$email);
	if (array_search($ch_mail[1],$banned_email)==true) $err[] = 4;

	# проверяем, не сущестует ли пользователя с таким именем
	$query = mfa("SELECT COUNT(cid) FROM users WHERE user='".$login."'");
	if($query[0] > 0) $err[] = 3;

	# Если нет ошибок, то добавляем в БД нового пользователя
	if(count($err) == 0)
	{
		$r=mq('INSERT INTO users SET user="'.addslashes($login).'", password="'.addslashes(trim($password)).'", 
			email="'.addslashes($email).'", registered="'.date("Y-m-d H:i:s").'", active=0');
		$uid=mysqli_insert_id($db);
		$user_code=md5(md5($uid));
		$r_link="Dear user!\n\n
		For confirmation of ZX Demo archive registration as ".$login." \n 
		click on this link:	http://bbb.retroscene.org/register.php?a=".$user_code."\n";
/*
	send_mime_mail ($name_from, // имя отправителя
						$email_from, // email отправителя
						$name_to, // имя получателя
						$email_to, // email получателя
						$data_charset, // кодировка переданных данных
						$send_charset, // кодировка письма
						$subject, // тема письма
						$body // текст письма
						);
	*/
	logging ($log_type['register'],$l['cid'],'Nick: '.$login.' email: '.$email);
	send_mime_mail('ZX demo archive',
               "zxn.ru",
               $login,
               $email,
               'CP1251',  // кодировка, в которой находятся передаваемые строки
               'UTF-8', // кодировка, в которой будет отправлено письмо
            	'Registration link',
				$r_link);
	quit ('/?e=2');
	}
	else
	{
		$er='';
		foreach($err AS $a) $er.=$a;
		quit ('register.php?e='.$er);
	}
}

include 'nav.php'; ?>

<?php 
if (isset($e))
{
	echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>';
	for ($x=0;$x<(strlen($e));$x++) echo $error[ $e[$x] ].'<br>'; 
		echo '</div>';
}
?>
<form class="form-horizontal" method=post>
  <div class="form-group">
	<label for="login" class="col-sm-2 control-label">Login</label>
	<div class="col-sm-10"><input name="login" class="form-control" id="login" type="text"  placeholder="Your login"></div>
  </div>
  <div class="form-group">
	<label for="inputPassword" class="col-sm-2 control-label">Password</label>
	<div class="col-sm-10">
	  <input type="password" name=password class="form-control" id="inputPassword" placeholder="Password">
	</div>
  </div>
  <div class="form-group">
	<label for="inputEmail" class="col-sm-2 control-label">Email</label>
	<div class="col-sm-10">
	  <input type="email" name=email class="form-control" id="inputEmail" placeholder="Email">
	</div>
  </div>
  <div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
	  <button type="submit" class="btn btn-default">Sign in</button>
	</div>
  </div>
</form>
<?php include 'bottom.php'; ?>