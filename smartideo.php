<?php

/*

Plugin Name: Smartideo

Plugin URI: http://www.fengziliu.com/

Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视、YouTube 等网站。

Version: 2.0.4

Author: Fens Liu

Author URI: http://www.fengziliu.com/smartideo-2.html

*/



define('SMARTIDEO_VERSION', '2.0.4');

define('SMARTIDEO_URL', plugins_url('', __FILE__));

define('SMARTIDEO_PATH', dirname( __FILE__ ));



$smartideo = new smartideo();

class smartideo{
    private $edit = false;
    private $width = '100%';
    private $height = '500px';
    private $strategy = 0;
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
        if(!empty($strategy)){
            $this->strategy = $strategy;
        }
        if($this->strategy != 1){
            add_action('wp_enqueue_scripts', array($this, 'smartideo_scripts'));
        }
        
        wp_embed_unregister_handler('youku');
        wp_embed_unregister_handler('tudou');
        wp_embed_unregister_handler('56com');
        wp_embed_unregister_handler('youtube');
        
        // video
        wp_embed_register_handler( 'smartideo_tudou',
            '#https?://(?:www\.)?tudou\.com/(?:programs/view|listplay/(?<list_id>[a-z0-9_=\-]+))/(?<video_id>[a-z0-9_=\-]+)/#i',
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
        
        wp_embed_register_handler( 'smartideo_yinyuetai',
            '#https?://v\.yinyuetai\.com/video/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_yinyuetai') );
        
        wp_embed_register_handler( 'smartideo_ku6',
            '#https?://v\.ku6\.com/show/(?<video_id>[a-z0-9\-_\.]+).html#i',
            array($this, 'smartideo_embed_handler_ku6') );
        
