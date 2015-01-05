<?php

/*

Plugin Name: Smartideo

Plugin URI: http://www.fengziliu.com/

Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视 等网站。

Version: 1.1

Author: Fens Liu

Author URI: http://www.fengziliu.com/smartideo-for-wordpress.html

*/



define('SMARTIDEO_VERSION', '1.0');

define('SMARTIDEO_URL', plugins_url('', __FILE__));

define('SMARTIDEO_PATH', dirname( __FILE__ ));



$smartideo = new smartideo();

class smartideo{
    private $width = '480';
    private $height = '400';
    public function __construct(){
        wp_embed_register_handler( 'smartideo_tudou',
            '#https?://(?:www\.)?tudou\.com/(?:programs/view|listplay/(?<list_id>[a-z0-9_=\-]+))/(?<video_id>[a-z0-9_=\-]+)#i',
            array($this, 'smartideo_embed_handler_tudou') );
        
        wp_embed_register_handler( 'smartideo_56',
            '#https?://(?:www\.)?56\.com/[a-z0-9]+/(?:play_album\-aid\-[0-9]+_vid\-(?<video_id1>[a-z0-9_=\-]+)|v_(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_56') );
        
        wp_embed_register_handler( 'smartideo_youku',
            '#https?://v\.youku\.com/v_show/id_(?<video_id>[a-z0-9_=\-]+)#i',
            array($this, 'smartideo_embed_handler_youku') );
        
        wp_embed_register_handler( 'smartideo_qq',
            '#https?://v\.qq\.com/(?:cover/g/[a-z0-9_\.]+\?vid=(?<video_id1>[a-z0-9_=\-]+)|(?:[a-z0-9/]+)/(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_qq') );
        
        wp_embed_register_handler( 'smartideo_sohu',
            '#https?://my\.tv\.sohu\.com/us/(?:\d+)/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_sohu') );
        
        wp_embed_register_handler( 'smartideo_wasu',
            '#https?://www\.wasu\.cn/play/show/id/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_wasu') );
        
        wp_embed_register_handler( 'smartideo_yinyuetai',
            '#https?://v\.yinyuetai\.com/video/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_yinyuetai') );
        
        wp_embed_register_handler( 'smartideo_ku6',
            '#https?://v\.ku6\.com/show/(?<video_id>[a-z0-9\-_\.]+).html#i',
            array($this, 'smartideo_embed_handler_ku6') );
        
        wp_embed_register_handler( 'smartideo_letv',
            '#https?://www\.letv\.com/ptv/vplay/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_letv') );
    }
    
    public function smartideo_embed_handler_tudou( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://www.tudou.com/programs/view/html5embed.action?type=0&code={$matches['video_id']}");
        }else{
            $embed = $this->get_embed("http://www.tudou.com/v/{$matches['video_id']}/&resourceId=0_05_05_99&bid=05/v.swf");
        }
	return apply_filters( 'embed_tudou', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_56( $matches, $attr, $url, $rawattr ) {
	$matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://www.56.com/iframe/{$matches['video_id']}");
        }else{
            $embed = $this->get_embed("http://player.56.com/v_{$matches['video_id']}.swf");
        }
	return apply_filters( 'embed_56', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_youku( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://player.youku.com/embed/{$matches['video_id']}");
        }else{
            $embed = $this->get_embed("http://player.youku.com/player.php/sid/{$matches['video_id']}/v.swf");
        }
	return apply_filters( 'embed_youku', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_qq( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://v.qq.com/iframe/player.html?vid={$matches['video_id']}");
        }else{
            $embed = $this->get_embed("http://static.video.qq.com/TPout.swf?vid={$matches['video_id']}");
        }
	return apply_filters( 'embed_qq', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_sohu( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://tv.sohu.com/upload/static/share/share_play.html#{$matches['video_id']}_0_0_9001_0");
        }else{
            $embed = $this->get_embed("http://share.vrs.sohu.com/my/v.swf&topBar=1&id={$matches['video_id']}&autoplay=false&xuid=&from=page");
        }
	return apply_filters( 'embed_sohu', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_wasu( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile()){
            $embed = $this->get_iframe("http://www.wasu.cn/Play/iframe/id/{$matches['video_id']}");
        }else{
            $embed = $this->get_embed("http://s.wasu.cn/portal/player/20141216/WsPlayer.swf?mode=3&vid={$matches['video_id']}&auto=0&ad=4228");
        }
	return apply_filters( 'embed_wasu', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_yinyuetai( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.yinyuetai.com/video/player/{$matches['video_id']}/v_0.swf");
	return apply_filters( 'embed_yinyuetai', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_ku6( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.ku6.com/refer/{$matches['video_id']}/v.swf");
	return apply_filters( 'embed_ku6', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_letv($matches, $attr, $url, $rawattr){
        $embed = $this->get_embed("http://i7.imgs.letv.com/player/swfPlayer.swf?id={$matches['video_id']}&autoplay=0");
	return apply_filters( 'embed_letv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    private function get_embed($url){
        $embed = sprintf(
            '<embed src="%1$s" allowFullScreen="true" quality="high" width="%2$s" height="%3$s" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>',
            $url, $this->width, $this->height);
        return $embed;
    }
    
    private function get_iframe($url){
        $iframe = sprintf(
            '<iframe src="%1$s" width="%2$s" height="%3$s" frameborder="0" allowfullscreen="true"></iframe>',
            $url, $this->width, $this->height);
        return $iframe;
    }
}
