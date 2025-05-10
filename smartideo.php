<?php
/*
Plugin Name: Smartideo
Plugin URI: https://7yper.com/3639
Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持YouTube、哔哩哔哩、腾讯视频、优酷、搜狐视频、56、华数等网站。
Version: 2.8.1
Stable tag: 2.8.1
Author: Fens Liu
Author URI: https://7yper.com/3639
*/

define('SMARTIDEO_VERSION', '2.8.1');
define('SMARTIDEO_URL', plugins_url('', __FILE__));
define('SMARTIDEO_PATH', dirname( __FILE__ ));

$smartideo = new smartideo();

class smartideo{
    private $edit = false;
    private $width = '100%';
    private $height = '500px';
    private $bilibili_pc_player = 0;
    private $option = array();
    public function __construct(){
        if(is_admin()){
            add_action('admin_menu', array($this, 'admin_menu'));
            $this->edit = true;
        }

        $option = get_option('smartideo_option');
        if(empty($option)){
            $option = array();
        }
        $this->option = $option;
        extract((array)$option);
        $this->bilibili_pc_player = isset($bilibili_pc_player) ? $bilibili_pc_player : 0;

        add_action('wp_enqueue_scripts', array($this, 'smartideo_scripts'));

        wp_embed_unregister_handler('youku');
        wp_embed_unregister_handler('tudou');
        wp_embed_unregister_handler('56com');
        wp_embed_unregister_handler('youtube');

        // video
        wp_embed_register_handler( 'smartideo_youtube',
            '#https?://www\.youtube\.com/watch\?v=(?<video_id>[a-zA-Z0-9_=\-]+)#i',
            array($this, 'smartideo_embed_handler_youtube') );
        
        wp_embed_register_handler( 'smartideo_bilibili',
            '#https?://www\.bilibili\.com/video/(?:[av|BV]+)(?:(?<video_id1>[a-zA-Z0-9_=\-]+)/(?:index_|\#page=)(?<video_id2>[a-zA-Z0-9_=\-]+)|(?<video_id>[a-zA-Z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_bilibili') );
        
