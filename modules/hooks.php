<?php 
 
add_action( 'init', 'wcc_init' );
function wcc_init(){
	
	if( isset( $_GET['cpfrom'] ) ){
		$post_to_copy_from = (int)$_GET['cpfrom'];
		$post_id = (int)$_GET['post'];
 
		$all_cf = get_post_custom( $post_to_copy_from );
		 
		foreach( $all_cf as $key => $value ){
			delete_post_meta( $post_id, $key );
			foreach( $value as $s_value ){
				add_post_meta( $post_id, $key, $s_value );
			}			
		}

		wp_redirect( admin_url('post.php?post='.(int)$_GET['post'].'&action=edit&msg=cf_applied'), 302 );
		die();
		 
	}
	
}

function sample_admin_notice__success() {
	if( isset( $_GET['msg'] ) ){
		if( $_GET['msg'] == 'cf_applied' ){
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'CF copied', 'sample-text-domain' ); ?></p>
			</div>
			<?php
		}
	}
    
}
add_action( 'admin_notices', 'sample_admin_notice__success' );

?>