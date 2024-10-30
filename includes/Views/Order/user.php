<h2><?php _e( 'Дополнительная информация' ); ?></h2>
<table class="shop_table shop_table_responsive additional_info">
    <tbody>
        <tr>
            <th><?php _e( 'Адрес IML ПВЗ:' ); ?></th>
            <td><?php echo esc_html(get_post_meta( $order_id, '_iml_selected_pvz_field', true )); ?></td>
        </tr>
    </tbody>
</table>
