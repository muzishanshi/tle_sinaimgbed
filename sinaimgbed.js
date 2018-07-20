function sinaimgbed_cpick(){
if(!document.getElementById('sinaimgbed_pick').files.length){return;}
sinaimgbed_pool=[];sinaimgbed_pid=[];Array.prototype.push.apply(sinaimgbed_pool,document.getElementById('sinaimgbed_pick').files);
document.getElementById('sinaimgbed_push').disabled=false;
document.getElementById('sinaimgbed_list').innerHTML='';
for(var numb=0;numb<sinaimgbed_pool.length;numb++){
document.getElementById('sinaimgbed_list').innerHTML+='<div class="sinaimgbed_pic" id="sinaimgbed_pic'+numb+'"><div class="sinaimgbed_act" id="sinaimgbed_act'+numb+'"><a>'+sinaimgbed_pool[numb].name.substr(0,18)+'...</a></div></div>';
}
}
function sinaimgbed_cpush(){
if(!sinaimgbed_pool.length){alert('请先选择文件');return;}
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
xhr.open('POST',BLOG_URL+'content/plugins/tle_sinaimgbed/tle_sinaimgbed_ajax.php',true);
xhr.upload.onprogress=function(e){if(e.lengthComputable){document.getElementById('sinaimgbed_act'+numb).innerHTML='<a>'+parseInt(100*e.loaded/e.total)+'%</a>';}}
xhr.onerror=function(e){alert('上传失败，执行中断');return;}
xhr.onreadystatechange=function(e){if(xhr.readyState===4 && xhr.status===200){
	sinaimgbed_pid[numb]=JSON.parse(xhr.responseText).data.pics.pic_1.pid;
	document.getElementById('sinaimgbed_act'+numb).innerHTML='<a href="https://ws3.sinaimg.cn/large/'+sinaimgbed_pid[numb]+'.jpg" target="_blank">上传成功，右键复制图片链接</a>';
	document.getElementById('sinaimgbed_pic'+numb).setAttribute('style','background:url('+sinaimgbed_pid[numb]+') no-repeat center;background-size:cover;');
	sinaimgbed_cpost(numb+1);
}}
var upqk=new FormData();
upqk.append('file',sinaimgbed_pool[numb]);
upqk.append('action','sinaimgbed');
xhr.send(upqk);
}
var sinaimgbed_pool=[],sinaimgbed_pid=[];