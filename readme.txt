=== Plugin Name ===
Contributors: Fens Liu
Tags: video, html5, flash, youku, tudou, QQ, 56, 视频, 播放器, 优酷, 土豆, 搜狐, 腾讯
Requires at least: 3.5.0
Tested up to: 4.7

Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视、YouTube 等网站。

== Description ==

Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视、YouTube 等网站。

直接粘贴视频播放页完整的URL到编辑器（单独一行），就可以加载视频播放器。

= 详细介绍 =
http://www.fengziliu.com/smartideo-2.html

= 效果展示 =
http://www.rifuyiri.net/tag/%e7%be%8e%e5%a5%b3/ （视频）
http://www.rifuyiri.net/music/m-1/ （音乐）
PS: 手机访问可以查看在移动设备上的效果

= 支持的URL示例 =
http://v.youku.com/v_show/id_XMTYzNTgxNTMy.html
http://www.tudou.com/programs/view/YBdHhxJqrLY/
http://www.56.com/u35/v_MTEwMjM5NDcy.html
http://v.qq.com/page/o/9/f/o0142tt1m9f.html
http://v.qq.com/cover/t/tyeqdw6rof7t5ow/p0015kjlai9.html
http://v.qq.com/cover/k/kl2zy755pnehxi3.html?vid=i0015mm1oo2
http://my.tv.sohu.com/us/94469256/77228432.shtml
http://my.tv.sohu.com/pl/6888667/78050474.shtml
http://www.wasu.cn/Play/show/id/5079941
http://www.acfun.tv/v/ac1963444
http://www.meipai.com/media/531841278
http://www.bilibili.com/video/av2436095/

不支持手机播放
http://www.letv.com/ptv/vplay/20932037.html
http://www.hunantv.com/v/2/103460/f/1088659.html
http://www.miaopai.com/show/mlnIHcTzrRusUb4SZuFI0Q__.htm
http://v.yinyuetai.com/video/2207109
http://v.ku6.com/show/P0Ib_pTne6-FBSa1AbtKUQ...html

音乐
http://music.163.com/#/song?id=186513
http://www.xiami.com/song/389307


== Installation ==

1. 你可以在后台插件管理页面中直接搜索 `Smartideo` 并安装
2. 或者上传文件夹 `smartideo` 至 `/wp-content/plugins/` 目录
3. 在插件管理页面中激活 Smartideo

== Changelog ==

= 请关注微信公众号：ri-fu-yi-ri 获取高级功能 =
= 小广告：开发者必备的Chrome插件 http://t.cn/RJe4SNI =

= 2.1.6 =

* 修复 秒拍SWF播放器不能加载的BUG

= 2.1.5 =

* 新增 支持AcFun新域名（www.acfun.cn）
* 优化 对哔哩哔哩HTTPS的支持
* 优化 HTTPS检测方法
* 优化 Smartideo设置

= 2.1.4 =

* 新增 支持秒拍H5播放
* 优化 对优酷HTTPS的支持
* 优化 对哔哩哔哩HTTPS的支持

= 2.1.3 =

* 修复 Smartideo设置的BUG

= 2.1.2 =

* 新增 支持优酷HTTPS
* 优化 Smartideo设置

= 2.1.1 =
* 新增 支持WordPress 4.7
* 修复 bilibili H5不能播放的问题（临时方案）

= 2.1.0 =
* 修复 bilibili H5不能播放的问题
* 修复 美拍视频解析失败的问题

= 2.0.9 =
* 新增 支持秒拍
* 新增 支持美拍beta
* 新增 bilibili支持分P视频

= 2.0.8 =
* 新增 bilibili播放器版本设置

= 2.0.7 =
* 新增 支持乐视新域名（le.com）
* 新增 支持芒果TV新域名（mgtv.com）
* 新增 bilibili支持H5播放

= 2.0.6 =
* 新增 优酷开放平台的支持
* 新增 Tips区分PC和移动端

= 2.0.5 =
* 修复 AcFun读取视频信息出错的BUG

= 2.0.4 =
* 修复 Tips不能加载的BUG
* 修复 Tips可能与其他插件产生冲突的BUG

= 2.0.3 =
* 新增 支持YouTube
* 新增 Tips配置
* 优化 Tips加载逻辑
* 优化 对bilibili https的支持

= 2.0.2 =
* 新增 AcFun支持H5播放
* 新增 支持部分网站https

= 2.0.1 =
* 新增 支持乐视体育
* 优化 AcFun引用
* 优化 Tips加载方式

= 2.0.0 =
* 新增 播放器响应式支持（播放器根据不同设备的屏幕大小设置不同的屏幕高度和宽度）
* 新增 播放器下方友好提示（不支持H5播放的视频可通过这里引导回源站播放）
* 优化 播放器引用方式（先 iframe 后 embed）
* 优化 核心代码（更简洁更高效）

= 1.3.7 =
* 修复 播放器可能遮罩其他元素的BUG
* 优化 embed handler

= 1.3.6 =
* 优化 网易云音乐取消自动播放

= 1.3.5 =
* 新增 资源加载策略设置

= 1.3.4 =
* 新增 支持AcFun（A站）
* 新增 支持bilibili（B站）

= 1.3.3 =
* 新增 支持网易云音乐
* 新增 支持虾米音乐
* 优化 核心代码

= 1.3.2 =
* 新增 支持搜狐视频专辑URL
* 优化 播放器样式，兼容各种主题

= 1.3.1 =
* 修复 播放器下方有空白条的BUG

= 1.3.0 =
* 新增 CSS
* 新增 移动设备不能播放的视频友好显示
* 新增 支持芒果TV
* 修复 腾讯视频部分URL不能正确解析的BUG
* 优化 核心代码

= 1.2.0 =
* 新增 Smartideo设置
* 优化 核心代码

= 1.1.0 =
* 新增 支持乐视网
* 新增 支持腾讯视频专辑
* 修复 一个已知的BUG
* 优化 核心代码

= 1.0.0 =
* Smartideo 的第一个版本