<?php
require "../../../init.php";
require 'Sinaupload.php';
date_default_timezone_set("Etc/GMT-8");
if(ROLE != ROLE_ADMIN)die("no");

$action=@$_POST['action'];
if($action=='sinaimgbed'){
	$file=$_FILES['file']["tmp_name"];
	
	$tle_sinaimgbed_set=unserialize(ltrim(file_get_contents(dirname(__FILE__).'/sinaimgbed.php'),'<?php die; ?>'));
	$Sinaupload=new Sinaupload('');
	$cookie=$Sinaupload->login($tle_sinaimgbed_set['user'],$tle_sinaimgbed_set['pass']);
	$result=$Sinaupload->upload($file);
	
	echo $result;
}
?>