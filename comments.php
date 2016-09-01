<?
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset('np');
if (isset($np)) {$num_page=$np=intval(htmlentities($np));} else {$num_page=$np=1;}

$demos_on_page=32;
$sql='SELECT * FROM prod_comments, gift, users WHERE prod_id = gift.cid AND users.cid = user_id ';
$title='Comments list:';
$rad=mq($sql.$sql_l);
$all_prods=mysqli_num_rows($rad);
$total_pages=ceil($all_prods/$demos_on_page);

include 'nav.php'; 
	echo '<div class=row><div class="alert alert-info" role="alert"><h4><span class="glyphicon glyphicon-search"></span> BBB comments:</h4></div></div>';
	$r=mq('SELECT * FROM prod_comments, gift, users
WHERE prod_id = gift.cid
AND VIEW =1
AND users.cid = user_id
order by prod_comments.cid desc limit '.(($num_page-1)*$demos_on_page).','.$demos_on_page);
	show_comment($r);


 if ($total_pages>1)
 {
	  echo '<nav><ul class="pagination pull-right"><li ';
	  if ($num_page==1) { echo 'class="disabled"><a href="#">'; } 
		else { 
			echo '><a class="prev page-numbers" href="comments.php?np='.($num_page-1).'" >';	
	}
	echo '<span class="icon-text left">◂</span> Prev</a></li> ';
	echo '<li';
 		if ($num_page==1) echo ' class="active"';
	echo '><a href="comments.php?np=1">1</a></li>';	

	echo '<li class=disabled><a>...</a></li>';	
	 for ($i = ($num_page-2); $i < ($num_page+8); $i++) {
	  if ($i > 1 AND $i < $total_pages) 
	  {
		echo '<li';
		if ($num_page==$i) echo ' class="active" ';
		echo '><a href="comments.php?np='.$i.'">'.$i.'</a></li>';
	  }
	 }

	 echo '<li class=disabled><a>...</a></li>';
	 echo '<li ';
 		if ($num_page==$total_pages) echo 'class="active"';
	 echo '><a href="comments.php?np='.$total_pages.'">'.$total_pages.'</a> ';	
	 if ($num_page==$total_pages) { echo '<li class=disabled><a href=# >'; } 
		else { echo '<li><a href="comments.php?np='.($num_page+1).'" >';	}
	 echo 'Next <span class="icon-text right">▸</span></a></li';
	 echo '</ul></nav>';
 }


include 'bottom.php'; 