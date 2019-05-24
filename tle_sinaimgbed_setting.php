<?php
if(!defined('EMLOG_ROOT')){die('error');}
define('TLESINAIMGBED_VERSION', '2');
if(!empty($_POST)){
	$DB = MySql::getInstance();
	$weibouser=empty($_POST['weibouser'])?'':trim($_POST['weibouser']);
	$weibopass=empty($_POST['weibopass'])?'':trim($_POST['weibopass']);
	$weiboprefix=empty($_POST['weiboprefix'])?'':trim($_POST['weiboprefix']);
	$issavealbum=empty($_POST['issavealbum'])?'':trim($_POST['issavealbum']);
	$webimgupload=empty($_POST['webimgupload'])?'':trim($_POST['webimgupload']);
	$webimgbg=empty($_POST['webimgbg'])?'':trim($_POST['webimgbg']);
	$webimgheight=empty($_POST['webimgheight'])?'':trim($_POST['webimgheight']);
	if(get_magic_quotes_gpc()){
		$weibouser=stripslashes($weibouser);
		$weibopass=stripslashes($weibopass);
		$weiboprefix=stripslashes($weiboprefix);
		$issavealbum=stripslashes($issavealbum);
		$webimgupload=stripslashes($webimgupload);
		$webimgbg=stripslashes($webimgbg);
		$webimgheight=stripslashes($webimgheight);
	}
	$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
	$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
	$tle_sinaimgbed_set["weibouser"]=$weibouser;
	$tle_sinaimgbed_set["weibopass"]=$weibopass;
	$tle_sinaimgbed_set["weiboprefix"]=$weiboprefix;
	$tle_sinaimgbed_set["issavealbum"]=$issavealbum;
	$tle_sinaimgbed_set["webimgupload"]=$webimgupload;
	$tle_sinaimgbed_set["webimgbg"]=$webimgbg;
	$tle_sinaimgbed_set["webimgheight"]=$webimgheight;
	$DB -> query("UPDATE `".DB_PREFIX."options`  SET `option_value` = '".serialize($tle_sinaimgbed_set)."' WHERE `option_name` = 'tle_sinaimgbed_option' ");
	header('Location:./plugin.php?plugin=tle_sinaimgbed');
}
function plugin_setting_view(){
	$DB = MySql::getInstance();
	$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
	$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
	?>
	<div style="background-color:#fff;">
	<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
	  <legend>版本检测</legend>
	</fieldset>
	<div id="versionCode"></div>
	<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
	  <legend>图床设置</legend>
	</fieldset>
	<form class="layui-form" method="post">
		<p>
			是否保存相册：
			<input type="radio" name="issavealbum" value="n" <?=isset($tle_sinaimgbed_set['issavealbum'])?($tle_sinaimgbed_set['issavealbum']=="n"?"checked":""):"checked";?> />否
			<input type="radio" name="issavealbum" value="y" <?=isset($tle_sinaimgbed_set['issavealbum'])?($tle_sinaimgbed_set['issavealbum']=="y"?"checked":""):"";?> />是
			（需开启并配置好<a href="http://www.emlog.net/plugin/310" target="_blank">新浪微博同步插件</a>）
		</p>
		<p>
			微博小号账号：<input style="margin:5px auto;" class="layui-input" name="weibouser" value="<?php echo $tle_sinaimgbed_set['weibouser']; ?>" />
		</p>
		<p>
			微博小号密码：<input style="margin:5px auto;" class="layui-input" type="password" name="weibopass" value="<?php echo $tle_sinaimgbed_set['weibopass']; ?>" />
		</p>
		<p>
			图床链接前缀：<input style="margin:5px auto;" class="layui-input" type="text" name="weiboprefix" placeholder="图床链接前缀" value="<?=$tle_sinaimgbed_set['weiboprefix']?$tle_sinaimgbed_set['weiboprefix']:"https://ws3.sinaimg.cn/large/";?>" />
		</p>
		<p>
			是否开启前台：
			<input type="radio" name="webimgupload" value="n" <?=isset($tle_sinaimgbed_set['webimgupload'])?($tle_sinaimgbed_set['webimgupload']=="n"?"checked":""):"checked";?> />否
			<input type="radio" name="webimgupload" value="y" <?=isset($tle_sinaimgbed_set['webimgupload'])?($tle_sinaimgbed_set['webimgupload']=="y"?"checked":""):"";?> />是
		</p>
		<p>
			前台图床背景：<input style="margin:5px auto;" class="layui-input" type="text" name="webimgbg" placeholder="前台图床背景" value="<?=$tle_sinaimgbed_set['webimgbg'];?>" />
		</p>
		<p>
			前台图床高度：<input style="margin:5px auto;" class="layui-input" type="number" name="webimgheight" placeholder="前台图床高度" value="<?=$tle_sinaimgbed_set['webimgheight'];?>" />
		</p>
		<input type="submit" value="保存配置" />
	</form>
	</div>
	<?php
}
?>
<?php
$DB = MySql::getInstance();

