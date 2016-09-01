<?php
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset(array('f'));

if (!isset($_SESSION['user_id'])) exit; 
if ($_SESSION['user_type']==2) exit;


$grant = '';
foreach($referer as $a) if(strstr($_SERVER['HTTP_REFERER'],$a)) $grant = true;
if ($grant!=true) exit;

// fav check
$vol_cnt=mfa('select * from fav where prod_id="'.$f.'" and user_id="'.$_SESSION['user_id'].'"');
// echo mysqli_error($db); exit;

if ($vol_cnt[0]==null)
 {
	mq ('insert into fav (prod_id,user_id) values ("'.$f.'","'.$_SESSION['user_id'].'")');
	mq ('update gift set fav=fav+1 where cid="'.$f.'"');
	logging ($log_type['fav'],$f,'by '.$_SESSION['user']);
 	$l=mfa('select fav from gift where cid='.$f.' limit 1');
	echo '<span id=fav data-id="'.$f.'" class="pull-right pointer glyphicon glyphicon-star">'.$l['fav'].'</span></a>';

 }
	else
 {
	mq('delete from fav where prod_id='.$f.' and user_id='.$_SESSION['user_id']);
	mq('update gift set fav=fav-1 where cid='.$f);
	logging ($log_type['unfav'],$f,'by '.$_SESSION['user']);
	$l=mfa('select fav from gift where cid='.$f.' limit 1');
	echo '<span id=fav data-id="'.$f.'" class="pull-right pointer glyphicon glyphicon-star-empty">'.$l['fav'].'</span></a>';

 }