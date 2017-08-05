<?php

/*

Plugin Name: Smartideo

Plugin URI: https://www.rifuyiri.net/t/3639

Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、腾讯视频、爱奇艺、哔哩哔哩，酷6、华数、乐视、YouTube 等网站。

Version: 2.4.1

Author: Fens Liu

Author URI: https://www.rifuyiri.net/t/3639

*/



define('SMARTIDEO_VERSION', '2.4.1');
define('SMARTIDEO_URL', plugins_url('', __FILE__));
define('SMARTIDEO_PATH', dirname( __FILE__ ));

$smartideo = new smartideo();

class smartideo{
    private $edit = false;
    private $width = '100%';
    private $height = '500px';
    private $youku_client_id = 'd0b1b77a17cded3b';
    private $option = array();
    public function __construct(){
        if(is_admin()){
            add_action('admin_menu', array($this, 'admin_menu'));
            $this->edit = true;
        }

        $option = get_option('smartideo_option');
        if(!empty($option)){
            $option = json_decode($option, true);
        }else{
            $option = array();
        }
        $this->option = $option;
        extract($option);
        if(!empty($youku_client_id) && strlen($youku_client_id) == 16){
            $this->youku_client_id = $youku_client_id;
        }

        add_action('wp_enqueue_scripts', array($this, 'smartideo_scripts'));

        wp_embed_unregister_handler('youku');
        wp_embed_unregister_handler('tudou');
        wp_embed_unregister_handler('56com');
        wp_embed_unregister_handler('youtube');

        // video
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
            '#https?://v\.qq\.com/(?:[a-z0-9_\./]+\?vid=(?<video_id1>[a-z0-9_=\-]+)|(?:[a-z0-9/]+)/(?<video_id2>[a-z0-9_=\-]+))#i',
            array($this, 'smartideo_embed_handler_qq') );

