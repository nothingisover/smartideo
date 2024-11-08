<?php
class smartideo_admin{
    public function __construct(){
        if(!current_user_can('update_core')){
            echo '<p>没有权限！</p>';
            return false;
        }
        if(isset($_POST['smartideo_submit']) && $_POST['smartideo_submit'] == '保存'){
            if(!wp_verify_nonce($_POST['nonce'])){
                echo '<p>保存失败，请重试！</p>';
                return false;
            }
            $param = array('smartideo_code', 'width', 'height', 'width_mobile', 'height_mobile', 'response', 'tips_status', 'tips_content', 'tips_content_mobile', 'youku_client_id', 'bilibili_player', 'bilibili_pc_player');
            $option = get_option('smartideo_option');
            if(!is_array($option)){
                $option = array();
            }
            foreach($_POST as $key => $val){
                if(in_array($key, $param)){
                    $option[$key] = sanitize_text_field($val);
                }
            }
            update_option('smartideo_option', $option);
        }
        $option = (array)get_option('smartideo_option');
        $option['width'] = isset($option['width']) && $option['width'] > 0 ? $option['width'] : '100%';
        $option['height'] = isset($option['height']) && $option['height'] > 0 ? $option['height'] : '400px';
        $option['width_mobile'] = isset($option['width_mobile']) && $option['width_mobile'] > 0 ? $option['width_mobile'] : '100%';
        $option['height_mobile'] = isset($option['height_mobile']) && $option['height_mobile'] > 0 ? $option['height_mobile'] : '200px';

        $nonce = wp_create_nonce();
        echo '<h2>Smartideo 设置</h2>';
        echo '<form action="" method="post">
            <input type="hidden" name="nonce" value="' . $nonce . '" />
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">播放器尺寸</th>
                    <td>
                        <fieldset>
                            <p>
                                <label>
                                    <input name="response" type="radio" value="1" class="tog" ' . (isset($option['response']) && $option['response'] == 1 ? 'checked="checked"' : '') . '> 响应式（推荐）
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input name="response" type="radio" value="0" class="tog" ' . (isset($option['response']) && $option['response'] != 1 ? 'checked="checked"' : '') . '> 固定大小（下方设置，宽 x 高）
                                </label>
                            </p>
                        <ul>
                            <li>
                                电脑端：
                                <label><input type="text" class="small-text" name="width" value="' . esc_html($option['width']) . '" /></label> x 
                                <label><input type="text" class="small-text" name="height" value="' . esc_html($option['height']) . '" /></label>
                            </li>
                            <li>
                                移动端：
                                <label><input type="text" class="small-text" name="width_mobile" value="' . esc_html($option['width_mobile']) . '" /></label> x 
                                <label><input type="text" class="small-text" name="height_mobile" value="' . esc_html($option['height_mobile']) . '" /></label>
                            </li>
                        </ul>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">贴士</th>
                    <td>
                        <fieldset>
                            <p>
                                <label title="开启">
                                    <input type="radio" name="tips_status" value="1" ' . (isset($option['tips_status']) && $option['tips_status'] == 1 ? 'checked="checked"' : '') . '/>
                                    <span>开启</span>
                                </label>
                            </p>
                            <p>
                                <label title="关闭">
                                    <input type="radio" name="tips_status" value="0" ' . (isset($option['tips_status']) && $option['tips_status'] != 1 ? 'checked="checked"' : '') . '/>
                                    <span>关闭（默认）</span>
                                </label>
                            </p>
                            <ul>
                                <li>
                                    电脑端：
                                    <label><input type="text" class="regular-text code" name="tips_content" value="' . (isset($option['tips_status']) ? $option['tips_content'] : '') . '" /></label>
                                    <br />
                                    <p class="description">如：如果视频无法播放，点击这里试试</p>
                                </li>
                                <li>
                                    移动端：
                                    <label><input type="text" class="regular-text code" name="tips_content_mobile" value="' . (isset($option['tips_status']) ? $option['tips_content_mobile'] : '') . '" /></label>
                                    <br />
                                    <p class="description">如：建议在WIFI环境下播放，土豪请随意~</p>
                                </li>
                            </ul>
                        </fieldset>
                    </td>
                </tr>';
            echo '<tr valign="top">
                    <th scope="row">优酷client_id</th>
                    <td>
                        <label><input type="text" class="regular-text code" name="youku_client_id" value="' . (isset($option['youku_client_id']) ? $option['youku_client_id'] : '') . '"></label>
                        <br />
                        <p class="description">供优酷开发者使用，没有client_id请留空</p>
                    </td>
                </tr>';
            echo '<tr valign="top">
                    <th scope="row">哔哩哔哩电脑端播放器</th>
                    <td>
                        <fieldset>
                            <p>
                                <label title="使用H5播放器">
                                    <input type="radio" name="bilibili_pc_player" value="1" ' . (isset($option['bilibili_pc_player']) && $option['bilibili_pc_player'] == 1 ? 'checked="checked"' : '') . '/>
                                    <span>使用H5播放器（如果你的博客有哔哩哔哩的授权可以使用）</span>
                                </label>
                            </p>
                            <p>
                                <label title="源站播放">
                                    <input type="radio" name="bilibili_pc_player" value="0" ' . (isset($option['bilibili_pc_player']) && $option['bilibili_pc_player'] != 1 ? 'checked="checked"' : '') . '/>
                                    <span>源站播放（默认，跳转至哔哩哔哩播放）</span>
                                </label>
                            </p>
                        </fieldset>
                    </td>
                </tr>';
        echo '</table>
            <p class="submit"><input type="submit" name="smartideo_submit" id="submit" class="button-primary" value="保存"></p>
            </form>';
        echo '<h2>意见反馈</h2>
            <p>你的意见是Smartido成长的动力，欢迎给我们留言，或许你想要的功能下一个版本就会实现哦！</p>
            <p>插件官方页面：<a href="https://7yper.com/3639" target="_blank">https://7yper.com/3639</a></p>
            <p>微信公众号：Typer</p>
        ';
    }
}