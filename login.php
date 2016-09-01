<?php
include 'func.php';
session_start();
$db=dbconnect ();

getpost_ifset(array('login','e','password','submit'));

if (isset($e))
{
	session_destroy();
	setcookie ("retrobbb", "", time() - 3600);
	quit('/');

}

# Соединямся с БД
if(isset($_POST['submit']))
{
    # Вытаскиваем из БД запись, у которой логин равняеться введенному
	$data = mfa("SELECT * FROM users WHERE user='".trim($login)."' LIMIT 1");
	if ($data[0]==NULL) quit ('/?e=0');
	if ($data['active']==0) quit ('/?e=2');
	# Сравниваем пароли
	if ($data['password'] === $password)
	{
        # Ставим куки
		$_SESSION['user']=$data['user'];
		$_SESSION['user_id']=$data['cid'];
		$_SESSION['user_type']=$data['user_type'];
		$_SESSION['ban']=$data['ban'];
		$_SESSION['avatar']=$data['avatar'];
		setcookie('retrobbb',md5($data['cid']),time()+60*60*24*3);
	$a=$_SERVER['HTTP_REFERER'];
	header("Location: $a");
	exit;
	}
quit ('/?e=0');
}
/*
<form method="POST">
Логин <input name="login" type="text"><br>
Пароль <input name="password" type="password"><br>
<input name="submit" type="submit" value="Войти">
</form>
*/
?>