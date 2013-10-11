<?php
/*
Plugin Name: translated HTML Sitemap
Plugin URI: http://www.translatedinfotech.com/our-work/translated-html-sitemap/
Description: translated HTML Sitemap will generate HTML (not XML) sitemap for your sitemap page. The plugin will not only show Page and Posts but also your other Custom Post Type like Products etc. top. You can also configure to show or hide your Post Types. You just need to create a page for Sitemap and insert our shortcode <code>[translated-sitemap]</code> to display HTML sitemap. You can get support at http://forum.translatedinfotech.com/
Version: 3.1
Author: Fabio Brunelli
Author URI: http://www.fabiobrunelli.it/
License: GPL2
*/


/*** Redirect on activation ***/
register_activation_hook(__FILE__, 'translatedhs_activate');
add_action('admin_init', 'translatedhs_redirect');
function translatedhs_activate(){
    add_option('translatedhs_do_activation_redirect', true);
}
function translatedhs_redirect(){
    if (get_option('translatedhs_do_activation_redirect', false)){
        delete_option('translatedhs_do_activation_redirect');
        
        translatedhs_set_default_option();
        
        wp_redirect('options-general.php?page=translatedhs');
    }
}
/*** ***/


if( isset($_POST['translatedhs-update']) ){
	add_action( 'admin_notices', 'translatedhs_theme_upgrade_notice' );
}
function translatedhs_theme_upgrade_notice() { ?>
	<div id="message" class="updated fade">
		<p>translated HTML Sitemap options saved successfully. </p>
	</div>

<?php
}


function translatedhs_set_default_option(){
	$list       = array();
	$post_types = translatedhs_post_types();
	
	foreach ($post_types  as $post_type ){
		$list[] = $post_type->name;
		add_option('translatedhs_active_'.$post_type->name , 'active' );
	}
	$list = implode(',', $list);
	add_option('translatedhs_sortorder' , $list ); // storing sort order
}






function translatedhs_post_types(){
	// http://codex.wordpress.org/Function_Reference/get_post_types
	$args=array(
	  'public'   => true
	  //'_builtin' => false
	);
	
	$output     = 'objects'; // names or objects, note names is the default
	$operator   = 'and'; // 'and' or 'or'
	$post_types = get_post_types($args,$output,$operator); 
	
	// Removing Attachment Custom Post Type
	unset($post_types["attachment"]);
	
	return $post_types;
}




add_action( 'admin_init', 'translatedhs_init', 1 );
add_action( 'admin_menu', 'translatedhs_adminbar_menu' );
add_action( 'plugin_action_links_' . plugin_basename(__FILE__), 'translatedhs_plugin_actions');



function translatedhs_plugin_actions($links){
	$new_links = array();
	$adminlink = get_bloginfo('url').'/wp-admin/';
	$fcmlink = 'http://www.fischercreativemedia.com/wordpress-plugins';
	$new_links[] = '<a href="'.$adminlink.'options-general.php?page=translatedhs">Settings</a>';
	return array_merge($links,$new_links );
}


function translatedhs_adminbar_menu(){
	if(is_multisite() && is_super_admin()){
		add_options_page( 'translated HTML Sitemap Options', 'translated HTML Sitemap Options','manage_network', 'translatedhs', 'translatedhs_page' );
	}elseif(is_multisite() && !is_super_admin()){
	    $theRoles = get_option('global-admin-bar-roles');
	    if(!is_array($theRoles)){$theRoles = array();}
	    if(!in_array(get_current_user_role(),$theRoles)){
			add_options_page( 'translated HTML Sitemap Options', 'translated HTML Sitemap Options','manage_options', 'translatedhs', 'translatedhs_page' );
		}
	}elseif(!is_multisite() && current_user_can('manage_options')){
		add_options_page( 'translated HTML Sitemap Options', 'translated HTML Sitemap Options','manage_options', 'translatedhs', 'translatedhs_page' );
	}
}



