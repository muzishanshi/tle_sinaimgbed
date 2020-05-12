<?php if(!defined('EMLOG_ROOT')){die('error');}?>
<style type="text/css">
    .uploadbutton{margin: auto;text-align: center;background-color: rgba(255, 255, 255, .3);}
    .uploadbutton input[type=file] {opacity:0;width:140px;height:30px;position:absolute;display:inline-block;}
	.form-control:focus{background-color: rgba(255, 255, 255, .3);}
</style>
<div class="uploadbutton" style="margin:auto;">
	<input id="qihulocalpic" type="file" accept="image/*" multiple>
	<button type="button" class="btn btn-danger btn-sm">选择本地图片上传奇虎</button>
	<button id="qihuremotepic" type="button" class="btn btn-primary btn-sm">上传远程图片上传奇虎</button>
	<span id="qihuUploadprogress"></span>
</div>
<center id="qihuPreview"></center>
<div id="qihuRemotepicwindow" style="display:none;">
	<textarea class="form-control" name="qihupicurls" rows="3" style="width:100%;" id="qihupicurls" placeholder="请在下方输入远程图片地址~每行一个~"></textarea>
	<p><button type="button" class="btn btn-primary" onclick="qihuremoteupload();">上传</button></p>
</div>
<script type="text/javascript">
	$("#qihuremotepic").click(function(){
		if($("#qihuRemotepicwindow").is(':hidden')){
			$("#qihuRemotepicwindow").show();
		}else{
			$("#qihuRemotepicwindow").hide()
		}
	});

	var qihuurl = 'https://www.tongleer.com/api/web/?action=weiboimg&type=qihu';
	$(document).ready(function() {
		$("#qihulocalpic").change(function(e) {
			qihulocalupload(this.files)
		});
	});
	function qihulocalupload(files){
		document.getElementById('qihuUploadprogress').innerHTML="上传中……";
		$("#qihuPreview").html('');
		if (files.length == 0) return alert('请选择图片文件！');
		for(var j = 0,len = files.length; j < len; j++){
			console.log(files[j]);
			let imageData = new FormData();
			imageData.append("file", files[j]);
			$.ajax({
				url: qihuurl,
				type: 'POST',
				data: imageData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				// 图片上传成功
				success: function (result) {
					document.getElementById('qihuUploadprogress').innerHTML="";
					if (result.code == 0){
						addToEditor(result.data.src);
						$("#qihuPreview").append('<p>'+result.data.src+'</p>');
					}else{
						addToEditor('<p>第'+j+'个图片上传失败</p>');
						$("#qihuPreview").append('<p>第'+j+'个图片上传失败</p>');
					}
				},
				// 图片上传失败
				error: function () {
					console.log('图片上传失败');
				}
			});
		}
	}
	function qihuremoteupload(){
		document.getElementById('qihuUploadprogress').innerHTML="上传中……";
		$("#qihuPreview").html('');
		var qihupicurls = $('#qihupicurls').val();
		if (qihupicurls == false) return alert('请输入图片链接！');
		var UrlArr = qihupicurls.split("\n");
		$('#qihuRemotepicwindow').hide();
		for(var j = 0,len = UrlArr.length; j < len; j++){
			console.log(UrlArr[j]);
			$.getJSON(qihuurl, {imgurl: UrlArr[j]}, function(result, textStatus) {
				document.getElementById('qihuUploadprogress').innerHTML="";
				if (result.code == 1){
					addToEditor(result.imgurl);
					$("#qihuPreview").append('<p>'+result.imgurl+'</p>');
				}else{
					$("#qihuPreview").append('<p>第'+j+'个图片上传失败</p>');
				}
				console.log(result);
			});
		}
	}
</script>