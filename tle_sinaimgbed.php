<?php
/*
Plugin Name: 阿里新浪微博图床
Version: 1.0.3
Description: 这是一款简单的Emlog微博图床插件，可把图片上传到阿里新浪微博存储，支持远程和本地链接互相转换、自定义图床链接前缀，可上传到自己的微博相册。
Plugin URL: http://www.tongleer.com/1696.html
ForEmlog: 5.3.1
Author: 二呆
Author URL: http://www.tongleer.com
*/
if(!defined('EMLOG_ROOT')){die('err');}
function tle_sinaimgbed_menu(){
	echo '<div class="sidebarsubmenu" id="tle_sinaimgbed"><a href="./plugin.php?plugin=tle_sinaimgbed">新浪图床</a></div>';
}
addAction('adm_sidebar_ext', 'tle_sinaimgbed_menu');
function tle_sinaimgbed_head(){
	require 'tle_sinaimgbed_output.php';
	echo '<span onclick="tle_sinaimgbed_show(this);" class="show_advset">新浪图床</span>';
}
addAction('adm_writelog_head', 'tle_sinaimgbed_head');
function tle_sinaimgbed_webimg(){
	require 'tle_sinaimgbed_webimg.php';
}
addAction('diff_side', 'tle_sinaimgbed_webimg');

function tle_sinaalibed_head(){
	require 'tle_sinaalibed_output.php';
}
addAction('adm_writelog_head', 'tle_sinaalibed_head');