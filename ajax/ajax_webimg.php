<?php
require "../../../../init.php";
require '../libs/Sinaupload.php';
date_default_timezone_set('Asia/Shanghai');

$DB = Database::getInstance();

$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
$tle_sinaimgbed_set=unserialize($get_option["option_value"]);

$action=empty($_POST['action'])?'':trim($_POST['action']);
if($action=='upload'){
	if($tle_sinaimgbed_set['albumtype']=="weibo"){
		if($tle_sinaimgbed_set['issavealbum']=="y"){
			if($tle_sinaimgbed_set["webimgupload"]!="y"){
				$json=json_encode(array("status"=>"disable","msg"=>"站长已禁用此图床"));echo $json;exit;
			}
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
			$utfname=$time."_".$_FILES["file"]["name"][0];
			$gbkname = iconv("utf-8", "gbk", $utfname);
			move_uploaded_file($_FILES["file"]["tmp_name"][0], dirname(__FILE__).'/'.$gbkname);
			$img=BLOG_URL."content/plugins/tle_sinaimgbed/ajax/".$utfname;
			/* 修改了下风格，并添加文章关键词作为微博话题，提高与其他相关微博的关联率 */
			$string1 = '【新浪图床】';
			$string2 = '来源：https://me.tongleer.com'.BLOG_URL;
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
				$urls=$original_pic;
				$hrefs="<a style='text-decoration:none;' href='".$urls."' target='_blank' title='".$_FILES['file']['name'][0]."'>".$urls."</a>";
				$codes="<a href='".$urls."' target='_blank' title='".$_FILES['file']['name'][0]."'><img src='".$urls."' alt='".$_FILES['file']['name'][0]."' /></a>";
				$json=json_encode(array("status"=>"ok","msg"=>"上传结果","urls"=>$urls,"hrefs"=>$hrefs,"codes"=>$codes));
				echo $json;
			}else{
				$json=json_encode(array("status"=>"fail","msg"=>"上传失败"));
				echo $json;
			}
		}else if($tle_sinaimgbed_set['issavealbum']=="n"){
			if($tle_sinaimgbed_set["weibouser"]==""||$tle_sinaimgbed_set["weibopass"]==""){
				$json=json_encode(array("status"=>"noset","msg"=>"站长暂无配置好此图床"));echo $json;exit;
			}
			if($tle_sinaimgbed_set["webimgupload"]!="y"){
				$json=json_encode(array("status"=>"disable","msg"=>"站长已禁用此图床"));echo $json;exit;
			}
			$urls="";
			$hrefs="";
			$codes="";
			for($i=0,$j=count($_FILES["file"]["name"]);$i<$j;$i++){
				$Sinaupload=new Sinaupload('');
				$cookie=$Sinaupload->login($tle_sinaimgbed_set["weibouser"],$tle_sinaimgbed_set["weibopass"]);
				$result=$Sinaupload->upload($_FILES['file']['tmp_name'][$i]);
				$arr = json_decode($result,true);
				$urls.=$tle_sinaimgbed_set["weiboprefix"].$arr['data']['pics']['pic_1']['pid'].".jpg<br />";
				$hrefs.="<a style='text-decoration:none;' href='".$tle_sinaimgbed_set["weiboprefix"].$arr['data']['pics']['pic_1']['pid'].".jpg' target='_blank' title='".$_FILES['file']['name'][$i]."'>".$tle_sinaimgbed_set["weiboprefix"].$arr['data']['pics']['pic_1']['pid'].".jpg</a><br />";
				$codes.="<a href='".$tle_sinaimgbed_set["weiboprefix"].$arr['data']['pics']['pic_1']['pid'].".jpg' target='_blank' title='".$_FILES['file']['name'][$i]."'><img src='".$tle_sinaimgbed_set["weiboprefix"].$arr['data']['pics']['pic_1']['pid'].".jpg' alt='".$_FILES['file']['name'][$i]."' /></a>\r\n";
			}
			$json=json_encode(array("status"=>"ok","msg"=>"上传结果","urls"=>$urls,"hrefs"=>$hrefs,"codes"=>$codes));
			echo $json;
		}
	}else if($tle_sinaimgbed_set['albumtype']=="ali"){
		$file=$_FILES['file'];
		if(@$file['size'][0]<=1024*1024*3){
			$tempfilename = iconv("utf-8", "gbk", @$file['name'][0]);
			move_uploaded_file(@$file['tmp_name'][0], dirname(__FILE__).'/'.$tempfilename);
			$ch = curl_init();
			$filePath = dirname(__FILE__).'/'.$tempfilename;
			$data = array('file' => "multipart", 'Filedata' => '@' . $filePath);
			if (class_exists('\CURLFile')) {
				$data['Filedata'] = new \CURLFile(realpath($filePath));
			} else {
				if (defined('CURLOPT_SAFE_UPLOAD')) {
					curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
				}
			}
			curl_setopt($ch, CURLOPT_URL, 'https://api.uomg.com/api/image.ali');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$json=curl_exec($ch);
			curl_close($ch);
			@unlink(dirname(__FILE__).'/'.$tempfilename);
			$arr=json_decode($json,true);
			if(isset($arr['data']['fs_url'])){
				$picname=$arr['data']['fs_url'];
				$aliprefix=!empty($tle_sinaimgbed_set['aliprefix'])?$tle_sinaimgbed_set['aliprefix']:"https://ae01.alicdn.com/kf/";
				$urls=$aliprefix . $picname;
				$hrefs="<a style='text-decoration:none;' href='".$urls."' target='_blank' title='".$file['name'][0]."'>".$urls."</a>";
				$codes="<a href='".$urls."' target='_blank' title='".$file['name'][0]."'><img src='".$urls."' alt='".$file['name'][0]."' /></a>";
				$json=json_encode(array("status"=>"ok","msg"=>"上传结果","urls"=>$urls,"hrefs"=>$hrefs,"codes"=>$codes));
			}else{
				$json=json_encode(array("status"=>"fail","msg"=>"上传失败"));
			}
			echo $json;
		}
	}
}
?>