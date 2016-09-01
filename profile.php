<?php
include 'func.php';
session_start();
$db=dbconnect ();

getpost_ifset(array('login','password','email','submit','avatar'));

if (isset($_SESSION['user_type'])&&$_SESSION['user_type']==$banned) quit('/');


if(isset($password))
{
 if (isset($_FILES['avatar']))
 {
  include('clupl.php');

  $handle = new upload($_FILES['avatar']);
  if ($handle->uploaded) 
   {

    $handle->allowed = array('image/*');
    $handle->image_resize          = true;
    $handle->image_ratio_crop      = true;
    $handle->image_ratio_no_zoom_in = true;
    $handle->image_x              = 150;
    $handle->image_y              = 150;

      $handle->process('avatars');
      if ($handle->processed) 
    {
    $fl=$handle->file_dst_name;
    mq('update users set avatar="'.addslashes('avatars/'.$fl).'" where cid='.$_SESSION['user_id']);
    $handle->clean();
      }  else { echo 'error : ' . $handle->error; echo $handle->log; exit;}
  }
 }

mq('update users set password="'.trim($_POST['password']).'",email="'.addslashes($email).'" where cid='.$_SESSION['user_id']);
logging ($log_type['change profile'],0,'email: '.$email);
quit('profile.php');

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
  <span class="sr-only">Profile updated</span></div>';
}

echo '
  <div class="row">
    <div class="col-sm-12" align=center><img src='.$user['avatar'].'> <h2 >'.$_SESSION['user'].'</h2>';

  $r=mq('SELECT * FROM user_alias, authors WHERE user_id='.$_SESSION['user_id'].' AND author_id = authors.cid ');
  if ($r!=false)
  {
    echo '<br>My other nicks: ';
    $nks='';
    while ($l=mysqli_fetch_array($r))
    {
      $nks.='<a href="prods.php?a='.$l['author_id'].'"><span class="glyphicon glyphicon-link"></span> '.$l['author'].'</a> | ';
    }
  echo substr ($nks,0,-2).'<br></div></div>';
  }

echo '<div class=clearfix></div>
<form class="form-horizontal" method=post enctype="multipart/form-data" >
  <div class="form-group" id=pass_check>
    <label for="inputPassword" class="col-sm-4 control-label">Password</label>
    <div class="col-sm-8  ">
      <input type="password" name=password class="form-control disabled" id="inputPassword" placeholder="Password" value="'.$user['password'].'">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail" class="col-sm-4 control-label">Email</label>
    <div class="col-sm-8">
      <input type="email" name=email class="form-control" id="inputEmail" value="'.$user['email'].'">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAvatar" class="col-sm-4 control-label">Avatar <br>Size: 150x150px </label>
    <div class="col-sm-8">
      <input type="file" name=avatar class="form-control" id="inputAvatar">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
      <button type="submit" class="btn btn-default">Save changes</button>
    </div>
  </div>
</form>';


include 'bottom.php'; ?>