function translatedhs_page(){
	
	// storing plugin options as array
	if( isset($_POST['translatedhs-update']) ){
		update_option( 'translatedhs_sortorder' , $_POST['translatedhs-sortorder'] );
		update_option( 'translatedhs_exclude' , $_POST['translatedhs-exclude'] );
		
		$post_types2  = translatedhs_post_types();
		
		foreach ( $post_types2 as $post_type ){
			if( isset( $_POST['translatedhs_active_'.$post_type->name] ) ){
				update_option('translatedhs_active_'.$post_type->name, 'active' );
			} else {
				update_option('translatedhs_active_'.$post_type->name, 'deactive' );
			}
			
			
			
			// Change default name
			update_option('translatedhs_newname_'.$post_type->name, $_POST['translatedhs_newname_'.$post_type->name] );
			// translatedhs_newname_
			/*if( $_POST['translatedhs_active_'.$post_type->label->name] != ''   ){
				
			}*/
		}
	}
	
	// Retrive all options
	$translatedhs_sortorder = get_option('translatedhs_sortorder');
	$translatedhs_exclude   = get_option('translatedhs_exclude');
	?>
	
	
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div> <h2>translated HTML Sitemap Options</h2>
		<br />
	
		<form method="post" action="">
			<input type="hidden" name="translatedhs-update" id="translatedhs-update" value="y" />
		
			<?php
			settings_fields( 'translatedhs' );
			translatedhs_set_default_option();
			?>
    
			
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2" style="min-width:650px;">
					<div id="post-body-content">
						<div id="translatedhs-option-wrapper">
							<div id="translatedhs-option-title">
								<div class="translatedhs-title translatedhs-title1">Drag to Sort</div>
								<div class="translatedhs-title translatedhs-title2">Show</div>
								<div class="translatedhs-title translatedhs-title3">Custom Post Name</div>
								<div class="translatedhs-title translatedhs-title4">Custom Post SLUG</div>
							</div><!-- #translatedhs-option-title -->
				
							<ul id="translatedhs-sortable">
								<?php
								$allposttype     = array();
								$post_types      = translatedhs_post_types();
								$post_list_array = translatedhs_post_list();
								//var_dump($post_list_array);
								echo translatedhs_sortableList( $post_list_array );
								
								// creating sort order adding new post tye and removing removed post type
								$translatedhs_sortorder = implode( ',',array_keys($post_list_array) );
								
								?>
							</ul><!-- #sortable -->
					
					
					
					
						</div><!-- #translatedhs-option-wrapper -->
					
						
						<br />
					
					
						<div class="postbox" style="width:650px;" >
							<h3 class='hndle'>Exclude Post</h3>
							<div class="inside">
								<div class="submitbox">
									Exclude post:
									<input type="text" name="translatedhs-exclude" id="translatedhs-exclude" style="width:400px;" value="<?php echo $translatedhs_exclude; ?>" />
									<p class="description">Please insert comma separated page IDs which you want to hide on Sitemap page. <br> Example: <code>8,56,98,106</code></p>
									<div class="clear"></div>
								</div><!-- .submitbox -->
							</div><!-- .inside -->
						</div><!-- #postbox-container-1 .postbox-container -->
					
					
					
					

					
					
					
						
				
						<input type="hidden" name="translatedhs-sortorder" id="translatedhs-sortorder" value="<?php echo $translatedhs_sortorder; ?>" />
						<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
				
					</div><!-- #post-body-content -->
			
			
			
					<div id="postbox-container-1" class="postbox-container">
						<div class="postbox">
							<h3 class='hndle'>Quick Guide</h3>
							<div class="inside">
								<div class="submitbox">
									Steps:
									<ol>
										<li>Select Custom Post Type from left, which you want to show on Sitemap Page. Than click "Save Changes" button.</li>
										<li>Create a new page (for sitemap) and insert <code>[translated-sitemap]</code> in content area.</li>
										<li>Done, yeah that's easy.</li>
									</ol>
									<hr>
									<div style="padding:10px; text-align:center;">
									<a href="http://forum.translatedinfotech.com/categories/translated-html-sitemap" target="_blank" class="button-primary" >Get Help</a> &nbsp; &nbsp; &nbsp; <a href="http://forum.translatedinfotech.com/categories/translated-html-sitemap" target="_blank" class="button">Report Bug</a>
									</div>
									<div class="clear"></div>
								
								</div><!-- .submitbox -->
							</div><!-- .inside -->
						</div><!-- .postbox -->
						
					</div><!-- .postbox-container #postbox-container-1 -->
					
					
					
					<div class="clear"></div>
				</div><!-- #post-body -->
				<div class="clear"></div>
			
			</div><!-- #poststuff -->
			
			
			
			
		</form>
		
	</div><!-- .wrap -->
	
	<?php
}




function translatedhs_sortableList($post_types){
	$return  = '';
	
	foreach($post_types as $post_type){
		$checked = '';
		if($post_type->translated_active == 'yes' ){
			$checked = ' checked="checked" ';
		}
		
		$newname = $post_type->labels->name;
		if( isset($post_type->newname) ){
			$newname = $post_type->newname;
		}
		
		$return .= '
		<li class="translatedhs-ui-state-default" id="' . $post_type->name . '">
			<div class="translatedhs-cpt">
				<div class="translatedhs-dragable-handler"></div>
				<div class="translatedhs-dragable-checkbox"><input name="translatedhs_active_'.$post_type->name.'" id="translatedhs_active_'.$post_type->name.'" type="checkbox" ' . $checked . ' /></div>
				<div class="translatedhs-cpt-name">
					<span class="translatedhs-cpt-name-title">' . $newname . '</span>
					&nbsp; <span class="translatedhs_changename">(<a href="#" title="The title of this custom post type is dynamically generated. But you can also give it another title too.">Change</a>)</span>
					<div class="translatedhs-newname"><input type="text" name="translatedhs_newname_'.$post_type->name.'" value="'.$newname.'" /> <a class="translated-save-newname" href="#">OK</a> &nbsp; <a class="translated-cancel-newname" href="#">CANCEL</a></div>
				</div>
				<div class="translatedhs-cpt-slug">' . $post_type->name . '</div>
				<span style="display:none;" class="translatedhs-originalname">'.$post_type->labels->name.'</span>
				<div class="clr"></div>
			</div>
		</li>
		';
	}
	return $return;
}



