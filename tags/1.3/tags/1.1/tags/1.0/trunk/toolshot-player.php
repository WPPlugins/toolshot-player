<?php
/**
 * Plugin Name: ToolsHot Player
 * Plugin URI: http://toolshot.com/player
 * Description: Perfect player video, support resources available, simplicity and ease of use. You can custom player change skins, logo, add ads player. Metabox in 'Add New Post' screen capture feature, suggested video post.
 * Version: 1.0
 * Author: ad.toolshot@gmail.com
 * Author URI: http://toolshot.com/
 */

/*
 * Init
 */
defined( 'ABSPATH' ) or die();
define( 'TOOLSHOT_PLAYER_VERSION', '1.0' );
include 'func/func_class.php';
global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_config;
$url_toolshot = 'http://toolshot.com/';
$url_toolshot_player = 'http://player1.toolshot.com/';
$toolshot_config = toolshot_class_table_select('toolshot_config', ['select' => 'tc_type, tc_key, tc_option, tc_text']);
foreach($toolshot_config as $val) $toolshot_config[$val->tc_type] = $val;
/*
 * Function POST
 */
/*
 * Metabox
 */
include 'func/func_metabox.php';
/*
 * Add plugin menu
 */
add_action('admin_menu', 'toolshot_player_plugins_menu');
function toolshot_player_plugins_menu() {
    $toolshot_player_view_player_settings = add_menu_page('Player', 'Player', 'manage_options', 'toolshot_player_view_player_settings', 'toolshot_player_view_player_settings', 'dashicons-controls-play');
    add_action('admin_print_styles-' . $toolshot_player_view_player_settings, 'toolshot_player_view_player_settings_script');
}
/*
 * Load View
 */
function toolshot_player_view_player_settings() {
    global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_config;
    if(isset($_GET['task']))
        include 'func/func_get.php';
    $tc_text = json_decode($toolshot_config['player']->tc_text);
    //wp_enqueue_style("toolshot_player_skin_css", plugins_url("assets/css/skin-player/".$tc_text->skin.".css", __FILE__), FALSE);
    //wp_enqueue_script("toolshot_player_js", plugins_url("assets/js/toolshot.player.js", __FILE__), FALSE);
    wp_enqueue_style('toolshot_player_skin_css', plugin_dir_url( __FILE__ ).'/assets/css/skin-player/'.$tc_text->skin.'.css');
    wp_enqueue_script('toolshot_player_js', plugin_dir_url( __FILE__ ).'/assets/js/toolshot.player.js');

    include 'view/view_toolshot_player_settings.php';
}
/*
 * Load css, javascript
 */
function toolshot_player_view_player_settings_script(){
    /*wp_enqueue_script("jquery_js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js", FALSE);
    wp_enqueue_style("jquery_ui", "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css", FALSE);
    wp_enqueue_script("jquery_ui", "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js", FALSE);

    wp_enqueue_style("toolshot_player_css", plugins_url("assets/css/toolshot.player.css", __FILE__), FALSE);
    wp_enqueue_script("jw_player_js", plugins_url("assets/js/jwplayer.js", __FILE__), FALSE);*/
    wp_enqueue_script("jquery_js", "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js", FALSE);
    wp_enqueue_style("jquery_ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css", FALSE);
    wp_enqueue_script("jquery_ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js", FALSE);

    wp_enqueue_style("toolshot_player_css", plugin_dir_url( __FILE__ )."/assets/css/toolshot.player.css");
    wp_enqueue_script("jw_player_js", plugin_dir_url( __FILE__ )."/assets/js/jwplayer.js");
}
/*
 *  Hook
 */
function toolshot_player_activate() {
    toolshot_class_table_create('toolshot_config',[
        'tc_type varchar(30) NOT NULL',
        'tc_key varchar(100) NOT NULL',
        'tc_option text NOT NULL',
        'tc_text text NOT NULL',
        'tc_date datetime NOT NULL',
        'UNIQUE KEY id (tc_type)'
    ]);
    $tc_key = '';
    if(preg_match('#https?:\/\/(?:www\.)?([^\/]+)#mis', get_site_url(), $match)) $tc_key = $match[1].'_';

    toolshot_class_table_insert('toolshot_config', ['tc_type' => 'player', 'tc_key'=>md5($tc_key), 'tc_option'=>'{"source_player":[]}', 'tc_text'=>'{"player":"toolshot", "skin":"default", "autoplay":"true","download":"true","rewind":"false","fast_forward":"false","logo":"","logo_size":"30vw","logo_position":"topleft","logo_x":"3vw","logo_y":"5vh","logo_url":""}', 'tc_date'=>current_time('mysql', 1)]);
}
function toolshot_player_uninstall() {
    toolshot_class_table_drop('toolshot_config');
}
register_activation_hook(__FILE__, 'toolshot_player_activate');
register_deactivation_hook( __FILE__, 'toolshot_player_uninstall');
?>