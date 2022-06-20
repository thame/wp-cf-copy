<?php
/*
Plugin Name: wp-cf-copy
Plugin URI: http://voodoopress.net
Description: Some description.
Version: 1.1.1
Author: Evgen "EvgenDob" Dobrzhanskiy
Author URI: http://voodoopress.net
Stable tag: 1.1
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');


if ( ! defined( 'ABSPATH' ) ) {
	wp_die( 'Direct Access is not Allowed' );
}

// core initiation
if( !class_Exists('vooMainStart') ){
	class vooMainStart{
		public $locale;
		function __construct( $locale, $includes, $path ){
			$this->locale = $locale;
			
			// include files
			foreach( $includes as $single_path ){
				include( $path.$single_path );				
			}
			// calling localization
			add_action('plugins_loaded', array( $this, 'myplugin_init' ) );

			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			
			register_uninstall_hook(__FILE__, 'plugin_uninstall');
		}

		function plugin_activation(){
			flush_rewrite_rules();
		}
		
		function plugin_uninstall(){
			 
		}

		function myplugin_init() {
		 	$plugin_dir = basename(dirname(__FILE__));
		 	load_plugin_textdomain( $this->locale , false, $plugin_dir );
		}
	}
	
	
}



// initiate main class

$obj = new vooMainStart('wcc', array(
	'modules/class-form-elements.php',
	//'modules/class-core-helper.php',
	
	'modules/scripts.php',
	
	'modules/meta_box.php',
	'modules/hooks.php',
), dirname(__FILE__).'/' );
 
 
if( !function_exists('vd') ){
	function vd( $variable ){
		var_dump( $variable );
	}
}

if( !function_exists('ve') ){
	function ve( $variable ){
		var_export( $variable, true );
	}
}

if( !function_exists('fw') ){
	function fw( $variable ){
		$fp = fopen( dirname( __FILE__ ).'/data.txt', 'w');
		fwrite($fp, var_export( $variable, true ) );
		fclose($fp);
	}
}





 
?>