$Log_Model = new Log_Model();
$Tag_Model = new Tag_Model();
$User_Model = new User_Model();

$pid = isset($_GET['pid']) ? $_GET['pid'] : '';
$sid = isset($_GET['sid']) ? intval($_GET['sid']) : '';
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : '';
$keyword = isset($_GET['keyword']) ? addslashes($_GET['keyword']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

//排序
$desc = (isset($_GET['desc']) && $_GET['desc'] == 'SORT_ASC') ?  'SORT_DESC' : 'SORT_ASC';
$sortShare = isset($_GET['sortShare']) ? $_GET['sortShare'] : '';
$sortFlow = isset($_GET['sortFlow']) ? $_GET['sortFlow'] : '';

$sqlSegment = '';
if ($sid) {
	$sqlSegment = "and sortid=$sid";
} elseif ($uid) {
	$sqlSegment = "and author=$uid";
} elseif ($keyword) {
	$sqlSegment = "and title like '%$keyword%'";
}
$sqlSegment .= ' ORDER BY ';
$sqlSegment .= 'top DESC, sortop DESC, date DESC';

$hide_state = $pid ? 'y' : 'n';
if ($pid == 'draft') {
	$hide_stae = 'y';
	$sorturl = '&pid=draft';
	$pwd = '草稿箱';
} else{
	$hide_stae = 'n';
	$sorturl = '';
	$pwd = '文章管理';
}

$logNum = $Log_Model->getLogNum($hide_state, $sqlSegment, 'blog', 1);
$logs = $Log_Model->getLogsForAdmin($sqlSegment, $hide_state, $page);

$sorts = $CACHE->readCache('sort');
$log_cache_tags = $CACHE->readCache('logtags');
$tags = $Tag_Model->getTag();

$subPage = '';
foreach ($_GET as $key=>$val) {
	$subPage .= $key != 'page' ? "&$key=$val" : '';
}
$pageurl =  pagination($logNum, Option::get('admin_perpage_num'), $page, "./plugin.php?plugin=tle_sinaimgbed&{$subPage}&page=");

$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
?>
<div class="filters">
<div id="f_title">
	<div style="float:left; margin-top:8px;">
		<span <?php echo !$sid && !$tagId && !$uid && !$keyword ? "class=\"filter\"" : ''; ?>><a href="./plugin.php?plugin=tle_sinaimgbed&?<?php echo $isdraft; ?>">全部</a></span>
        <span id="f_t_sort">
            <select name="bysort" id="bysort" onChange="selectSort(this);" style="width:110px;">
            <option value="" selected="selected">按分类查看...</option>
            <?php 
            foreach($sorts as $key=>$value):
            if ($value['pid'] != 0) {
                continue;
            }
            $flg = $value['sid'] == $sid ? 'selected' : '';
            ?>
            <option value="<?php echo $value['sid']; ?>" <?php echo $flg; ?>><?php echo $value['sortname']; ?></option>
            <?php
                $children = $value['children'];
                foreach ($children as $key):
                $value = $sorts[$key];
                $flg = $value['sid'] == $sid ? 'selected' : '';
            ?>
            <option value="<?php echo $value['sid']; ?>" <?php echo $flg; ?>>&nbsp; &nbsp; &nbsp; <?php echo $value['sortname']; ?></option>
            <?php
            endforeach;
            endforeach;
            ?>
            <option value="-1" <?php if($sid == -1) echo 'selected'; ?>>未分类</option>
            </select>
        </span>
        <?php if (ROLE == ROLE_ADMIN && count($user_cache) > 1):?>
        <span id="f_t_user">
            <select name="byuser" id="byuser" onChange="selectUser(this);" style="width:110px;">
                <option value="" selected="selected">按作者查看...</option>
                <?php 
                foreach($user_cache as $key=>$value):
                $flg = $key == $uid ? 'selected' : '';
                ?>
                <option value="<?php echo $key; ?>" <?php echo $flg; ?>><?php echo $value['name']; ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </span>
        <?php endif;?>
	</div>
	<div style="float:right;">
		<form class="layui-form" action="./plugin.php" method="get">
		<input type="hidden" name="plugin" value="tle_sinaimgbed">
		<input class="layui-input" type="text" id="input_s" name="keyword">
		<?php if($pid):?>
		<input type="hidden" id="pid" name="pid" value="draft">
		<?php endif;?>
		</form>
	</div>
	<div style="clear:both"></div>
</div>
</div>
<form class="layui-form" action="./plugin.php?plugin=tle_sinaimgbed&action=operate_log" method="post" name="form_log" id="form_log">
  <input type="hidden" name="pid" value="<?php echo $pid; ?>">
  <table width="100%" id="adm_log_list" class="layui-table">
  <thead>
      <tr>
        <th width="511" colspan="2"><b>标题</b></th>
		<?php if ($pid != 'draft'): ?>
		<th width="40" class="tdcenter"><b>查看</b></th>
		<?php endif; ?>
		<th width="100"><b>作者</b></th>
        <th width="100"><b>分类</b></th>
        <th width="160"><b>时间</b></th>
		<th width="40" class="tdcenter"><b>评论</b></th>
		<th width="50" class="tdcenter"><b>阅读</b></th>
		<th width="200" class="tdcenter"><b>阅读</b></th>
      </tr>
	</thead>
 	<tbody>
	<?php
	$logNum = $Log_Model->getLogNum($hide_state, $sqlSegment, 'blog', 1);
	$logs = $Log_Model->getLogsForAdmin($sqlSegment, $hide_state, $page);
	if($logs):
	foreach($logs as $key=>$value):
	$sortName = $value['sortid'] == -1 && !array_key_exists($value['sortid'], $sorts) ? '未分类' : $sorts[$value['sortid']]['sortname'];
	$author = $user_cache[$value['author']]['name'];
	?>
      <tr>
      <td width="21"><input type="checkbox" name="blog[]" value="<?php echo $value['gid']; ?>" class="ids" lay-skin="primary" /></td>
      <td width="490">
		<a href="write_log.php?action=edit&gid=<?php echo $value['gid']; ?>"><?php echo $value['title']; ?></a>
      </td>
	  <?php if ($pid != 'draft'): ?>
	  <td class="tdcenter">
	  <a href="<?php echo Url::log($value['gid']); ?>" target="_blank" title="在新窗口查看">
	  <img src="./views/images/vlog.gif" align="absbottom" border="0" /></a>
	  </td>
	  <?php endif; ?>
      <td><?php echo $author; ?></td>
      <td><?php echo $sortName; ?></td>
      <td><?php echo $value['date']; ?></td>
	  <td class="tdcenter"><a href="comment.php?gid=<?php echo $value['gid']; ?>"><?php echo $value['comnum']; ?></a></td>
	  <td class="tdcenter"><?php echo $value['views']; ?></td>
	  <td class="tdcenter">
		<?php
		$post_content = $value['content'];
		preg_match_all( "/<(img|IMG).*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/", $post_content, $matches );
		if(count($matches[2])>0){
			//转换微博图传链接
			$tle_weiboprefix=str_replace("/","\/",$tle_sinaimgbed_set['weiboprefix']);
			$tle_weiboprefix=str_replace(".","\.",$tle_weiboprefix);
			preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$tle_weiboprefix.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $submatches );
			if(count($submatches[2])>0){
				echo '
					<a href="javascript:;" class="tle_sinaimgbed_convert_id" id="tle_sinaimgbed_convert_id'.$value['gid'].'" data-id="'.$value['gid'].'">转换</a>
				';
			}else{
				echo '无需转换';
			}
			//图片本地化
			$blogurl=str_replace("/","\/",BLOG_URL);
			$blogurl=str_replace(".","\.",$blogurl);
			preg_match_all( "/<(img|IMG).*?src=[\'|\"](?!".$blogurl.")(.*?)[\'|\"].*?[\/]?>/", $post_content, $localmatches );
			if(count($localmatches[2])>0){
				echo '
					<a href="javascript:;" class="tle_sinaimgbed_local_id" id="tle_sinaimgbed_local_id'.$value['gid'].'" data-id="'.$value['gid'].'">本地化</a>
				';
			}else{
				echo '无需本地化';
			}
		}else{
			echo '未包含图片';
		}
		?>
	  </td>
      </tr>
	<?php endforeach;else:?>
	  <tr><td class="tdcenter" colspan="8">还没有文章</td></tr>
	<?php endif;?>
	</tbody>
	</table>
    <input name="token" id="token" value="<?php echo LoginAuth::genToken(); ?>" type="hidden" />
	<input name="operate" id="operate" value="" type="hidden" />
