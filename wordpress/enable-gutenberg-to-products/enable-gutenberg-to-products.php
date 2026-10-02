<?php

add_filter('use_block_editor_for_post_type', function ($can_edit, $post_type) {
	return 'product' === $post_type ? true : $can_edit;
}, 10, 2);

$tds_taxonomy_show_in_rest = function ($args) {
	$args['show_in_rest'] = true;
	return $args;
};

add_filter('woocommerce_taxonomy_args_product_cat', $tds_taxonomy_show_in_rest);
add_filter('woocommerce_taxonomy_args_product_tag', $tds_taxonomy_show_in_rest);
