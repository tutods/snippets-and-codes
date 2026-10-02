<?php

function tds_login_settings() {
	return array(
		'logo'             => '', // Image URL. Empty uses the site logo, then the site icon.
		'logo_width'       => 240,
		'logo_height'      => 120,
		'primary'          => '#2eb8dc',
		'primary_dark'     => '#0f7896',
		'text'             => '#1d2327',
		'background'       => '#f3f7f9',
		'background_image' => '', // Optional image URL, covers the whole page.
	);
}

function tds_login_logo_url() {
	$settings = tds_login_settings();
	if ( $settings['logo'] ) {
		return $settings['logo'];
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return wp_get_attachment_image_url( $logo_id, 'full' );
	}
	return get_site_icon_url( 256 );
}

add_action( 'login_enqueue_scripts', function () {
	$s    = tds_login_settings();
	$logo = tds_login_logo_url();
	$bg   = $s['background_image'] ? ' url(' . esc_url( $s['background_image'] ) . ') center / cover no-repeat' : '';
	?>
	<style>
		body.login {
			background: <?php echo esc_attr( $s['background'] ) . $bg; ?>;
			color: <?php echo esc_attr( $s['text'] ); ?>;
			display: flex;
			align-items: center;
			min-height: 100vh;
		}
		body.login #login {
			width: 360px;
			padding: 0 16px;
		}
		<?php if ( $logo ) : ?>
		body.login h1 a {
			background-image: url(<?php echo esc_url( $logo ); ?>);
			background-size: contain;
			background-position: center;
			width: 100%;
			max-width: <?php echo (int) $s['logo_width']; ?>px;
			height: <?php echo (int) $s['logo_height']; ?>px;
			margin-bottom: 24px;
		}
		<?php endif; ?>
		body.login form {
			border: 1px solid #e5e9ec;
			border-radius: 14px;
			box-shadow: 0 18px 40px -24px rgba(15, 35, 50, .35);
			padding: 28px 24px;
		}
		body.login label {
			font-weight: 600;
			color: <?php echo esc_attr( $s['text'] ); ?>;
		}
		body.login input[type=text],
		body.login input[type=password],
		body.login input[type=email] {
			border: 1px solid #d5dde3;
			border-radius: 8px;
			padding: 6px 12px;
			box-shadow: none;
		}
		body.login input:focus {
			border-color: <?php echo esc_attr( $s['primary'] ); ?>;
			box-shadow: 0 0 0 1px <?php echo esc_attr( $s['primary'] ); ?>;
		}
		body.login .button-primary {
			background: <?php echo esc_attr( $s['primary'] ); ?>;
			border-color: <?php echo esc_attr( $s['primary'] ); ?>;
			border-radius: 8px;
			box-shadow: none;
			text-shadow: none;
			font-weight: 600;
			padding: 0 18px;
		}
		body.login .button-primary:hover,
		body.login .button-primary:focus {
			background: <?php echo esc_attr( $s['primary_dark'] ); ?>;
			border-color: <?php echo esc_attr( $s['primary_dark'] ); ?>;
		}
		body.login .button:not(.button-primary):not(.wp-hide-pw) {
			background: #fff;
			border: 1px solid <?php echo esc_attr( $s['primary'] ); ?>;
			border-radius: 8px;
			color: <?php echo esc_attr( $s['primary_dark'] ); ?>;
			box-shadow: none;
			font-weight: 600;
			padding: 0 18px;
		}
		body.login .button:not(.button-primary):not(.wp-hide-pw):hover,
		body.login .button:not(.button-primary):not(.wp-hide-pw):focus {
			background: <?php echo esc_attr( $s['background'] ); ?>;
			border-color: <?php echo esc_attr( $s['primary_dark'] ); ?>;
			color: <?php echo esc_attr( $s['primary_dark'] ); ?>;
		}
		body.login .button-primary:focus,
		body.login .button:focus {
			box-shadow: 0 0 0 2px #fff, 0 0 0 4px <?php echo esc_attr( $s['primary'] ); ?>;
		}
		body.login .button.wp-hide-pw .dashicons,
		body.login form a,
		body.login .privacy-policy-page-link a,
		body.login #nav a:hover,
		body.login #backtoblog a:hover {
			color: <?php echo esc_attr( $s['primary_dark'] ); ?>;
		}
		body.login form a:hover {
			color: <?php echo esc_attr( $s['text'] ); ?>;
		}
		body.login .admin-email__actions {
			gap: 10px;
		}
		body.login #nav,
		body.login #backtoblog {
			text-align: center;
		}
		body.login .message,
		body.login .notice,
		body.login #login_error {
			border-radius: 8px;
			border-left-color: <?php echo esc_attr( $s['primary'] ); ?>;
		}
	</style>
	<?php
} );

add_filter( 'login_headerurl', function () {
	return home_url( '/' );
} );

add_filter( 'login_headertext', function () {
	return get_bloginfo( 'name' );
} );

add_filter( 'login_display_language_dropdown', '__return_false' );
