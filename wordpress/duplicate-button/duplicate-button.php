<?php

add_action('admin_action_tds_duplicate_post', function () {
	$post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
	check_admin_referer('tds_duplicate_post_' . $post_id, 'duplicate_nonce');

	$post = get_post($post_id);
	if (!$post) {
		wp_die(esc_html__('Could not find the original post.'), 404);
	}

	$post_type = get_post_type_object($post->post_type);
	if (!$post_type || !current_user_can('edit_post', $post_id) || !current_user_can($post_type->cap->create_posts)) {
		wp_die(esc_html__('Sorry, you are not allowed to duplicate this item.'), 403);
	}

	$new_post_id = wp_insert_post(wp_slash([
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => get_current_user_id(),
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => 'draft',
		'post_title'     => $post->post_title,
		'post_type'      => $post->post_type,
		'to_ping'        => $post->to_ping,
		'menu_order'     => $post->menu_order,
	]), true);

	if (is_wp_error($new_post_id)) {
		wp_die(esc_html($new_post_id->get_error_message()));
	}

	foreach (get_object_taxonomies($post->post_type) as $taxonomy) {
		$terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'ids']);
		if (!is_wp_error($terms)) {
			wp_set_object_terms($new_post_id, $terms, $taxonomy);
		}
	}

	foreach (get_post_meta($post_id) as $meta_key => $meta_values) {
		if (in_array($meta_key, ['_wp_old_slug', '_edit_lock', '_edit_last'], true)) {
			continue;
		}
		foreach ($meta_values as $meta_value) {
			add_post_meta($new_post_id, $meta_key, wp_slash(maybe_unserialize($meta_value)));
		}
	}

	wp_safe_redirect(get_edit_post_link($new_post_id, 'raw'));
	exit;
});

$tds_duplicate_post_link = function ($actions, $post) {
	$post_type = get_post_type_object($post->post_type);
	if ($post_type && current_user_can('edit_post', $post->ID) && current_user_can($post_type->cap->create_posts)) {
		$url = wp_nonce_url(
			admin_url('admin.php?action=tds_duplicate_post&post=' . $post->ID),
			'tds_duplicate_post_' . $post->ID,
			'duplicate_nonce'
		);
		$actions['duplicate'] = '<a href="' . esc_url($url) . '">' . esc_html__('Duplicate') . '</a>';
	}
	return $actions;
};

add_filter('post_row_actions', $tds_duplicate_post_link, 10, 2);
add_filter('page_row_actions', $tds_duplicate_post_link, 10, 2);
