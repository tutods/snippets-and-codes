<?php

function tds_remove_comments_settings() {
	return array(
		'post_types' => array(),
	);
}

function tds_remove_comments_applies( $post_id ) {
	$post_types = tds_remove_comments_settings()['post_types'];
	return empty( $post_types ) || in_array( get_post_type( $post_id ), $post_types, true );
}

add_action( 'admin_init', function () {
	$post_types = tds_remove_comments_settings()['post_types'];
	if ( ! empty( $post_types ) ) {
		return;
	}

	global $pagenow;
	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
} );

add_action( 'init', function () {
	$post_types = tds_remove_comments_settings()['post_types'];
	foreach ( ( empty( $post_types ) ? get_post_types() : $post_types ) as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}, 100 );

add_filter( 'comments_open', function ( $open, $post_id ) {
	return tds_remove_comments_applies( $post_id ) ? false : $open;
}, 20, 2 );

add_filter( 'pings_open', function ( $open, $post_id ) {
	return tds_remove_comments_applies( $post_id ) ? false : $open;
}, 20, 2 );

add_filter( 'comments_array', function ( $comments, $post_id ) {
	return tds_remove_comments_applies( $post_id ) ? array() : $comments;
}, 10, 2 );

add_action( 'admin_menu', function () {
	if ( empty( tds_remove_comments_settings()['post_types'] ) ) {
		remove_menu_page( 'edit-comments.php' );
	}
} );

// The admin bar adds its nodes after init, so remove the node itself late.
add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
	if ( empty( tds_remove_comments_settings()['post_types'] ) ) {
		$wp_admin_bar->remove_node( 'comments' );
	}
}, 999 );
