<?php
/*
Plugin Name: PIXNET PA code WordPress Plugin
Description: This is PIXNET PA code WordPress plugin
Author: Daniel
Version: 2.9.10
*/

require(plugin_dir_path( __FILE__ ) . 'libs/widget_sticker.php');
require(plugin_dir_path( __FILE__ ) . 'libs/widget_medal.php');
require(plugin_dir_path( __FILE__ ) . 'libs/widget_mamacakes.php');
require(plugin_dir_path( __FILE__ ) . 'libs/widget_ksblogger.php');

define('MAX_RETRIES', 1);
define('CACHED_PERIOD', 10800);

class PixnetPlugin
{
    public function __construct()
    {
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->folder = plugin_dir_path( __FILE__ );
        $this->plugin->dir = plugin_dir_path( __DIR__ );
        $this->plugin->site_url = site_url();
        $this->plugin->site_title = get_bloginfo('name');
        $this->verify_code = (null != get_option('google_verify')) ? get_option('google_verify') : '';
        $this->is_onrank = false;

        $rank_response = $this->call_api_personal_rank($this->plugin->site_url);

        if (!$rank_response or $rank_response->error) {
            $this->is_onrank = false;
        } else {
            $this->is_onrank = $rank_response->data->is_ranking;
        }

        register_sidebar(['id' => 'sidebar-amp', 'name' => 'AMP Sidebar']);
        add_action('init', [&$this, 'init']);
        // amp 的 function 要放在這邊
        add_action('pre_amp_render_post', [&$this, 'amp_init']);

        // 共用的 script
        add_action('admin_menu', [&$this, 'paCodeVenue']);
        add_action('widgets_init', [&$this, 'register_sticker_widget']);
        add_action('admin_enqueue_scripts', [&$this, 'loadAdminScript']);
        add_action('wp_ajax_saveVenue', [&$this, 'saveVenue']);

    }

    function amp_init()
    {
        add_action('amp_post_template_head', [&$this, 'gtmAmpHeadCode']);
        add_action('amp_post_template_head', [&$this, 'saveVerify']);
        add_action('amp_post_template_head', [&$this, 'siteVerification']);
        add_action('amp_post_template_footer', [&$this, 'gtmAmpFooterCode']);
    }

    function init()
    {
        add_action('wp_head', [&$this, 'paCode']);
        add_action('wp_head', [&$this, 'saveVerify']);
        add_action('wp_head', [&$this, 'siteVerification']);
        add_action('wp_enqueue_scripts', [&$this, 'loadScript']);
    }

    function paCodeVenue()
    {
        add_menu_page('PA-Venue', 'PA-Venue', 'manage_options', 'PA-Code-options', [&$this, 'paVenueContent']);
    }

    function gtmAmpHeadCode()
    {
        echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';

    }

    function gtmAmpFooterCode()
    {
        $gtm = (null !== get_option('pagtm')) ? get_option('pagtm') : '';

        if (is_active_sidebar('sidebar-amp')) {
            dynamic_sidebar('sidebar-amp');
        }

        if (!$gtm) {
            return;
        }

        echo '<amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $gtm . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>';
    }

    function paCode()
    {
        $venue = (null !== get_option('pavenue')) ? get_option('pavenue') : '';

        echo "
            <script>
            var _piq = _piq || [];
        _piq.push(
                ['setCustomVar', 'venue', '$venue'],
                ['trackPageView'],
                ['trackPageClick']
                );
        (function(d, t, u, f, j) {
         f = d.getElementsByTagName(t)[0];
         j = d.createElement(t);
         j.async = 1;
         j.src = u;
         f.parentNode.insertBefore(j, f);
         })(document, 'script', '//s.pixanalytics.com/js/pi.min.js');
        </script>
            ";
    }

    function siteVerification()
    {
        if (empty($this->verify_code)) {
            echo '<meta name="google-site-verification" content="none"/>';
            return;
        }

        echo $this->verify_code;
    }

    function call_api_personal_rank($domain)
    {
        $cache_type = 'personal-rank';
        $cache = $this->get_cache($cache_type, $domain);

        if ($cache) {
            return $cache;
        }

        $response = new StdClass;
        $response->error = true;
        for ($i=0; $i<constant('MAX_RETRIES'); $i++) {
            $result = wp_remote_get("https://api-smartranking.pixplug.in/api/personalrank?domain={$domain}", ['timeout' => 1]);
            if (200 != wp_remote_retrieve_response_code($result)) {
                continue;
            }

            $response = json_decode($result['body']);
            break;
        }

        if (!$response->error) {
            $this->set_cache($cache_type, $domain, $response);
            return $response;
        }

        return $response;
    }

    function call_api_pixstar($domain)
    {
        $cache_type = 'pixstar2020';
        $cache = $this->get_cache($cache_type, $domain);

        if ($cache) {
            return $cache;
        }

        $response = new StdClass;
        $response->error = true;
        for ($i=0; $i<constant('MAX_RETRIES'); $i++) {
            $result = wp_remote_get("https://pixstar.events.pixnet.net/2020/api.php?type=checklist&domain={$domain}", ['timeout' => 1]);

            if (200 != wp_remote_retrieve_response_code($result)) {
                continue;
            }

            $response = json_decode($result['body']);
            break;
        }

        if (!$response->error) {
            $this->set_cache($cache_type, $domain, $response);
            return $response;
        }

        return $response;
    }

