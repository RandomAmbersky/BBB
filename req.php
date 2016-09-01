<?php
include 'func.php';
session_start();
$db=dbconnect ();
getpost_ifset(array('i_req','a_req','e','a','u','r'));

if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$banned) quit('/');
if (!isset($_SESSION['user_type'])) quit('/');
if (isset($u)&&isset($r))
{
	$user=mfa('select * from users where cid='.$u);
	logging ($log_type['reject author'],$u,'req from '.$user['user'].' for nick '.urldecode($r).' decline');
	quit ('req.php?e');
}

if (isset($u)&&isset($a))
{
	$author=mfa('select * from authors where author="'.urldecode($a).'"');
	$user=mfa('select * from users where cid='.$u);
	if ($author['cid']!=null)
	{
		mq ('insert into user_alias (user_id,author_id) values ('.$u.','.$author['cid'].')');
		logging ($log_type['set author'],$u,'req from '.$user['user'].' for nick '.$author['author'].' accept');
	}
	quit ('req.php?e');
}


if (isset($i_req))
{
	logging ($log_type['admin info'],$_SESSION['user_id'],$i_req);
	quit ('req.php?e');
}

if (isset($a_req))
{
	logging ($log_type['req author'],$_SESSION['user_id'],$a_req);  
	quit ('req.php?e');
}


/*
    $_SESSION['user']=$data['user'];
    $_SESSION['user_id']=$data['cid'];
    $_SESSION['admin']=$data['admin'];
*/

include 'nav.php'; 
$user=mfa('select * from users where cid='.$_SESSION['user_id']);

if (isset($e))
{
	echo '<div class="alert alert-warning" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span> Request sent</span></div>';
}

echo '
  <div class="row">
    <div class="col-sm-12" align=center><img src='.$user['avatar'].'> <h2 >'.$_SESSION['user'].'</h2></div>
  </div>
<div class=clearfix></div>
<form class="form-horizontal" method=post >
  <div class="form-group" id=pass_check>
    <label for="inputPassword" class="col-sm-4 control-label">Send info to admins</label>
    <div class="col-sm-8  ">
      <textarea name=i_req class="form-control disabled" placeholder="Your information" ></textarea>
    <span class="sub-text">And admin will complete your request. May be ;)</span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
      <button type="submit" class="btn btn-default">Send info</button>
    </div>
  </div>
</form>

<form class="form-horizontal" method=post >
  <div class="form-group">
    <label for="input" class="col-sm-4 control-label">Hey! This nick is MY!</label>
    <div class="col-sm-8">
      <input type="text" name="a_req" autocomplete="off"  placeholder="Search author/group" class="form-control" id=typeahead data-provide="typeahead">
    <span class="sub-text">One nick - one try, please</span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
      <button type="submit" class="btn btn-default">Send request</button>
    </div>
  </div>
</form>';


include 'bottom.php'; ?>