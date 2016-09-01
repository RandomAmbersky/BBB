<?php
	$referer = array("zxn.ru","bbb.retroscene.org","localhost","127.0.0.1");
	$rand_value='hype.retroscene';
	$admin=1; $banned=2;
  
  $role=array(0=>'',1=>'code', 2=>'music', 3=>'sounds',4=> 'graphics', 5=>'text',6=> 'idea',7=> 'concept', 8=>'design', 9=>'animation', 10=>'3d',11=>'Group');
  define ('Group_role',11);

$compo_list=array('none','demo','enhanced demo','combined demo/intro','BASIC demo','low-end demo','oldskool demo','other platform demo','invitation','game',
	'wild','128b intro','256b intro','512b intro','1k intro','4k intro','8k intro','16k intro','border intro','intro','1k procedural graphics','4k procedural graphics','realtime BASIC demo','realtime coding','realtime wild','gravedigger/old demo','zapilyator','crack intro','miniature','surprise');

$log_type=array('register'=>0,'activate'=>1,'change profile'=>10,'send email'=>11,
	'add demo'=>20,'del demo'=>21,'edit demo'=>30,'delete gfx'=>31,'add gfx'=>32,'change url'=>33,'change gfx'=>34,
	'delete comment'=>38,
	'ban user'=>40,'unban user'=>41, 'req author'=>42,'set author'=>45,'reject author'=>44,'unset author'=>43,
	'set admin'=>50,'unset admin'=>51,'admin info'=>52, 	
	'thumb up'=>60,'thumb down'=>61,'fav'=>62,'unfav'=>63,'add party'=>70,'del party'=>71);

  
function dbconnect ()
 {
	date_default_timezone_set('Etc/GMT-3');
	$db=mysqli_connect("", "", "");
	mysqli_select_db($db,"");
	mysqli_set_charset ($db,"utf8");
  
  if (!isset($_SESSION['user_id']))
  	if (isset($_COOKIE['retrobbb']))
  	{
		$data = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM users WHERE md5(cid)='".$_COOKIE['retrobbb']."' LIMIT 1"));
		if ($data[0]!='') 
		{
			$_SESSION['user']=$data['user'];
			$_SESSION['user_id']=$data['cid'];
			$_SESSION['user_type']=$data['user_type'];
			$_SESSION['ban']=$data['ban'];
			$_SESSION['avatar']=$data['avatar'];
			setcookie('retrobbb',md5($data['cid']),time()+60*60*24*3);
		}
	
  	}

  if (isset($_SESSION['user_id']))
  {
	$data = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM users WHERE cid='".$_SESSION['user_id']."' LIMIT 1"));
	
	if ((int)$data['ban']!=0)
		if ($data['ban']<date("Y-m-d H:i:s"))	
		{ 
			mysqli_query($db,'update users set ban="0000000000", user_type=0 where cid="'.$_SESSION['user_id'].'"');
			$data['user_type']=0;
		}
		 else
		{ $data['user_type']=2; $_SESSION['ban']=$data['ban'];	}
	
	$_SESSION['user_type']=$data['user_type'];
  }

	return ($db);
 }



function logging ($t,$id,$i)
{
	global $log_type;
	$uid=0;
	if (isset($_SESSION['user_id'])) $uid=$_SESSION['user_id'];
	mq ('insert into log (type,id,user_id,ip,info,date) values ('.$t.','.$id.','.$uid.', "'.ip().'", "'.addslashes($i).'","'.date("Y-m-d H:i:s").'")');
}



function ip()
{
 if (empty($REMOTE_ADDR))
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $REMOTE_ADDR=$_SERVER['HTTP_X_FORWARDED_FOR'];
    else $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

    return trim($REMOTE_ADDR);
}

function mfa($s)
{
	return @mysqli_fetch_array(mq($s));
}

function mq($s)
{
	global $db;
//echo $s;
	$r=mysqli_query($db,$s);
	echo mysqli_error($db);
	return $r;

}

function quit($s)
{
	header ("Location: $s");
	exit;
}

function check_site()
{
 
 $referer = array('zxn.ru',"bbb.retroscene.org",'127.0.0.1'); $grant = '';
 foreach($referer as $a)
 {
  if(strpos($_SERVER['HTTP_REFERER'],$a)) $grant = true;
 }
 if ($grant!=true) quit("/");
}

function getpost_ifset($test_vars) 
	{ 
		if (!is_array($test_vars)) { 
			$test_vars = array($test_vars); 
		} 
		 
		foreach($test_vars as $test_var) { 
			if (isset($_POST[$test_var])) { 
				global $$test_var; 
				$$test_var = $_POST[$test_var]; 
			} elseif (isset($_GET[$test_var])) { 
				global $$test_var; 
				$$test_var = $_GET[$test_var]; 
			} 
		} 
	} 
	

