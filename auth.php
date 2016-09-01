<?
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset('q','n');

$auth=array();

if (isset($q))
{
	$r=mq('select * from authors where author like "%'.htmlentities($q).'%" order by cid limit 10');
	while ($l=mysqli_fetch_array($r))	$auth[]=$l['author'];
	echo $json = json_encode($auth);
	exit;
}

if (isset($n))
{
echo '666';
	$r=mq('select * from gift where title like "%'.htmlentities($n).'%" order by cid desc limit 10');
	while ($l=mysqli_fetch_array($r)) $auth[]=$l['title'];
	echo $json = json_encode($auth);
	exit;
}
/*
include 'nav.php'; 




$authors=array();
$r=mq('select * from authors where ');
while ($l=mysqli_fetch_array($r))
{
	$auth= explode('/', $l['author']);		
		foreach ($auth as $ax) 
		{ 
			$authors[]=trim($ax);
		}	
}
$authors = array_unique($authors,SORT_STRING);
array_multisort($authors, SORT_ASC,SORT_STRING);
echo count($authors);
//foreach ($authors as $a) { echo $a; }

echo '</div>';
*/