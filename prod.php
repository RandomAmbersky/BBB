<?php
include 'func.php';
session_start();
$db=dbconnect ();
error_reporting(E_ALL);
ini_set("display_errors", 1);
/*
$input_win=array('author','email','info','keystring');
getpost_ifset($input_win);
*/
$input_int=array('p','d','prod','cmnt');
getpost_ifset($input_int);
$p=intval(($p));

if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin)
	if (isset($d))
	{
		$l=mfa('select * from prod_comments where cid='.$d);
		mq ('update gift set comments=comments-1 where cid='.$p);
		$r=mq ('delete from prod_comments where cid='.$d);
		logging ($log_type['delete comment'],$l['prod_id'],'user_id '.$l['user_id'].': '.$l['info']);
		quit ('prod.php?p='.$p);
	}

if (isset($prod)&&isset($cmnt)&&isset($_SESSION['user_id']))
{
	if (trim($cmnt)!='')	
	{
		mq ('update gift set comments=comments+1 where cid='.$p);
		mq ('insert into prod_comments (user_id,info,prod_id,date) values
			("'.$_SESSION['user_id'].'", "'.addslashes(nl2br(htmlentities($cmnt))).'", "'.$prod.'","'.date("Y-m-d H:i").'")');
	}
	quit ('prod.php?p='.$p);
}


$parties=$party_logo=$party_url=array(); $parties[]=$party_logo[]=$party_url[]='';
$r=mq('select * from party order by cid');
while ($lx=mysqli_fetch_array($r)) { $parties[$lx['cid']]=$lx['name']; $party_logo[$lx['cid']]=$lx['logo']; $party_url[$lx['cid']]=$lx['party_url']; }

$auth=array();
$r=mq('select * from gift,prod_author,authors where gift.cid='.$p.' and prod_id=gift.cid and authors.cid=author_id');
while ($l=mysqli_fetch_array($r))
{
	$auth[]=$l['author'];
	$roles[]=$l['role'];
	$auth_id[]=$l['author_id'];	
	$name=$l['title'];
	$url=$l['url'];
	$video=$l['video'];
	$gfx=$l['gfx'];
	$year=$l['year'];
	$added=$l['added'];
	$verify=$l['verify'];
	$cid=$l['cid'];
	$TS=$l['TS'];
	$FM=$l['FM'];
	$SAA=$l['SAA'];
	$moon=$l['moon'];	
	$oneM=$l['1M'];
	$GS=$l['GS'];
	$tags=$l['tags'];
	$whom=$l['whom'];
    $city=$l['city'];
    $party=$l['party'];
	$place=$l['place'];
	$compo=$l['compo'];
	$th_up=$l['th_up'];
	$th_down=$l['th_down'];
	$classic=$l['classic'];
	$fav=$l['fav'];
}

$name = str_replace($aaa_evo, "", stripslashes($name));

include 'nav.php'; ?>

		<script type='text/javascript' src='js/unitegallery/js/unitegallery.min.js'></script> 
		<script src='js/unitegallery/themes/compact/ug-theme-compact.js' type='text/javascript'></script>
		<link rel='stylesheet' href='js/unitegallery/css/unite-gallery.css' type='text/css' /> 
		<script type='text/javascript' src='js/unitegallery/themes/default/ug-theme-default.js'></script> 
		<link rel='stylesheet' href='js/unitegallery/themes/default/ug-theme-default.css' type='text/css' /> 

<?php

	echo '<div class="row"><div class="col-sm-5 col-md-5"> </div><div class="col-sm-7 col-md-7"><a href="'.stripslashes($url).'"><h1>'.$name.'</h1></a></div></div>';
        echo '<div class="row"><div class="col-sm-5 col-md-5" align=center>
	<div id="gallery" style="display:none;">';
	if ($video!='')
		{	
        	$vid= explode(';', $video);		
			if (count($vid)!=0) foreach ($vid as $ax) echo '<img alt="Youtube With Images" data-type="youtube" src="http://img.youtube.com/vi/'.video_code($ax).'/default.jpg" data-image="http://img.youtube.com/vi/'.video_code($ax).'/hqdefault.jpg"	data-videoid="'.video_code($ax).'" data-description="'.$name.' Youtube video">';
		}

		$gfx= explode(';', $gfx);          
		if (count($gfx)!=0)	foreach ($gfx as $ax) { if ($ax!='') echo '<img alt="Image 1 Title" src="'.$ax.'" data-image="'.$ax.'"	data-description="Screens from '.$name.' demo">'; }
	
	echo '</div>
	</div>';
	
	echo '<div class="col-sm-7 col-md-7"><div class="panel panel-default"><div class="panel-body">';

	// fav
	echo '<div id=fav_prod><span  class="pull-right  glyphicon ';

if (isset($_SESSION['user_id']))
{
	$l=mfa('SELECT * FROM fav WHERE user_id = '.$_SESSION['user_id'].' AND prod_id ='.$p.' limit 1');
	echo ($l[0]==null ? 'glyphicon-star-empty pointer': 'glyphicon-star pointer');
 	echo '" id=fav data-id="'.$p.'"';
} else { echo 'glyphicon-star-empty"';}
	echo '>';
	echo ($fav!=0 ? $fav : '' );
	echo '</span></a></div>';

		$authors='';$group='';$cur_author='';
		foreach ($auth as $key=>$value) 
		{
			if ($roles[$key]==Group_role) 
				{ $group .= ' <a href="prods.php?a='.$auth_id[$key].'">'.$value.'</a> '; }
			else
				{ 
				if ($cur_author!=$value){
					$authors.=' <a href="prods.php?a='.$auth_id[$key].'">'.$value.' <span class="sub-text">'.$role[$roles[$key]].',</span></a> '; 
					$cur_author=$value;
				} else {
					$authors.=' <span class="sub-text">'.$role[$roles[$key]].',</span></a> '; 
				}
			}
					
		}


//	$authors='';
//	foreach ($auth as $key=>$value) $authors.=' <a href="prods.php?a='.$key.'">'.$value.': '.$role[$roles[$key]].'</a> /';
  $authors=substr($authors,0,-1);

	
	echo '<p>';
	if ($group!='')   echo '<h4><span class="glyphicon glyphicon-star"></span> by'.$group.'</h4>';
	if ($authors!='') echo '<span class="glyphicon glyphicon-user"></span> Authors: '.$authors;
	echo ' Year: <a href="prods.php?y='.$year.'">'.$year.'</a></p>';
  
$edited=false;
if (isset($_SESSION['user_id']))
{
	if ($_SESSION['user_type']!=$banned) {
		$ua=mfa('select * from prod_user where prod_id='.$p.' and user_id='.$_SESSION['user_id']);
		if ($ua['user_id']==$_SESSION['user_id']) $edited=true;

		$l=mfa('SELECT gift.cid as gc FROM gift, user_alias, prod_author WHERE user_alias.user_id ='.$_SESSION['user_id'].' AND gift.cid = prod_id AND user_alias.author_id = prod_author.author_id and gift.cid='.$p);	
		if ($l['gc']!=null) $edited=true;
	}
}

if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin) $edited=true;
if ($edited==true) echo '<a class=pull-right href=prod_ed.php?p='.$p.'><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';

