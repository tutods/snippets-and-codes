<?php

add_action('admin_init', function () {
	global $pagenow;

	if ('edit-comments.php' === $pagenow) {
		wp_safe_redirect(admin_url());
		exit;
	}

	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

	foreach (get_post_types() as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
});

// The admin bar adds its nodes after init, so remove the node itself late.
add_action('admin_bar_menu', function ($wp_admin_bar) {
	$wp_admin_bar->remove_node('comments');
}, 999);
