<?php

// Enable Gutenberg to Products
function wplook_activate_gutenberg_products($can_edit, $post_type)
{
    if ($post_type == 'product') {
        $can_edit = true;
    }

    return $can_edit;
}
add_filter('use_block_editor_for_post_type', 'wplook_activate_gutenberg_products', 10, 2);

// Enable Product Cateogires and Tags in Gutenberg
function enable_taxonomy_rest($args)
{
    $args['show_in_rest'] = true;
    return $args;
}
add_filter('woocommerce_taxonomy_args_product_cat', 'enable_taxonomy_rest');
add_filter('woocommerce_taxonomy_args_product_tag', 'enable_taxonomy_rest');
