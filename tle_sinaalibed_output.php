<?php
if(!defined('EMLOG_ROOT')){die('error');}

$DB = MySql::getInstance();
?>
<script src="https://lib.baomitu.com/jquery/3.4.0/jquery.min.js" type="text/javascript"></script>
<style type="text/css">
    .uploadbutton{margin: auto;text-align: center;background-color: rgba(255, 255, 255, .3);}
    .uploadbutton input[type=file] {opacity:0;width:102px;height:31px;position:absolute;display:inline-block;}
	.form-control:focus{background-color: rgba(255, 255, 255, .3);}
</style>
<div class="uploadbutton" style="margin:auto;">
	<input id="alilocalpic" type="file" accept="image/*" multiple>
	<button type="button" class="btn btn-danger btn-sm">选择本地图片上传阿里</button>
	<button id="aliremotepic" type="button" class="btn btn-primary btn-sm">上传远程图片上传阿里</button>
</div>
<center id="preview"></center>
<div id="remotepicwindow" style="display:none;">
	<textarea class="form-control" name="alipicurls" rows="3" style="width:100%;" id="alipicurls" placeholder="请在下方输入远程图片地址~每行一个~"></textarea>
	<p><button type="button" class="btn btn-primary" onclick="aliremoteupload();">上传</button></p>
</div>
<script type="text/javascript">
	$("#aliremotepic").click(function(){
		if($("#remotepicwindow").is(':hidden')){
			$("#remotepicwindow").show();
		}else{
			$("#remotepicwindow").hide()
		}
	});

	var url = 'https://api.uomg.com/api/image.ali';
	$(document).ready(function() {
		$("#alilocalpic").change(function(e) {
			alilocalupload(this.files)
		});
	});
	function alilocalupload(files){
		$("#preview").html('');
		if (files.length == 0) return alert('请选择图片文件！');
		for(var j = 0,len = files.length; j < len; j++){
			console.log(files[j]);
			let imageData = new FormData();
			imageData.append("file", 'multipart');
			imageData.append("Filedata", files[j]);
			$.ajax({
				url: url,
				type: 'POST',
				data: imageData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				// 图片上传成功
				success: function (result) {
					if (result.code == 1){
						addToEditor('<p><img src="'+result.imgurl+'" src="" /></p>');
						$("#preview").append('<p>'+result.imgurl+'</p>');
					}else{
						addToEditor('<p>第'+j+'个图片上传失败</p>');
						$("#preview").append('<p>第'+j+'个图片上传失败</p>');
					}
				},
				// 图片上传失败
				error: function () {
					console.log('图片上传失败');
				}
			});
		}
	}
	function aliremoteupload(){
		$("#preview").html('');
		var alipicurls = $('#alipicurls').val();
		if (alipicurls == false) return alert('请输入图片链接！');
		var UrlArr = alipicurls.split("\n");
		$('#remotepicwindow').hide();
		for(var j = 0,len = UrlArr.length; j < len; j++){
			console.log(UrlArr[j]);
			$.getJSON(url, {imgurl: UrlArr[j]}, function(result, textStatus) {
				if (result.code == 1){
					addToEditor('<p><img src="'+result.imgurl+'" src="" /></p>');
					$("#preview").append('<p>'+result.imgurl+'</p>');
				}else{
					addToEditor('<p>第'+j+'个图片上传失败</p>');
					$("#preview").append('<p>第'+j+'个图片上传失败</p>');
				}
				console.log(result);
			});
		}
	}
</script>