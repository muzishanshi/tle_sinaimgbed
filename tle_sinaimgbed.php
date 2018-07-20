<?php
/*
Plugin Name: 新浪微博图床
Version: 1.0.1
Description: 把图片上传到新浪微博存储
Plugin URL: http://www.tongleer.com/1696.html
ForEmlog: 5.3.1
Author: 二呆
Author URL: http://www.tongleer.com
*/
if(!defined('EMLOG_ROOT')){die('err');}
function tle_sinaimgbed_head(){
	require 'tle_sinaimgbed_output.php';
	echo '<span onclick="tle_sinaimgbed_show(this);" class="show_advset">新浪图床</span>';
}
function tle_sinaimgbed_menu(){echo '<div class="sidebarsubmenu"><a href="./plugin.php?plugin=tle_sinaimgbed">新浪图床</a></div>';}
addAction('adm_writelog_center', 'tle_sinaimgbed_head');
addAction('adm_sidebar_ext', 'tle_sinaimgbed_menu');