<?php
/*
Plugin Name: Apply with LinkedIn buttons
Plugin URI: http://wordpress.org/extend/plugins/apply-with-linkedin-buttons/
Description: Use this plugin to easily add "Apply with LinkedIn" buttons to job opening posts and lets you customize them
Author: Martijn Heesters - d-Media
Version: 1.2.2
Author URI: http://d-media.nl
*/

/* init */

// Only of use in the admin interface
if ( is_admin() ) {
    add_action( 'admin_init' , 'applywithlinkedin_register_plugin_settings' ); // Setup plugin component registration
    add_action( 'admin_menu' , 'applywithlinkedin_options' ); // if you're in the admin menu, show the options panel
} else {
    add_action( 'wp_print_footer_scripts','add_applywithlinkedin_js' ); // add js to footer
}

/* front end */

// this function adds the google js code to (the end of) the page
function add_applywithlinkedin_js(){
	if ( get_option( 'applywithlinkedin_apikey' ) != ''){
		echo '<script src="//platform.linkedin.com/in.js" type="text/javascript"> api_key: '.stripslashes(strip_tags(get_option( 'applywithlinkedin_apikey' ))).' </script>';
	} // if
}

// shortcode for adding linkedin buttons to post
//[applywithlinkedin jobtitle="Job title" companyname="My Company" email="myemail@address.com" logo="http://yoursite.com/yourlogo.png" themecolor="#ff0000" coverletter="required"]
function applywithlinkedin_sc_func( $atts ) {
	extract( shortcode_atts( array(
		'jobtitle' => '',
		'companyname' => '',
		'email' => '',
		'logo' => '',
		'themecolor' => '',
		'coverletter' => '',
		'size' => ''
	), $atts ) );
	// check if set email address is my work email address (author). Some people won't change it so i get spammed
	if ( $email == 'martijn@d-media.nl'){
		return 'Please notify website administrator to check the email addresses used in the shortcodes of the "Apply with LinkedIn" plugin. The current address is still set to the authors email address';
	} else {
		// clean vars
		if (( $coverletter != 'optional' ) && ( $coverletter != 'required' ) ){	$coverletter = 'hidden'; } // optional, required or hidden (default)
		if (( $size != 'medium' ) && ( $size != 'small' ) ){ $size = ''; } // small, medium or empty (=large=default)
		$logo = sanitize_text_field( $logo ); // http://www.xyzcompany.com/images/logo.png
		if ($logo != ''){ $logo='data-logo="'.$logo.'"'; }
		$themecolor = sanitize_text_field( $themecolor ); // #ff0000
		if ($themecolor != ''){ $themecolor='data-themecolor="'.$themecolor.'"'; }
		// build button
		$result='<script type="IN/Apply" data-jobtitle="'.sanitize_text_field($jobtitle).'" data-email="'.sanitize_text_field($email).'" data-companyname="'.sanitize_text_field($companyname).'" '.$logo.' '.$themecolor.' data-coverLetter="'.$coverletter.'" data-size="'.sanitize_text_field($size).'"></script>';
		// add div for styling
		if ( get_option( 'applywithlinkedin_divstyling' ) == 1){ $result='<div class="applywithlinkedinButton">'.$result.'</div>'; }
		// return button
		return $result;
	}
}
add_shortcode( 'applywithlinkedin', 'applywithlinkedin_sc_func' );

/* admin area */

// register plugin options
function applywithlinkedin_register_plugin_settings() {
	// only for users who can manage options
	if ( current_user_can( 'manage_options' ) ){
		// add options with default values (only adds them if they don't exist yet)
		add_option( 'applywithlinkedin_apikey' ,'' );
		add_option( 'applywithlinkedin_divstyling','' );
	}
}

// adds page to the admin menu
function applywithlinkedin_options(){
    $page=add_options_page( 'Apply with LinkedIn button settings', 'Apply with LinkedIn', 'administrator', basename(__FILE__), 'applywithlinkedin_options_page' );
    // Using registered $page handle to hook stylesheet loading
    add_action( 'admin_print_styles-' . $page, 'applywithlinkedin_admin_stylesandscripts' );
}

// add js and stylesheet for options page, It will be called only on your plugin admin page, enqueue our stylesheet here
function applywithlinkedin_admin_stylesandscripts() {
    wp_enqueue_style('applywithlinkedinStylesheet');
    wp_enqueue_script('applywithlinkedinScript');
}

// plugin options page
function applywithlinkedin_options_page(){
	if ( isset( $_POST ) ){
		if ( isset( $_POST['Submit'] ) ){
			update_option( 'applywithlinkedin_apikey', $_POST['apikey'] );
			update_option( 'applywithlinkedin_divstyling', $_POST['divstyling'] );
		}
	}
	?>
	 <div class="wrap">
            <div class="icon32" id="icon-options-general"><br/></div>
            <h2><?php _e( 'Apply with LinkedIn button settings', 'applywithlinkedin' );?></h2>
            <form method="post" action="options-general.php?page=applywithlinkedin.php">
                <table class="form-table">
                    <tr>
                        <td valign="top"><strong><?php _e( 'Display options', 'applywithlinkedin' );?></strong></td>
                        <td valign="top">
                            <input type="checkbox" id="awl_divstyling" value="1" <?php if (get_option( 'applywithlinkedin_divstyling' ) == '1' ) echo 'checked="checked"'; ?> name="divstyling" />
                            <label for="divstyling"><?php _e( 'Add a containing div for each button with the classname <i>applywithlinkedinButton</i>, use this to style and position the button', 'applywithlinkedin' );?></label>
                            <br />							
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong><?php _e( 'Settings', 'applywithlinkedin' );?></strong></td>
                        <td>
							<label for="awl_apikey"><?php _e( 'API key', 'applywithlinkedin' );?>:</label>
							<input name="apikey" id="awl_apikey" value="<?php echo stripslashes(( get_option( 'applywithlinkedin_apikey' ) ));?>"/> (required) <?php _e( 'More information on how to obtain one can be found <a href="https://www.linkedin.com/secure/developer" target="_blank">here</a>.', 'applywithlinkedin' );?>
							<br />
						</td>
					</tr>
				</table>
            <p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Save Changes', 'applywithlinkedin' );?>" /></p>
            </form>

			After setting up the API key you can use the following shortcode to add buttons to your post:<br /><br />
			<span style="display:block;font-family: Courier !important;font-size: 14px;background-color: #fff;padding: 5px;">[applywithlinkedin jobtitle="Job title" companyname="My Company" email="myemail@address.com" logo="http://yoursite.com/yourlogo.png" themecolor="#ff0000" coverletter="required" size="small"]</span>
			<br />
			Note that setting a logo, theme color, size or cover letter is optional.<br />
			The possible values for cover letter are: optional, required and hidden (default)<br />
			The possible values for size are: small, medium and large (default)<br />

	</div>
	<?php
}

// add plugin settings link on the plugin overview page
add_action( 'plugin_action_links_' . plugin_basename(__FILE__), 'applywithlinkedin_filter_plugin_actions' );
function applywithlinkedin_filter_plugin_actions( $links ){
	return array_merge( array( '<a href="options-general.php?page=applywithlinkedin.php">Settings</a>' ), $links );
}

?>
