<?
include 'func.php';
session_start();
$db=dbconnect ();
$input_win=array('user','title','author','year','whom','city','l','a','c','s','ts','fm','saa','gs','m1','tag');
$input_int=array('y','w','np','t','py','id');
getpost_ifset($input_win); getpost_ifset($input_int);

// filters
/*
foreach ($input_win as &$ai) $ai=mysqli_real_escape_string(htmlspecialchars($ai, ENT_QUOTES, 'win-1251'));
unset($ai);

foreach ($input_int as &$ai) $ai=intval(mysqli_real_escape_string($ai));
unset($ai);
*/
if (isset($np)) {$num_page=$np=intval(htmlentities($np));} else {$num_page=$np=1;}
if (!isset($user)) $user=$_SESSION['user_id'];
$demos_on_page=30;

/*
$parties=array(); $parties[]='';
$r=mq('select * from party order by cid');
while ($lx=mysqli_fetch_array($r)) $parties[$lx['cid']]=$lx['name'];
*/



include 'nav.php'; 

$l = mfa('SELECT * FROM users WHERE cid='.intval($user).' LIMIT 1');
	echo '<div class="row">
	<div class="col-sm-12" align=center><img src=';
	echo ($l['avatar']!='' ? $l['avatar'] : 'i/ava.jpg');
	echo ' ><h2>'.$l['user'].'</h2></div></div>';

$sql='SELECT *,gift.cid as gc FROM prod_user,gift where user_id='.$user.' and prod_id=gift.cid';
$rad=mq($sql);
$all_prods=mysqli_num_rows($rad);

$total_pages=ceil($all_prods/$demos_on_page);


	$r=mq('SELECT * FROM user_alias, authors WHERE user_id='.$user.' AND author_id = authors.cid ');
	echo '<div class="well"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> List of my other demoscene nicks: ';
    $nks='';
    while ($l=mysqli_fetch_array($r))
    {
      $nks.='<a href="prods.php?a='.$l['author_id'].'"><span class="glyphicon glyphicon-link"></span> '.$l['author'].'</a> | ';
    }
  echo substr ($nks,0,-2).'<br></div>';
  


echo '<div class="well"><span class="glyphicon glyphicon-star" aria-hidden="true"></span> My favorites:</div>';

$rz=mq('SELECT *,gift.cid as gc FROM fav,gift where user_id='.$user.' and prod_id=gift.cid order by gift.cid desc ');
// limit '.(($num_page-1)*$demos_on_page).','.$demos_on_page
show_prod_list($rz,false,3,'');



$l=mfa('select * from users where cid='.$user);
echo '<div class="well"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Demos, uploaded by '.$l['user'].':</div>';

	$rz=mq($sql.' order by gift.cid desc limit '.(($num_page-1)*$demos_on_page).','.$demos_on_page);
show_prod_list($rz,false,3,'');

 if ($total_pages>1)
 {
	  echo '<nav><ul class="pagination pull-right"><li ';
	  if ($num_page==1) { echo 'class="disabled"><a href="#">'; } 
		else { 
			echo '><a class="prev page-numbers" href="?np='.($num_page-1);
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($tag)) echo '&tag='.$tag;
			echo '" >';	
	}
	echo '<span class="icon-text left">◂</span> Prev</a></li> ';
	echo '<li';
 		if ($num_page==1) echo ' class="active"';
	echo '><a href="?np=1';
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($tag)) echo '&tag='.$tag;
	echo '">1</a></li>';	
	echo '<li class=disabled><a>...</a></li>';	
	 for ($i = ($num_page-2); $i < ($num_page+8); $i++) {
	  if ($i > 1 AND $i < $total_pages) 
	  {
		echo '<li';
		if ($num_page==$i) echo ' class="active" ';
		echo '><a href="?np='.$i;
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($tag)) echo '&tag='.$tag;
		echo '">'.$i.'</a></li>';
	  }
	 }

	 echo '<li class=disabled><a>...</a></li>';
	 echo '<li ';
 		if ($num_page==$total_pages) echo 'class="active"';
	 echo '><a href="?np='.$total_pages;
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($tag)) echo '&tag='.$tag;
	 echo '">'.$total_pages.'</a> ';	
	 if ($num_page==$total_pages) { echo '<li class=disabled><a href=# >'; } 
		else { echo '<li><a href="?np='.($num_page+1);
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($tag)) echo '&tag='.$tag;
		echo '" >';	}
	 echo 'Next <span class="icon-text right">▸</span></a></li';
	 echo '</ul></nav>';
 }

/*
if (isset($y))  $sql_l.='and year="'.$y.'"';
if (isset($a))  $sql='select * from gift where author like "%'.urldecode(trim($a)).'%"';
if (isset($c))  $sql_l.='and city="'.urldecode($c).'"';
if (isset($w))  $sql_l.='and whom="'.urldecode($w).'"';
if (isset($py))  $sql_l.='and party="'.$py.'"';
if (isset($id)) $sql_l.='and cid="'.$id.'"';

if (isset($ts)) $sql_l.='and TS="1"';
if (isset($fm)) $sql_l.='and FM="1"';
if (isset($gs)) $sql_l.='and GS="1"';
if (isset($saa)) $sql_l.='and SAA="1"';
if (isset($m1)) $sql_l.='and 1M="1"';
if (isset($tag)) $sql='select * from gift where tags like "%'.$tag.'%"';	
*/

include 'bottom.php';
?>
