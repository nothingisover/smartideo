<?php

/*

Plugin Name: Smartideo

Plugin URI: http://www.fengziliu.com/

Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视 等网站。

Version: 1.3

Author: Fens Liu

Author URI: http://www.fengziliu.com/smartideo-for-wordpress.html

*/



define('SMARTIDEO_VERSION', '1.3');

define('SMARTIDEO_URL', plugins_url('', __FILE__));

define('SMARTIDEO_PATH', dirname( __FILE__ ));



$smartideo = new smartideo();

class smartideo{
    private $edit = false;
    private $width = '100%';
    private $height = '500';
    private $mobile_width = '100%';
    private $mobile_height = '250';
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
        extract($option);
        if(!empty($width)){
            $this->width = $width;
        }
        if(!empty($height)){
            $this->height = $height;
        }
        if(!empty($mobile_width)){
            $this->mobile_width = $mobile_width;
        }
        if(!empty($mobile_height)){
            $this->mobile_height = $mobile_height;
        }
        
        add_action('wp_enqueue_scripts', array($this, 'smartideo_scripts'));
        
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
        
        wp_embed_register_handler( 'smartideo_hunantv',
            '#https?://www\.hunantv\.com/(?:[a-z0-9/]+)/(?<video_id>\d+)\.html#i',
            array($this, 'smartideo_embed_handler_hunantv') );
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
        if(wp_is_mobile() && !$this->edit){
            $embed = $this->get_link($url);
        }else{
            $embed = $this->get_embed("http://player.yinyuetai.com/video/player/{$matches['video_id']}/v_0.swf");
        }
	return apply_filters( 'embed_yinyuetai', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_ku6( $matches, $attr, $url, $rawattr ){
        if(wp_is_mobile() && !$this->edit){
            $embed = $this->get_link($url);
        }else{
            $embed = $this->get_embed("http://player.ku6.com/refer/{$matches['video_id']}/v.swf");
        }
	return apply_filters( 'embed_ku6', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_letv($matches, $attr, $url, $rawattr){
        if(wp_is_mobile() && !$this->edit){
            $embed = $this->get_link($url);
        }else{
            $embed = $this->get_embed("http://i7.imgs.letv.com/player/swfPlayer.swf?id={$matches['video_id']}&autoplay=0");
        }
	return apply_filters( 'embed_letv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_hunantv( $matches, $attr, $url, $rawattr ) {
        if(wp_is_mobile() && !$this->edit){
            $embed = $this->get_link($url);
        }else{
            $embed = $this->get_embed("http://i1.hunantv.com/ui/swf/share/player.swf?video_id={$matches['video_id']}");
        }
	return apply_filters( 'embed_hunantv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    private function get_embed($url){
        $embed = sprintf(
            '<div id="smartideo">
                <embed src="%1$s" allowFullScreen="true" quality="high" width="%2$s" height="%3$s" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>
            </div>',
            $url, $this->width, $this->height);
        return $embed;
    }
    
    private function get_iframe($url){
        $iframe = sprintf(
            '<div id="smartideo">
                <iframe src="%1$s" width="%2$s" height="%3$s" frameborder="0" allowfullscreen="true"></iframe>
            </div>',
            $url, $this->mobile_width, $this->mobile_height);
        return $iframe;
    }
    
    private function get_link($url){
        $link = sprintf('<div id="smartideo" style="width: %2$spx; height: %3$spx; line-height: %3$spx;">
            <a href="%1$s" target="_blank" title="该视频不支持您的浏览器，请点击这里播放~" class="link">播放</a>
        </div>', $url, $this->mobile_width, $this->mobile_height);
        return $link;
    }
    
    public function smartideo_scripts(){
        wp_enqueue_style('smartideo', SMARTIDEO_URL . '/static/smartideo.css', array(), SMARTIDEO_VERSION, 'screen');
    }
    
    public function admin_menu(){
        add_plugins_page('Smartideo 设置', 'Smartideo 设置', 'manage_options', 'smartideo_settings', array($this, 'admin_settings'));
    }
    
    public function admin_settings(){
        if($_POST['smartideo_submit'] == '保存'){
            $param = array('width', 'height', 'mobile_width', 'mobile_height');
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
        if(empty($option['width'])){
            $option['width'] = '100%';
        }
        if(empty($option['height'])){
            $option['height'] = '500';
        }
        if(empty($option['mobile_width'])){
            $option['mobile_width'] = '100%';
        }
        if(empty($option['mobile_height'])){
            $option['mobile_height'] = '250';
        }
        
        echo '<h2>Smartideo 设置</h2>';
        echo '<form action="" method="post">	
            <table class="form-table">
		<tr valign="top">
                    <th scope="row">播放器宽度</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="width" value="'.$option['width'].'"></label>
                        <br />
                        <p class="description">默认宽度为100%</p>
                    </td>
		</tr>
		<tr valign="top">
                    <th scope="row">播放器高度</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="height" value="'.$option['height'].'"></label>
                        <br />
                        <p class="description">默认高度为500px</p>
                    </td>
		</tr>
                <tr valign="top">
                    <th scope="row">移动设备播放器宽度</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="mobile_width" value="'.$option['mobile_width'].'"></label>
                        <br />
                        <p class="description">手机、平板等设备访问时，默认宽度为100%</p>
                    </td>
		</tr>
		<tr valign="top">
                    <th scope="row">移动设备播放器高度</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="mobile_height" value="'.$option['mobile_height'].'"></label>
                        <br />
                        <p class="description">手机、平板等设备访问时，默认高度为250px</p>
                    </td>
		</tr>
            </table>
            <p class="submit"><input type="submit" name="smartideo_submit" id="submit" class="button-primary" value="保存"></p>
        </form>';
    }
}
