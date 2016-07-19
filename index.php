<?php
/**
 * Plugin Name: Alphabetical Post Index
 * Plugin URI: http://briankappgoldstein
 * Description: Create an alphabetical index of a custom post type
 * Version: 1.0
 * Author: Brian Goldstein
 * Author URI: http://briankappgoldstein.com
 */

function alphaindex_alpha_tax() {
	register_taxonomy( 'alpha',array (
		0 => 'post',
	),
	array( 'hierarchical' => false,
		'label' => 'Alpha',
		'show_ui' => false,
		'query_var' => true,
		'show_admin_column' => false,
	) );
}
add_action('init', 'alphaindex_alpha_tax');

function alphaindex_save_alpha( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;
	//only run for posts
	$slug = 'post';
	$letter = '';
	// If this isn't a post, don't update it.
	if ( isset( $_POST['post_type'] ) && ( $slug != $_POST['post_type'] ) )
	return;
	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
	return;
	// OK, we're authenticated: we need to find and save the data
	$taxonomy = 'alpha';
	if ( isset( $_POST['post_type'] ) ) {
		// Get the title of the post
		$title = strtolower( $_POST['post_title'] );
		
		// The next few lines remove A, An, or The from the start of the title
		$splitTitle = explode(" ", $title);
		$blacklist = array("an ","a ","the ");
		$splitTitle[0] = str_replace($blacklist,"",strtolower($splitTitle[0]));
		$title = implode(" ", $splitTitle);
		
		// Get the first letter of the title
		$letter = substr( $title, 0, 1 );
		// Set to 0-9 if it's a number
		if ( is_numeric( $letter ) ) {
			$letter = '0-9';
		}
		
	}
	//set term as first letter of post title, lower case
	wp_set_post_terms( $post_id, $letter, $taxonomy );
}
add_action( 'save_post', 'alphaindex_save_alpha' );



