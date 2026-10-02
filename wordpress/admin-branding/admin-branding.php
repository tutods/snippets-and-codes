<?php

function tds_admin_branding_settings() {
	return array(
		'developer'     => 'Daniel Sousa',
		'developer_url' => 'https://github.com/TutoDS',
		'support_email' => '', // Empty hides the support links.
		'greetings'     => array(
			'morning'   => 'Bom dia',
			'afternoon' => 'Boa tarde',
			'evening'   => 'Boa noite',
		),
		'texts'         => array(
			'built_by'    => 'Site desenvolvido por',
			'support'     => 'Suporte',
			'welcome'     => 'Bem-vindo ao painel de',
			'intro'       => 'Atalhos para o dia a dia.',
			'new_product' => 'Novo produto',
			'orders'      => 'Encomendas',
			'customers'   => 'Clientes',
			'new_post'    => 'Novo artigo',
			'view_site'   => 'Ver site',
			'need_help'   => 'Precisa de ajuda? Fale connosco',
		),
	);
}

function tds_admin_greeting() {
	$s    = tds_admin_branding_settings();
	$hour = (int) current_time( 'G' );
	if ( $hour >= 5 && $hour < 13 ) {
		return $s['greetings']['morning'];
	}
	return $hour < 20 && $hour >= 13 ? $s['greetings']['afternoon'] : $s['greetings']['evening'];
}

function tds_admin_first_name() {
	$user = wp_get_current_user();
	return $user->first_name ? $user->first_name : $user->display_name;
}

add_action( 'admin_bar_menu', function ( $bar ) {
	$bar->remove_node( 'wp-logo' );
	if ( $bar->get_node( 'my-account' ) ) {
		$bar->add_node( array(
			'id'    => 'my-account',
			'title' => esc_html( tds_admin_greeting() . ', ' . tds_admin_first_name() ) . get_avatar( get_current_user_id(), 26 ),
		) );
	}
}, 9999 );

add_filter( 'admin_footer_text', function () {
	$s     = tds_admin_branding_settings();
	$parts = array( esc_html( $s['texts']['built_by'] ) . ' <a href="' . esc_url( $s['developer_url'] ) . '" target="_blank" rel="noopener">' . esc_html( $s['developer'] ) . '</a>' );
	if ( $s['support_email'] ) {
		$parts[] = '<a href="mailto:' . esc_attr( $s['support_email'] ) . '">' . esc_html( $s['texts']['support'] ) . '</a>';
	}
	return '<span id="footer-thankyou">' . implode( ' · ', $parts ) . '</span>';
} );

add_filter( 'update_footer', function ( $content ) {
	$footer = esc_html( get_bloginfo( 'name' ) ) . ' © ' . esc_html( wp_date( 'Y' ) );
	return current_user_can( 'update_core' ) ? $footer . ' · ' . $content : $footer;
}, 9999 );

add_action( 'wp_dashboard_setup', function () {
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	}
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
} );

// A notice, not a dashboard widget: widgets follow each user's saved order, so a new one lands at the bottom.
add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( ! $screen || 'dashboard' !== $screen->id ) {
		return;
	}
	$s     = tds_admin_branding_settings();
	$t     = $s['texts'];
	$links = array();
	if ( class_exists( 'WooCommerce' ) && current_user_can( 'edit_products' ) ) {
		$hpos    = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		$links[] = array( $t['new_product'], admin_url( 'post-new.php?post_type=product' ), 'dashicons-plus-alt' );
		$links[] = array( $t['orders'], admin_url( $hpos ? 'admin.php?page=wc-orders' : 'edit.php?post_type=shop_order' ), 'dashicons-cart' );
		$links[] = array( $t['customers'], admin_url( 'users.php?role=customer' ), 'dashicons-groups' );
	}
	if ( current_user_can( 'edit_posts' ) ) {
		$links[] = array( $t['new_post'], admin_url( 'post-new.php' ), 'dashicons-edit' );
	}
	$links[] = array( $t['view_site'], home_url( '/' ), 'dashicons-external' );
	?>
	<div class="notice tds-welcome">
		<h2><?php echo esc_html( tds_admin_greeting() . ', ' . tds_admin_first_name() ); ?></h2>
		<p><?php echo esc_html( $t['welcome'] . ' ' . get_bloginfo( 'name' ) . '. ' . $t['intro'] ); ?></p>
		<div class="tds-welcome-links">
			<?php foreach ( $links as $link ) : ?>
				<a href="<?php echo esc_url( $link[1] ); ?>"><span class="dashicons <?php echo esc_attr( $link[2] ); ?>"></span><?php echo esc_html( $link[0] ); ?></a>
			<?php endforeach; ?>
			<?php if ( $s['support_email'] ) : ?>
				<a href="mailto:<?php echo esc_attr( $s['support_email'] ); ?>"><span class="dashicons dashicons-sos"></span><?php echo esc_html( $t['need_help'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<style>
		.notice.tds-welcome { margin: 16px 0; padding: 20px 22px; border: 1px solid #dcdcde; border-radius: 10px; background: #fff; box-shadow: none; }
		.tds-welcome h2 { margin: 0 0 4px; font-size: 20px; }
		.tds-welcome p { margin: 0 0 14px; font-size: 14px; color: #50575e; }
		.tds-welcome-links { display: flex; flex-wrap: wrap; gap: 8px; }
		.tds-welcome-links a, .tds-welcome-links a:hover { text-decoration: none !important; }
		.tds-welcome-links a { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1px solid #dcdcde; border-radius: 999px; color: #1d2327; font-weight: 600; text-decoration: none; }
		.tds-welcome-links a:hover { border-color: #2271b1; color: #2271b1; }
	</style>
	<?php
} );
