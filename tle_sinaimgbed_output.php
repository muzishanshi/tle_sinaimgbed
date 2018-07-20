<?php
if(!defined('EMLOG_ROOT')){die('err');}
echo '
<style>
#sinaimgbed_pick{max-width:35%;}
#sinaimgbed_list{margin:2px 0;font-size:12px;}
.sinaimgbed_pic{margin:2px;width:160px;height:120px;display:inline-block;}
.sinaimgbed_act{padding-top:50px;width:160px;height:70px;background:rgba(0,0,0,0.2);text-align:center;}
.sinaimgbed_act a{color:#FFF;font-weight:bold;text-shadow:1px 1px 3px #000;}
</style>
<script>
var BLOG_URL="'.BLOG_URL.'";
function tle_sinaimgbed_show(node){
var item=document.createElement("div");
item.setAttribute("id","tle_sinaimgbed_body");
item.innerHTML="<input type=\"file\" onchange=\"sinaimgbed_cpick()\" id=\"sinaimgbed_pick\" multiple=\"multiple\" accept=\"image/jpeg,image/png,image/gif\" /><input type=\"button\" onclick=\"sinaimgbed_cpush()\" id=\"sinaimgbed_push\" value=\"开始\" /><div id=\"sinaimgbed_list\"></div>";
if(document.getElementById("tle_sinaimgbed_body")){node.parentNode.removeChild(document.getElementById("tle_sinaimgbed_body"));}
else{node.parentNode.appendChild(item);}
}
</script>
<script src="'.BLOG_URL.'content/plugins/tle_sinaimgbed/sinaimgbed.js"></script>
';
?>