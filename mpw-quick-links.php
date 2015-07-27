<?php
/*
Plugin Name: MPW Quick Links
Description: Add Large Button like qucik links
Version:     0.1-alpha
Text Domain: mpw-quick-links

 */
// Register Custom Post Type
function quick_link_custom_post_type() {

	$labels = array(
		'name'                => _x( 'Quick Links', 'Post Type General Name', 'mpw' ),
		'singular_name'       => _x( 'Quick Link', 'Post Type Singular Name', 'mpw' ),
		'menu_name'           => __( 'Quick Links', 'mpw' ),
		'name_admin_bar'      => __( 'Quick Links', 'mpw' ),
		'parent_item_colon'   => __( 'Parent Item:', 'mpw' ),
		'all_items'           => __( 'All Items', 'mpw' ),
		'add_new_item'        => __( 'Add New Item', 'mpw' ),
		'add_new'             => __( 'Add New', 'mpw' ),
		'new_item'            => __( 'New Item', 'mpw' ),
		'edit_item'           => __( 'Edit Item', 'mpw' ),
		'update_item'         => __( 'Update Item', 'mpw' ),
		'view_item'           => __( 'View Item', 'mpw' ),
		'search_items'        => __( 'Search Item', 'mpw' ),
		'not_found'           => __( 'Not found', 'mpw' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'mpw' ),
	);
	$args = array(
		'label'               => __( 'quick_link', 'mpw' ),
		'description'         => __( 'Quick Link Blocks', 'mpw' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'quick_link', $args );

}

// Hook into the 'init' action
add_action( 'init', 'quick_link_custom_post_type', 0 );



function quick_links_metaboxes( array $meta_boxes ) {

	$fields = array( 
    'id'   => 'ql-page-link', 
    'name' => 'URL to Link to', 
    'type' => 'text_url', 
);
    $meta_boxes[] = array(
        'title' => 'URL linked to',
        'pages' => 'quick_link',
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => $fields // an array of fields - see individual field documentation.
    );

    return $meta_boxes; 

}

function create_quick_links ( $atts ) {
	    $attributes = shortcode_atts( array(
        'addclass' => '',
    ), $atts );
$cont = '<div class="quick-links-outer"><div class="quick-links-container">';
// WP_Query arguments
$args = array (
	'post_type'              => array( 'quick_link' ),
);
$number_of_post = 0;
// The Query
$query = new WP_Query( $args );
	if ($query -> have_posts() ) :
		while ( $query -> have_posts() ) : $query -> the_post();
			$number_of_post++;
			$thumb_id = get_post_thumbnail_id();
			$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', true);
			$thumb_url = $thumb_url_array[0];
			$link_url = rwmb_meta( 'ql_url' );
			$cont .= '<div class="quick-link';
			if (($number_of_post % 3) === 0) {
				$cont .= ' last';
			}
				$cont .= '"><a href="'.$link_url.'"><img class="img-icon" src="' . $thumb_url . '"/></a>
			<div class="link-content">
			<span class="link-title"><a href="'.$link_url.'">'.get_the_title().'</a></span><br />
			<span class="link-info">'.get_the_content().'</span>
			</div>
			</div>';
		endwhile; 
	endif; 


$cont .= '</div><!--/quick-links-container-->
</div><!--/quick-links-outer-->
<script>
jQuery( document ).ready( function ( $ ) {
	$( ".quick-link" ).click( function ( e ) {
		e.preventDefault;
		location = $( this ).children("a").attr("href");
	});
});
</script>';

return do_shortcode( $cont );
}

add_shortcode( 'quick-links', 'create_quick_links' );

add_filter( 'rwmb_meta_boxes', 'quick_links_register_meta_boxes' );
function quick_links_register_meta_boxes( $meta_boxes )
{
    $prefix = 'ql_';
    // 1st meta box
    $meta_boxes[] = array(
        'id'       => 'quick_link_meta',
        'title'    => 'Enter the Quick Link meta',
        'pages'    => array( 'quick_link' ),
        'context'  => 'normal',
        'priority' => 'high',
        'fields' => array(
            array(
                'name'  => 'Linked URL',
                'desc'  => 'Enter the URL to link to',
                'id'    => $prefix . 'url',
                'type'  => 'text',
            ),
        )
    );
    return $meta_boxes;
}
