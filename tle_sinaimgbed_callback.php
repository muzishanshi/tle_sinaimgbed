<?php
function callback_init(){
	tle_sinaimgbed_callback_do('y');
}

function callback_rm(){
	tle_sinaimgbed_callback_do('n');
}

function tle_sinaimgbed_callback_do($enable){
	global $CACHE;
	$DB = Database::getInstance();
	if($enable=="y"){
		$get_option = $DB -> query("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
		$num = $DB -> num_rows($get_option);
		if($num == 0){
			$tle_sinaimgbed_option=addslashes(serialize(array(
				"weibouser"=>"",
				"weibopass"=>"",
				"issavealbum"=>"y",
				"weiboprefix"=>"https://ws3.sinaimg.cn/large/",
				"webimgupload"=>"n",
				"albumtype"=>"jd",
				"weibohttps"=>"n",
				"isEnableJQuery"=>"y",
				"webimgheight"=>"",
				"webimgbg"=>"",
				"aliprefix"=>"https://ae01.alicdn.com/kf/",
				"jdprefix"=>"https://img14.360buyimg.com/uba/",
				"qihuprefix"=>"http://p0.so.qhimgs1.com/"
			)));
			$DB -> query("INSERT INTO `".DB_PREFIX."options`  (`option_name`, `option_value`) VALUES('tle_sinaimgbed_option', '".$tle_sinaimgbed_option."') ");
		}
	}else{
		$get_option = $DB -> query("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
		$num = $DB -> num_rows($get_option);
		if($num > 0){
			$DB -> query("DELETE FROM `".DB_PREFIX."options` WHERE option_name='tle_sinaimgbed_option'");
		}
	}
	$CACHE->updateCache('options');
}
?>