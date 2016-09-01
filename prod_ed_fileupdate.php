<?php
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset(array('gfx','add','find','title','url','author','nauthor','nauthor_url','del_author','year','whom','city','gfx','video','party','demo','d','party','place','del_party','p','s','e','ed','scroll','dc','ts','saa','fm','m1','gs','tags','upl','ugfx1','ugfx2','ugfx3','ugfx4','ugfx5','ugfx6'));
if (!isset($_SESSION['user_id'])) quit ('/'); 

if (isset($_FILES['url'])&&isset($e))
{
include('clupl.php');
$handle = new upload($_FILES['url']);
if ($handle->uploaded) 
  {
	    $handle->process('demos/'.date("ymd"));
      if ($handle->processed) 
	  {
		$fl=$handle->file_dst_name;
		$url='demos/'.date("ymd").'/'.$fl;
		$handle->clean();
		$l=mfa('select url from gift where cid='.$e);
	mq('update gift set url="'.addslashes($url).'" where cid='.$e);
	mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
	logging ($log_type['change url'],$e,'old: '.$l['url'].', new: '.$url);
	echo $url;
      }  else { echo 'error : ' . $handle->error; echo $handle->log; exit;}
  }

}
