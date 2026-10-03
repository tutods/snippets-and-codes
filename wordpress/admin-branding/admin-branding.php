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
			'today'       => 'Encomendas hoje',
			'month'       => 'Vendas este mês',
			'processing'  => 'Por processar',
			'low_stock'   => 'Stock baixo',
		),
		// Empty strings switch the brand colours off.
		'primary'       => '#2eb8dc',
		'primary_dark'  => '#0f7896',
		// Hidden for users without manage_options (shop managers, editors).
		'hide_widgets'  => array( 'google_dashboard_widget', 'commercekit_stats_widget', 'e-dashboard-overview', 'dashboard_site_health', 'wps_limit_logindashboard_widget' ),
		'hide_menus'    => array( 'googlesitekit-dashboard', 'elementor-home', 'elementor', 'edit.php?post_type=elementor_library', 'complianz', 'tools.php', 'advanced_db_cleaner', 'wpcode', 'seopress-option', 'commercekit', 'really-simple-security', 'litespeed' ),
	);
}

function tds_shop_summary() {
	$cached = get_transient( 'tds_shop_summary' );
	if ( false !== $cached ) {
		return $cached;
	}
	$paid  = array( 'wc-processing', 'wc-completed', 'wc-on-hold' );
	$today = wc_get_orders( array( 'status' => $paid, 'date_created' => '>=' . strtotime( 'today', current_time( 'timestamp' ) ), 'limit' => -1, 'return' => 'ids' ) );
	// ponytail: sums every order this month in PHP; switch to the WC Analytics API if monthly orders reach the thousands.
	$month = wc_get_orders( array( 'status' => array( 'wc-processing', 'wc-completed' ), 'date_created' => '>=' . strtotime( wp_date( 'Y-m-01' ) ), 'limit' => -1 ) );
	$low   = new WP_Query( array(
		'post_type'      => array( 'product', 'product_variation' ),
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array( 'key' => '_manage_stock', 'value' => 'yes' ),
			array( 'key' => '_stock', 'value' => array( 1, (int) get_option( 'woocommerce_notify_low_stock_amount', 2 ) ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' ),
		),
	) );
	$summary = array(
		'today'      => count( $today ),
		'month'      => array_sum( array_map( function ( $order ) {
			return (float) $order->get_total();
		}, $month ) ),
		'processing' => wc_orders_count( 'processing' ),
		'low_stock'  => $low->found_posts,
	);
	set_transient( 'tds_shop_summary', $summary, 10 * MINUTE_IN_SECONDS );
	return $summary;
}

foreach ( array( 'woocommerce_new_order', 'woocommerce_order_status_changed', 'woocommerce_product_set_stock', 'woocommerce_variation_set_stock' ) as $tds_hook ) {
	add_action( $tds_hook, function () {
		delete_transient( 'tds_shop_summary' );
	} );
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
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	foreach ( tds_admin_branding_settings()['hide_widgets'] as $id ) {
		foreach ( array( 'normal', 'side', 'column3', 'column4' ) as $context ) {
			remove_meta_box( $id, 'dashboard', $context );
		}
	}
}, 9999 );

add_action( 'admin_menu', function () {
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	foreach ( tds_admin_branding_settings()['hide_menus'] as $slug ) {
		remove_menu_page( $slug );
	}
}, 9999 );

