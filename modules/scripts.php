<?php 
if( !class_exists('vooAddStylesSync') ){
	class vooAddStylesSync{
		/* V1.0.1 */
		
		protected $plugin_prefix;
		protected $plugin_version;
		protected $files_list;
		
		public  function __construct( $prefix, $parameters ){
			
			$this->files_list = $parameters;
			
			$this->plugin_prefix = $prefix;
			$this->plugin_version = '1.0';
			
			add_action('wp_print_scripts', array( $this, 'add_script_fn') );
		}
		public function add_script_fn(){
			//wp_enqueue_media();
			
			 foreach( $this->files_list as $key => $value ){
				 if( $key == 'common' ){
					foreach( $value as $single_line ) {
						$this->process_enq_line( $single_line );
					}
				 }
				 if( $key == 'admin' && is_admin() ){
					foreach( $value as $single_line ) {
						$this->process_enq_line( $single_line );
					}
				 }
				 if( $key == 'front' && !is_admin() ){
					foreach( $value as $single_line ) {
						$this->process_enq_line( $single_line );
					}
				 }
			 }
		}
		public function process_enq_line( $line ){

			$default_array = [
				'type' => '',
				'url' => '',
				'enq' => '',
				'localization' => '',
			];	
			$line = array_merge( $default_array, $line );

			$custom_id  = rand( 1000, 9999).basename( $line['url'] );
			if( $line['type'] == 'style' ){
				wp_enqueue_style( $this->plugin_prefix.$custom_id, $line['url'] ) ;
			}
			if( $line['type'] == 'script' ){
				
				$rand_prefix = rand(1000, 9999);
				if( isset( $line['id'] )  ){
					$script_prefix = $line['id'];
				}else{
					$script_prefix = $this->plugin_prefix.$custom_id.$rand_prefix;
				}
				
				wp_register_script( $script_prefix, $line['url'], $line['enq'] ) ;
				if( $line['localization'] ){
			 
					wp_localize_script( $script_prefix, $this->plugin_prefix.'_local_data', $line['localization'] );
				}				
				wp_enqueue_script( $script_prefix  ) ;		
			}
		}
	}
}
add_Action('init', function(  ){
		$scripts_list = array(
		'common' => array(
			array( 'type' => 'style', 'url' => plugins_url('/inc/assets/css/tw-bs4.css', __FILE__ ) ),
			//array( 'type' => 'style', 'url' => plugins_url('/inc/fa/css/font-awesome.min.css', __FILE__ ) ),
		),
		'admin' => array(
			/*
				array( 'type' => 'script', 'url' => plugins_url('/inc/jscolor/jscolor.js', __FILE__ ), 
				*/

			array( 'type' => 'style', 'url' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' ),			
			array( 'type' => 'script', 'url' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' ),


			array( 
				'type' => 'script', 
				'url' => plugins_url('/js/admin.js', __FILE__ ), 
				'enq' => array( 'jquery' ), 
				'localization' => array( 
				'add_url' => get_option('home').'/wp-admin/post-new.php?post_type=event',
				'nonce' => wp_create_nonce( 'ajax_call_nonce' ),
				'ajaxurl' => admin_url('admin-ajax.php')
				)
			),
			array( 'type' => 'style', 'url' => plugins_url('/css/admin.css', __FILE__ ) ),
		),
		'front' => array(
			/*
			array( 'type' => 'style', 'url' => plugins_url('/inc/fb/dist/jquery.fancybox.css', __FILE__ )),
			array( 'type' => 'script', 'url' => plugins_url('/inc/fb/dist/jquery.fancybox.js', __FILE__ ), 'enq' => array( 'jquery' ) ),
			*/
	 

			array( 
				'type' => 'script', 
				'url' => plugins_url('/js/front.js', __FILE__ ), 
				'enq' => array( 'jquery' ), 
				'localization' => array( 
				'add_url' => get_option('home').'/wp-admin/post-new.php?post_type=event',
				'nonce' => wp_create_nonce( 'ajax_call_nonce' ),
				'ajaxurl' => admin_url('admin-ajax.php')
				)
			),

			array( 'type' => 'style', 'url' => plugins_url('/css/front.css', __FILE__ ) ),
		)
	);

 
	$insert_script = new vooAddStylesSync( 'wis' , $scripts_list);
})


?>