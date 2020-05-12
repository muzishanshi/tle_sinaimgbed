<?php if(!defined('EMLOG_ROOT')){die('error');}?>
<style type="text/css">
    .uploadbutton{margin: auto;text-align: center;background-color: rgba(255, 255, 255, .3);}
    .uploadbutton input[type=file] {opacity:0;width:140px;height:30px;position:absolute;display:inline-block;}
	.form-control:focus{background-color: rgba(255, 255, 255, .3);}
</style>
<div class="uploadbutton" style="margin:auto;">
	<input id="jdlocalpic" type="file" accept="image/*" multiple>
	<button type="button" class="btn btn-danger btn-sm">选择本地图片上传京东</button>
	<button id="jdremotepic" type="button" class="btn btn-primary btn-sm">上传远程图片上传京东</button>
	<span id="jduploadprogress"></span>
</div>
<center id="jdpreview"></center>
<div id="jdremotepicwindow" style="display:none;">
	<textarea class="form-control" name="jdpicurls" rows="3" style="width:100%;" id="jdpicurls" placeholder="请在下方输入远程图片地址~每行一个~"></textarea>
	<p><button type="button" class="btn btn-primary" onclick="jdremoteupload();">上传</button></p>
</div>
<script type="text/javascript">
	$("#jdremotepic").click(function(){
		if($("#jdremotepicwindow").is(':hidden')){
			$("#jdremotepicwindow").show();
		}else{
			$("#jdremotepicwindow").hide()
		}
	});

	var jdurl = 'https://www.tongleer.com/api/web/?action=weiboimg&type=jd';
	$(document).ready(function() {
		$("#jdlocalpic").change(function(e) {
			jdlocalupload(this.files)
		});
	});
	function jdlocalupload(files){
		document.getElementById('jduploadprogress').innerHTML="上传中……";
		$("#jdpreview").html('');
		if (files.length == 0) return alert('请选择图片文件！');
		for(var j = 0,len = files.length; j < len; j++){
			console.log(files[j]);
			let imageData = new FormData();
			imageData.append("file", files[j]);
			$.ajax({
				url: jdurl,
				type: 'POST',
				data: imageData,
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
				// 图片上传成功
				success: function (result) {
					document.getElementById('jduploadprogress').innerHTML="";
					if (result.code == 0){
						if(result.data.src.indexOf("ERROR")==-1){
							addToEditor(result.data.src);
							$("#jdpreview").append('<p>'+result.data.src+'</p>');
						}else{
							addToEditor('<p>第'+j+'个图片上传失败，图片不能太小</p>');
							$("#jdpreview").append('<p>第'+j+'个图片上传失败，图片不能太小</p>');
						}
					}else{
						addToEditor('<p>第'+j+'个图片上传失败</p>');
						$("#jdpreview").append('<p>第'+j+'个图片上传失败</p>');
					}
				},
				// 图片上传失败
				error: function () {
					console.log('图片上传失败');
				}
			});
		}
	}
	function jdremoteupload(){
		document.getElementById('jduploadprogress').innerHTML="上传中……";
		$("#jdpreview").html('');
		var jdpicurls = $('#jdpicurls').val();
		if (jdpicurls == false) return alert('请输入图片链接！');
		var UrlArr = jdpicurls.split("\n");
		$('#jdremotepicwindow').hide();
		for(var j = 0,len = UrlArr.length; j < len; j++){
			console.log(UrlArr[j]);
			$.getJSON(jdurl, {imgurl: UrlArr[j]}, function(result, textStatus) {
				document.getElementById('jduploadprogress').innerHTML="";
				if (result.code == 1){
					addToEditor(result.imgurl);
					$("#jdpreview").append('<p>'+result.imgurl+'</p>');
				}else{
					$("#jdpreview").append('<p>第'+j+'个图片上传失败</p>');
				}
				console.log(result);
			});
		}
	}
</script>