<?php
require "../../../../init.php";
require '../libs/Sinaupload.php';
date_default_timezone_set('Asia/Shanghai');
if(ROLE != ROLE_ADMIN)die("no");

$DB = Database::getInstance();
$time=time();

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
			$post_content=str_replace("'","\"",$post_content);
			
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
		$uploadfile=$time.$basename.".png";
		$html = file_get_contents($url);
		file_put_contents(dirname(__FILE__)."/../../../uploadfile/".$uploaddir.$uploadfile, $html);
		$imgurl=BLOG_URL."content/uploadfile/".$uploaddir.$uploadfile;
		$post_content=str_replace($url,$imgurl,$post_content);
		$post_content=str_replace("'","\"",$post_content);
		
		$query = "INSERT INTO " . DB_PREFIX . "attachment (blogid, filename, filesize, filepath, addtime, width, height, mimetype, thumfor) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s', 0)";
		$query = sprintf($query, $postid, $uploadfile, 0, "../content/uploadfile/".$uploaddir.$uploadfile, $time, 0, 0, "image/png");
		$DB->query($query);
		$DB->query("UPDATE " . DB_PREFIX . "blog SET attnum=attnum+1 WHERE gid=$postid");
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	$json=json_encode(array("status"=>"ok","msg"=>"本地化成功"));
	echo $json;
}else if($action=='updateALTCLinks'){
	if(!isset($tle_sinaimgbed_set['aliprefix'])){
		$json=json_encode(array("status"=>"noneconfig","msg"=>"请先配置阿里图床前缀"));
		echo $json;
		exit;
	}
	$postid = isset($_POST['postid']) ? addslashes($_POST['postid']) : '';
	$blog = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."blog` WHERE `gid` = $postid ");
	$post_content = $blog["content"];
	$aliprefix=str_replace("/","\/",$tle_sinaimgbed_set['aliprefix']);
	$aliprefix=str_replace(".","\.",$aliprefix);
	preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$aliprefix.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $submatches );
	foreach($submatches[2] as $url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://www.tongleer.com/api/web/?action=weiboimg&type=ali&imgurl='.$url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($result, 0, $headerSize);
			$body = substr($result, $headerSize);
		}
		curl_close($curl);
		$arr=json_decode($body,true);
		if($arr['code']==0){
			if(isset($arr['data']["src"])){
				$imgurl=$tle_sinaimgbed_set['aliprefix'].basename($arr['data']["src"]);
				$post_content=str_replace($url,$imgurl,$post_content);
				
				if(strpos($url,BLOG_URL)!== false){
					$path=str_replace(BLOG_URL,"",$url);
					$oldpath=EMLOG_ROOT."/".$path;
					@unlink($oldpath);
				}
			}
		}
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	$json=json_encode(array("status"=>"ok","msg"=>"转换成功"));
	echo $json;
}else if($action=='updateQHTCLinks'){
	if(!isset($tle_sinaimgbed_set['qihuprefix'])){
		$json=json_encode(array("status"=>"noneconfig","msg"=>"请先配置奇虎360图床前缀"));
		echo $json;
		exit;
	}
	$postid = isset($_POST['postid']) ? addslashes($_POST['postid']) : '';
	$blog = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."blog` WHERE `gid` = $postid ");
	$post_content = $blog["content"];
	$qihuprefix=str_replace("/","\/",$tle_sinaimgbed_set['qihuprefix']);
	$qihuprefix=str_replace(".","\.",$qihuprefix);
	preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$qihuprefix.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $submatches );
	foreach($submatches[2] as $url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://www.tongleer.com/api/web/?action=weiboimg&type=qihu&imgurl='.$url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($result, 0, $headerSize);
			$body = substr($result, $headerSize);
		}
		curl_close($curl);
		$arr=json_decode($body,true);
		if($arr['code']==0){
			if(isset($arr['data']["src"])){
				$imgurl=$tle_sinaimgbed_set['qihuprefix'].basename($arr['data']["src"]);
				$post_content=str_replace($url,$imgurl,$post_content);
				
				if(strpos($url,BLOG_URL)!== false){
					$path=str_replace(BLOG_URL,"",$url);
					$oldpath=EMLOG_ROOT."/".$path;
					@unlink($oldpath);
				}
			}
		}
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	$json=json_encode(array("status"=>"ok","msg"=>"转换成功"));
	echo $json;
}else if($action=='updateJDTCLinks'){
	if(!isset($tle_sinaimgbed_set['jdprefix'])){
		$json=json_encode(array("status"=>"noneconfig","msg"=>"请先配置京东图床前缀"));
		echo $json;
		exit;
	}
	$postid = isset($_POST['postid']) ? addslashes($_POST['postid']) : '';
	$blog = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."blog` WHERE `gid` = $postid ");
	$post_content = $blog["content"];
	$jdprefix=str_replace("/","\/",$tle_sinaimgbed_set['jdprefix']);
	$jdprefix=str_replace(".","\.",$jdprefix);
	preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$jdprefix.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $submatches );
	foreach($submatches[2] as $url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://www.tongleer.com/api/web/?action=weiboimg&type=jd&imgurl='.$url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($result, 0, $headerSize);
			$body = substr($result, $headerSize);
		}
		curl_close($curl);
		$arr=json_decode($body,true);
		if($arr['code']==0){
			if(isset($arr['data']["src"])){
				$imgurls=explode("/",$arr['data']["src"]);
				if(strpos($imgurls[4],"ERROR")!==false){
					$urls="上传失败换张图片试试";
				}else{
					$urls=substr($arr['data']["src"],strpos($arr['data']["src"],$imgurls[4]));
				}
				$imgurl=$tle_sinaimgbed_set['jdprefix'].$arr['data']["title"];
				$post_content=str_replace($url,$imgurl,$post_content);
				
				if(strpos($url,BLOG_URL)!== false){
					$path=str_replace(BLOG_URL,"",$url);
					$oldpath=EMLOG_ROOT."/".$path;
					@unlink($oldpath);
				}
			}
		}
	}
	$DB->query("UPDATE " . DB_PREFIX . "blog SET content='$post_content' WHERE gid=$postid");
	$json=json_encode(array("status"=>"ok","msg"=>"转换成功"));
	echo $json;
}
?>