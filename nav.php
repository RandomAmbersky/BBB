<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ru" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php 

if (isset($title)) { echo 'ZX Spectrum DEMO '.$title;}
	else { echo 'ZX Spectrum demo archive';} ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="Author" content="Russia Hackers" />
<meta name="description" content="ZX Spectrum global demo archive. Demoscene is live! View, download ZX Spectrum demos from 1982 to now." />
<meta name="Keywords" content="demoscene, ZX Spectrum, Pentagon, megademo, Party, Speccy, archive demos, demo download">
<meta name="description" content="demoscene, ZX Spectrum, Pentagon, megademo, Party, Speccy, archive demos, demo download">
<script src="https://code.jquery.com/jquery-1.12.4.min.js" ></script>
<!-- Latest compiled and minified CSS -->
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"> 
<link rel="stylesheet" href="js/jquery.tagsinput.css" >
<link rel="stylesheet" href="js/social-likes_birman.css">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous"> -->

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="js/jquery.tagsinput.min.js" /></script>
<script src="js/social-likes.min.js"></script>
<script src="js/bootstrap3-typeahead.js"></script>
<style>

@import url(https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,400italic,700italic,300italic,300&subset=latin,cyrillic);
.caption>h3 {white-space: nowrap; overflow: hidden;}
.caption>p {white-space: nowrap; overflow: hidden;}

@media (min-width: 768px) {
.container {
    padding-top: 50px;
}
.thumbnail {display: inline; }
}

@media (min-width: 992px) {
.container {
    width: 1170px;

}
}
@media (min-width: 1200px) {
.container {
    width: 1170px;
}
}

.well-sm {
    padding: 8px 8px 10px 8px;
	margin: 0px;
    background-color: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-radius: 15px;
    margin-bottom: 15px;
    box-shadow: 0 10px 15px -6px rgba(0, 0, 0, 0.15);
}
.pic
{background-repeat: no-repeat;
background-position: center;
	height: 192px; 
	width:100%;
	background-size: 256px auto;
}
/*
.thumbnail.pic { 
    max-width: 100%;
    max-height: 192px;
    min-height: 192px;
    min-width: 100%;  
	}
	*/
.thumbnail {
    padding: 0px;
	box-shadow: 0px 10px 23px -6px rgba(0,0,0,0.3);
	border-radius: 10px 10px 0 0;
	border:0;
}	
.cmnt_img
{
    width:60px;
    margin-right:5px;
    float:left;
}
.commenterImage {
    width:90px;
    margin-right:5px;
    height:100%;
    float:left;
}
.commenterImage img {
    width:100%;
    border-radius:7%;
}
.sub-text {
    color:#aaa;
    font-size:11px;
}
.dropdown-menu > li > a {
    clear: both;
    color: #333;
    display: block;
    font-weight: 100;
    line-height: 1em;
    white-space: nowrap;
}
.navbar-nav > li > .dropdown-menu {
    margin-top: 0;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    -webkit-columns: 3;
    -moz-columns: 3;
    columns: 3;
}
.nocolumns{ columns: 1 !important; -webkit-columns: 1 !important;    -moz-columns: 1 !important; }
.zxlogo
{
    background-image: url('i/spectrum_stripe_aku1.png');
    background-position: center;
    background-repeat: no-repeat;
    font-size: small;
}
body {
    background-color: #eee;
    color: #333;
    font-family: "Roboto Condensed",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857;
}
#pic {
    width: auto;
    max-width: 100%;
    margin-top: 0px;
    float: right;
}
hr {
    border-color: #ccc -moz-use-text-color -moz-use-text-color;
    }
.pointer
    {cursor:pointer;}
