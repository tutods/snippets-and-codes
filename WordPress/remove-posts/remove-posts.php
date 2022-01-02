<?php

// Remove side menu
add_action( 'admin_menu', 'remove_default_post_type' );
function remove_default_post_type() {
    remove_menu_page( 'edit.php' );
}
// Remove +New post in top Admin Menu Bar
add_action( 'admin_bar_menu', 'remove_default_post_type_menu_bar', 999 );
function remove_default_post_type_menu_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'new-post' );
}
// Remove Quick Draft Dashboard Widget
add_action( 'wp_dashboard_setup', 'remove_draft_widget', 999 );
function remove_draft_widget(){
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}

// Remove Categories and Tags
function remove_default_taxonomies(){
    global $pagenow;

    register_taxonomy( 'post_tag', array() );
    register_taxonomy( 'category', array() );

    $tax = array('post_tag','category');

    if($pagenow == 'edit-tags.php' && in_array($_GET['taxonomy'],$tax) ){
    wp_die('Invalid taxonomy');
    }
}
add_action('init', 'remove_default_taxonomies');