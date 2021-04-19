<?php
/*
Plugin Name: 阿里新浪微博图床
Version: 1.0.8
Description: 这是一款简单的Emlog微博图床插件，可把图片上传到阿里新浪微博存储，支持远程和本地链接互相转换、自定义图床链接前缀，可上传到自己的微博相册、京东图床、360图床，不过此插件只推荐配合微博同步插件使用微博图床进行上传，其他图床能用就用，不能就不用，若想用其他图床，则请前往https://image.kieng.cn/或https://pic.onji.cn/进行上传
Plugin URL: https://www.tongleer.com/1696.html
ForEmlog: 5.3.1
Author: 二呆
Author URL: https://www.tongleer.com
*/
if(!defined('EMLOG_ROOT')){die('err');}
function tle_sinaimgbed_menu(){
	echo '<div class="layui-nav-item sidebarsubmenu" id="tle_sinaimgbed"><a href="./plugin.php?plugin=tle_sinaimgbed">新浪图床</a></div>';
}
addAction('adm_sidebar_ext', 'tle_sinaimgbed_menu');
function tle_sinaimgbed_head(){
	$DB = Database::getInstance();
	$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
	$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
	if($tle_sinaimgbed_set["isEnableJQuery"]=="y"){
		echo '<script src="https://lib.baomitu.com/jquery/3.4.0/jquery.min.js" type="text/javascript"></script>';
	}
	require 'tle_output_sinaimgbed.php';
	echo '<h3 style="margin: auto;text-align: center;"><b class="layui-btn layui-btn-primary">附加图床</b></h3>';
	require 'tle_output_alibed.php';
	echo '<br />';
	require 'tle_output_qihubed.php';
	echo '<br />';
	require 'tle_output_jdbed.php';
}
addAction('adm_writelog_head', 'tle_sinaimgbed_head');
function tle_sinaimgbed_webimg(){
	$DB = Database::getInstance();
	$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
	$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
	if($tle_sinaimgbed_set['webimgupload']=="y"){
		require 'tle_webimg_bed.php';
	}
}
addAction('diff_side', 'tle_sinaimgbed_webimg');