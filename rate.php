<?php
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset(array('u','d'));

if (!isset($_SESSION['user_id'])) exit; 
if ($_SESSION['user_type']==2) exit;


$grant = '';
foreach($referer as $a) if(strstr($_SERVER['HTTP_REFERER'],$a)) $grant = true;
if ($grant!=true) exit;

$id=0;
if (isset($u)) $id=$u;
if (isset($d)) $id=$d;

// Page_rate
$vol_cnt=mfa('select * from rating where id="'.$id.'" and uid="'.$_SESSION['user_id'].'"');
// echo mysqli_error($db); exit;

if ($vol_cnt[0]==null)
{
	mq ('insert into rating (id,uid) values ("'.$id.'","'.$_SESSION['user_id'].'")');
 
// Page rating
if (isset($u))
	{
		mq('update gift set th_up=th_up+1 where cid="'.$id.'"');
		logging ($log_type['thumb up'],$id,'by '.$_SESSION['user']);
	}

if (isset($d)) 
	{ 
		mq('update gift set th_down=th_down+1 where cid="'.$id.'"');
		logging ($log_type['thumb down'],$id,'by '.$_SESSION['user']);
	}

  $l=mfa('select th_up,th_down from gift where cid='.$id.' limit 1');
  if (isset($u)) echo $l['th_up'];
  if (isset($d)) echo $l['th_down'];
}