        wp_embed_register_handler( 'smartideo_sohu',
            '#https?://my\.tv\.sohu\.com/(?:pl|us)/(?:\d+)/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_sohu') );

        wp_embed_register_handler( 'smartideo_wasu',
            '#https?://www\.wasu\.cn/play/show/id/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_wasu') );

        wp_embed_register_handler( 'smartideo_youtube',
            '#https?://www\.youtube\.com/watch\?v=(?<video_id>[a-zA-Z0-9_=\-]+)#i',
            array($this, 'smartideo_embed_handler_youtube') );

        wp_embed_register_handler( 'smartideo_acfun',
            '#https?://www\.acfun\.(?:[tv|cn]+)/v/ac(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_acfun') );

        wp_embed_register_handler( 'smartideo_meipai',
            '#https?://(?:www\.)?meipai\.com/media/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_meipai') );
        
        wp_embed_register_handler( 'smartideo_bilibili',
            '#https?://www\.bilibili\.com/video/av(?:(?<video_id1>\d+)/index_(?<video_id2>\d+)|(?<video_id>\d+))#i',
            array($this, 'smartideo_embed_handler_bilibili') );

        wp_embed_register_handler( 'smartideo_miaopai',
            '#https?://www\.miaopai\.com/show/(?<video_id>[a-z0-9_~\-]+)#i',
            array($this, 'smartideo_embed_handler_miaopai') );
        
        wp_embed_register_handler( 'smartideo_iqiyi',
            '#https?://www\.iqiyi\.com/v_(?<video_id>[a-z0-9_~\-]+)#i',
            array($this, 'smartideo_embed_handler_iqiyi') );
        
        // Not supported HTML5
        wp_embed_register_handler( 'smartideo_yinyuetai',
            '#https?://(?:[www|v]+)\.yinyuetai\.com/video/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_yinyuetai') );

        wp_embed_register_handler( 'smartideo_ku6',
            '#https?://v\.ku6\.com/show/(?<video_id>[a-z0-9\-_\.]+).html#i',
            array($this, 'smartideo_embed_handler_ku6') );

        wp_embed_register_handler( 'smartideo_letv',
            '#https?://(?:[a-z0-9/]+\.)?(?:[letv|le]+)\.com/(?:[a-z0-9/]+)/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_letv') );

        wp_embed_register_handler( 'smartideo_hunantv',
            '#https?://www\.(?:[hunantv|mgtv]+)\.com/(?:[a-z0-9/]+)/(?<video_id>\d+)\.html#i',
            array($this, 'smartideo_embed_handler_hunantv') );

        // music
        wp_embed_register_handler( 'smartideo_music163',
            '#https?://music\.163\.com/\#/song\?id=(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_music163') );

        wp_embed_register_handler( 'smartideo_musicqq',
            '#https?://y\.qq\.com/n/yqq/song/(?<video_id>\w+)\.html#i',
            array($this, 'smartideo_embed_handler_musicqq') );
        
        wp_embed_register_handler( 'smartideo_xiami',
            '#https?://www\.xiami\.com/song/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_xiami') );

    }

    # video
    public function smartideo_embed_handler_56( $matches, $attr, $url, $rawattr ) {
	$matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("http://www.56.com/iframe/{$matches['video_id']}", $url);
        return apply_filters( 'embed_56', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_youku( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//player.youku.com/embed/{$matches['video_id']}?client_id={$this->youku_client_id}", $url);
        return apply_filters( 'embed_youku', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_qq( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("//v.qq.com/iframe/player.html?vid={$matches['video_id']}&tiny=1&auto=0", $url);
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

    public function smartideo_embed_handler_youtube( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//www.youtube.com/embed/{$matches['video_id']}", $url);
        return apply_filters( 'embed_youtube', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_bilibili( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = ($matches['video_id1'] == '') ? $matches['video_id'] : $matches['video_id1'];
        $page = ($matches['video_id2'] > 1) ? $matches['video_id2'] : 1;
        if(wp_is_mobile() || $this->option['bilibili_player']){
            $embed = '';
            try{
                $api = ($this->is_https() ? 'https' : 'http') . '://www.bilibili.com/video/av' . $matches['video_id'] . ($page > 1 ? "/index_{$page}.html" : '');
                $request = new WP_Http();
                $data = (array)$request->request($api, array('timeout' => 3));
                if(!isset($data['body'])){
                    $data['data'] = '';
                }
                preg_match('/cid=(\d+)&aid=/i', (string)$data['body'], $match);
                $cid = (int)$match[1];
                if ($cid > 0) {
                    $embed = $this->get_iframe(($this->is_https() ? 'https' : 'http') . "://www.bilibili.com/html/html5player.html?aid={$matches['video_id']}&cid={$cid}&page={$page}&as_wide=1", $url);
                }
            }catch(Exception $e){}
        }
        if(empty($embed)){
            $embed = $this->get_embed("//static.hdslb.com/miniloader.swf?aid={$matches['video_id']}&page={$page}&as_wide=1", $url);
        }
        return apply_filters( 'embed_bilibili', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_meipai( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_link($url);
        return apply_filters( 'embed_meipai', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_miaopai( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile()){
            $embed = $this->get_iframe("//gslb.miaopai.com/stream/{$matches['video_id']}.mp4", $url);
        }else{
            $embed = $this->get_embed("//wscdn.miaopai.com/splayer2.2.0.swf?scid={$matches['video_id']}&token=&autopause=true", $url);
        }
        return apply_filters( 'embed_miaopai', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_iqiyi( $matches, $attr, $url, $rawattr ) {
        $embed = '';
        try{
            $api = 'http://www.iqiyi.com/v_' . $matches['video_id'] . '.html';
            $request = new WP_Http();
            $data = (array)$request->request($api, array('timeout' => 3));
            if(!isset($data['body'])){
                $data['data'] = '';
            }
            preg_match('/data-player-videoid="(\w+)"/i', (string)$data['body'], $match);
            $vid = $match[1];
            preg_match('/data-player-tvid="(\d+)"/i', (string)$data['body'], $match);
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

    # video widthout h5
    public function smartideo_embed_handler_yinyuetai( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.yinyuetai.com/video/player/{$matches['video_id']}/v_0.swf", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_yinyuetai', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_ku6( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.ku6.com/refer/{$matches['video_id']}/v.swf", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_ku6', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_letv($matches, $attr, $url, $rawattr){
        $embed = $this->get_embed("http://img1.c0.letv.com/ptv/player/swfPlayer.swf?id={$matches['video_id']}&autoplay=0", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_letv', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_hunantv( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_embed("//i1.hunantv.com/ui/swf/share/player.swf?video_id={$matches['video_id']}&autoplay=0", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_hunantv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_acfun( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_embed("http://cdn.aixifan.com/player/ACFlashPlayer.out.swf?type=page&url=http://www.acfun.cn/v/ac{$matches['video_id']}", $url);
        if(wp_is_mobile()){
            $embed = $this->get_link($url);
        }
        return apply_filters( 'embed_acfun', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_tudou( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_link($url);
        return apply_filters( 'embed_tudou', $embed, $matches, $attr, $url, $rawattr );
    }
    
    # music
    public function smartideo_embed_handler_music163( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//music.163.com/outchain/player?type=2&id={$matches['video_id']}&auto=0&height=90", '', '100%', '110px');
        return apply_filters( 'embed_music163', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_musicqq( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//cc.stream.qqmusic.qq.com/C100{$matches['video_id']}.m4a?fromtag=52", '', '100%', '110px');
        return apply_filters( 'embed_musicqq', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_xiami( $matches, $attr, $url, $rawattr ) {
        $embed =
            '<div class="smartideo" style="background: transparent;">
                <script src="http://www.xiami.com/widget/player-single?uid=0&sid='.$matches['video_id'].'&autoplay=0&mode=js" type="text/javascript"></script>
            </div>';
        return apply_filters( 'embed_xiami', $embed, $matches, $attr, $url, $rawattr );
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
            }else if($this->option['response'] == 0){
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
