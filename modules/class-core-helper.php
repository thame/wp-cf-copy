<?php 
 
class classCoreHelper{
	public static function set_featured_image( $post_id, $image_url ){
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		
		$upload_dir = wp_upload_dir();
		$response = wp_remote_get($image_url, array(
		  'timeout' => 20,
		  'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
		));
		$image_data = wp_remote_retrieve_body( $response );
		$filename = basename($image_url);
		
		$filename = md5( time() ).$filename;
		
		$filename_ar = explode('.', $filename);
		$filename = sanitize_file_name( $filename_ar[0] ).'.'.$filename_ar[count($filename_ar)-1];
		if(wp_mkdir_p($upload_dir['path']))
			$file = $upload_dir['path'] . '/' . $filename;
		else
			$file = $upload_dir['basedir'] . '/' . $filename;
		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $post_id, $attach_id );
	
	}
	public static function add_image( $image_url ){
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
			
			$upload_dir = wp_upload_dir();
			$response = wp_remote_get($image_url, array(
			  'timeout' => 20,
			  'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
			));
			$image_data = wp_remote_retrieve_body( $response );
			$filename = basename($image_url);
			
			$filename = md5( time() ).$filename;
			
			$filename_ar = explode('.', $filename);
			$filename = sanitize_file_name( $filename_ar[0] ).'.'.$filename_ar[count($filename_ar)-1];
			if(wp_mkdir_p($upload_dir['path']))
				$file = $upload_dir['path'] . '/' . $filename;
			else
				$file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents($file, $image_data);

			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return $attach_id ;
	
	}
	public static function return_post_type_list( $post_type ){
		$args = array(
			'showposts' => -1,
			'post_type' => $post_type,
			'orderby' => 'title',
			'order' => 'ASC'
		);
		$all_posts = get_posts( $args );
		$all_prods = array();
		if( count( $all_posts ) > 0 ){
			foreach( $all_posts as $single_post ){
				$all_prods[ $single_post->ID ] = $single_post->post_title;
			}
		}
		return $all_prods;
	}
	public static function return_post_type_list_full( $post_type, $pre_text ){
		$args = array(
			'showposts' => -1,
			'post_type' => $post_type,
			'orderby' => 'title',
			'order' => 'ASC'
		);
		$all_posts = get_posts( $args );
		$all_prods = array();
		
		$all_prods[''] = $pre_text;
		
		if( count( $all_posts ) > 0 ){
			foreach( $all_posts as $single_post ){
				$all_prods[ $single_post->ID ] = $single_post->post_title;
			}
		}
		return $all_prods;
	}
	public static function get_shortcode_page( $shortcode ){
		$args = array(
			'showposts' => 1,
			'post_type' => array( 'any' ),
			's' => $shortcode
		);
		$all_posts = get_posts( $args );
	 
		 
		return $all_posts[0]->ID;
	}
	public static function get_terms_list( $taxonomy ){
		$out_terms = array();
		$terms = get_terms( $taxonomy );
		if( count($terms) > 0 ){
			foreach( $terms as $s_term ){
				$out_terms[$s_term->term_id] = $s_term->name;
			}
		}
	
		return $out_terms;
	}
	public static function get_users_list( $args = array() ){

		$all_users = get_users( $args );
		$out_users = array();
		foreach( $all_users as $s_user ){
			$out_users[ $s_user->ID ] = $s_user->user_nicename;
		}
	 
		 
		return $out_users;
	}
	public static function get_attach_url( $post_id, $size ){
		$src_full = wp_get_attachment_image_src(get_post_thumbnail_id( $post_id ), $size ); 
		return $src_full;
	}

	public static function get_post_types( ){
		
		$all_post_types = get_post_types( array(), 'objects' );
		$new_post_list = [];
		foreach( $all_post_types as $s_type ){
			if( $s_type->public == 'true' ){
				$new_post_list[$s_type->name] = $s_type->label;
			}
		}
 
		return $new_post_list;
	}
	public static function get_taxonomies_list( $post_type ){
		
		$taxonomies = get_object_taxonomies( array( 'post_type' => $post_type ) );
		$out_tax[0] = 'None';
		foreach( $taxonomies as $s_tax ){
			$tax_info = get_taxonomy( $s_tax );
			if( $tax_info->show_in_menu == false ){ continue; }
			$out_tax[$s_tax] = $tax_info->label;
		}
 
		return $out_tax;
	}
	public static function get_post_terms_list( $post_type ){
		
		$taxonomies = get_object_taxonomies( array( 'post_type' => $post_type ) );
		$out2[0] = 'None';

		foreach( $taxonomies as $s_tax ){
			$tax_info = get_taxonomy( $s_tax );
			if( $tax_info->show_in_menu == false ){ continue; }
			 
			$terms = get_terms( $s_tax, [
				'hide_empty' => false,
			] );
			foreach( $terms as $s_term ){
				$out2[$s_term->term_id] = $tax_info->label.' - '.$s_term->name;
			}
 
		}
 
		return $out2;
	}
	
}

?>