</form>
<center>
	<div class="page"><?php echo $pageurl; ?> (有<?php echo $logNum; ?>篇<?php echo $pid == 'draft' ? '草稿' : '文章'; ?>)</div>
</center>
<script>
$(document).ready(function(){
	layui.use(["form"], function(){
		var form = layui.form;
	});
	$("#adm_log_list tbody tr:odd").addClass("tralt_b");
	$("#adm_log_list tbody tr")
		.mouseover(function(){$(this).addClass("trover");$(this).find("span").show();})
		.mouseout(function(){$(this).removeClass("trover");$(this).find("span").hide();});
});
setTimeout(hideActived,2600);
function selectSort(obj) {
    window.open("./plugin.php?plugin=tle_sinaimgbed&sid=" + obj.value + "<?php echo $isdraft?>", "_self");
}
function selectUser(obj) {
    window.open("./plugin.php?plugin=tle_sinaimgbed&uid=" + obj.value + "<?php echo $isdraft?>", "_self");
}
$("#tle_sinaimgbed").addClass('sidebarsubmenu1');

$(".tle_sinaimgbed_convert_id").each(function(){
	var id=$(this).attr("id");
	$("#"+id).click( function () {
		$.post("<?=BLOG_URL.'content/plugins/tle_sinaimgbed/ajax/ajax_sync.php';?>",{action:"updateWBTCLinks",postid:$(this).attr("data-id")},function(data){
			var data=JSON.parse(data);
			if(data.status=="noneconfig"){
				alert(data.msg);
			}
			window.location.reload();
		});
	});
});
$(".tle_sinaimgbed_local_id").each(function(){
	var id=$(this).attr("id");
	$("#"+id).click( function () {
		$.post("<?=BLOG_URL.'content/plugins/tle_sinaimgbed/ajax/ajax_sync.php';?>",{action:"localWBTCLinks",postid:$(this).attr("data-id")},function(data){
			window.location.reload();
		});
	});
});
$.post("<?=BLOG_URL.'content/plugins/tle_sinaimgbed/ajax/update.php';?>",{version:"<?=TLESINAIMGBED_VERSION;?>"},function(data){
	$("#versionCode").html(data);
});
</script>