function timez($t) 
 {
	GLOBAL $monthz;
  $t=explode ('-',$t);
  return (int)$t[2].' '.$monthz[(int)$t[1]].' '.trim($t[0]);
 }

function datetimez($d) 
 {
	GLOBAL $monthz;
  $u=explode (' ',$d);
  $t=explode ('-',$u[0]);
  $y=explode (':',$u[1]);
  return (int)$t[2].' '.$monthz[(int)$t[1]].' '.trim($t[0]).', '.$y[0].':'.$y[1];
 }


function litl_time($t) 
 {
	GLOBAL $monthz;
  $t=explode ('-',$t);
  $y=explode (' ',$t[2]);
  return trim($t[0]).'-'.$t[1].'-'.$y[0];
 }

function litl_timestamp($t) // 22 вересня, 22:15
 {
	GLOBAL $monthz;

 $t=explode (' ',$t); // 2008-09-25 15:35:00
 $y=explode ('-',$t[0]);
 $t=explode (':',$t[1]);
 return ((int)($y[2]).''.$monthz[(int)$y[1]].' '.$y[0].', '.$t[0].':'.$t[1]);
 }

function small_timestamp($t) 
 {
 $t=explode (' ',$t); // 2008-09-25 15:35:00
 $y=explode ('-',$t[0]);
 $t=explode (':',$t[1]);
 return ((int)($y[2]).'-'.$y[1].'-'.$y[0].', '.$t[0].':'.$t[1]);
 }

function html2txt($document){
$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			   '@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
);

$text = preg_replace($search, '', $document);
return $text;
}

function get_img($l)
{
	$img=get_img_src(stripslashes($l['anonce']));
	if (!isset($img[1])) $img=get_img_src(stripslashes($l['info']));
	return $img;
}

function get_img_src($l)
{
	preg_match( '/src="([^"]*)"/i', stripslashes($l), $i);
	return $i;
}
function video_code($url)
{
	$image_url = parse_url($url);
	parse_str($image_url['query'],$url_params);
	return $url_params['v'];
}

$aaa_evo= array("(ZX Evolution)", "(spg)", "(TS Conf)","(ATM Turbo 2+)","(Base Conf)","(1M)","(wmf)","(Cache)","(Covox)","(Neo GS)","(GS)","(TS)");

$fx=array('3d','analyzer','animation','blobs','bobs','bump mapping','chaos zoomer','chessboard','conwaylife','copper bars','distortrotozoom','doom','dots','dots landscape','dots zoomer','dotsphere','dottunnel','DXYPP','DYC','environment mapping','fire','flatshade','floor','FLP','fractals','glenz','IFS','interference','isometric','kaleidoscope','lens','lens flare','l-system','mandelzoom','metaballs','moire','morphing','moving shit','noise','palette cycling','parallax','particles','physics','plasma','plotter','polar coordinates','raster bars','raycast','reflection','rotator','rotozoomer','rubbercube','scaling','screens','scroll','scrollshooter','shadebobs','sierpinsky','sinedots','sinewaves','smoke','snow','spiral','sprites','star wars scroll','starfield','stretch','texture mapping','truchet tiles','tunnel','twister','unlimited bobs','vector','voxel landscape','water','windows','wormhole','zoom','zoomrotator');
$do=array('3color','ascii','attributes','border','chunks','gigascreen','multiborder','multicolor');
$sound=array('beeper','digital ay','drum','no sound','speech sample','vocal sample');
$type=array('zapilator','artpack','basic','boot','cracktro','intro','effect example','fuck','game','gift','graphicspack','help','invitation','magazine','megademo','musicpack','player','trackmo');
$feat=array('hardware test','hidden part','interaction','lyrics','message','porno','story');
$size=array('32b','64b','128b','256b','512b','1k','tiny size intro','4k','8k','16k');
$hard=array('TS-Config','Base-config','ATM2','16 colors','320x200','Covox','General Sound','1m',"NeoGS","TurboSound");

$demo_type=array('demo','gift','cracktro');
 function view_tags($n,$t)
 {
  echo '<div class=pull-left><span class="glyphicon glyphicon-tag" aria-hidden="true"></span> ';
	echo '<b>'.$n.'&nbsp;&nbsp;</b></div> <div id=tags> ';
	foreach ($t as $a) echo ' <i>'.$a.'</i> | '; 
	echo '</div>';
 }

 function link_tags($t)
 {
	foreach ($t as $a) echo '<a href="prod.php?tag='.$a.'">'.$a.'</a><br>'; 
 }


