<?php
if(!defined('EMLOG_ROOT')){die('err');}
function plugin_setting_view(){
$tle_sinaimgbed_set=unserialize(ltrim(file_get_contents(dirname(__FILE__).'/sinaimgbed.php'),'<?php die; ?>'));
?>
<?php
	$version=file_get_contents('http://api.tongleer.com/interface/TleSinaimgbed.php?action=update&version=1');
	echo $version;
?>
<br />
<form method="post">
微博小号用户名：<input name="user" value="<?php echo $tle_sinaimgbed_set['user']; ?>" /><br />
微博小号密码：<input name="pass" value="<?php echo $tle_sinaimgbed_set['pass']; ?>" /><br />
<br />
<input type="submit" value="提交" />
</form>
<?php }
if(!empty($_POST)){
$user=empty($_POST['user'])?'':trim($_POST['user']);
$pass=empty($_POST['pass'])?'':trim($_POST['pass']);
if(get_magic_quotes_gpc()){
$user=stripslashes($user);
$pass=stripslashes($pass);
}
file_put_contents(dirname(__FILE__).'/sinaimgbed.php','<?php die; ?>'.serialize(array(
	'user'=>$user,
	'pass'=>$pass
)));
header('Location:./plugin.php?plugin=tle_sinaimgbed');
}
?>