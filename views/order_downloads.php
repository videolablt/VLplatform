
<?php
$orders=woordf_get_user_orders_list();
$cols=woordf_get_account_downloads_columns();

?>
<section class="woocommerce-order-downloads">
	<table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details woordf-records">
		<thead>
			<tr>
				<?php foreach ( woordf_get_account_downloads_columns() as $column_id => $column_name ) : ?>
				<th class="woordf-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php foreach ( $orders as $order_id=>$order ) : ?>
			<tr>
				<?php foreach ( woordf_get_account_downloads_columns() as $column_id => $column_name ) : ?>
					<td class="woordf-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php
						switch ( $column_id ) {
							case 'order_id':

								echo '<a href="'.woordf_get_view_order_url($order_id).'">#'.$order_id.'</a>';
								break;
							
							case 'type':
							/*
								echo '<div class="coll left"><div class="flex-center">';
								woordf_output_icon(woordf_icon_by_type_and_key($column_id,$order[$column_id]));
								echo '</div></div>';
								echo '<div class="coll right"><div class="flex-center">'.woordf_get_type_label($order[$column_id]).'</div></div>';
								*/
								echo '<div class="inline">';
								woordf_output_order_product_types($order['types']);
								echo '</div>';
								break;
							
							case 'date':
								echo woordf_get_datetime($order['date'],'Y-m-d');
								break;
							
							case 'actions':
								woordf_output_preview_button($order_id);

								break;

						}
						
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
</section>
