<?php defined( 'ABSPATH' ) || die( -1 ); // don't load directly

if ( ! $atts['symbol'] ) {
	throw new Exception( "Static view of this shortcode requires a symbol attribute!" );
}

$balance = apply_filters( 'wallets_api_balance', 0, array(
	'symbol' => $atts['symbol'],
	'user_id' => $atts['user_id'],
));

$fiat_symbol = Dashed_Slug_Wallets_Rates::get_fiat_selection();
if ( 'none' != $fiat_symbol ) {
	$rate = Dashed_Slug_Wallets_Rates::get_exchange_rate( $fiat_symbol, $atts['symbol'] );
	if ( $rate ) {
		$fiat_balance = $balance * $rate;
	}
	unset( $rate );
}

$adapters = apply_filters( 'wallets_api_adapters', array() );
if ( isset( $adapters[ $atts['symbol'] ] ) ) {
	$balance_str = sprintf( $adapters[ $atts['symbol'] ]->get_sprintf(), $balance );
} else {
	$balance_str = sprintf( "$atts[symbol] %01.8f", $balance );
}
unset( $adapters );
?>
<div class="dashed-slug-wallets balance static balance-static wallets-ready<?php

if ( Dashed_Slug_Wallets_Rates::is_fiat( $atts['symbol'] ) ) {
	echo ' fiat-coin';
}

if ( Dashed_Slug_Wallets_Rates::is_crypto( $atts['symbol'] ) ) {
	echo ' crypto-coin';
} ?>">
	<?php
		do_action( 'wallets_ui_before' );
		do_action( 'wallets_ui_before_balance' );
	?>
	<label class="balance">
		<?php echo apply_filters( 'wallets_ui_text_balance', esc_html__( 'Balance', 'wallets-front' ) ); ?>:
		<span><?php echo $balance_str; ?></span>
		<?php if ( isset( $fiat_balance ) ): ?>
		<span class="fiat-amount"><?php printf( '%s %01.2f', $fiat_symbol, $fiat_balance ); ?></span>
		<?php endif; ?>
	</label>
	<?php
		do_action( 'wallets_ui_after_balance' );
		do_action( 'wallets_ui_after' );
	?>
</div>
<?php

unset( $balance );
if ( isset( $balance_str ) ) {
	unset ( $balance_str );
};
