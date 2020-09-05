<?php


/**
 * Update Slug when change title
 *
 * With this code, when you change title and save POST or PAGE, th slug
 * is updated automatically
 *
 * Notes:
 * -> Contains "+": slug have "-plus"
 * -> Contains "#": slug have "-sharp"
 */

add_filter(
	'wp_insert_post_data',
	function ($data, $postarr) {
		if (strpos($data['post_title'], '+')) {

			$title = sanitize_title($data['post_title']);
			$title = trim($title, ' ') . '-plus';
			$data['post_name'] = $title;
		} else if (strpos($data['post_title'], '#')) {

			$title = sanitize_title($data['post_title']);
			$title = trim($title, ' ') . '-sharp';
			$data['post_name'] = $title;
		} else {
			$data['post_name'] = sanitize_title($data['post_title']);
		}
		return $data;
	},
	99,
	2
);
