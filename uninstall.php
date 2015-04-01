<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ){ exit(); }
		
delete_option( 'applywithlinkedin_apikey' );
delete_option( 'applywithlinkedin_divstyling' );
delete_option( 'applywithlinkedin_apidebug' );
?>