    function call_api_event_stickers($domain)
    {
        $cache_type = 'event_stickers';
        $cache = $this->get_cache($cache_type, $domain);

        if ($cache) {
            return $cache;
        }

        $response = new StdClass;
        $response->error = true;
        for ($i=0; $i<constant('MAX_RETRIES'); $i++) {
            $result = wp_remote_get("http://rhino-api.pixinsight.com.tw/wordpress/eventstickers?domain={$domain}", ['timeout' => 1]);

            if (200 != wp_remote_retrieve_response_code($result)) {
                continue;
            }

            $response = json_decode($result['body']);
            break;
        }

        if (!$response->error) {
            $this->set_cache($cache_type, $domain, $response);
            return $response;
        }

        return $response;
    }

    function set_cache($cache_type, $domain, $content)
    {
        $result = new StdClass;
        $result->timestamp = time();
        $result->content = $content;

        $cached_content = json_encode($result);
        @file_put_contents(sprintf('/tmp/pixnet-%s-%s-cache.txt', md5($domain), $cache_type), $cached_content);
    }

    function get_cache($cache_type, $domain)
    {
        $cached_content = @file_get_contents(sprintf('/tmp/pixnet-%s-%s-cache.txt', md5($domain), $cache_type));

        if (empty($cached_content)) {
            return '';
        }

        if (!$cached_content) {
            return '';
        }

        $result = json_decode($cached_content);
        if (time() - $result->timestamp > constant('CACHED_PERIOD')) {
            return '';
        }

        return $result->content;
    }

    function register_sticker_widget() {
        $domain = parse_url($this->plugin->site_url)['host'];

        // 聯盟貼紙
        $sticker_widget = new WP_Widget_Sticker($this->is_onrank);
        register_widget($sticker_widget);
        $this->insertWidget('sidebar');
        $this->insertWidget('sidebar-1');

        // 金點賞
        $response = $this->call_api_pixstar($domain);
        if (!$response->error) {
            if ($response->data->is_pixstar) {
                $medal_widget = new WP_Widget_Medal($response->data->user_name);
                register_widget($medal_widget);
            }
        }

        // 活動貼紙
        $response = $this->call_api_event_stickers($domain);
        if (!$response->error) {
            $stickers = $response->data ? $response->data->stickers : [];

            // 蛋糕達人
            if (in_array('mamacakes', $stickers)) {
                $mamacakes_widget = new WP_Widget_Mamacakes($response->data->user_name);
                register_widget($mamacakes_widget);
            }

            // 高雄山城
            if (in_array('ksblogger', $stickers)) {
                $ksblogger_widget = new WP_Widget_Ksblogger($response->data->user_name);
                register_widget($ksblogger_widget);
            }
        }
    }

    function insertWidget($target_sidebar)
    {
        $widget_count = 0;
        $active_widgets = get_option('sidebars_widgets');

        if (!array_key_exists($target_sidebar, $active_widgets)){
            return;
        }

        foreach ($active_widgets as $sidebars) {
            if (!is_array($sidebars)) {
                continue;
            }
            if (0 == count($sidebars)) {
                continue;
            }
            foreach ($sidebars as $widget) {
                if ('side_sticker_widget' == preg_split('/-/', $widget)[0]) {
                    $widget_count++;
                }
            }
        }

        if (0 != $widget_count){
            return;
        }

        $counter = count(get_option('widget_side_sticker_widget', [])) + 1;
        $active_widgets[$target_sidebar][] = 'side_sticker_widget-' . $counter;

        $sticker_content[$counter] = array (
                'title' => 'ABCSoftwarehtec'
                );
        update_option('widget_side_sticker_widget', $sticker_content);
        update_option('sidebars_widgets', $active_widgets);
    }

    function loadScript()
    {
        wp_enqueue_script('stickerjs', plugins_url('/resource/js/stickers.js', __FILE__ ), array('jquery'), '2.9.9');

        wp_enqueue_style('stickercss', plugins_url('/resource/css/custom.css', __FILE__ ), array(), '2.9.9');

        $translation_array = [
            'stickerUrl' => plugins_url('/resource/img', __FILE__ ),
            'siteUrl' => $this->plugin->site_url,
            'is_mobile' => wp_is_mobile(),
            'siteUrl' => $this->plugin->site_url,
            'is_set' => (empty($this->verify_code)) ? true : false,
            'is_onrank' => $this->is_onrank
        ];

        wp_localize_script('stickerjs', 'object_name', $translation_array);
    }

    function loadAdminScript()
    {
        wp_register_script('admin_script', plugins_url('/resource/js/admin.js', __FILE__ ), array('jquery'), '1.7.4', true);
        wp_enqueue_script('admin_script');
    }

    function paVenueContent()
    {
        include_once($this->plugin->folder . '/resource/view/menu.php');
    }

    function saveVenue()
    {
        global $wpdb;

        $venue = $_POST['venue'];
        $gtm = $_POST['gtm'];
        update_option('pavenue', $venue);
        update_option('pagtm', $gtm);
    }

    function saveVerify()
    {
        if (!empty($this->verify_code)) {
            return;
        }

        $result = wp_remote_get('https://rhino-api.pixinsight.com.tw/wordpress/searchconsole?domain=' . $this->plugin->site_url .'/', array('timeout' => 1));

        if (is_wp_error($result)) {
            return;
        }

        $rank_response = json_decode($result['body']);
        $this->verify_code = ($rank_response->error) ? '' : $rank_response->data->token;

        update_option('google_verify', $this->verify_code);
    }
}

$Pixplugin = new PixnetPlugin();