if ($classic!=0) echo '<p><a href=prods.php?classic><span class="glyphicon glyphicon-king"></span> <font color=red>Classic ZX Spectrum demo</font></a></p>';

$lz=mfa ('SELECT * FROM prod_user, users WHERE prod_id ='.$p.' AND user_id = users.cid');
if ($lz[0]!='') echo '<span class="glyphicon glyphicon-cloud-upload"></span> Uploaded by: <b><a href=users.php?u='.$lz['user_id'].'>'.$lz['user'].'</a></b>';
if ($added!='0000-00-00 00:00:00') echo '&nbsp;&nbsp;&nbsp; <span class="glyphicon glyphicon-time"></span> '.small_timestamp($added);
if ($verify!=0) { echo ' <i>Verified by author/admin</i>'; } 
echo '<blockquote>';
		if ($TS !=0 ||$FM !=0 ||$SAA!=0 ||$oneM !=0 || $GS!=0 || $moon!=0)
		{
			echo 'Hardware type: ';
			echo ($TS !=0 ? '<a href=prods.php?ts=1 title="TurboSound">TurboSound</a> ':'');
			echo ($FM !=0 ? '<a href=prods.php?fm=1 title="FM" >FM soundchip</a> ':'');
			echo ($SAA!=0 ? '<a href=prods.php?saa=1 title="SAA" >SAA soundchip</a> ':'');
			echo ($oneM !=0 ? '<a href=prods.php?m1=1 title="Pentagon 1024,ATMs,ZX Evo" >ZX Enhanced</a> ':'');
			echo ($GS !=0 ? '<a href=prods.php?gs=1 title="GeneralSound">GeneralSound</a>':'');
			echo ($moon!=0 ? '<a href=prods.php?moon=1 title="MoonSound">MoonSound</a> ':'');
		}
        if ($tags!='')
        {
		$tags=explode (',',$tags);
		echo '<br>';
		foreach ($tags as $a) echo '<a href="prods.php?tag='.$a.'"><span class="label label-success">'.$a.'</span></a> ';
        echo '<br>';
        }
