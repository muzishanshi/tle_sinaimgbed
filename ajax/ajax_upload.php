<?php
require "../../../../init.php";
require '../libs/Sinaupload.php';
date_default_timezone_set('Asia/Shanghai');
if(ROLE != ROLE_ADMIN)die("no");

$DB = Database::getInstance();

$action=empty($_POST['action'])?'':trim($_POST['action']);
if($action=='upload'){
	$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
	$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
	
	if($tle_sinaimgbed_set['issavealbum']=="y"){
		if (file_exists(dirname(__FILE__).'/../../sinav2/saetv2.ex.class.php')) {
			if(!class_exists('SaeTOAuthV2')&&!class_exists('SaeTClientV2')){
				require_once dirname(__FILE__) . '/../../sinav2/saetv2.ex.class.php';
			}
		}
		if (file_exists(dirname(__FILE__).'/../../sinav2/sinav2_token_conf.php')) {
			include( dirname(__FILE__).'/../../sinav2/sinav2_token_conf.php' );
		}
		if (file_exists(dirname(__FILE__).'/../../sinav2/sinav2_config.php')) {
			include( dirname(__FILE__).'/../../sinav2/sinav2_config.php' );
		}
		if (!defined('SINAV2_ACCESS_TOKEN')||!defined('SINAV2_AKEY')||!defined('SINAV2_SKEY')){
			$json=json_encode(array("status"=>"notoauth","msg"=>"请先开启新浪微博同步插件并授权"));
			echo $json;
			exit;
		}
		$time=time();
		$utfname=$time."_".$_FILES["file"]["name"];
		$gbkname = iconv("utf-8", "gbk", $utfname);
		move_uploaded_file($_FILES["file"]["tmp_name"], dirname(__FILE__).'/'.$gbkname);
		$img=BLOG_URL."content/plugins/tle_sinaimgbed/ajax/".$utfname;
		/* 修改了下风格，并添加文章关键词作为微博话题，提高与其他相关微博的关联率 */
		$string1 = '【新浪图床】';
		$string2 = '来源：'.BLOG_URL;
		/* 微博字数控制，避免超标同步失败 */
		$postData = $string1.mb_strimwidth("",0, 140,'...').$string2;
		$c = new SaeTClientV2( SINAV2_AKEY , SINAV2_SKEY , SINAV2_ACCESS_TOKEN );
		$arr=$c->share($postData,$img);
		@unlink(dirname(__FILE__).'/'.$gbkname);
		if(isset($arr["original_pic"])){
			if($tle_sinaimgbed_set["weibohttps"]=="y"){
				$original_pic=str_replace("http://","https://",$arr["original_pic"]);
			}else{
				$original_pic=$arr["original_pic"];
			}
			$json=json_encode(array("status"=>"ok","msg"=>"上传结果","url"=>$original_pic));
			echo $json;
		}else{
			$json=json_encode(array("status"=>"fail","msg"=>"上传失败"));
			echo $json;
		}
	}else if($tle_sinaimgbed_set['issavealbum']=="n"){
		$Sinaupload=new Sinaupload('');
		$cookie=$Sinaupload->login($tle_sinaimgbed_set['weibouser'],$tle_sinaimgbed_set['weibopass']);
		$result=$Sinaupload->upload($_FILES['file']["tmp_name"]);
		$arr = json_decode($result,true);
		if(isset($arr['data']['pics']['pic_1']['pid'])){
			$json=json_encode(array("status"=>"ok","msg"=>"上传结果","url"=>$tle_sinaimgbed_set['weiboprefix'].$arr['data']['pics']['pic_1']['pid'].".jpg"));
			echo $json;
		}else{
			$json=json_encode(array("status"=>"fail","msg"=>"上传失败"));
			echo $json;
		}
	}
}
?>