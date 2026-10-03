<?php

function tds_buy_now_settings() {
	return array(
		'label'            => 'Comprar agora',
		'background'       => '#1d2327',
		'hover_background' => '#0f7896',
		'color'            => '#fff',
	);
}

add_action( 'woocommerce_after_add_to_cart_button', function () {
	global $product;
	if ( ! $product instanceof WC_Product || $product->is_type( array( 'external', 'grouped' ) ) ) {
		return;
	}

	$settings = tds_buy_now_settings();
	// Same form as "Add to cart", so the chosen variation and quantity are sent; only the redirect differs.
	$action   = add_query_arg( 'tds_buy_now', '1', get_permalink( $product->get_id() ) );
	?>
	<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" formaction="<?php echo esc_url( $action ); ?>" class="button alt tds-buy-now"><?php echo esc_html( $settings['label'] ); ?></button>
	<style>
		form.cart .tds-buy-now { margin-left: 8px; background: <?php echo esc_attr( $settings['background'] ); ?> !important; color: <?php echo esc_attr( $settings['color'] ); ?> !important; }
		form.cart .tds-buy-now:hover { background: <?php echo esc_attr( $settings['hover_background'] ); ?> !important; }
		form.cart .tds-buy-now.disabled, form.cart .tds-buy-now.disabled:hover { opacity: .3; cursor: not-allowed; background: <?php echo esc_attr( $settings['background'] ); ?> !important; }
	</style>
	<?php if ( $product->is_type( 'variable' ) ) : ?>
	<script>
		jQuery(function($){
			var f=$('form.variations_form'),b=f.find('.tds-buy-now').addClass('disabled'),m=f.find('.single_add_to_cart_button');
			f.on('show_variation',function(e,v,p){setTimeout(function(){b.toggleClass('disabled',!p||!v||!v.is_in_stock||!v.is_purchasable||m.is('.disabled,:disabled'))})}).on('found_variation reset_data hide_variation',function(){b.addClass('disabled')});
			b.on('click',function(e){if(b.hasClass('disabled')){e.preventDefault();f.find('.single_add_to_cart_button').trigger('click')}});
		});
	</script>
	<?php endif;
} );

add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	return empty( $_GET['tds_buy_now'] ) ? $url : wc_get_checkout_url();
} );
