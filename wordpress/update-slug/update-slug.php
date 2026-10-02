<?php

add_filter('wp_insert_post_data', function ($data, $postarr) {
	if (
		!in_array($data['post_type'], ['post', 'page'], true)
		|| in_array($data['post_status'], ['auto-draft', 'trash', 'inherit'], true)
		|| '' === trim($data['post_title'])
	) {
		return $data;
	}

	$slug = sanitize_title($data['post_title']);
	if (str_contains($data['post_title'], '+')) {
		$slug .= '-plus';
	} elseif (str_contains($data['post_title'], '#')) {
		$slug .= '-sharp';
	}

	// Core already ran wp_unique_post_slug() before this filter, so redo it.
	$data['post_name'] = wp_unique_post_slug($slug, $postarr['ID'] ?? 0, $data['post_status'], $data['post_type'], $data['post_parent']);

	return $data;
}, 99, 2);