function send_mime_mail($name_from, // имя отправителя
						$email_from, // email отправителя
						$name_to, // имя получателя
						$email_to, // email получателя
						$data_charset, // кодировка переданных данных
						$send_charset, // кодировка письма
						$subject, // тема письма
						$body // текст письма
						) {
  $to = mime_header_encode($name_to, $data_charset, $send_charset)
				 . ' <' . $email_to . '>';
  $subject = mime_header_encode($subject, $data_charset, $send_charset);
  $from =  mime_header_encode($name_from, $data_charset, $send_charset)
					 .' <' . $email_from . '>';
  if($data_charset != $send_charset) {
   // $body = iconv($data_charset, $send_charset, $body);
  }
  $headers = "From: $from\r\n";
  $headers .= "Content-type: text/plain; charset=$send_charset\r\n";

  return mail($to, $subject, $body, $headers);
}

function mime_header_encode($str, $data_charset, $send_charset) {
  if($data_charset != $send_charset) {
  //  $str = iconv($data_charset, $send_charset, $str);
  }
  return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}



function show_prod_list($rz,$view_all,$num_in_row,$link)
{
	global $aaa_evo,$role;

	echo '<div class=row>';
    while ($l=mysqli_fetch_array($rz))
   {
	   	$auth=array();
	   	$r=mq('select * from authors,prod_author where prod_id='.$l['gc'].' and authors.cid=author_id');
	   		while ($lz=mysqli_fetch_array($r)) 
	   			{
	   				$auth[$lz['author_id']] =trim($lz['author']);
					$roles[$lz['author_id']]=$lz['role'];
	   			}
		
		$authors='';$group='';
		foreach ($auth as $key=>$value) 
		{
			$authors.=' <a href="prods.php?a='.$key.'">'.$value.'</a> /';
			if ($roles[$key]==Group_role) $group=' <a href="prods.php?a='.$key.'">'.$value.'</a>';
		}
		
//		foreach ($auth as $key=>$value) $authors.=' <a href="prods.php?a='.$key.'">'.$value.'</a> /';
  		$authors=substr($authors,0,-1);

	$gfx= explode(';', $l['gfx']);		
	echo '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-'.$num_in_row.'"><div class="well-sm">';

	if (count($gfx)!=0)	echo '<a class="thumbnail" href="prod.php?p='.$l['gc'].'"><div class="pic img-rounded" style="background-image:url(\''.$gfx[0].'\')"></div></a>';
	$name = str_replace($aaa_evo, "", stripslashes($l['title']));

	echo '<div class="caption">';
	

	if ($l['classic']!=0)echo '<a href=prods.php?classic><span class="pull-right glyphicon glyphicon-king"></span></a>';
	echo '<h3><a href="prod.php?p='.$l['gc'].'">'.$name.'</a></h3><p>';
//favs
	echo '<span class="pull-right glyphicon ';
	echo ($l['fav']!=0 ? 'glyphicon-star">'.$l['fav'].'' : 'glyphicon-star-empty">');
	echo '</span></a>';


	echo ($group!='' ? $group : $authors );
	

		echo ', <a href="prods.php?y='.$l['year'].'">'.$l['year'].'</a></p>
	<div class=pull-right><span class="glyphicon glyphicon-thumbs-up">'.$l['th_up'].'</span> <span class="glyphicon glyphicon-thumbs-down">'.$l['th_down'].'</span>&nbsp;&nbsp;<a href="prod.php?p='.$l['gc'].'#cmnt"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span> '.$l['comments'].'</a></div>
	<a href="'.$l['url'].'" ><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></a>';
	if ($l['verify']!=0) echo '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
      echo '</div></div></div>';
    }

	if ($link!='')  echo '<a href="prods.php" class="pull-right" style="padding: 0 15px 15px 0"><span class="glyphicon glyphicon-th"></span> View all demos</a>';

echo '</div>';
}


function show_comment($rz)
{
    while ($l=mysqli_fetch_array($rz))
    {
	echo '<div class="row"><a href="prod.php?p='.$l['prod_id'].'#cmnt"><img src="';
	echo ($l['avatar']!='' ? $l['avatar'] : 'i/ava.jpg');
	echo '" class=cmnt_img >';
	echo '<b>'.$l['user'].'</b></a><br />';
	echo stripslashes($l['info']); 
	echo '<br /><span class="sub-text"><span class="glyphicon glyphicon-eye-open"></span> '.small_timestamp($l['date']).'</span><a class=pull-right href="prod.php?p='.$l['prod_id'].'#cmnt">'.$l['title'].'</a> </div><br />';
    }
}