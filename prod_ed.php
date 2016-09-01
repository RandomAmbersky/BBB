<?php
include 'func.php';
session_start();
$db=dbconnect ();
error_reporting(E_ALL);
ini_set("display_errors", 1);

getpost_ifset(array('addscreen','add','find','title','url','author','roles','nauthor','nauthor_url','del_author','year','whom','city','gfx','video','party','demo','d','party','place','del_party','p','s','e','ed','scroll','dc','ts','moon','saa','fm','m1','gs','tags','upl','ugfx1','ugfx2','ugfx3','ugfx4','ugfx5','ugfx6','verify','compo','classic'));

if (!isset($_SESSION['user_id'])) quit ('/'); 
if ($_SESSION['user_type']==$banned) quit ('/'); 


if (isset($p)&&isset($s))	// delete one screen from prod
{
	$l=mfa('select * from gift where cid='.$p);
	$gfx=''; $old_scr=explode (';',$l['gfx']);
	foreach ($old_scr as $a) if ($a!='') if ($a!=$s) $gfx.=$a.';';

	mq('update gift set gfx="'.addslashes($gfx).'" where cid='.$p);
  mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
  logging ($log_type['delete gfx'],$p,'new: '.$gfx.', old screens: '.$l['gfx']);
  quit('prod_ed.php?p='.$p);
}

if (isset($p)) $ed=$p;


if (isset($find))
{
	$r=mq('select * from gift where title="'.trim($find).'"');	
	if (mysqli_num_rows($r)==0) quit('gift_add.php');
	$l=mysqli_fetch_array($r);
	quit('prod_ed.php?ed='.$l['cid']);
}

if (isset($ts)&&$ts=='on') { $ts=1;} else {$ts=0;}
if (isset($saa)&&$saa=='on'){ $saa=1;} else {$saa=0;}
if (isset($fm)&&$fm=='on')  { $fm=1;} else {$fm=0;}
if (isset($m1)&&$m1=='on')  { $m1=1;} else {$m1=0;}
if (isset($gs)&&$gs=='on')  { $gs=1;} else {$gs=0;}
if (isset($moon)&&$moon=='on') { $moon=1;} else {$moon=0;}

if (isset($e)&&isset($title))	// update prod
{
	if (isset($verify)&&$verify=='on') { $verify=1;} else {$verify=0;}
	if (isset($classic)&&$classic=='on') { $classic=1;} else {$classic=0;}	
	check_authors($author,$e,true,$roles);

	$vid='';foreach ($video as $ax) if ($ax!='') $vid.=$ax.';';
	$vid=substr($vid,0,-1);

	mq('update gift set title="'.addslashes($title).'",year="'.addslashes($year).'",whom="'.addslashes($whom).'",city="'.addslashes($city).'",video="'.addslashes($vid).'",party="'.$party.'",place="'.$place.'",moon="'.$moon.'",SAA="'.$saa.'",TS="'.$ts.'",FM="'.$fm.'", 1M="'.$m1.'",GS="'.$gs.'",tags="'.addslashes($tags).'", view=1, verify="'.$verify.'",compo="'.$compo.'",classic="'.$classic.'" where cid='.$e);

	mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
	logging ($log_type['edit demo'],$e,'edited '.$title);
	quit('prod.php?p='.$e);
}

/*

 include('clupl.php');
 $handle = new upload($_FILES['userfile']);
 if ($handle->uploaded) 
  {
		$handle->allowed = array('application/pdf','application/msword', 'image/*','application/x-rar-compressed','application/x-rar','application/zip','application/x-zip');
		if (!is_dir('u/'.date("ymd"))) mkdir('u/'.date("ymd"), 0755, true);

		$handle->process('u/'.date("ymd"));
	  if ($handle->processed) 
	  {
		$fl=$handle->file_dst_name;
//		chmod('u/'.date("ymd").$fl, 0755);
		mq ('Insert into upl (name,dat,info) values ("'.addslashes(date("ymd").'/'.$fl).'","'.date("Y-m-d").'","'.addslashes($info).'")');
//	echo 'mysql: '.mysqli_error().'<br>';
//	echo $handle->log;		
		$handle->clean();
	  }  else { 
				echo 'error : ' . $handle->error; 
				echo $handle->log;		
				die();}
  }
  
*/

