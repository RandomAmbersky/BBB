<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'func.php';
session_start();
$db=dbconnect ();


$input_win=array('title','author','year','whom','city','l','a','c','s','ts','fm','saa','gs','m1','moon','tag','d');
$input_int=array('y','w','np','t','py','id','e','classic');
getpost_ifset($input_win); getpost_ifset($input_int);


foreach ($input_win as &$ai) $ai=(htmlspecialchars($ai, ENT_QUOTES, 'win-1251'));
unset($ai);
foreach ($input_int as &$ai) $ai=intval(($ai));
unset($ai);

if (isset($np)) {$num_page=$np=intval(htmlentities($np));} else {$num_page=$np=1;}

$demos_on_page=32;

$parties=$party_logo=$party_url=array(); $parties[]=$party_logo[]=$party_url[]='';
$r=mq('select * from party order by cid');
while ($lx=mysqli_fetch_array($r)) { $parties[$lx['cid']]=$lx['name']; $party_logo[$lx['cid']]=$lx['logo']; $party_url[$lx['cid']]=$lx['party_url']; }


$sql_l=' ';$title=array();

if (isset($s))
{
// Find author
		$sql='select *,gift.cid as gc from gift,prod_author,authors where authors.author="'.strtolower(trim($s)).'" and prod_id=gift.cid and authors.cid=author_id and view=1 ';
		$title[]="Demos from ".$s; 
}
else 
{
$sql='select *,gift.cid as gc from gift where view=1 ';


if (isset($a))  
	{ 
		$sql='select *,gift.cid as gc from gift,prod_author,authors where authors.cid='.$a.' and prod_id=gift.cid and authors.cid=author_id and view=1 GROUP BY gift.cid ';
		$l=mfa ('SELECT * FROM authors where cid='.$a);
		$title[]="Demos from ".$l['author']; 
	}
	//if (isset($w))  { $sql_l.='and whom="'.urldecode($w).'"'; $title="For: ".urldecode($w); }
	if (isset($d))
	{
	// Find demo
		$sql='select *,gift.cid as gc from gift,prod_author,authors where gift.title like "%'.trim(urldecode($d)).'%" and prod_id=gift.cid and authors.cid=author_id and view=1 GROUP BY gift.cid ';
		$title[]="Search by name: ".$d; 

	}
	if (isset($classic))  { $sql_l.='and classic=1'; $title[]="Classic ZX Spectrum demos: "; }
	if (isset($c))  { $sql_l.='and city="'.urldecode($c).'"'; $title[]=" ".urldecode($c); }
	if (isset($py)) { $sql_l.='and party="'.$py.'"'; $title[]=" Party: ".$parties[$py]; }
	if (isset($y))  { $sql_l.='and year="'.$y.'"'; $title[]=" Year: ".$y; }
	if (isset($ts)) { $sql_l.='and TS="1"'; $title[]=" TurboSound"; }
	if (isset($fm)) { $sql_l.='and FM="1"'; $title[]=" FM sound card"; }
	if (isset($gs)) { $sql_l.='and GS="1"'; $title[]=" GeneralSound"; }
	if (isset($saa)) { $sql_l.='and SAA="1"'; $title[]=" SAA sound chip"; }
	if (isset($moon)) { $sql_l.='and moon="1"'; $title[]=" MoonSound"; }
	if (isset($m1)) { $sql_l.='and 1M="1"'; $title[]=" ZX Enchanced"; }
	if (isset($tag)) { $sql='select *,gift.cid as gc from gift where tags like "%'.$tag.'%" and view=1 '; $title[]=" by Tag: ".$tag; }

}
$rad=mq($sql.$sql_l);

$all_prods=mysqli_num_rows($rad);
$total_pages=ceil($all_prods/$demos_on_page);

	$tit='';
	if (count($title)!=0) { foreach ($title as $ax) $tit.= $ax.' ';}
	else { $tit='Full demo list';}
$title=$tit;
include 'nav.php'; 


if (isset($e))
{
	echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only"> Error:</span> Вы ввели неправильный логин/пароль</div>';
}
	$rz=mq($sql.$sql_l.' order by gift.cid desc limit '.(($num_page-1)*$demos_on_page).','.$demos_on_page);
	$demo=array();

	echo '<div class=row><div class="alert alert-info" role="alert">';

if (isset($py))
		if ($party_logo[$py]!='') 
		{
			if ($party_url[$py]!='') echo '<a href='.$party_url[$py].' target=_blank >';
				echo '<img class=pull-right src=party/'.$party_logo[$py].' width=200 >';
			if ($party_url[$py]!='') echo '</a>';
		}

	echo '<h4><span class="glyphicon glyphicon-search"></span> '.$tit.'</h4></div></div>';

	if ($all_prods==0) echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> Ничего не найдено</div>';

show_prod_list($rz,false,3,'');


 if ($total_pages>1)
 {
	  echo '<nav><ul class="pagination pull-right"><li ';
	  if ($num_page==1) { echo 'class="disabled"><a href="#">'; } 
		else { 
			echo '><a class="prev page-numbers" href="prods.php?np='.($num_page-1);
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($d)) echo '&d='.$d;
			if (isset($classic)) echo '&classic';
			if (isset($tag)) echo '&tag='.$tag;
			echo '" >';	
	}
	echo '<span class="icon-text left">◂</span> Prev</a></li> ';
	echo '<li';
 		if ($num_page==1) echo ' class="active"';
	echo '><a href="prods.php?np=1';
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($d)) echo '&d='.$d;
			if (isset($classic)) echo '&classic';
			if (isset($tag)) echo '&tag='.$tag;
	echo '">1</a></li>';	
	echo '<li class=disabled><a>...</a></li>';	
	 for ($i = ($num_page-2); $i < ($num_page+8); $i++) {
	  if ($i > 1 AND $i < $total_pages) 
	  {
		echo '<li';
		if ($num_page==$i) echo ' class="active" ';
		echo '><a href="prods.php?np='.$i;
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($d)) echo '&d='.$d;
			if (isset($classic)) echo '&classic';
			if (isset($tag)) echo '&tag='.$tag;
		echo '">'.$i.'</a></li>';
	  }
	 }

	 echo '<li class=disabled><a>...</a></li>';
	 echo '<li ';
 		if ($num_page==$total_pages) echo 'class="active"';
	 echo '><a href="prods.php?np='.$total_pages;
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($d)) echo '&d='.$d;
			if (isset($classic)) echo '&classic';
			if (isset($tag)) echo '&tag='.$tag;
	 echo '">'.$total_pages.'</a> ';	
	 if ($num_page==$total_pages) { echo '<li class=disabled><a href=# >'; } 
		else { echo '<li><a href="prods.php?np='.($num_page+1);
			if (isset($s)) echo '&s='.$s;
			if (isset($y)) echo '&y='.$y;
			if (isset($a)) echo '&a='.$a;
			if (isset($c)) echo '&c='.$c;
			if (isset($m1)) echo '&m1='.$m1;
			if (isset($py)) echo '&py='.$py;
			if (isset($d)) echo '&d='.$d;
			if (isset($classic)) echo '&classic';
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