        wp_embed_register_handler( 'smartideo_letv',
            '#https?://(?:[a-z0-9/]+\.)?letv\.com/(?:[a-z0-9/]+)/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_letv') );
        
        wp_embed_register_handler( 'smartideo_hunantv',
            '#https?://www\.hunantv\.com/(?:[a-z0-9/]+)/(?<video_id>\d+)\.html#i',
            array($this, 'smartideo_embed_handler_hunantv') );
        
        wp_embed_register_handler( 'smartideo_acfun',
            '#https?://www\.acfun\.tv/v/ac(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_acfun') );
        
        wp_embed_register_handler( 'smartideo_bilibili',
            '#https?://www\.bilibili\.com/video/av(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_bilibili') );
        
        wp_embed_register_handler( 'smartideo_youtube',
            '#https?://www\.youtube\.com/watch\?v=(?<video_id>\w+)#i',
            array($this, 'smartideo_embed_handler_youtube') );
        
        // music
        wp_embed_register_handler( 'smartideo_music163',
            '#https?://music\.163\.com/\#/song\?id=(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_music163') );
        
        wp_embed_register_handler( 'smartideo_xiami',
            '#https?://www\.xiami\.com/song/(?<video_id>\d+)#i',
            array($this, 'smartideo_embed_handler_xiami') );
        
    }
    
    # video
    public function smartideo_embed_handler_tudou( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//www.tudou.com/programs/view/html5embed.action?type=0&code={$matches['video_id']}", $url);
        return apply_filters( 'embed_tudou', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_56( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("//www.56.com/iframe/{$matches['video_id']}", $url);
        return apply_filters( 'embed_56', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_youku( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//player.youku.com/embed/{$matches['video_id']}", $url);
        return apply_filters( 'embed_youku', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_qq( $matches, $attr, $url, $rawattr ) {
        $matches['video_id'] = $matches['video_id1'] == '' ? $matches['video_id2'] : $matches['video_id1'];
        $embed = $this->get_iframe("//v.qq.com/iframe/player.html?vid={$matches['video_id']}&tiny=0&auto=0", $url);
        return apply_filters( 'embed_qq', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_sohu( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//tv.sohu.com/upload/static/share/share_play.html#{$matches['video_id']}_0_0_9001_0", $url);
        return apply_filters( 'embed_sohu', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_wasu( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//www.wasu.cn/Play/iframe/id/{$matches['video_id']}", $url);
        return apply_filters( 'embed_wasu', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_acfun( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//ssl.acfun.tv/block-player-homura.html#vid={$matches['video_id']};from=http://www.acfun.tv", $url);
        return apply_filters( 'embed_acfun', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_youtube( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//www.youtube.com/embed/{$matches['video_id']}", $url);
        return apply_filters( 'embed_youtube', $embed, $matches, $attr, $url, $rawattr );
    }
    
    # video widthout h5
    public function smartideo_embed_handler_yinyuetai( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.yinyuetai.com/video/player/{$matches['video_id']}/v_0.swf", $url);
        return apply_filters( 'embed_yinyuetai', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_ku6( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("//player.ku6.com/refer/{$matches['video_id']}/v.swf", $url);
        return apply_filters( 'embed_ku6', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_letv($matches, $attr, $url, $rawattr){
        $embed = $this->get_embed("//i7.imgs.letv.com/player/swfPlayer.swf?id={$matches['video_id']}&autoplay=0", $url);
        return apply_filters( 'embed_letv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_hunantv( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_embed("//i1.hunantv.com/ui/swf/share/player.swf?video_id={$matches['video_id']}&autoplay=0", $url);
        return apply_filters( 'embed_hunantv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_bilibili( $matches, $attr, $url, $rawattr ) {
        if($this->is_https()){
            $embed = $this->get_embed("//static-s.bilibili.com/miniloader.swf?aid={$matches['video_id']}&page=1", $url);
        }else{
            $embed = $this->get_embed("//static.hdslb.com/miniloader.swf?aid={$matches['video_id']}&page=1", $url);
        }
        return apply_filters( 'embed_bilibili', $embed, $matches, $attr, $url, $rawattr );
    }
    
    # music
    public function smartideo_embed_handler_music163( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//music.163.com/outchain/player?type=2&id={$matches['video_id']}&auto=0&height=90", '', '100%', '110px');
        return apply_filters( 'embed_music163', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_xiami( $matches, $attr, $url, $rawattr ) {
        $embed = 
            '<div id="smartideo" style="background: transparent;">
                <script src="//www.xiami.com/widget/player-single?uid=0&sid='.$matches['video_id'].'&autoplay=0&mode=js" type="text/javascript"></script>
            </div>';
        return apply_filters( 'embed_music163', $embed, $matches, $attr, $url, $rawattr );
    }
    
    private function get_embed($url = '', $source = '', $width = '', $height = ''){
        $style = $html = '';
        if($this->strategy == 1){
            $html .= sprintf('<link rel="stylesheet" href="%1$s" type="text/css" media="screen">', SMARTIDEO_URL . '/static/smartideo.css?ver=' . SMARTIDEO_VERSION);
            $html .= sprintf('<script type="text/javascript" src="%1$s"></script>', SMARTIDEO_URL . '/static/smartideo.js?ver=' . SMARTIDEO_VERSION);
        }
        if($this->edit){
            $width = $this->width;
            $height = $this->height;
        }
        if(!empty($width)){
            $style .= "width: {$width};";
        }
        if(!empty($height)){
            $style .= "height: {$height};";
        }
        if(!empty($style)){
            $style = ' style="' . $style . '"';
        }
        $html .= 
            '<div id="smartideo">
                <div class="player"' . $style . '>
                    <embed src="' . $url . '" allowFullScreen="true" quality="high" width="100%" height="100%" allowScriptAccess="always" type="application/x-shockwave-flash" wmode="transparent"></embed>
                </div>';
        if($this->option['tips_status'] == 1 && !$this->edit){
            if(!empty($source)){
                $source = 'javascript:void(0);';
            }
            if(empty($this->option['tips_content'])){
                $this->option['tips_content'] = '建议在WIFI环境下播放，土豪请随意~';
            }
            $html .= 
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" title="' . $this->option['tips_content'] . '" id="smartideo_tips" rel="nofollow">' . $this->option['tips_content'] . '</a>
                </div>';
        }
        $html .= '</div>';
        return $html;
    }
    
    private function get_iframe($url = '', $source = '', $width = '', $height = ''){
        $style = $html = '';
        if($this->strategy == 1){
            $html .= sprintf('<link rel="stylesheet" id="smartideo-cssdd" href="%1$s" type="text/css" media="screen">', SMARTIDEO_URL . '/static/smartideo.css?ver=' . SMARTIDEO_VERSION);
        }
        if($this->edit){
            $width = $this->width;
            $height = $this->height;
        }
        if(!empty($width)){
            $style .= "width: {$width};";
        }
        if(!empty($height)){
            $style .= "height: {$height};";
        }
        if(!empty($style)){
            $style = ' style="' . $style . '"';
        }
        $html .= 
            '<div id="smartideo">
                <div class="player"' . $style . '>
                    <iframe src="' . $url . '" width="100%" height="100%" frameborder="0" allowfullscreen="true"></iframe>
                </div>';
        if($this->option['tips_status'] == 1 && !$this->edit){
            if(!empty($source)){
                $source = 'javascript:void(0);';
            }
            if(empty($this->option['tips_content'])){
                $this->option['tips_content'] = '建议在WIFI环境下播放，土豪请随意~';
            }
            $html .= 
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" title="' . $this->option['tips_content'] . '" id="smartideo_tips" rel="nofollow">' . $this->option['tips_content'] . '</a>
                </div>';
        }
        $html .= '</div>';
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
        if($_POST['smartideo_submit'] == '保存'){
            $param = array('width', 'height', 'strategy', 'tips_status', 'tips_content');
            $json = array();
            foreach($_POST as $key => $val){
                if(in_array($key, $param)){
                    $json[$key] = $val;
                }
            }
            $json = json_encode($json); 
            update_option('smartideo_option', $json);
        }
        $option = get_option('smartideo_option');
        if(!empty($option)){
            $option = json_decode($option, true);
        }
        if(empty($option['tips_content'])){
            $option['tips_content'] = '建议在WIFI环境下播放，土豪请随意~';
        }
        
        echo '<h2>Smartideo 设置</h2>';
        echo '<form action="" method="post">	
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">资源加载策略</th>
                    <td>
                        <label title="按需加载">
                            <input type="radio" name="strategy" value="1" ' . ($option['strategy'] == 1 ? 'checked="checked"' : '') . '/>
                            <span>按需加载</span>
                        </label>
                        <label title="全局加载">
                            <input type="radio" name="strategy" value="0" ' . ($option['strategy'] != 1 ? 'checked="checked"' : '') . '/>
                            <span>全局加载</span>
                        </label>
                        <br />
                        <p class="description">默认全局加载</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">贴士</th>
                    <td>
                        <label title="开启">
                            <input type="radio" name="tips_status" value="1" ' . ($option['tips_status'] == 1 ? 'checked="checked"' : '') . '/>
                            <span>开启</span>
                        </label>
                        <label title="关闭">
                            <input type="radio" name="tips_status" value="0" ' . ($option['tips_status'] != 1 ? 'checked="checked"' : '') . '/>
                            <span>关闭</span>
                        </label>
                        <br />
                        <p class="description">默认关闭</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">贴士内容</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="tips_content" value="'.$option['tips_content'].'"></label>
                        <br />
                        <p class="description"></p>
                    </td>
		</tr>
            </table>
            <p class="submit"><input type="submit" name="smartideo_submit" id="submit" class="button-primary" value="保存"></p>
        </form>';
    }
    
    private function is_https(){
        if($_SERVER['HTTPS'] == 'on'){
            return true;
        }else{
            return false;
        }
    }
}