// A notice, not a dashboard widget: widgets follow each user's saved order, so a new one lands at the bottom.
add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( ! $screen || 'dashboard' !== $screen->id ) {
		return;
	}
	$s     = tds_admin_branding_settings();
	$t     = $s['texts'];
	$woo   = class_exists( 'WooCommerce' );
	$hpos  = $woo && class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	$order_list = admin_url( $hpos ? 'admin.php?page=wc-orders' : 'edit.php?post_type=shop_order' );
	$links = array();
	if ( $woo && current_user_can( 'edit_products' ) ) {
		$links[] = array( $t['new_product'], admin_url( 'post-new.php?post_type=product' ), 'dashicons-plus-alt' );
		$links[] = array( $t['orders'], $order_list, 'dashicons-cart' );
		$links[] = array( $t['customers'], admin_url( 'users.php?role=customer' ), 'dashicons-groups' );
	}
	if ( current_user_can( 'edit_posts' ) ) {
		$links[] = array( $t['new_post'], admin_url( 'post-new.php' ), 'dashicons-edit' );
	}
	$links[] = array( $t['view_site'], home_url( '/' ), 'dashicons-external' );

	$stats = array();
	if ( $woo && current_user_can( 'view_woocommerce_reports' ) ) {
		$sum     = tds_shop_summary();
		$stats[] = array( $t['today'], number_format_i18n( $sum['today'] ), $order_list, 'dashicons-cart', '' );
		$stats[] = array( $t['month'], html_entity_decode( wp_strip_all_tags( wc_price( $sum['month'] ) ) ), admin_url( 'admin.php?page=wc-admin&path=/analytics/revenue' ), 'dashicons-chart-bar', '' );
		$stats[] = array( $t['processing'], number_format_i18n( $sum['processing'] ), add_query_arg( 'status', 'wc-processing', $order_list ), 'dashicons-clock', $sum['processing'] ? 'is-alert' : '' );
		$stats[] = array( $t['low_stock'], number_format_i18n( $sum['low_stock'] ), admin_url( 'admin.php?page=wc-admin&path=/analytics/stock&type=lowstock' ), 'dashicons-warning', $sum['low_stock'] ? 'is-warn' : '' );
	}
	?>
	<div class="notice tds-welcome">
		<div class="tds-welcome-head">
			<div>
				<h2><?php echo esc_html( tds_admin_greeting() . ', ' . tds_admin_first_name() ); ?></h2>
				<p><?php echo esc_html( $t['welcome'] . ' ' . get_bloginfo( 'name' ) . '. ' . $t['intro'] ); ?></p>
			</div>
			<?php if ( $stats ) : ?>
				<div class="tds-welcome-stats">
					<?php foreach ( $stats as $stat ) : ?>
						<a class="<?php echo esc_attr( $stat[4] ); ?>" href="<?php echo esc_url( $stat[2] ); ?>">
							<span class="tds-stat-label"><span class="dashicons <?php echo esc_attr( $stat[3] ); ?>"></span><?php echo esc_html( $stat[0] ); ?></span>
							<strong><?php echo esc_html( $stat[1] ); ?></strong>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="tds-welcome-links">
			<?php foreach ( $links as $link ) : ?>
				<a href="<?php echo esc_url( $link[1] ); ?>"><span class="dashicons <?php echo esc_attr( $link[2] ); ?>"></span><?php echo esc_html( $link[0] ); ?></a>
			<?php endforeach; ?>
			<?php if ( $s['support_email'] ) : ?>
				<a href="mailto:<?php echo esc_attr( $s['support_email'] ); ?>"><span class="dashicons dashicons-sos"></span><?php echo esc_html( $t['need_help'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
} );

