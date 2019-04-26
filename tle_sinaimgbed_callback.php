<?php
function callback_init(){
	tle_sinaimgbed_callback_do('y');
}

function callback_rm(){
	tle_sinaimgbed_callback_do('n');
}

function tle_sinaimgbed_callback_do($enable){
	global $CACHE;
	$DB = MySql::getInstance();
	if($enable=="y"){
		$get_option = $DB -> query("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
		$num = $DB -> num_rows($get_option);
		if($num == 0){
			$tle_sinaimgbed_option=serialize(array(
				"weibouser"=>"",
				"weibopass"=>"",
				"issavealbum"=>"n",
				"weiboprefix"=>"https://ws3.sinaimg.cn/large/",
				"webimgupload"=>"n",
				"webimgheight"=>"",
				"webimgbg"=>""
			));
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