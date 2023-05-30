<?php
/**
 * Order Downloads.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="woocommerce-order-downloads">
	<?php if ( isset( $show_title ) ) : ?>
		<h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
	<?php endif; ?>

	<table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php foreach ( $downloads as $download ) : ?>
			<tr>
				<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php
						if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) {
							do_action( 'woocommerce_account_downloads_column_' . $column_id, $download );
						} else {
							switch ( $column_id ) {
								case 'download-product':
									if ( $download['product_url'] ) {
										echo '<a href="' . esc_url( $download['product_url'] ) . '">' . esc_html( $download['product_name'] ) . '</a>';
									} else {
										echo esc_html( $download['product_name'] );
									}
									break;
								case 'download-file':
									echo '<a href="' . esc_url( $download['download_url'] ) . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html( $download['download_name'] ) . '</a>';
									break;
								case 'download-remaining':
									echo is_numeric( $download['downloads_remaining'] ) ? esc_html( $download['downloads_remaining'] ) : esc_html__( '&infin;', 'woocommerce' );
									break;
								case 'download-expires':
									if ( ! empty( $download['access_expires'] ) ) {
										echo '<time datetime="' . esc_attr( date( 'Y-m-d', strtotime( $download['access_expires'] ) ) ) . '" title="' . esc_attr( strtotime( $download['access_expires'] ) ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) ) . '</time>';
									} else {
										esc_html_e( 'Never', 'woocommerce' );
									}
									break;
							}
						}
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<!-- new-->
<?php
$downloads=woordf_user_downloads();
$cols=woordf_get_account_downloads_columns();

?>
	
	<table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details woordf-records">
		<thead>
			<tr>
				<?php foreach ( woordf_get_account_downloads_columns() as $column_id => $column_name ) : ?>
				<th class="woordf-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php foreach ( $downloads as $download ) : ?>
			<tr>
				<?php foreach ( woordf_get_account_downloads_columns() as $column_id => $column_name ) : ?>
					<td class="woordf-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php
						switch ( $column_id ) {
							case 'order_id':
								$order = wc_get_order( $download[$column_id]);
								echo '<a href="'.$order->get_view_order_url().'">#'.$download[$column_id].'</a>';
								break;
							
							case 'type':
								echo '<div class="coll left"><div class="flex-center">';
								woordf_output_icon(woordf_icon_by_type_and_key($column_id,$download[$column_id]));
								echo '</div></div>';
								echo '<div class="coll right"><div class="flex-center">'.woordf_get_type_label($download[$column_id]).'</div></div>';
								break;
							
							case 'updated':
								echo woordf_get_datetime($download[$column_id],'Y-m-d H:i:s');
								break;
							
							case 'actions':
								woordf_output_preview_button($download);

								break;

						}
						
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
</section>
