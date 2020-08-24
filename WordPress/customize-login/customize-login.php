<?php
// ADD THIS CODE ON functions.php OR USE CODE SNIPPETS PLUGIN
/**
 * Change Logo and Background on WordPress Login
 */
add_action('login_enqueue_scripts', function () {
?>
	<style>
		#login h1 a,
		.login h1 a {
			background-image: url("/wp-content/uploads/path/logo.png");
			width: 200px;
			background-size: cover;
			background-repeat: no-repeat;
		}

		body {
			background: url("/wp-content/uploads/path/background.jpg") !important;
			background-repeat: no-repeat;
			background-size: cover;
		}
	</style>
<?php
});

/**
 * Change Logo URL
 */
add_filter('login_headerurl', function () {
	return home_url();
});

/**
 * Change logo title
 */
add_filter('login_headertitle', function () {
	return 'Website Title';
});