        wp_embed_register_handler( 'smartideo_douyin',
            '#https?://www\.douyin\.com/discover\?modal_id=(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_douyin') );
        
        wp_embed_register_handler( 'smartideo_youku',
            '#https?://v\.youku\.com/(?:v_show/id_(?<video_id1>[a-z0-9_=\-]+)\.html|video\?vid=(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_youku') );

        wp_embed_register_handler( 'smartideo_qq',
            '#https?://v\.qq\.com/(?:[a-z0-9_\./]+\?vid=(?<video_id1>[a-z0-9_=\-]+)|(?:[a-z0-9/]+)/(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_qq') );

        wp_embed_register_handler( 'smartideo_sohu',
            '#https?://my\.tv\.sohu\.com/(?:pl|us)/(?:\d+)/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_sohu') );

        wp_embed_register_handler( 'smartideo_wasu',
            '#https?://www\.wasu\.cn/play/show/id/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_wasu') );

        wp_embed_register_handler( 'smartideo_acfun',
            '#https?://www\.acfun\.(?:[tv|cn]+)/v/ac(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_acfun') );
        
        wp_embed_register_handler( 'smartideo_iqiyi',
            '#https?://www\.iqiyi\.com/(?:[a-zA-Z]+)_(?<video_id>[a-z0-9_~\-]+)#i',
            array($this, 'smartideo_embed_handler_iqiyi') );
        
        wp_embed_register_handler( 'smartideo_56',
            '#https?://(?:www\.)?56\.com/[a-z0-9]+/(?:play_album\-aid\-[0-9]+_vid\-(?<video_id1>[a-z0-9_=\-]+)|v_(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_56') );
        
        // music
        wp_embed_register_handler( 'smartideo_music163',
            '#https?://music\.163\.com/\#/song\?id=(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_music163') );

        wp_embed_register_handler( 'smartideo_musicqq',
            '#https?://y\.qq\.com/n/(?:ryqq|yqq)/(?:songDetail|song)/(?<video_id>\d+)(?:\.html)?#i',
            array($this, 'smartideo_embed_handler_musicqq') );

    }

    # video
    public function smartideo_embed_handler_youtube( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//www.youtube.com/embed/{$matches['video_id']}", $url);
        return apply_filters( 'embed_youtube', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_bilibili( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = ($matches['video_id1'] == '') ? $matches['video_id'] : $matches['video_id1'];
        $page = ($matches['video_id2'] > 1) ? $matches['video_id2'] : 1;
        $cid = '';
        if(wp_is_mobile() || $this->bilibili_pc_player == 1){
            $aid = 0;
            $bvid = '';
            if(is_numeric($matches['video_id'])){
                $aid = $matches['video_id'];
            }else{
                $bvid = $matches['video_id'];
            }
            $embed = $this->get_iframe("//player.bilibili.com/player.html?aid={$aid}&bvid={$bvid}&cid={$cid}&page={$page}", $url);
        }else{
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_bilibili', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_douyin( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//open.douyin.com/player/video?vid={$matches['video_id']}&autoplay=0", $url, '324px', '672px');
        return apply_filters( 'embed_douyin', $embed, $matches, $attr, $url, $rawattr );
        }
    
    public function smartideo_embed_handler_youku( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("//player.youku.com/embed/{$matches['video_id']}", $url);
        return apply_filters( 'embed_youku', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_qq( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("//v.qq.com/iframe/player.html?vid={$matches['video_id']}&auto=0", $url);
        return apply_filters( 'embed_qq', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_sohu( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//tv.sohu.com/upload/static/share/share_play.html#{$matches['video_id']}_0_0_9001_0", $url);
        return apply_filters( 'embed_sohu', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_wasu( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("http://www.wasu.cn/Play/iframe/id/{$matches['video_id']}", $url);
        return apply_filters( 'embed_wasu', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_acfun( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("https://www.acfun.cn/player/ac{$matches['video_id']}", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_acfun', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_iqiyi( $matches, $attr, $url, $rawattr ) {
        $embed = '';
        try{
            $request = new WP_Http();
            $data = (array)$request->request($url, array('timeout' => 3));
            if(!isset($data['body'])){
                $data['data'] = '';
            }
            preg_match('/"vid":"(\w+)"/i', (string)$data['body'], $match);
            $vid = $match[1];
            preg_match('/"tvId":(\d+)/i', (string)$data['body'], $match);
            $tvid = $match[1];
            if ($tvid > 0 && !empty($vid)) {
                $embed = $this->get_iframe("//open.iqiyi.com/developer/player_js/coopPlayerIndex.html?vid={$vid}&tvId={$tvid}&height=100%&width=100%&autoplay=0", $url);
            }
        }catch(Exception $e){}
        if(empty($embed)){
            $embed = '解析失败，请刷新页面重试';
        }
        return apply_filters( 'embed_iqiyi', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_56( $matches, $attr, $url, $rawattr ) {
	$matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("http://www.56.com/iframe/{$matches['video_id']}", $url);
        return apply_filters( 'embed_56', $embed, $matches, $attr, $url, $rawattr );
    }
    
    # music
    public function smartideo_embed_handler_music163( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//music.163.com/outchain/player?type=2&id={$matches['video_id']}&auto=0&height=90", $url, '100%', '110px');
        return apply_filters( 'embed_music163', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_musicqq( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//i.y.qq.com/n2/m/outchain/player/index.html?songid={$matches['video_id']}&songtype=0", $url, '100%', '66px');
        return apply_filters( 'embed_musicqq', $embed, $matches, $attr, $url, $rawattr );
    }

    private function get_embed($url = '', $source = '', $width = '', $height = ''){
        $html = '';
        $html .=
            '<div class="smartideo">
                <div class="player"' . $this->get_size_style($width, $height) . '>
                    <embed src="' . $url . '" allowFullScreen="true" quality="high" width="100%" height="100%" allowScriptAccess="always" type="application/x-shockwave-flash" wmode="transparent"></embed>
                </div>';
        if($this->option['tips_status'] == 1 && !$this->edit){
            if(empty($source)){
                $source = 'javascript:void(0);';
            }
            $html .=
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" smartideo-title-mobile="' . $this->option['tips_content_mobile'] . '" title="' . $this->option['tips_content'] . '" class="smartideo-tips" rel="nofollow"></a>
                </div>';
        }
        $html .= '</div>';
        return $html;
    }

    private function get_iframe($url = '', $source = '', $width = '', $height = ''){
        $html = '';
        $html .=
            '<div class="smartideo">
                <div class="player"' . $this->get_size_style($width, $height) . '>
                    <iframe src="' . $url . '" width="100%" height="100%" frameborder="0" allowfullscreen="true"></iframe>
                </div>';
        if(isset($this->option['tips_status']) && $this->option['tips_status'] == 1 && !$this->edit){
            if(empty($source)){
                $source = 'javascript:void(0);';
            }
            $html .=
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" smartideo-title-mobile="' . $this->option['tips_content_mobile'] . '" title="' . $this->option['tips_content'] . '" class="smartideo-tips" rel="nofollow"></a>
                </div>';
        }
        $html .= '</div>';
        return $html;
    }
    
    private function get_link($url){
        $html = '';
        $html .=  
            '<div class="smartideo">
                <div class="player"' . $this->get_size_style(0, 0) . '>
                    <a href="' . $url . '" target="_blank" class="smartideo-play-link"><div class="smartideo-play-button"></div></a>
                    <p style="color: #999;margin-top: 50px;">暂时无法播放，可回源网站播放</p>
                </div>
            </div>';
        return $html;
    }

    public function smartideo_scripts(){
        wp_enqueue_style('smartideo_css', SMARTIDEO_URL . '/static/smartideo.css', array(), SMARTIDEO_VERSION, 'screen');
        wp_enqueue_script('smartideo_js', SMARTIDEO_URL . '/static/smartideo.js', array(), SMARTIDEO_VERSION, true);
    }

    public function admin_menu(){
        add_plugins_page('Smartideo 设置', 'Smartideo 设置', 'manage_options', 'smartideo_settings', array($this, 'admin_settings'));
    }

    public function admin_settings(){
        require_once 'smartideo-admin.php';
        new smartideo_admin();
    }

    private function is_https(){
        if(strtolower($_SERVER['HTTPS']) == 'on'){
            return true;
        }else{
            return false;
        }
    }
    private function get_size_style($width, $height){
        if(empty($width) || empty($height)){
            $width = $height = 0;
            if($this->edit){
                $width = $this->width;
                $height = $this->height;
            }else if(isset($this->option['response']) && $this->option['response'] == 0){
                if(wp_is_mobile()){
                    $width = $this->option['width_mobile'];
                    $height = $this->option['height_mobile'];
                }else{
                    $width = $this->option['width'];
                    $height = $this->option['height'];
                }
            }
        }
        $style = '';
        if(!empty($width)){
            $style .= "width: {$width};";
        }
        if(!empty($height)){
            $style .= "height: {$height};";
        }
        if(!empty($style)){
            $style = ' style="' . $style . '"';
        }
        return $style;
    }
}
