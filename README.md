# TleSinaimgbedForEmlog阿里微博图床插件

这是一款简单的Emlog微博图床插件，可把图片上传到阿里新浪微博存储，支持远程和本地链接互相转换、自定义图床链接前缀，可上传到自己的微博相册。

# 注意事项
1、下载后需要把文件夹名改为：“tle_sinaimgbed”。

2、本人使用的是php5.6，如果出现报错，更换php版本5.6即可。

3、（过时请忽略）如果小号上传失败，可以尝试微博大号，可能跟微博账号等级有关，配成成功后尽可能不再登录新浪系的平台，登录也没关系，配置好上传成功后如果出现失败的情况，重新上传即可。

# 下载地址
Github：https://github.com/muzishanshi/TleSinaimgbed

# 版本记录
2019-10-02 V1.0.5

	修复因加载cloudflare的layer.js失效导致的bug。

2019-09-01 V1.0.4
	
	1、因微博图床防盗链和上传失败后，整合优化一次，依然可以实现微博图床(配合同步插件使用微博官方api实现)和阿里图床的正常使用；
	2、新增前台阿里图床和阿里图床转换；
	3、增加本地化图片后插入数据库操作，实现在媒体中可见。
	
2019-05-04 V1.0.3
	
	新增阿里图床
	
2019-04-26 V1.0.2
	
	1、优化原有图床、新增上传到微博相册、前台图床；
	2、远程地址和本地地址的转换;
	3、为针对微博的防盗链增加可自主设置图床链接前缀。
	
V1.0.1
	
	第一个版本降世