<?php

/*

Plugin Name: Smartideo

Plugin URI: http://www.fengziliu.com/

Description: Smartideo 是为 WordPress 添加对在线视频支持的一款插件（支持手机、平板等设备HTML5播放）。 目前支持优酷、搜狐视频、土豆、56、腾讯视频、新浪视频、酷6、华数、乐视、YouTube 等网站。

Version: 2.2.1

Author: Fens Liu

Author URI: http://www.fengziliu.com/smartideo-2.html

*/



define('SMARTIDEO_VERSION', '2.2.1');
define('SMARTIDEO_URL', plugins_url('', __FILE__));
define('SMARTIDEO_PATH', dirname( __FILE__ ));

$smartideo = new smartideo();

class smartideo{
    private $edit = false;
    private $width = '100%';
    private $height = '500px';
    private $strategy = 0;
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
        if(!empty($strategy)){
            $this->strategy = $strategy;
        }
        if(!empty($youku_client_id)){
            $this->youku_client_id = $youku_client_id;
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
            '#https?://www\.youtube\.com/watch\?v=(?<video_id>\w+)#i',
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
            '#https?://v\.yinyuetai\.com/video/(?<video_id>\d+)#i',
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
    public function smartideo_embed_handler_tudou( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("http://www.tudou.com/programs/view/html5embed.action?type=0&code={$matches['video_id']}", $url);
        return apply_filters( 'embed_tudou', $embed, $matches, $attr, $url, $rawattr );
    }

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
        $embed = $this->get_iframe("//v.qq.com/iframe/player.html?vid={$matches['video_id']}&tiny=0&auto=0", $url);
        return apply_filters( 'embed_qq', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_sohu( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("http://tv.sohu.com/upload/static/share/share_play.html#{$matches['video_id']}_0_0_9001_0", $url);
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
                $api = 'https://www.bilibili.com/video/av' . $matches['video_id'];
                $request = new WP_Http();
                $data = (array)$request->request($api, array('timeout' => 3));
                if(!isset($data['body'])){
                    $data['data'] = '';
                }
                preg_match('/cid=(\d+)&aid=/i', (string)$data['body'], $match);
                $cid = (int)$match[1];
                if ($cid > 0) {
                    $embed = $this->get_iframe("//www.bilibili.com/html/html5player.html?aid={$matches['video_id']}&cid={$cid}&page={$page}", $url);
                }
            }catch(Exception $e){}
        }
        if(empty($embed)){
            $embed = $this->get_embed("//static.hdslb.com/miniloader.swf?aid={$matches['video_id']}&page={$page}", $url);
        }
        return apply_filters( 'embed_bilibili', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_meipai( $matches, $attr, $url, $rawattr ) {
        $meipai_url = "http://www.meipai.com/media/{$matches['video_id']}";
        $request = new WP_Http();
        $data = $request->request($meipai_url, array('timeout' => 1));
        $data = $data['body'];
        if (!empty($data)) {
            preg_match('/<meta content="(.*)" property="og:video:url">/', $data, $match);
        }
        $embed = $this->get_iframe("{$match[1]}", $url);
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
        return apply_filters( 'embed_bilibili', $embed, $matches, $attr, $url, $rawattr );
    }

    # video widthout h5
    public function smartideo_embed_handler_yinyuetai( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.yinyuetai.com/video/player/{$matches['video_id']}/v_0.swf", $url);
        return apply_filters( 'embed_yinyuetai', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_ku6( $matches, $attr, $url, $rawattr ){
        $embed = $this->get_embed("http://player.ku6.com/refer/{$matches['video_id']}/v.swf", $url);
        return apply_filters( 'embed_ku6', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_letv($matches, $attr, $url, $rawattr){
        $embed = $this->get_embed("http://img1.c0.letv.com/ptv/player/swfPlayer.swf?id={$matches['video_id']}&autoplay=0", $url);
        return apply_filters( 'embed_letv', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_hunantv( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_embed("//i1.hunantv.com/ui/swf/share/player.swf?video_id={$matches['video_id']}&autoplay=0", $url);
        return apply_filters( 'embed_hunantv', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_acfun( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_embed("http://cdn.aixifan.com/player/ACFlashPlayer.out.swf?type=page&url=http://www.acfun.cn/v/ac{$matches['video_id']}", $url);
        return apply_filters( 'embed_acfun', $embed, $matches, $attr, $url, $rawattr );
    }

    # music
    public function smartideo_embed_handler_music163( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("http://music.163.com/outchain/player?type=2&id={$matches['video_id']}&auto=0&height=90", '', '100%', '110px');
        return apply_filters( 'embed_music163', $embed, $matches, $attr, $url, $rawattr );
    }
    
    public function smartideo_embed_handler_musicqq( $matches, $attr, $url, $rawattr ) {
        $embed = $this->get_iframe("//cc.stream.qqmusic.qq.com/C100{$matches['video_id']}.m4a?fromtag=52", '', '100%', '110px');
        return apply_filters( 'embed_musicqq', $embed, $matches, $attr, $url, $rawattr );
    }

    public function smartideo_embed_handler_xiami( $matches, $attr, $url, $rawattr ) {
        $embed =
            '<div id="smartideo" style="background: transparent;">
                <script src="http://www.xiami.com/widget/player-single?uid=0&sid='.$matches['video_id'].'&autoplay=0&mode=js" type="text/javascript"></script>
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
            $html .=
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" smartideo-title-mobile="' . $this->option['tips_content_mobile'] . '" title="' . $this->option['tips_content'] . '" id="smartideo_tips" rel="nofollow"></a>
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
        if(isset($this->option['tips_status']) && $this->option['tips_status'] == 1 && !$this->edit){
            if(!empty($source)){
                $source = 'javascript:void(0);';
            }
            $html .=
                '<div class="tips">
                    <a href="' . $source . '" target="_blank" smartideo-title="' . $this->option['tips_content'] . '" smartideo-title-mobile="' . $this->option['tips_content_mobile'] . '" title="' . $this->option['tips_content'] . '" id="smartideo_tips" rel="nofollow"></a>
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
            $param = array('smartideo_code', 'width', 'height', 'strategy', 'tips_status', 'tips_content', 'tips_content_mobile', 'youku_client_id', 'bilibili_player');
            $option = json_decode(get_option('smartideo_option'), true);
            foreach($_POST as $key => $val){
                if(in_array($key, $param)){
                    $option[$key] = $val;
                }
            }
            $json = json_encode($option);
            update_option('smartideo_option', $json);
        }
        $option = get_option('smartideo_option');
        if(!empty($option)){
            $option = json_decode($option, true);
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
                        <label><input type="text" class="regular-text code" name="tips_content" value="'.$option['tips_content'].'" /></label>
                        <br />
                        <p class="description">如：如果视频无法播放，点击这里试试</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">贴士内容（移动设备）</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="tips_content_mobile" value="'.$option['tips_content_mobile'].'" /></label>
                        <br />
                        <p class="description">如：建议在WIFI环境下播放，土豪请随意~</p>
                    </td>
                </tr>';
        if(in_array(strtolower(md5($option['smartideo_code'])), array('93144d072ad90bbd5896c0588b6b9267', 'b97d7058235b190f7594ad96374759b8', 'd885229d8e68e15cd0e2e5658902bfbf', 'c4f1f5e51b0d89c2f5f20e12282d667f'))){
            echo '<tr valign="top">
                    <th scope="row">优酷client_id</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="youku_client_id" value="'.$option['youku_client_id'].'"></label>
                        <br />
                        <p class="description">供优酷开发者使用，没有client_id请留空</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">哔哩哔哩播放器</th>
                    <td>
                        <label title="Flash版">
                            <input type="radio" name="bilibili_player" value="0" ' . ($option['bilibili_player'] != 1 ? 'checked="checked"' : '') . '/>
                            <span>自动选择，PC使用Flash播放器，手机使用H5播放器</span>
                        </label>
                        <label title="H5版">
                            <input type="radio" name="bilibili_player" value="1" ' . ($option['bilibili_player'] == 1 ? 'checked="checked"' : '') . '/>
                            <span>全平台使用H5播放器（beta）</span>
                        </label>
                        <br />
                        <p class="description">默认使用自动模式</p>
                    </td>
                </tr>';
        }else{
            echo '<tr valign="top">
                    <th scope="row">高级功能激活码</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="smartideo_code" value="'.$option['smartideo_code'].'"></label>
                        <br />
                        <p class="description">升级到最新版本（<a href="http://www.fengziliu.com/smartideo-2.html#changelog" target="_blank">' . SMARTIDEO_VERSION . '</a>），填入激活码保存后可开启高级功能。<br />
                            激活码关注微信公众号“<a href="http://www.rifuyiri.net/wp-content/uploads/2014/08/972e6fb0794d359.jpg" target="_blank">ri-fu-yi-ri</a>”回复“Smartideo Code”即可获得～<br />
                            如果激活码失效，请按照上述方法重新获取。</p>
                    </td>
                </tr>';
        }
        echo '</table>
            <p class="submit"><input type="submit" name="smartideo_submit" id="submit" class="button-primary" value="保存"></p>
            </form>';
        echo '<h2>意见反馈</h2>
            <p>你的意见是Smartido成长的动力，欢迎给我们留言，或许你想要的功能下一个版本就会实现哦！</p>
            <p>插件官方页面：<a href="http://www.fengziliu.com/smartideo-2.html" target="_blank">http://www.fengziliu.com/smartideo-2.html</a></p>
            <p>微信公众号：<a href="http://www.rifuyiri.net/wp-content/uploads/2014/08/972e6fb0794d359.jpg" target="_blank">ri-fu-yi-ri</a></p>
        ';
    }

    private function is_https(){
        if(strtolower($_SERVER['HTTPS']) == 'on'){
            return true;
        }else{
            return false;
        }
    }
}
