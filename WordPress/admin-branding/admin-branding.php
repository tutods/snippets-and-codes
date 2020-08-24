<?php

/**
 * Remove WP Icon on Admin Bar
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {
	$wp_admin_bar->remove_node('wp-logo');
}, 100);

/**
 * Change home icon on admin bar
 *
 * I changed to project logo (build a image like favicon)
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {

	echo "
        <style>
			#wpadminbar #wp-admin-bar-site-name>.ab-item::before {
				content: '' !important;
				background-image: url(/wp-content/uploads/path/logo.png) !important;
				background-size: cover;
				height: 25px;
				width: 25px;
				padding: 2px;
			}
		</style>
	";
}, 1);

/**
 * Change  Admin footer
 *
 * > Remove "Thank You" message
 * > Remove WP version
 */
// Remove WP Thank You message
add_filter(
	'admin_footer_text',
	function () {
		echo '<span id="footer-thankyou">Message <a href="https://yourcompany.com" target="_blank">Company</a></span>';
	}
);

// Remove WP Version

add_filter('update_footer', function () {
	return '<strong>Copyright© 2020</strong>';
}, 9999);