if (isset($title))
{

 include('clupl.php');
 $handle = new upload($_FILES['upl']);
 if ($handle->uploaded) 
  {
		$handle->process('demos/'.date("ymd"));
	  if ($handle->processed) 
	  {
		$fl=$handle->file_dst_name;
		$url='demos/'.date("ymd").'/'.$fl;
		$handle->clean();
	  }  else { echo 'error : ' . $handle->error; echo $handle->log; exit;}
  }

// gfx
$ugf='';
for ($x=1;$x<7;$x++)
{
 if ($_FILES['ugfx'.$x]['name']!='')
 {
	$handle = new upload($_FILES['ugfx'.$x]);
	if ($handle->uploaded) 
	{
		$handle->process('screens/'.date("ymd"));
	  if ($handle->processed) 
	  {
		$fl=$handle->file_dst_name;
		$ugf.='screens/'.date("ymd").'/'.$fl.';';
		$handle->clean();
	  }
	}
 }
}
$gfx=substr($ugf,0,-1);

if (!isset($ts)) $ts=0;
if (!isset($saa))$saa=0;
if (!isset($fm)) $fm=0;
if (!isset($m1)) $m1=0;
if (!isset($gs)) $gs=0;

mq('insert into gift (title,url,year,whom,city,gfx,video,downloads,party,place,moon,SAA,TS,FM,1M,GS,tags,compo,comments,th_up,th_down)
	values ("'.addslashes($title).'","'.addslashes($url).'","'.addslashes($year).'","'.addslashes($whom).'",
	"'.addslashes($city).'","'.addslashes($gfx).'","'.addslashes($video).'","0","'.$party.'","'.$place.'","'.$moon.'","'.$saa.'","'.$ts.'","'.$fm.'","'.$m1.'","'.$gs.'","'.$tags.'","'.$compo.'",0,0,0)');

$pr_id=mysqli_insert_id($db);
check_authors($author,$pr_id,false,$roles);

mq ('insert into prod_user (prod_id,user_id) values ('.$pr_id.','.$_SESSION['user_id'].')');
logging ($log_type['add demo'],$pr_id,'');
mq('update users set uploaded=uploaded+1 where cid='.$_SESSION['user_id']);
	quit('prod.php?p='.$pr_id);
}


if (isset($d))
{
// mq ('delete from gift where cid='.$d);
logging ($log_type['del demo'],$d,'');
quit('prod_ed.php');
}


if (isset($del_party)&&$del_party!='')
{
$l=mfa('select * from party where cid='.$del_party);
mq ('delete from party where cid='.$del_party);
logging ($log_type['del party'], $l['cid'],$l['name']);
mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
quit('prod_ed.php');
}

if (isset($party)&&$party!='')
{
mq ('insert	into party (name) values ("'.addslashes($party).'")');
logging ($log_type['add party'],$party,'');
mq('update users set updated=updated+1 where cid='.$_SESSION['user_id']);
quit('prod_ed.php');
}

 include 'nav.php'; ?>
<script src="js/touch-dnd.js"></script>
<script>
<?php /* https://github.com/xoxco/jQuery-Tags-Input */ ?>
$(document).ready(function() {
  $("#tags").tagsInput({
	'width':'100%',
	'height':'100px',
	'placeholderColor' : '#bbb'
  });

 $("i").click(function () {
  a=$(this).html();
  a=", "+a;
  b=$("#tags").val();
  $("#tags").importTags(b+a);
 });
 <?php 
 if (isset($p))
 {
	 ?>
 $('#imgs').sortable().on('sortable:update', function() {
		var arr=$(this).sortable("toArray");
		console.log(arr);
		$.post('prod_ed_addgfx.php?p=<?php echo $p;?>&s='+arr, function (data) 
				{	
					// console.log(data);	
				});
	})
  <?php 
 }
 ?>
});
function dels() { if (confirm("Уверен, что хочешь удалить?")) {return true;} else { return false; } } 
</script>
<style>
.placeholder
{
	height: 192px;
	width: 256px;
	background-color: transparent;
	border-style: dashed;
}

#proded > input
{ width:100%; }
#tags
{ cursor:pointer; width:100%; }
.au { width:33%; }
</style>

<?
if (isset($add)) echo '<div class="well"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Demo added!</div>';



if (!isset($ed))	// ADD NEW
{
?>
<div class="well"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Add demo to archive</div>
<form id=proded action=prod_ed.php method=post enctype="multipart/form-data" >
<div class=row><div class="col-sm-6 col-md-6">
<div class="well">
<table>
  <tr><td width=30%>Title</td><td><input name="title" type="text"></td></tr>
  <?php /* <tr>    <td>Url</td>		<td><input name="url" type="text"></td> </tr> */ ?>
  <tr><td>Year</td>    <td><input name="year" type="text"></td>  </tr>
  <tr><td valign=top><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Authors: </td><td>
<? 
for ($z=0;$z<6;$z++) 
{
	echo '<input autocomplete="off" id="typeahead" data-provide="typeahead" name=author[] class=au type=text>';
	$x=0;
	echo '<select name=roles[]>';
	foreach ($role as $a) echo '<option value='.$x++.'>'.$a.'</option>';
	echo '</select><br>';
}
 ?>
</td></tr>
  <tr><td>For</td>    <td><input name="whom" type="text"></td>  </tr>
  <tr><td>City</td>  <td><input name="city" type="text"></td></tr>
  <tr><td><b>ZX Enhanced:</b></td><td><input name="m1" type="checkbox"  ></td></tr>
  <tr><td>TurboSound:</td><td><input name="ts" type="checkbox" ></td></tr>
  <tr><td>FM:</td><td><input name="fm" type="checkbox" ></td></tr>
  <tr><td>SAA:</td><td><input name="saa" type="checkbox"  ></td></tr>
  <tr><td>GS:</td><td><input name="gs" type="checkbox"  ></td></tr>
  <tr><td>MoonSound:</td><td><input name="moon" type="checkbox" ></td>  </tr>

  <tr><td>Party</td><td><select name=party><option value=0>---</option>
<?php
$r=mq('select * from party order by name');
	while ($l=mysqli_fetch_array($r)) echo '<option value='.$l['cid'].'>'.$l['name'].'</option>';	
?>
</select>
 Place:<select name=place><option value=0>---</option>
<?php
for ($z=1;$z<16;$z++) echo '<option value='.$z.'>'.$z.'</option>';	
?>
</select>
 
 Compo:<select name=compo><option value=0>---</option>
<?php
$z=0;
foreach ($compo_list as $a)
{ 
	echo '<option value='.$z.'>'.$a.'</option>';	
	$z++;
}
?>
</select>
</td></tr>
  <tr><td><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span>  <font color=gray><b>Upload demo</b></font></td>    <td><input name="upl" type="file"><input type="hidden" name="MAX_FILE_SIZE" value="1900000"></td> </tr>
  </table></div></div>

<div class="col-sm-6 col-md-6"> <div class="well">
<?php /* Gfx urls: разделяй символом ;<input name="gfx" type="text"><br><br> */?>
<span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Upload graphic screens:<br><br>
<input name="ugfx1" type="file">
<input name="ugfx2" type="file">
<input name="ugfx3" type="file">
<input name="ugfx4" type="file">
<input name="ugfx5" type="file">
<input name="ugfx6" type="file"><br><br>
<span class="glyphicon glyphicon-film" aria-hidden="true"></span> Youtube url: <br><input name="video" type="text">
</div></div>
</div>
<div class=well>
<span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Tag cloud: <br>
<input name="tags" id="tags" value="" />
<?php
view_tags('FX:',$fx);
view_tags('DISPLAY OUTPUT:',$do);
view_tags('SOUND:',$sound);
view_tags('TYPE:',$type);
view_tags('FEATURES:',$feat);
view_tags('SIZE:',$size);
view_tags('HARDWARE:',$hard);
?>
</div>
<div class="well" align=center> <button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Submit prod</button></div>
</form><br><br>

<?php
if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin)
{
?>
<form action=prod_ed.php method=post>
<table width=33%>
  <tr><td><b>Add new party</b></td><td>party: <input name="party" type="text"></td></tr>
  <tr><td colspan=2 align=center><input  type="submit"></td></tr>
</table>
</form>
<br>
<br>
<form action=prod_ed.php method=post>
<table width=33%>
  <tr><td><b>!!! Delete party: </b></td><td><select name=del_party>
<?php
$r=mq('select * from party order by name');
	while ($l=mysqli_fetch_array($r)) echo '<option value='.$l['cid'].'>'.$l['name'].'</option>';	
?>
</select></td>
  <tr><td colspan=2 align=center><input  type="submit" onclick="return dels();"></td></tr>
</table>
</form>

<?php
}
 }
