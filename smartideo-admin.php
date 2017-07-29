<?php
class smartideo_admin{
    public function __construct(){
        if($_POST['smartideo_submit'] == '保存'){
            $param = array('smartideo_code', 'width', 'height', 'width_mobile', 'height_mobile', 'response', 'strategy', 'tips_status', 'tips_content', 'tips_content_mobile', 'youku_client_id', 'bilibili_player');
            $option = json_decode(get_option('smartideo_option'), true);
            foreach($_POST as $key => $val){
                if(in_array($key, $param)){
                    $option[$key] = sanitize_text_field($val);
                }
            }
            $json = json_encode($option);
            update_option('smartideo_option', $json);
        }
        $option = get_option('smartideo_option');
        if(!empty($option)){
            $option = json_decode($option, true);
        }
        $option['width'] = $option['width'] > 0 ? $option['width'] : '100%';
        $option['height'] = $option['height'] > 0 ? $option['height'] : '400px';
        $option['width_mobile'] = $option['width_mobile'] > 0 ? $option['width_mobile'] : '100%';
        $option['height_mobile'] = $option['height_mobile'] > 0 ? $option['height_mobile'] : '200px';

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
                        <p class="description">默认全局加载（推荐）</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">播放器尺寸</th>
                    <td>
                        <fieldset>
                            <p>
                                <label>
                                    <input name="response" type="radio" value="1" class="tog" ' . ($option['response'] == 1 ? 'checked="checked"' : '') . '> 响应式（推荐）
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input name="response" type="radio" value="0" class="tog" ' . ($option['response'] != 1 ? 'checked="checked"' : '') . '> 固定大小（下方设置，宽 x 高）
                                </label>
                            </p>
                        <ul>
                            <li>
                                电脑端：
                                <label><input type="text" class="small-text" name="width" value="'.$option['width'].'" /></label> x 
                                <label><input type="text" class="small-text" name="height" value="'.$option['height'].'" /></label>
                            </li>
                            <li>
                                移动端：
                                <label><input type="text" class="small-text" name="width_mobile" value="'.$option['width_mobile'].'" /></label> x 
                                <label><input type="text" class="small-text" name="height_mobile" value="'.$option['height_mobile'].'" /></label>
                            </li>
                        </ul>
                        </fieldset>
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
        if(in_array(strtolower(md5($option['smartideo_code'])), array('d885229d8e68e15cd0e2e5658902bfbf', 'c4f1f5e51b0d89c2f5f20e12282d667f', '97d762db98812f54996ae10bb0c00190', '1ba0c5c51cd381690eda3f96ba6fd2e1'))){
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
                    <p class="description">
                        使用方法：<br />
                        1.升级到最新版本（<a href="http://www.rifuyiri.net/t/3639#changelog" target="_blank">' . SMARTIDEO_VERSION . '</a>），填入激活码保存后可开启高级功能。<br />
                        2.激活码关注微信公众号“<a href="/wp-content/plugins/smartideo/static/qrcode.jpg" target="_blank">ri-fu-yi-ri</a>”回复“Smartideo Code”即可获得～<br />
                        注意：如果激活码失效，请按照上述方法重新获取。</p>
                </td>
            </tr>';
        }
        echo '</table>
            <p class="submit"><input type="submit" name="smartideo_submit" id="submit" class="button-primary" value="保存"></p>
            </form>';
        echo '<h2>意见反馈</h2>
            <p>你的意见是Smartido成长的动力，欢迎给我们留言，或许你想要的功能下一个版本就会实现哦！</p>
            <p>插件官方页面：<a href="http://www.rifuyiri.net/t/3639" target="_blank">http://www.rifuyiri.net/t/3639</a></p>
            <p>微信公众号：<a href="/wp-content/plugins/smartideo/static/qrcode.jpg" target="_blank">ri-fu-yi-ri</a></p>
        ';
    }
}