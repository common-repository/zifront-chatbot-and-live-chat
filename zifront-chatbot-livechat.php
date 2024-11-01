<?php
/*
   Plugin Name: Zifront Chatbot and Live Chat
   Plugin URI: https://www.zifront.com/wordpress_integration/
   Version: 1.0
   Author: <a href="https://www.zifront.com/">zifront.com</a>
   Description: Zifront is a Chatbot and live chat software for lead generation and customer success.
   Text Domain: Zifront
   License: GPLv3
*/

function zifront_options_page() {
?>
    
    <div id="zifront_ui_container">
        <div id="zifront_ui_content">
            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/zifront_banner_internal.svg'; ?>" />
            <h1>Zifront Chatbot and Live Chat for lead generation and customer success</h1>
            <p>Install Zifront to this Wordpress by entering your Zifront widget id and chatbot flow id.</p>
            <p>If you do not have a Zifront widget id you can create your Free account at <a href="https://app.zifront.com/signup" target="_blank">zifront.com</a></p>
            <form method="post"  action="options.php">
                <?php settings_fields( 'zifront_settings' ); ?>
                <?php zifront_do_options(); ?>
                <div id="zifront_buttons_section">
                    <p>
                        <input type="submit" class="button-primary" id="zifront_save" value="<?php _e('Save Changes', 'zifront') ?>"/>
                        <a class="button-primary" href="https://app.zifront.com/dashboard" target="_blank">Dashboard</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
<?php
}

function zifront_menu() {
    add_menu_page(__('Zifront', 'zifront'), __('Zifront', 'zifront'), 'manage_options', basename(__FILE__), 'zifront_options_page',plugin_dir_url( __FILE__ ) . 'images/dashboard_icon.svg');
}
add_action( 'admin_menu', 'zifront_menu' );

function zifront_init() {
	register_setting( 'zifront_settings', 'zifront', 'zifront_validate' );
}
add_action( 'admin_init', 'zifront_init' );

function zifront_add_stylesheet() 
{
    wp_enqueue_style( 'zifront', plugins_url( '/css/zifront_styles.css', __FILE__ ) );
}
add_action('admin_print_styles', 'zifront_add_stylesheet');

function zifront_do_options() {
	$options = get_option( 'zifront' );
    ob_start();
    
    
	?>
        <div class="zifront_ui_row"><?php _e( '<strong>Enter your Zifront widget id</strong> ', 'zifront' ); ?> <input type="text" class="regular-text" id="zifront_live_id" name="zifront[zifront_live_id]" value="<?php echo esc_js($options['zifront_live_id']); ?>" /></div>
        <div class="zifront_ui_row"><?php _e( '<strong>Enter your Zifront chatbot flow id</strong> ', 'zifront' ); ?> <input type="text" class="regular-text" id="zifront_bot_id" name="zifront[zifront_bot_id]" value="<?php echo esc_js($options['zifront_bot_id']); ?>" /></div>
	<?php
}

function zifront_enqueue_scripts() {
  $options = get_option( 'zifront' );
  $zifront_live_id = $options['zifront_live_id'];
  $zifront_bot_id = $options['zifront_bot_id'];
  
  $src = 'https://app.zifront.com/widget';
  $script_id = 'ziwidget';
  $inline_script = "
    var zifrontParams = {
      data_id: '$zifront_live_id',
      data_flow: '$zifront_bot_id',
    };
  ";
  
  wp_enqueue_script( $script_id, $src, array(), '1.0.0', true );
  wp_add_inline_script( $script_id, $inline_script );
}
add_action( 'wp_enqueue_scripts', 'zifront_enqueue_scripts' );

function zifront_get_account_javascript() { 

    function randomPassword($length = 8, $add_dashes = false, $available_sets = 'luds') {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

}

function zifront_validate($input) {

    $input['zifront_live_id'] = wp_filter_nohtml_kses( $input['zifront_live_id'] );

	return $input;
}