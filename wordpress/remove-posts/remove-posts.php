<?php

add_action('admin_menu', function () {
	remove_menu_page('edit.php');
});

add_action('admin_bar_menu', function ($wp_admin_bar) {
	$wp_admin_bar->remove_node('new-post');
}, 999);

add_action('wp_dashboard_setup', function () {
	remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
}, 999);

add_action('init', function () {
	unregister_taxonomy_for_object_type('category', 'post');
	unregister_taxonomy_for_object_type('post_tag', 'post');
});

add_action('admin_init', function () {
	global $pagenow;

	$taxonomy = isset($_GET['taxonomy']) ? sanitize_key($_GET['taxonomy']) : '';
	if ('edit-tags.php' === $pagenow && in_array($taxonomy, ['category', 'post_tag'], true)) {
		wp_die(esc_html__('Invalid taxonomy.'));
	}
});