echo '</blockquote>';
//        if ($whom!='') echo 'For: <a href="prods.php?w='.urlencode($whom).'">'.$whom.'</a><br>';
        if ($city!='') echo '<br>From: <a href="prods.php?c='.$city.'">'.$city.'</a><br>';
        if ($party!=0) 
        {
			echo '<h3>';
			if ($place!=0) echo addOrdinalNumberSuffix($place).' on ';
					echo '<a href="prods.php?py='.$party.'&y='.$year.'">'.$parties[$party].'\''.$year.'</a>';

					if ($compo!=0) echo ', compo: '.$compo_list[$compo];
					echo '</h3>';
			if ($party_logo[$party]!='') 
			{
				if ($party_url[$party]!='') echo '<a href='.$party_url[$party].' target=_blank >';
					echo '<br /><img src=party/'.$party_logo[$party].' width=200 ><br />';
				if ($party_url[$party]!='') echo '</a>';
			}
		}

$demo_url=$url;
$demo_title=$name.' by '.$authors;
        echo '<br><button type="button" class="btn btn-primary" id=usp data-toggle="modal" ><span class="glyphicon glyphicon-sunglasses" aria-hidden="true"></span> Launch in USP emulator</button>&nbsp;
        <a class="btn btn-success" href="'.$url.'"  role="button"><span class="glyphicon glyphicon-download" aria-hidden="true"></span> Direct download</a>'; 
/* <a class="btn btn-info" href="https://github.com/tslabs/zx-evo/raw/master/pentevo/unreal/Unreal/bin/unreal.7z" title="Download Emulator"  role="button"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Download emulator</a> */        
echo '<br><br>
<div class=well>
	<div class="pull-right lead"><span id=prod_up data-id="'.$p.'" class="glyphicon glyphicon-thumbs-up pointer">'.$th_up.'</span>&nbsp;&nbsp;&nbsp;<span id=prod_down data-id="'.$p.'" class="glyphicon glyphicon-thumbs-down pointer">'.$th_down.'</span></div>
	<div class="social-likes">
		<div class="facebook" title="Поделиться ссылкой на Фейсбуке">Facebook</div>
		<div class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</div>
		<div class="plusone" title="Поделиться ссылкой в Гугл-плюсе">Google+</div>
		<div class="twitter" title="Поделиться ссылкой в Твиттере">Twitter</div>
	</div>	
</div>';
// 	<div class="mailru" title="Поделиться ссылкой в Моём мире">Мой мир</div>
//	<div class="odnoklassniki" title="Поделиться ссылкой в Одноклассниках">Одноклассники</div>

echo '</div></div>';

