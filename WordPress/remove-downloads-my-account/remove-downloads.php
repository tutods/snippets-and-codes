<?php

function removeDownloadsOnAccount($items) {
    unset( $items['downloads'] );
    return $items;
}
add_filter('woocommerce_account_menu_items', 'removeDownloadsOnAccount', 10, 1);