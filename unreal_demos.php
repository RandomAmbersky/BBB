<?
include 'func.php';
$db=dbconnect ();

// ob_start('ob_gzhandler');
ob_start();
//ob_implicit_flush(0); // отключаем неявную отправку буфера

$input_win=array('l');
getpost_ifset($input_win); 


// filters
foreach ($input_win as &$ai) $ai=htmlspecialchars($ai, ENT_QUOTES, 'win-1251');
unset($ai);


if (!isset($l)) $l='a';
$sql='select * from gift where substring(title,1,1)="'.$l.'" and view=1 ';
if ($l=='@') $sql='select * from gift where substring(title,1,1) IN ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9") and view=1 ';

// $rad=mq($sql);
// $all_gifts=mysql_num_rows($rad);

// echo $sql.$sql_l.' order by title limit '.(($num_page-1)*$gifts_on_page).','.$gifts_on_page;
$rz=mq($sql.' order by title ');

    while ($lx=mysqli_fetch_array($rz))
    {

		echo '{"title":"'.addslashes($lx['title']).'","url":"http://bbb.retroscene.org/'.$lx['url'].'",';

//		 $gfx= explode(';', $lx['gfx']);		
//		if (count($gfx)!=0)	echo '"gfx":"http://zxaaa.untergrund.net/'.$gfx[0].'",';

		echo '"year":"'.$lx['year'].'",';
//		echo '<td><a href="?w='.urlencode($lx['whom']).'">'.$lx['whom'].'</td>';

		echo '"author":"';
		$auth= explode('/', $lx['author']);		
		$authors='';
		foreach ($auth as $ax) 	$authors.=addslashes($ax).'/';
		echo substr($authors,0,-1);
		echo '",';

		echo '"city":"'.addslashes($lx['city']).'"},';

    }
/* Здесь идет код скрипта, в нем не должно быть ob_flush, так как потом нельзя будет выдавать заголовки */
header('Content-Length: '.ob_get_length()); // если заголовки еще можно отправить, выдаем загловок Content-Length, иначе придется завершать передачу по закрытию

ob_end_flush();
exit;
?>