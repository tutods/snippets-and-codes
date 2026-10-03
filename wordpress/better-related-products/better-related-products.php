<?php

function tds_better_related_products_settings() {
	return array(
		'per_page'           => 8,
		'same_leaf_category' => true,
		'relate_by_tag'      => false,
		'hide_out_of_stock'  => true,
	);
}

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = tds_better_related_products_settings()['per_page'];
	return $args;
}, 100 );

if ( tds_better_related_products_settings()['same_leaf_category'] ) {
	add_filter( 'woocommerce_get_related_product_cat_terms', function ( $term_ids ) {
		$parents = array();
		foreach ( $term_ids as $id ) {
			$parents = array_merge( $parents, get_ancestors( $id, 'product_cat' ) );
		}
		// A parent category holding every product would make everything related, so keep only the deepest one.
		$leaves = array_values( array_diff( $term_ids, $parents ) );
		return $leaves ? $leaves : $term_ids;
	} );
}

if ( ! tds_better_related_products_settings()['relate_by_tag'] ) {
	add_filter( 'woocommerce_product_related_posts_relate_by_tag', '__return_false' );
}

if ( tds_better_related_products_settings()['hide_out_of_stock'] ) {
	add_filter( 'woocommerce_related_products', function ( $ids ) {
		return array_values( array_filter( $ids, function ( $id ) {
			$product = wc_get_product( $id );
			return $product && $product->is_in_stock();
		} ) );
	} );
}