else
{	
if ($_SESSION['user_type']!=$admin)
	{
	// check prod author
	$l=mfa('SELECT * FROM gift,prod_user where user_id='.$_SESSION['user_id'].' and gift.cid=prod_id and gift.cid='.$ed);	// check user uploads
	if ($l['cid']==null)
	 {	// check linked authors
		$l=mfa('SELECT gift.cid as gc FROM gift, user_alias, prod_author WHERE user_alias.user_id ='.$_SESSION['user_id'].' AND gift.cid = prod_id AND user_alias.author_id = prod_author.author_id and gift.cid='.$ed);	
		if ($l['gc']==null) 
		{ 
			echo '<div class="well"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Shit! This is <b>NOT Your</b> prod...</div>';
			include 'bottom.php'; 
			exit;
		}
	 }
	}

// EDIT PROD
$l=mfa('select * from gift where cid='.$ed);

echo '<script>
function gfxadd()
{
	var check=$("#cur_gfx").val();
	if (check=="") return;
	var formData = new FormData($("form")[3]);
	$.ajax({
		url: "prod_ed_addgfx.php",  //Server script to process data
		type: "POST",
		success: function (data) { $("#newgfx").append("<img src="+data+" width=256>")},
		// Form data
		data: formData,
		//Options to tell jQuery not to process data or worry about content-type.
		cache: false,
		contentType: false,
		processData: false
	});
}
function fileupdate()
{
	var check=$("#cur_file").val();
	if (check=="") return;

	var formData = new FormData($("form")[3]);
	$.ajax({
		url: "prod_ed_fileupdate.php",  //Server script to process data
		type: "POST",
		success: function (data) { $("#curfile").html(data)},
		// Form data
		data: formData,
		//Options to tell jQuery not to process data or worry about content-type.
		cache: false,
		contentType: false,
		processData: false
	});
}
</script>
<form action=prod_ed.php method=post >
<table border="0" cellspacing="0" cellpadding="0" id=proded>
  <tr>
	<td width=30% >Title:</td><td><input name="title" type="text" value="'.stripslashes($l['title']).'" ></td></tr>
  <tr>    <td>Current Url: <div id=curfile>'.stripslashes($l['url']).'</div></td>
			<td><input name="url" type="file" id=cur_file ><input type=button value="Update file" onclick=fileupdate(); /></td> </tr>
  <tr>    <td>Year:</td>    <td><input name="year" type="text" value="'.stripslashes($l['year']).'" ></td>  </tr>
  <tr>    <td>For:</td>    <td><input name="whom" type="text" value="'.stripslashes($l['whom']).'" ></td>  </tr>';

  
echo '<tr><td valign=top><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Authors: </td><td>';

$rz=mq('SELECT * FROM prod_author, authors WHERE prod_id ='.$ed.' AND author_id = authors.cid');
while ($lz=mysqli_fetch_array($rz))
{
	echo '<input id="typeahead" data-provide="typeahead"  autocomplete="off" name=author[] class=au type=text value="'.$lz['author'].'">';
	$x=0;
	echo '<select name=roles[]>';
	foreach ($role as $a) 
	{ 	
		echo '<option value='.$x;
		if ($lz['role']==$x++) echo ' selected="selected"';
		echo ' >'.$a.'</option>';
	}
	echo '</select><br>';
}
echo '<input id="typeahead" data-provide="typeahead"  autocomplete="off" name=author[] class=au type=text>';
	$x=0;
	echo '<select name=roles[]>';
	foreach ($role as $a) echo '<option value='.$x++.' >'.$a.'</option>';
	echo '</select><br>';
echo '</td></tr>
  <tr><td>City:</td>  <td><input name="city" type="text" value="'.stripslashes($l['city']).'" ></td></tr>
  <tr><td valign=top>Gfx:</td>  <td>';
		$gfx= explode(';', $l['gfx']);          
		if (count($gfx)!=0)
		{
		echo '<div id=imgs>';
		  $x=2;$y=0;
		  foreach ($gfx as $ax) if ($ax!='') echo '<div id="'.$ax.'"><img src="'.$ax.'" width=256></div><a href="prod_ed.php?p='.$l['cid'].'&s='.$ax.'"><span class="glyphicon glyphicon-remove-circle"></span></a><hr>';
		  echo '</div>';
		}

	echo '<div id=newgfx></div><b>Add new screen:</b> <input name="gfx" type="file" id=cur_gfx ><input type=button value="Add screen" onclick=gfxadd(); /></td></tr>';
		$youtube= explode(';', stripslashes($l['video']));          
		if (count($youtube)!=0)
		{
	echo ' <tr><td valign=top>Youtube links:</td><td>';
		  foreach ($youtube as $ax) if ($ax!='') echo '<input name="video[]" type="text" value="'.$ax.'" ><br>';
		  echo '<input name="video[]" type="text" value="" ></td></tr>';
		}
  echo '<tr><td>TS:</td>    <td align=left><input name="ts" type="checkbox" ';
  echo ($l['TS']==0 ? '' : ' checked');
  echo ' ></td></tr>
  <tr><td>FM:</td>    <td align=left><input name="fm" type="checkbox" ';
  echo ($l['FM']==0 ? '' : ' checked');
  echo ' ></td></tr>
  <tr><td>SAA:</td>    <td align=left><input name="saa" type="checkbox" ';
  echo ($l['SAA']==0 ? '' : ' checked');
  echo ' ></td></tr>
  <tr><td>GS:</td>    <td align=left><input name="gs" type="checkbox" ';
  echo ($l['GS']==0 ? '' : ' checked');
  echo ' ></td></tr>
  <tr><td>MoonSound:</td>    <td align=left><input name="moon" type="checkbox" ';
  echo ($l['moon']==0 ? '' : ' checked');
  echo ' ></td></tr>
  <tr><td>ZX Enhanced:</td>    <td align=left><input name="m1" type="checkbox" ';
  echo ($l['1M']==0 ? '' : ' checked');
  echo ' ></td></tr>

  <tr><td>Party</td><td align=left><select name=party>';
  
  echo '<option value=0';
	if ($l['party']==0) echo ' selected=selected ';
  echo '>---</option>';

	$r=mq('select * from party order by name');
	while ($lx=mysqli_fetch_array($r)) 
	{
		echo '<option ';
		if ($l['party']==$lx['cid']) echo ' selected="selected" ';
		echo ' value='.$lx['cid'].' >'.$lx['name'].'</option>';	
	}

echo '</select> Place:<select name=place><option value=0>---</option>';

for ($z=1;$z<16;$z++) { 
	echo '<option value='.$z;
	if ($l['place']==$z) echo ' selected=selected ';
	echo '>'.$z.'</option>';	
}

echo '</select> Compo:<select name=compo>';
$z=0;
foreach ($compo_list as $a){ 
	echo '<option value='.$z;
	if ($l['compo']==$z) echo ' selected=selected ';
	echo '>'.$a.'</option>';	
	$z++;
}
echo '</select></td></tr>';

if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$admin)
{
	echo '<tr><td><hr><b>View on Classic ZX demo?</b><hr></td><td><hr><input type=checkbox name=classic ';
	if ($l['classic']!=0) { echo 'checked'; } 
	echo '><hr></td></tr>';
}
?>