</style>
<script>
function add_ed(user)
{
	$("#editor").val(
		$("#editor").val()+' '+user+', ');
	$("#editor").focus();
}
</script>
<script>
$(document).ready(function(){
	$("#prod_up").click(function () {
		var id = $(this).data('id');
//		$("#rcmnt_img").attr("src",'i/wait16.gif');
		$.get('rate.php?u='+id, function (data) {
//		 	$("#rcmnt_img").attr("src",'i/ch.gif');
			$("#prod_up").html(data); 
		})
	})
	$("#prod_down").click(function () {
		var id = $(this).data('id');
//		$("#rcmnt_img").attr("src",'i/wait16.gif');
		$.get('rate.php?d='+id, function (data) {
//		 	$("#rcmnt_img").attr("src",'i/ch.gif');
			$("#prod_down").html(data); 
		})
	})
	$("#fav").click(function () {
		var id = $(this).data('id');
//		$("#rcmnt_img").attr("src",'i/wait16.gif');
		$.get('fav.php?f='+id, function (data) {
//		 	$("#rcmnt_img").attr("src",'i/ch.gif');
			$("#fav_prod").html(data); 
		})
	})


$('input#typeahead').typeahead({
  source: function (query, process) 
  			{
            	return $.get('auth.php?q=' + query, function (data) 
				{
//					console.log($.parseJSON(data));
	                return process($.parseJSON(data));
    	        }
		  					)
		  }
 })


})
/*
function prod_up(cid,id)
	{
		$("body").append("<div class=loader align=center id=ldr><img src=js/facebox/loading.gif></div>");
		$.get('rate.php?nc='+cid+'&u', function (data) 
		 {
			a=$("#p"+cid).text(); a++; $("#p"+cid).text(a);
			$.modal(data);$("#ldr").remove();
		 });
	}
function prod_down(cid,id)
	{
		$("body").append("<div class=loader align=center id=ldr><img src=js/facebox/loading.gif></div>");
		$.get('rate.php?nc='+cid+'&d', function (data) 
		 {
			a=$("#p"+cid).text(); a--; $("#p"+cid).text(a);
			$.modal(data);$("#ldr").remove();
		 });
	}
*/
</script>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
   <div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	<span class="sr-only">Toggle navigation</span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="/"><?php 
$site=explode ('.',$_SERVER['SERVER_NAME']);
echo strtoupper($site[0]);	//	  BBB
	  ?>
	  </a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" aria-expanded="false" style="height: 1px;">
	  <ul class="nav navbar-nav">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Years <span class="caret"></span></a>
	  <ul class="dropdown-menu">
<li><a href="prods.php?classic">Classic ZX Spectrum demos</a></li>
<?php
$ry=mq('SELECT year, count(cid) FROM gift group by year');
while ($ly=mysqli_fetch_array($ry))
	if ($ly[0]!='0000')	echo '<li><a href="prods.php?y='.$ly[0].'">'.$ly[0].' - '.$ly[1].'</a></li>';
?>
	  </ul>
	</li>
	  </ul>

<?php 
/*
	<ul class="nav navbar-nav">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Authors <span class="caret"></span></a>
	  <ul class="dropdown-menu">
<?php
$ry=mq('SELECT author, count(cid) as cc FROM gift where author not like "%???%" group by author order by cc desc limit 120');
while ($ly=mysqli_fetch_array($ry))
	if ($ly[0]!='')	echo '<li><a href="prods.php?a='.urlencode($ly[0]).'">'.$ly[0].' - '.$ly[1].'</a></li>';
?>
	  </ul>
	</li>
	  </ul>
*/
	  ?>

	  <ul class="nav navbar-nav">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">City <span class="caret"></span></a>
	  <ul class="dropdown-menu">
<?php
$ry=mq('SELECT city, count(cid) as cc FROM gift where city not like "%???%" group by city order by cc desc limit 120');
while ($ly=mysqli_fetch_array($ry))
	if ($ly[0]!='')	echo '<li><a href="prods.php?c='.$ly[0].'">'.$ly[0].' - '.$ly[1].'</a></li>';
?>
	  </ul>
	</li>
	  </ul>  
	 <ul class="nav navbar-nav">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Party <span class="caret"></span></a>
	  <ul class="dropdown-menu">
