<?php
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset(array('gfx','p','s','e'));
if (!isset($_SESSION['user_id'])) quit ('/'); 


if (isset($p)&&isset($s))
{
	$s=explode (',',$s);
	foreach ($s as $a) if ($a!='__ph0') $gfx.=$a.';';
//	echo $p.' '.$gfx;
	mq('update gift set gfx="'.addslashes($gfx).'" where cid='.$p);
	mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
	logging ($log_type['change gfx'],$p,'urls: '.$gfx);
	exit;

}


if (isset($_FILES['gfx'])&&isset($e))
{
include('clupl.php');
$handle = new upload($_FILES['gfx']);
	if ($handle->uploaded) 
    {
		if (!is_dir('screens/'.date("ymd"))) 
			mkdir('screens/'.date("ymd"), 0755, true);

	    $handle->process('screens/'.date("ymd"));
      if ($handle->processed) 
	  {

		$fl=$handle->file_dst_name;
		$ugf='screens/'.date("ymd").'/'.$fl.';';
		$handle->clean();

	$l=mfa('select * from gift where cid='.$e);
	$gfx=''; $old_scr=explode (';',$l['gfx']);
	foreach ($old_scr as $a) if ($a!='') $gfx.=$a.';';
	mq('update gift set gfx="'.addslashes($gfx.$ugf).'" where cid='.$e);
	mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
	logging ($log_type['add gfx'],$e,'url: '.$l['gfx'].';'.$ugf);
	echo substr($ugf,0,-1);
	  }
	}
}
