<?php 
if( !class_exists( 'vooMetaBoxCFCopy' ) ){
	class vooMetaBoxCFCopy{
		/* V1.0.1 */
		private $metabox_parameters = null;
		private $fields_parameters = null;
		private $data_html = null;
		
		function __construct( $metabox_parameters , $fields_parameters){
			$this->metabox_parameters = $metabox_parameters;
			$this->fields_parameters = $fields_parameters;
 
			add_action( 'add_meta_boxes', array( $this, 'add_custom_box' ) );
			add_action( 'save_post', array( $this, 'save_postdata' ) );
		}
		
		function add_custom_box(){
			add_meta_box( 
				'custom_meta_editor_'.rand( 100, 999 ),
				$this->metabox_parameters['title'],
				array( $this, 'custom_meta_editor' ),
				$this->metabox_parameters['post_type'] , 
				$this->metabox_parameters['position'], 
				$this->metabox_parameters['place']
			);
		}
		function custom_meta_editor(){
			global $post;
			
			$out = '

			<div class="tw-bs4">
				<div class="form-horizontal ">';
			
			foreach( $this->fields_parameters as $single_field){
			 
				$interface_element = new formElementsClassCFCopy( $single_field['type'], $single_field, get_post_meta( $post->ID, $single_field['name'], true ) );
				$out .= $interface_element->get_code();
			  
			}		
			
					
					
			$out .= '
					</div>	
				</div>
				';	
			$this->data_html = $out;
			 
			$this->echo_data();
		}
		
		function echo_data(){
			echo $this->data_html;
		}
		
		function save_postdata( $post_id ) {
			global $current_user; 
			 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				  return;

				if( isset( $_POST['post_type'] ) ){
					if ( $_POST['post_type'] == 'page' ) 
					{
					  if ( !current_user_can( 'edit_page', $post_id ) )
						  return;
					}
					else
					{
					  if ( !current_user_can( 'edit_post', $post_id ) )
						  return;
					}
				}
			  
			  /// User editotions

				if( get_post_type($post_id) == $this->metabox_parameters['post_type'] ){
					foreach( $this->fields_parameters as $single_parameter ){
						if( isset( $_POST[$single_parameter['name']] ) ){
							update_post_meta( $post_id, $single_parameter['name'], $_POST[$single_parameter['name']] );
						}
						
					}
					
				}
				
			}
	}
}

 
 
add_Action('admin_init',  function (){
	 
	$post_type = 'repairs';

	$all_posts = get_posts( [
		'post_type' => $post_type,
		'showposts' => -1,
		'post_not__in' => [ $_GET['post'] ]
	]);
	

	$out_posts = [];
	$out_posts[0] = 'Select Post';
 
	foreach( $all_posts as $s_post ){
		$out_posts[$s_post->ID] = $s_post->post_title;
	}
	 
	 $meta_box = array(
		'title' => 'Copy CF',
		'post_type' => $post_type,
		'position' => 'advanced',
		'place' => 'high'
	);

	$fields_parameters = [];
	if( !isset( $_GET['post'] ) ){
		$fields_parameters[] = 
		array(
			'type' => 'alert',
			'name' => 'alert',
			'text' => 'Please, save post first',				
		);
	}else{
		$fields_parameters[] = 
		array(
			'type' => 'select',
			'class' => ' select2 ',
			'title' => 'Post to copy from',
			'name' => 'post_to_copy_from',
			'id' => 'post_to_copy_from',
			'value' => $out_posts
		);


		$fields_parameters[] =  
		array(
			'type' => 'button',
			'title' => 'Apply',
			'name' => 'apply_cf',
			'class' => 'btn btn-success apply_cf',
			'href' => admin_url( 'post.php?post='.(int)$_GET['post'].'&action=edit&cpfrom=%FROM%' ), 
		);	
	}

		

	

	$new_metabox = new vooMetaBoxCFCopy( $meta_box, $fields_parameters); 
	 
 } );
 

?>