<?php
$ry=mq('SELECT count(gift.cid) as cc, name, party.cid as pc FROM gift,party where party!=0 and party=party.cid group by name order by cc desc ');
while ($ly=mysqli_fetch_array($ry)) echo '<li><a href="prods.php?py='.$ly['pc'].'">'.$ly['name'].' - '.$ly[0].'</a></li>';
?>
	  </ul>
	</li>
	  </ul>      

	<ul class="nav navbar-nav ">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Extensions <span class="caret"></span></a>
	  <ul class="dropdown-menu nocolumns" >
	<li><a href="prods.php?m1">Enhanced: TS-Config, ATM, misc.</a></li>
	<li role="separator" class="divider"></li>
	<li><a href="prods.php?ts">TurboSound</a></li>
	<li><a href="prods.php?gs">GeneralSound</a></li>
	<li><a href="prods.php?fm">FM soundchip</a></li>
	<li><a href="prods.php?saa">SAA</a></li>
	<li><a href="prods.php?moon">MoonSound</a></li>
<!--	<li role="separator" class="divider"></li>
	<li><a href="http://prods.tslabs.info/">TS-Config prods</a></li> -->
	  </ul>
	</li>
	  </ul>

<form action="prods.php" role="search" class="navbar-form navbar-left hidden-xs hidden-sm">
    <div class="input-group">
      <input type="text" name="s" placeholder="Search author/group"  autocomplete="off" class="form-control" id=typeahead data-provide="typeahead">
      <div class="input-group-addon">
        <button type="submit" class="btn btn-link btn-xs"><span class="glyphicon glyphicon-search"></span></button>
        </div>
  </div>
</form>
<form action="prods.php" role="search" class="navbar-form navbar-left" >
	<div class="input-group">
	  <input type="text" name="d" placeholder="Search demo" class="form-control">
	  <div class="input-group-addon">
		<button type="submit" class="btn btn-link btn-xs"><span class="glyphicon glyphicon-search"></span></button>
		</div>
  </div>
	</form>
<ul class="nav navbar-nav">
	<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
<?php

if (!isset($_SESSION['user'])) 
{
	echo 'Log in<span class="caret"></span></a>
	  <ul class="dropdown-menu nocolumns">
	<form method="post" action="login.php">
	<li><table width=95% style="margin: 6px;">
	<tr><td width=50%>Login: </td><td><input placeholder="Login"  name="login" type="text"></td></tr>
	<tr><td>Password: </td><td><input placeholder="Password"  name="password" type="password"></td></tr>
		<tr><td><input name="submit" type="submit" value="Войти"></td><td align=right><a href=register.php>Registration</a></td></tr></table></li>
	</form>';
} else {
	echo '<span class="glyphicon glyphicon-star" aria-hidden="true"></span> '.$_SESSION['user'].' <span class="caret"></span></a>
	<ul class="dropdown-menu nocolumns">';
	if ($_SESSION['user_type']==2) 
		{
			echo '<li><a href=#>Read only to '.small_timestamp($_SESSION['ban']).'</a></li>';}
			else {
	echo '<li><a href=prod_ed.php>Add demo</a></li>
	<li><a href=scener_prods.php>Your demos</a></li>
	<li><a href=#>Find user:<form action=users.php method=post><input type=text name=user></form></a></li>
	<li role="separator" class="divider"></li>
	<li><a href=profile.php>Profile</a></li>
	<li><a href=req.php>Requests</a></li>';
}
	echo '<li role="separator" class="divider"></li>
	<li><a href=login.php?e>Exit</a></li>';
}
?>	  </ul>

	</li>
	  </ul>    

	  <ul class="zxlogo nav navbar-nav navbar-right visible-lg">
	<li><a href=#><b><font color=black>ZX Spectrum DEMOSCENE archive</font></b></a></li>
	  </ul>
	</div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class=container style="padding-top: 70px">