<?php
include 'func.php';
session_start();
$db=dbconnect ();

// Скрипт проверки

# Соединямся с БД
if (isset($_COOKIE['id']))
{
    $userdata=mfa('SELECT * FROM users WHERE md5(cid+"'.$rand_value.'") = "'.$_COOKIE['id'].'" LIMIT 1');

    if($userdata['cid'] !== $_COOKIE['id'])
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        print "Хм, что-то не получилось";
    }
    else
    {
        print "Привет, ".$userdata['user'].". Всё работает!";
    }
}
else
{
    print "Включите куки";
}
?>