<tr><td><b>Verified by author/admin?</b></td><td><input type=checkbox name=verify <?php 
if ($l['verify']!=0) { echo 'checked'; } 
?> ></td></tr>
<tr><td>Tags:</td><td><input name="tags" id="tags" value="<?php echo $l['tags']; ?>" /></td></tr>
  <tr><td colspan=2 align=center><input type=hidden name=e value=<?php echo $ed; ?> ><button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-check" aria-hidden="true"></span>Sumbit</button></td>  </tr>

</table>
</form>
<?php
view_tags('FX:',$fx);
view_tags('DISPLAY OUTPUT:',$do);
view_tags('SOUND:',$sound);
view_tags('TYPE:',$type);
view_tags('FEATURES:',$feat);
view_tags('SIZE:',$size);
view_tags('HARDWARE:',$hard);
}
?>
</div>

<?php include 'bottom.php'; 

function check_authors($author,$e,$check,$rol)
{
global $db;
if ($check) mq('delete from prod_author where prod_id='.$e);
	$cur_roles=array();
	$x=0;
	foreach ($rol as $a) $cur_roles[$x++]=$a;

	$x=0;
	$auth=array();
	$sql='insert into prod_author (prod_id,author_id,role) values ';
	foreach ($author as $a)
	{
		if ($a!='')
		{
			$l=mfa('select cid from authors where author="'.trim($a).'"');
			$id=$l['cid'];
			if ($id==NULL)
			{
				mq ('insert into authors (author) values ("'.$a.'")');
				$id=mysqli_insert_id($db);
			}
			$sql.='('.$e.','.$id.','.$cur_roles[$x].'),';
		}
		$x++;
	}
	mq (substr($sql,0,-1));
}
?>
