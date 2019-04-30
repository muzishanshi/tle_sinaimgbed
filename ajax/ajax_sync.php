<?php
require "../../../../init.php";
require '../libs/Sinaupload.php';
date_default_timezone_set('Asia/Shanghai');
if(ROLE != ROLE_ADMIN)die("no");

$DB = MySql::getInstance();

$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
$tle_sinaimgbed_set=unserialize($get_option["option_value"]);

$action=empty($_POST['action'])?'':trim($_POST['action']);
if($action=='updateWBTCLinks'){
	if(!isset($tle_sinaimgbed_set['weibouser']) || !isset($tle_sinaimgbed_set['weibopass'])){
		$json=json_encode(array("status"=>"noneconfig","msg"=>"请先配置微博小号"));
		echo $json;
		exit;
	}
	$postid = isset($_POST['postid']) ? addslashes($_POST['postid']) : '';
	
	$blog = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."blog` WHERE `gid` = $postid ");
	$post_content = $blog["content"];
	$tle_weiboprefix=str_replace("/","\/",$tle_sinaimgbed_set['weiboprefix']);
	$tle_weiboprefix=str_replace(".","\.",$tle_weiboprefix);
	preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$tle_weiboprefix.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $submatches );
	$savepath=dirname(__FILE__)."/x.jpg";
	foreach($submatches[2] as $url){
		$html = file_get_contents($url);
		file_put_contents($savepath, $html);
		$Sinaupload=new Sinaupload('');
		$cookie=$Sinaupload->login($tle_sinaimgbed_set['weibouser'],$tle_sinaimgbed_set['weibopass']);
		$result=$Sinaupload->upload($savepath);
		$arr = json_decode($result,true);
		if(isset($arr['data']['pics']['pic_1']['pid'])){
			$imgurl="".$tle_sinaimgbed_set['weiboprefix'].$arr['data']['pics']['pic_1']['pid'].".jpg";
			$post_content=str_replace($url,$imgurl,$post_content);
			
			if(strpos($url,BLOG_URL)!== false){
				$path=str_replace(BLOG_URL,"",$url);
				$oldpath=EMLOG_ROOT."/".$path;
				@unlink($oldpath);
			}
		}
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	@unlink($savepath);
	$json=json_encode(array("status"=>"ok","msg"=>"转换成功"));
	echo $json;
}else if($action=='localWBTCLinks'){
	$uploaddir=date("Y").date("m")."/";
	if(!is_dir(dirname(__FILE__)."/../../../uploadfile/".$uploaddir)){
		mkdir (dirname(__FILE__)."/../../../uploadfile/".$uploaddir, 0777, true );
	}
	
	$postid = isset($_POST['postid']) ? addslashes($_POST['postid']) : '';
	
	$blog = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."blog` WHERE `gid` = $postid ");
	$post_content = $blog["content"];
	$blogurl=str_replace("/","\/",BLOG_URL);
	$blogurl=str_replace(".","\.",$blogurl);
	preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$blogurl.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $localmatches );
	
	foreach($localmatches[2] as $url){
		$basename=basename($url);
		if(strpos($basename,"?")!== false){
			$basename=explode("?",$basename)[0];
		}
		$uploadfile=time().$basename.".png";
		$html = file_get_contents($url);
		file_put_contents(dirname(__FILE__)."/../../../uploadfile/".$uploaddir.$uploadfile, $html);
		$imgurl=BLOG_URL."content/uploadfile/".$uploaddir.$uploadfile;
		$post_content=str_replace($url,$imgurl,$post_content);
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	$json=json_encode(array("status"=>"ok","msg"=>"本地化成功"));
	echo $json;
}
?>