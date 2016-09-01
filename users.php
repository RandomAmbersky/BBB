<?php
include 'func.php';
session_start();
$db=dbconnect ();

getpost_ifset(array('user','u','ban','will_be_admin','send_email'));

if (!isset($_SESSION['user_id'])) quit ('/'); 

if (isset($send_email))
{
		$l = mfa('SELECT * FROM users WHERE cid='.intval($u).' LIMIT 1');
		$r_link="Mail 2 you from: ".$_SESSION['user']."\n\n".$send_email." \n\n 
		ZX demo archive zxn.ru mail robot";
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
	send_mime_mail('ZX demo archive',
               "zxn.ru",
               $_SESSION['user'],
               $l['email'],
               'CP1251',  // кодировка, в которой находятся передаваемые строки
               'UTF-8', // кодировка, в которой будет отправлено письмо
            	'Mail 2 you from '.$_SESSION['user'],
				$r_link);
	logging ($log_type['send email'],0,'To: '.$l['email']);
	quit ('/?e=4');
}




if (isset($ban)&&$ban!=0)
  {
	if ($_SESSION['user_type']!=$admin) quit ('/');
	if ($u<3) quit ('users.php?u='.$u);
	{
		switch ($ban) {
    		case 1:
				$days=1;
				break;
			case 2:
				$days=3;
				break;
			case 3:
				$days=7;
				break;
			case 4:
				$days=30;
				break;
			case 5:
				$days=0;
				break;
		}

		if ($days!=0) { 
			mq ('update users set ban=NOW() + INTERVAL '.$days.' DAY, user_type=2 where cid='.$u);
			logging ($log_type['ban user'],$u,'Nick: '.$user.', '.$days.' days');

		}
		
		if ($days==0) {
			mq ('update users set ban="0000000000" and user_type=0 where cid='.$u);
			logging ($log_type['unban user'],$u,'Nick: '.$user);
		}
	}
	quit ('users.php?u='.$u);

  }

if (isset($will_be_admin)&&$will_be_admin!=0)
{
	if ($_SESSION['user_type']!=$admin) quit ('/');
	if ($u<3) quit ('users.php?u='.$u);
	
	$will_be_admin--;
	mq ('update users set user_type='.$will_be_admin.' where cid='.$u);
	
	if ($will_be_admin==1) { $t=$log_type['set admin']; } else {$t=$log_type['unset admin']; }
	logging ($t,$u,'To user '.$user);
	
	quit ('users.php?u='.$u);
}

if(isset($user)||isset($u))
{
  if (isset($user)) $r = mq("SELECT * FROM users WHERE user='".trim(htmlentities($user))."' LIMIT 1");
  if (isset($u))    $r = mq('SELECT * FROM users WHERE cid='.intval($u).' LIMIT 1');

	if (mysqli_num_rows($r)!=0)
		{

	include 'nav.php'; 
	
	$data = mysqli_fetch_array($r);
	echo '<div class="row">
	<div class="col-sm-12" align=center><img src=';
	echo ($data['avatar']!='' ? $data['avatar'] : 'i/ava.jpg');
	echo ' ><h2>'.$data['user'].'</h2><br>
	<a href=scener_prods.php?user='.$data['cid'].'>View user uploaded prods</a><br>';
	
	$r=mq('SELECT * FROM user_alias, authors WHERE user_id='.$data['cid'].' AND author_id = authors.cid ');
	if ($r!=false)
	{
		echo ' Other nicks by '.$data['user'].':<br>';
		$nks='';
		while ($l=mysqli_fetch_array($r))
		{
			$nks.='<a href="prods.php?a='.$l['author_id'].'"><span class="glyphicon glyphicon-link"></span> '.$l['author'].'</a> | ';
		}
	echo substr ($nks,0,-2).'<br><br>';
	}

	if ((int)$data['ban']!=0) echo '<br><span class="glyphicon glyphicon-eye-open"></span> <b>Banned to : '.small_timestamp($data['ban']).'</b><br><br>';
	if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin) 
		if ($data['user_type']==1)	echo '<br><span class="glyphicon glyphicon-eye-open"></span> <b>Has Admin role</b><br><br>';

	echo ' <div class="sub-text ">Registered: '.small_timestamp($data['registered']).'<br><br>Send email to user:</div>
<form class="form-horizontal" method=post enctype="multipart/form-data" >
  <textarea name=send_email rows=5 cols=80></textarea>
  <input type=hidden name=u value='.$data['cid'].'>
  <br><button type="submit" class="btn btn-default">Send</button>
</form>';


  if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin) 
  {
	echo '<form class="form-horizontal" method=post enctype="multipart/form-data" ><br><div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Ban this user? <select name=ban>
	<option value=0>Not today</option>
	<option value=1>One day</option>
	<option value=2>Three day</option>
	<option value=3>Week</option>
	<option value=4>Month</option>
	<option value=5>UNBAN</option>
	</select><br><br>
	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Make this user Admin role? <select name=will_be_admin>
	<option value=0>Not today</option>
	<option value=1>No, '.$data['user'].' simply User</option>
	<option value=2>Yes, '.$data['user'].' will be Admin</option>
	</select>
	<input type=hidden value='.$data['cid'].' name=u><input type=hidden value='.$data['user'].' name=user><br>
	<button type="submit" class="btn btn-default">Send</button></div></form>';
  }

		}

}	else
	{ quit ('/?e=1');	}

		echo '<br><br><br>';
include 'bottom.php';
?>