function translatedhs_init(){
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('translatedhs-custom-js' ,  plugins_url( 'translated-html-sitemap.js', __FILE__ ) );
	wp_enqueue_style('translatedhs-custom-css' ,  plugins_url( 'translated-html-sitemap.css', __FILE__ ) );
}




function sortArrayByArray($array,$orderArray){
	$ordered = array();
	foreach($orderArray as $key) {
		if(array_key_exists($key,$array)){
			if( get_option('translatedhs_active_'.$key) == 'active' ){
				$array[$key]->translated_active = 'yes';
			}
			
			// New Name
			if( get_option('translatedhs_newname_'.$key) != '' ){
				$array[$key]->newname = get_option('translatedhs_newname_'.$key);
			} else {
				$array[$key]->label->name;
			}
			
			
			
			$ordered[$key] = $array[$key];
			unset($array[$key]);
		}
	}
	return $ordered + $array;
}



function translatedhs_post_list(){
	$allposttype       = array();
	$new_allposttype   = array();
	$post_types        = translatedhs_post_types();
	$translatedhs_sortorder = get_option('translatedhs_sortorder');
	
	
	$translatedhs_sortorder_array = explode( ',', $translatedhs_sortorder );
	$allposttype             = sortArrayByArray($post_types, $translatedhs_sortorder_array);

	return $allposttype;
}




/******************* SHORTCODE *********************/
//[translated-sitemap]
function shortcode_translated_sitemap( $atts ){
	$return            = '<div class="translated-html-sitemap-wrapper">';
	$post_types        = translatedhs_post_types();
	$translatedhs_sortorder = get_option('translatedhs_sortorder');
	//$translatedhs_exclude   = get_option('translatedhs_exclude');
	
	$translatedhs_sortorder_array = explode( ',', $translatedhs_sortorder );
	foreach($translatedhs_sortorder_array as $post_type){
		if( get_option('translatedhs_active_'.$post_type) == 'active' ){
			
			$newname = $post_types[$post_type]->labels->name;
			if( get_option('translatedhs_newname_'.$post_type) != '' ){
				$newname = get_option('translatedhs_newname_'.$post_type);
			}
			//var_dump($post_types[$post_type]->newname);
		
			
			$return .= translatedhs_get_post_by_post_type( $post_type , $newname );
		}
	}
	
	$return .= '</div> <!-- .translated-html-sitemap-wrapper -->';
	
	return $return;
}

add_shortcode( 'translated-sitemap', 'shortcode_translated_sitemap' );


function translatedhs_get_post_by_post_type( $postype , $title , $orderby = 'menu_order' , $order = 'ASC' ){
	global $post;
	$curr_page_id = '';
	
	if( isset($post->ID) ){
		$curr_page_id = $post->ID;
	}
	
	$return = '';
	$args = array( 'post_type' => $postype, 'posts_per_page' => -1, 'orderby' => $orderby, 'order' => $order );
	$loop = new WP_Query( $args );
	wp_reset_query(); // Restting WP_Query
	
	$posts    = $loop->posts;
	$return .= '<h2 class="translated-html-sitemap-post-title translated-'.$loop->query_vars['post_type'].'-title">'.ucfirst(translate($title)).'</h2>';
	
	if(count($posts) > 0 ){
		//echo '<pre>';
		//var_dump($loop->query_vars);
		//echo '</pre>';
		$return   .= '<ul class="translated-html-sitemap-post-list translated-'.$loop->query_vars['post_type'].'-list">';
		$parent_id = 0; // We are first start by fetching parent pages
		$return   .= translatedhs_get_subpost( $posts , $parent_id , $curr_page_id );
		$return   .= '</ul>';
	}
	
	
	
	return $return;
}

function translatedhs_get_subpost( $posts , $parent_id , $curr_page_id ){
	$return = '';
	$posts2 = $posts;
	
	$translatedhs_exclude = get_option('translatedhs_exclude');
	$translatedhs_exclude = explode(',',$translatedhs_exclude);
	//var_dump($translatedhs_exclude);
	
	
	if( $posts > 0 ){
		foreach($posts as $post){
			
			if($post->post_parent == $parent_id){
				if( $post->ID != $curr_page_id ){
					if( !in_array($post->ID, $translatedhs_exclude) ){
						$return .= '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
						$return .= translatedhs_get_subpost( $posts2, $post->ID , $curr_page_id  );
						$return .= '</li>';
					}
				}
			}
		}
		if($return != ''){
			$return = '<ul>'.$return.'</ul>';
		}
	}
	
	return $return;
	
}

/***************************************************/

