<?
include 'func.php';
session_start();
$db=dbconnect ();
$input_win=array('title','author','year','whom','city','l','a','c','s','ts','fm','saa','gs','m1','tag','nu');
$input_int=array('y','w','np','t','py','id','e');
getpost_ifset($input_win); getpost_ifset($input_int);
if (isset($np)) {$num_page=$np=intval(htmlentities($np));} else {$num_page=$np=1;}

$demos_on_page=30;

$parties=array(); $parties[]='';
$r=mq('select * from party order by cid');
while ($lx=mysqli_fetch_array($r)) $parties[$lx['cid']]=$lx['name'];

$demo=array();

include 'nav.php'; ?>
<?php 
$err=array('Error in login/pass','User not found','Please, check Your email and click on link for confirm registration','Bad activation code','Message sent');
if (isset($e))
{
	$e=intval($e);

if ($e==2)
 { echo '<div class="alert alert-success" role="alert">
  <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
  <span class="sr-only">Note:</span> '.$err[$e].'</div>';
 }
 else
 {
	echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span> '.$err[$e].'</div>';
 }

}
echo '<div class="col-xs-12 col-sm-8 col-md-7 col-lg-9">';

	// Last prods
	echo '<div class=well-sm hidden-xs><h4><span class="glyphicon glyphicon-import"></span> <a href=prods.php>Latest uploads:</a></h4></div>';
	$r=mq('SELECT *,cid as gc FROM gift where view=1 order by cid desc limit 12');
	show_prod_list($r,true,4,'a');


	// Most Favorites
	echo '<div class=well-sm hidden-xs><h4><span class="glyphicon glyphicon-heart"></span> Most Favoured demos:</h4></div>';
	$r=mq('SELECT * , cid AS gc
		FROM gift WHERE VIEW =1
		ORDER BY fav DESC LIMIT 12');
	show_prod_list($r,true,4,'');

	// Most Liked
	echo '<div class=well-sm hidden-xs><h4><span class="glyphicon glyphicon-thumbs-up"></span> Most Liked demos:</h4></div>';
	$r=mq('SELECT * , cid AS gc
		FROM gift WHERE VIEW =1
		ORDER BY th_up DESC LIMIT 12');
	show_prod_list($r,true,4,'');

	echo '<div class=well-sm hidden-xs><h4><span class="glyphicon glyphicon-refresh"></span> Fresh updates:</h4></div>';
	// Last edited prods
	$r=mq('SELECT *,gift.cid as gc FROM log, gift where log.type in ('.$log_type['edit demo'].','.$log_type['add gfx'].','.$log_type['change url'].') and id=gift.cid and view=1 order by log.cid desc limit 12');
	show_prod_list($r,true,4,'');

	echo '</div>';
	echo '<div class="col-xs-12 col-sm-4 col-md-5 col-lg-3">';


	// last comments:
	echo '<div class=well><h4><a href=comments.php><span class="glyphicon glyphicon-comment"></span> Latest comments:</a></h4><hr>';
	$r=mq('SELECT * FROM prod_comments, gift, users
WHERE prod_id = gift.cid
AND VIEW =1
AND users.cid = user_id
ORDER BY prod_comments.cid DESC
LIMIT 16');
	show_comment($r);
echo '<a class=pull-right href=comments.php><span class="glyphicon glyphicon-th"></span> View all comments</a><br></div>';


if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin) 
{
	echo '<div class=well><h4><span class="glyphicon glyphicon-wrench"></span> Events&requests:</h4><hr>';
	$r=mq('SELECT * FROM log,users where type in (
	'.$log_type['register'].','.$log_type['activate'].','.$log_type['req author'].','.$log_type['set author'].','.$log_type['unset author'].',
	'.$log_type['reject author'].','.$log_type['admin info'].','.$log_type['edit demo'].','.$log_type['ban user'].','.$log_type['unban user'].',
	'.$log_type['change profile'].'
	) and user_id=users.cid order by log.cid desc limit 12');
	 while ($l=mysqli_fetch_array($r))
    {
		echo '<div class="row"><img src="';
		echo ($l['avatar']!='' ? $l['avatar'] : 'i/ava.jpg');
		echo '" class=cmnt_img >';
		echo '<b>'.$l['user'].'</b></a><br />';

		if ($l['type']==$log_type['admin info']) 
		{
			echo 'request: '.stripslashes($l['info']);
			echo '<br><form method=post action=req.php>
			Admin answer: <input type=text name=i_req size=12></form>';
		}
		if ($l['type']==$log_type['ban user']) echo $l['user'].': ban '.stripslashes($l['info']);
		if ($l['type']==$log_type['unban user']) echo $l['user'].': Unban '.stripslashes($l['info']);
		
		if ($l['type']==$log_type['change profile']) echo $l['user'].' change profile: '.stripslashes($l['info']);
		if ($l['type']==$log_type['register']) echo 'User '.stripslashes($l['info']).' try to register';
		if ($l['type']==$log_type['activate']) echo 'User '.stripslashes($l['info']).' activated, ip: '.$l['ip'];
		if ($l['type']==$log_type['reject author']) echo stripslashes($l['info']);
		if ($l['type']==$log_type['edit demo']) echo $l['user'].' '.stripslashes($l['info']);
		if ($l['type']==$log_type['req author']) 
			echo 'Please, attach to my profile nick: <b>'.stripslashes($l['info']).'</b><br>
		<a href="req.php?u='.$l['user_id'].'&a='.urlencode($l['info']).'"><span class="glyphicon glyphicon-ok"></span> Accept request</a> <a href="req.php?u='.$l['user_id'].'&r='.urlencode($l['info']).'"><span class="glyphicon glyphicon-remove"></span> Reject</a>';
		
		if ($l['type']==$log_type['set author']) echo stripslashes($l['info']);

		echo '<br /><span class="sub-text"><span class="glyphicon glyphicon-eye-open"></span> '.small_timestamp($l['date']).'</span></div><br />';
    }
echo '</div>';
}

// Most discussed:
	echo '<div class=well><h4><span class="glyphicon glyphicon-comment"></span> Most discussed prods:</h4><hr>';
$r=mq('SELECT * FROM prod_comments, gift where date>NOW() - INTERVAL 7 DAY and prod_id=gift.cid and view=1 group by prod_comments.prod_id order by comments desc limit 9');
	show_most_comment($r);
	echo '</div>';


// Tags
	echo '<div class="well-sm"><h4><span class="glyphicon glyphicon-comment"></span> Popular tags:</h4><hr>';
	show_tags('DISPLAY OUTPUT:',$do);
	show_tags('SOUND:',$sound);
	show_tags('TYPE:',$type);
	show_tags('FEATURES:',$feat);
	show_tags('SIZE:',$size);
	show_tags('HARDWARE:',$hard);
	echo '</div>';

echo '</div>';

include 'bottom.php';

function show_tags($n,$t)
 {
  	echo '<div class=pull-left><span class="glyphicon glyphicon-tag" aria-hidden="true"></span> ';
	echo '<b>'.$n.'&nbsp;&nbsp;</b></div> <div id=tags> <i>';
	foreach ($t as $a) echo ' <a href="prods.php?tag='.trim($a).'">'.$a.'</a> | '; 
	echo '</i></div>';
 }

function show_most_comment($rz)
{
    while ($l=mysqli_fetch_array($rz))
    {
	$gfx= explode(';', $l['gfx']);		
	echo '<div class="row"><a href="prod.php?p='.$l['prod_id'].'#cmnt">';
	if (count($gfx)!=0)	echo '<img class="img-rounded cmnt_img" src="'.$gfx[0].'" >';
	echo '<b>'.$l['title'].'</b></a> <span class="sub-text"><i>['.$l['comments'].']</i><br />';
	echo ''.small_timestamp($l['date']).'</span></div><br />';
    }
}

?>