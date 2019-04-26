<?php
if(!defined('EMLOG_ROOT')){die('error');}

$DB = MySql::getInstance();

$get_option = $DB -> once_fetch_array("SELECT * FROM `".DB_PREFIX."options` WHERE `option_name` = 'tle_sinaimgbed_option' ");
$tle_sinaimgbed_set=unserialize($get_option["option_value"]);
?>
<style>
#sinaimgbed_pick{max-width:35%;}
#sinaimgbed_list{margin:2px 0;font-size:12px;}
.sinaimgbed_pic{margin:2px;width:160px;height:120px;display:inline-block;}
.sinaimgbed_act{padding-top:50px;width:160px;height:70px;background:rgba(0,0,0,0.2);text-align:center;}
.sinaimgbed_act a{color:#FFF;font-weight:bold;text-shadow:1px 1px 3px #000;}
</style>
<script>
var BLOG_URL="<?=BLOG_URL;?>";
function tle_sinaimgbed_show(node){
	var item=document.createElement("div");
	item.setAttribute("id","tle_sinaimgbed_body");
	item.innerHTML="<input type=\"file\" onchange=\"sinaimgbed_cpick()\" id=\"sinaimgbed_pick\"<?php if($tle_sinaimgbed_set["issavealbum"]=="n"){?> multiple=\"multiple\"<?php }?> accept=\"image/jpeg,image/png,image/gif\" /><input type=\"button\" onclick=\"sinaimgbed_cpush()\" id=\"sinaimgbed_push\" value=\"开始\" /><div id=\"sinaimgbed_list\"></div>";
	if(document.getElementById("tle_sinaimgbed_body")){
		node.parentNode.removeChild(document.getElementById("tle_sinaimgbed_body"));
	}
	else{
		node.parentNode.appendChild(item);
	}
}
</script>
<script>
function sinaimgbed_cpick(){
	if(!document.getElementById('sinaimgbed_pick').files.length){
		return;
	}
	sinaimgbed_pool=[];
	sinaimgbed_pid=[];
	Array.prototype.push.apply(sinaimgbed_pool,document.getElementById('sinaimgbed_pick').files);
	document.getElementById('sinaimgbed_push').disabled=false;
	document.getElementById('sinaimgbed_list').innerHTML='';
	for(var numb=0;numb<sinaimgbed_pool.length;numb++){
		document.getElementById('sinaimgbed_list').innerHTML+='<div class="sinaimgbed_pic" id="sinaimgbed_pic'+numb+'"><div class="sinaimgbed_act" id="sinaimgbed_act'+numb+'"><a>'+sinaimgbed_pool[numb].name.substr(0,18)+'...</a></div></div>';
	}
}
function sinaimgbed_cpush(){
	if(!sinaimgbed_pool.length){
		alert('请先选择文件');
		return;
	}
	var ImageFileExtend = ".gif,.png,.jpg,.jpeg,.bmp";
	/*判断后缀*/
	for(var i=0;i<sinaimgbed_pool.length;i++){
		var fileExtend=sinaimgbed_pool[i].name.substring(sinaimgbed_pool[i].name.lastIndexOf('.')).toLowerCase();
		if(ImageFileExtend.indexOf(fileExtend)==-1){
			alert("格式不正确");
			return;
		}
	}
	document.getElementById('sinaimgbed_push').disabled=true;
	sinaimgbed_cpost(0);
}
function sinaimgbed_cpost(numb){
	if(numb>=sinaimgbed_pool.length){
		/*alert('任务结束');*/
		return;
	}
	var xhr=new XMLHttpRequest();
	xhr.open('POST',BLOG_URL+'content/plugins/tle_sinaimgbed/ajax/ajax_upload.php',true);
	xhr.upload.onprogress=function(e){
		if(e.lengthComputable){
			document.getElementById('sinaimgbed_act'+numb).innerHTML='<a>'+parseInt(100*e.loaded/e.total)+'%</a>';
		}
	}
	xhr.onerror=function(e){
		alert('上传失败，执行中断');
		return;
	}
	xhr.onreadystatechange=function(e){
		if(xhr.readyState===4 && xhr.status===200){
			var data=JSON.parse(xhr.responseText);
			if(data.status=="ok"){
				sinaimgbed_pid[numb]=data.url;
				document.getElementById('sinaimgbed_act'+numb).innerHTML="<a href='"+sinaimgbed_pid[numb]+"' target=\"_blank\">上传成功，右键复制图片链接</a><br /><a href=\"javascript:addToEditor('<img src="+sinaimgbed_pid[numb]+" />');\">添加到编辑器</a>";
				document.getElementById('sinaimgbed_pic'+numb).style.backgroundImage="url("+sinaimgbed_pid[numb]+")";
				document.getElementById('sinaimgbed_pic'+numb).style.backgroundRepeat="no-repeat";
				document.getElementById('sinaimgbed_pic'+numb).style.backgroundSize="cover";
			}else{
				document.getElementById('sinaimgbed_act'+numb).innerHTML=data.msg;
			}
			sinaimgbed_cpost(numb+1);
		}
	}
	var upqk=new FormData();
	upqk.append('file',sinaimgbed_pool[numb]);
	upqk.append('action','upload');
	xhr.send(upqk);
}
var sinaimgbed_pool=[],sinaimgbed_pid=[];
function addToEditor(html){
	if(typeof(KindEditor)!='undefined'){
		KindEditor.insertHtml('#content',html);
	}else if(typeof(UE)!='undefined'){
		UE.getEditor('content').execCommand('insertHtml',html);
	}else if(typeof(UM)!='undefined'){
		UM.getEditor('content').execCommand('insertHtml',html);
	}else{
		var msg=document.getElementById('content');
		if(document.selection){
			this.focus();
			var sel=document.selection.createRange();
			sel.text=html;this.focus();
		}else if(msg.selectionStart||msg.selectionStart=='0'){
			var startPos=msg.selectionStart;
			var endPos=msg.selectionEnd;
			var scrollTop=msg.scrollTop;
			msg.value=msg.value.substring(0,startPos)+html+msg.value.substring(endPos, msg.value.length);
			this.focus();
			msg.selectionStart=startPos+html.length;
			msg.selectionEnd=startPos+html.length;
			msg.scrollTop=scrollTop;
		}else{
			this.value+=html;
			this.focus();
		}
	}
}
</script>