add_action( 'admin_head', function () {
	$s       = tds_admin_branding_settings();
	$primary = $s['primary'] ? $s['primary'] : '#2271b1';
	$dark    = $s['primary_dark'] ? $s['primary_dark'] : '#135e96';
	?>
	<style>
		.notice.tds-welcome { margin: 16px 0; padding: 22px 24px; border: 1px solid #dcdcde; border-radius: 12px; background: #fff; box-shadow: none; }
		.tds-welcome-head { display: flex; flex-wrap: wrap; gap: 16px 32px; justify-content: space-between; align-items: center; margin-bottom: 16px; }
		.tds-welcome h2 { margin: 0 0 4px; font-size: 22px; }
		.tds-welcome p { margin: 0; font-size: 14px; color: #50575e; }
		.tds-welcome-stats { display: grid; grid-template-columns: repeat(4, minmax(140px, 1fr)); gap: 10px; }
		#wpbody-content .tds-welcome-stats a { display: flex; flex-direction: column; gap: 6px; padding: 12px 16px; border: 1px solid #e5e9ec; border-radius: 12px; background: #fff; color: #1d2327; text-decoration: none !important; transition: border-color .15s, box-shadow .15s; }
		#wpbody-content .tds-welcome-stats a:hover, #wpbody-content .tds-welcome-stats a:focus { border-color: <?php echo esc_attr( $primary ); ?>; box-shadow: 0 6px 18px -10px rgba(15, 120, 150, .5); }
		.tds-stat-label { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #50575e; }
		.tds-stat-label .dashicons { width: 16px; height: 16px; font-size: 16px; color: <?php echo esc_attr( $dark ); ?>; }
		.tds-welcome-stats strong { font-size: 22px; font-weight: 700; line-height: 1.1; color: #1d2327; }
		#wpbody-content .tds-welcome-stats a.is-alert { border-color: <?php echo esc_attr( $primary ); ?>; background: #f1f9fc; }
		#wpbody-content .tds-welcome-stats a.is-warn { border-color: #f0c36d; background: #fff8e8; }
		.tds-welcome-stats a.is-warn .dashicons { color: #b26200; }
		.tds-welcome-links { display: flex; flex-wrap: wrap; gap: 8px; }
		.tds-welcome-links a, .tds-welcome-links a:hover { text-decoration: none !important; }
		.tds-welcome-links a { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1px solid #dcdcde; border-radius: 999px; color: #1d2327; font-weight: 600; }
		.tds-welcome-links a:hover { border-color: <?php echo esc_attr( $primary ); ?>; color: <?php echo esc_attr( $dark ); ?>; }
		@media (max-width: 782px) { .tds-welcome-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); width: 100%; } }

		#dashboard-widgets .postbox { border: 1px solid #e5e9ec; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 10px 24px -18px rgba(15, 120, 150, .35); transition: box-shadow .15s; }
		#dashboard-widgets .postbox:hover { box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 14px 30px -16px rgba(15, 120, 150, .45); }
		#dashboard-widgets .postbox-header { border-bottom: 1px solid #eef1f3; background: linear-gradient(180deg, #f7fbfc, #fff); }
		#dashboard-widgets .postbox-header .hndle { gap: 10px; justify-content: flex-start; font-size: 14px; font-weight: 600; padding: 14px 18px; color: #0b2530; }
		#dashboard-widgets .postbox-header .hndle::before { content: ""; flex: none; width: 8px; height: 8px; border-radius: 50%; background: <?php echo esc_attr( $primary ); ?>; box-shadow: 0 0 0 4px rgba(46, 184, 220, .18); }
		#dashboard-widgets .postbox .handle-actions button { color: #a7aaad; }
		#dashboard-widgets .postbox .handle-actions button:hover .dashicons, #dashboard-widgets .postbox .handle-actions button:hover { color: <?php echo esc_attr( $dark ); ?>; }
		#dashboard-widgets .postbox .inside { padding: 4px 18px 18px; }
		#dashboard-widgets .postbox .inside > :first-child { margin-top: 14px; }
		#dashboard-widgets-wrap .meta-box-sortables { gap: 0; }

		#dashboard-widgets ul.wc_status_list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 14px 0 0; border: 0; }
		#dashboard-widgets ul.wc_status_list li { float: none; width: auto; margin: 0; border: 0 !important; border-radius: 10px; background: #f6f8f9; }
		#dashboard-widgets ul.wc_status_list li:hover { background: #eef7fa; }
		#dashboard-widgets ul.wc_status_list li.sales-this-month, #dashboard-widgets ul.wc_status_list li.best-seller-this-month { grid-column: 1 / -1; }
		#dashboard-widgets ul.wc_status_list li a { padding: 12px 14px 12px 52px; }
		#dashboard-widgets ul.wc_status_list li a::before { left: 18px; }
		#dashboard-widgets ul.wc_status_list li a::before { color: <?php echo esc_attr( $dark ); ?>; }
		#dashboard-widgets ul.wc_status_list li.low-in-stock { background: #fff8e8; }
		#dashboard-widgets ul.wc_status_list li.low-in-stock a::before { color: #b26200; }
		#dashboard-widgets ul.wc_status_list li.out-of-stock a::before { color: #b32d2e; }
		#dashboard-widgets ul.wc_status_list li a strong { font-size: 18px; }

		#dashboard_right_now .main ul { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 0; }
		#dashboard_right_now .main ul li { float: none; width: auto; margin: 0; padding: 10px 12px; border-radius: 10px; background: #f6f8f9; }
		#dashboard_right_now li a::before, #dashboard_right_now li > span::before { color: <?php echo esc_attr( $dark ); ?>; }
		#dashboard_right_now .sub { border-top: 1px solid #eef1f3; background: none; }

		#activity-widget #published-posts li, #activity-widget #future-posts li { padding: 8px 0; margin: 0; border-bottom: 1px solid #eef1f3; }
		#activity-widget li:last-child { border-bottom: 0; }
		#activity-widget h3 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #646970; }
		#woocommerce_dashboard_recent_reviews li { padding: 10px 0; margin: 0; border-bottom: 1px solid #eef1f3; }
		#woocommerce_dashboard_recent_reviews li:last-child { border-bottom: 0; }
		#woocommerce_dashboard_recent_reviews li img.avatar { border-radius: 50%; }

		<?php if ( $s['primary'] ) : ?>
		#adminmenu li.menu-top:hover > a, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus { background: <?php echo esc_attr( $dark ); ?>; color: #fff; }
		#adminmenu li.menu-top:hover div.wp-menu-image::before, #adminmenu li.opensub > a.menu-top div.wp-menu-image::before, #adminmenu li > a.menu-top:focus div.wp-menu-image::before, #adminmenu li.menu-top:hover div.wp-menu-image svg { color: #fff; fill: #fff; }
		#adminmenu li.menu-top:hover div.wp-menu-image img, #adminmenu li.opensub > a.menu-top div.wp-menu-image img { opacity: 1; filter: brightness(0) invert(1); }
		#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.current a.menu-top, #adminmenu .wp-menu-arrow, #adminmenu .wp-menu-arrow div, #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head { background: <?php echo esc_attr( $dark ); ?>; }
		#adminmenu .wp-submenu a:hover, #adminmenu .wp-submenu a:focus, #adminmenu .wp-submenu li.current a, #adminmenu .wp-submenu li.current a:hover { color: <?php echo esc_attr( $primary ); ?>; }
		#adminmenu .awaiting-mod, #adminmenu .update-plugins { background: <?php echo esc_attr( $primary ); ?>; color: #0b2530; }
		.wp-core-ui .button-primary { background: <?php echo esc_attr( $dark ); ?>; border-color: <?php echo esc_attr( $dark ); ?>; }
		.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus { background: #0b5d73; border-color: #0b5d73; }
		.wp-core-ui .button:not(.button-primary), .wp-core-ui .button-secondary { color: <?php echo esc_attr( $dark ); ?>; border-color: <?php echo esc_attr( $dark ); ?>; }
		.wp-core-ui .button:not(.button-primary):hover { color: #0b5d73; border-color: #0b5d73; background: #f6fbfd; }
		#wpbody-content a:not(.button):not(.page-title-action):not(.nav-tab) { color: <?php echo esc_attr( $dark ); ?>; }
		#wpbody-content a:not(.button):not(.page-title-action):not(.nav-tab):hover { color: #0b5d73; }
		#wpbody-content .tds-welcome-stats a, #wpbody-content .tds-welcome-links a, #wpbody-content .wc_status_list a { color: #1d2327; text-decoration: none; }
		#wpbody-content .wc_status_list a strong { color: inherit; }
		.wrap .page-title-action { color: <?php echo esc_attr( $dark ); ?>; border-color: <?php echo esc_attr( $dark ); ?>; }
		input[type=text]:focus, input[type=search]:focus, input[type=email]:focus, input[type=number]:focus, input[type=password]:focus, select:focus, textarea:focus { border-color: <?php echo esc_attr( $primary ); ?>; box-shadow: 0 0 0 1px <?php echo esc_attr( $primary ); ?>; }
		<?php endif; ?>
	</style>
	<?php
} );