/*
	if ($l['video']!='')
	{	
                echo '<div class="embed-responsive embed-responsive-4by3">';
	$vid= explode(';', $l['video']);		
	if (count($vid)!=0) 
		{	
		foreach ($vid as $ax) 
			echo '<iframe width="420" height="315" src="http://www.youtube.com/embed/'.video_code($ax).'" frameborder="0" allowfullscreen></iframe>';
		}
	echo '</div>';
        }

        echo '</div></div><BR>';
*/
echo '</div><div class=clearfix></div><br>

	<div class=row><div class=well><a name="cmnt"></a><h3><span class="glyphicon glyphicon-comment" aria-hidden="true"></span> Comments on '.$name.'</h3><hr>';
$r=mq('select *, prod_comments.cid as pcc from prod_comments,users where prod_id='.$p.' and user_id=users.cid order by pcc');
while ($l=mysqli_fetch_array($r))
{
	echo '<div class=row><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><div class="media">
		<a class="pull-left pointer" href=# onclick="add_ed(\''.stripslashes($l['user']).'\');" ><img src=';
	echo ($l['avatar']!='' ? $l['avatar'] : 'i/ava.jpg');
	echo ' data-pin-nopin="true" style="width: 90px;" class="img-rounded"></a>
	<div class="media-body"><h4 class="media-heading">
	<span class="sub-text pull-right">'.small_timestamp($l['date']);
	if (isset($_SESSION['user_id'])) echo ' <a href=users.php?u='.$l['cid'].'><span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>';
	
	echo '</span><a class=pointer onclick="add_ed(\''.stripslashes($l['user']).'\');" >'.stripslashes($l['user']).'</a><span class="sub-text pull-right"></h4>'.stripslashes($l['info']);
	if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin&&$_SESSION['user_id']<3) echo '<a class=pull-right href=prod.php?d='.$l['pcc'].'&p='.$p.' title="delete comment"><span class="glyphicon glyphicon-remove"></span></a>';
	echo '</div>';
	echo '</div></div></div><hr>';
}
echo '';
if (isset($_SESSION['user_id']))
 if ($_SESSION['user_type']!=2)
echo '
<form method=post><div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="media" style="padding-left:5px">
            <a class="pull-left">
            <img class="img-circle" style="width: 90px;" src="'.$_SESSION['avatar'].'"></a>
                <div class="media-body">
                    <h5 style="padding-left: 15px" class="media-heading">
                    Post a comment:
                    </h5>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <form method="post">
                            <textarea rows="2" name="cmnt" id="editor" class="form-control"></textarea>
                            <p></p><input type="hidden" value='.$p.' name="prod">
                            <button class="btn btn-default pull-left" type="submit">Send</button>
                            </form>
                        </div>
                </div>
        </div>
    </div>
</div></form>';

echo '</div></div>';

include 'bottom.php';

function addOrdinalNumberSuffix($num) {
    if (!in_array(($num % 100),array(11,12,13))){
      switch ($num % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $num.'<sup>st</sup>';
        case 2:  return $num.'<sup>nd</sup>';
        case 3:  return $num.'<sup>rd</sup>';
      }
    }
    return $num.'<sup>th</sup>';
  }
?>
<script>
			jQuery(document).ready(function(){ 
				
				var api;
				api = jQuery("#gallery").unitegallery({
					gallery_width:"100%",
					gallery_theme: "compact",
					slider_enable_play_button: false,
					slider_enable_fullscreen_button: false,
					slider_enable_zoom_panel: false,
					strip_control_avia:false
				});
//				api.play();

			}); 

  $('#usp').click(function () {
        var src = <?php echo '"usp.php?p='.$demo_url.'";'; ?>
        $('#myModal').modal('show');
        $('#myModal iframe').attr('src', src);
    });

    $('#myModal button').click(function () {
		$('.modal-body iframe').remove()
    });

</script>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $demo_title; ?></h4>
      </div>
      <div class="modal-body">
      <iframe width="400" height="300" frameborder="0" allowfullscreen=""></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Hide</button>
      </div>
    </div>
  </div>
</div>