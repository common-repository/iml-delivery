<div class="wrap">
<form method="post" action="options.php">
    <?php settings_fields( 'iml-sg-cart' ); ?>
    <table class="form-table">
        <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">Курьерская доставка IML с кассовым обслуживанием</th>
        <td>
          <table>
            <tr>
              <th style="width: 50px;">Включено</th>
              <th style="width: 350px;">Название в корзине</th>
              <th style="width: 150px;">Стоимость при неответе API</th>
            </tr>
            <tr>
              <td><input style="" type="checkbox" name="enable_method_24ko" <?php echo get_option('enable_method_24ko') ? 'checked="checked"' : '' ?>/></td>
              <td><input style="width: 350px;" type="text" name="method_24ko_Name" value="<?php echo esc_html(get_option('method_24ko_Name')); ?>" /></td>
              <td><input style="width: 80px;" type="text" name="fail_con_method_24ko_price" value="<?php echo esc_html(get_option('fail_con_method_24ko_price')); ?>" />&nbsp;₽</td>
            </tr>
          </table>
        </td>
        </tr>

        <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">Предоплаченная курьерская доставка IML</th>
        <td>
          <table>
            <tr>
              <th style="width: 50px;">Включено</th>
              <th style="width: 350px;">Название в корзине</th>
              <th style="width: 150px;">Стоимость при неответе API</th>
            </tr>
            <tr>
              <td><input style="" type="checkbox" name="enable_method_24" <?php echo get_option('enable_method_24') ? 'checked="checked"' : '' ?>/></td>
              <td><input style="width: 350px;" type="text" name="method_24_Name" value="<?php echo esc_html(get_option('method_24_Name')); ?>" /></td>
              <td><input style="width: 80px;" type="text" name="fail_con_method_24_price" value="<?php echo esc_html(get_option('fail_con_method_24_price')); ?>" />&nbsp;₽</td>
            </tr>
          </table>
        </td>
        </tr>


        <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">Доставки IML до ПВЗ с кассовым обслуживанием</th>
        <td>
          <table>
            <tr>
              <th style="width: 50px;">Включено</th>
              <th style="width: 350px;">Название в корзине</th>
              <th style="width: 150px;">Стоимость при неответе API</th>
            </tr>
            <tr>
              <td><input style="" type="checkbox" name="enable_method_c24ko" <?php echo get_option('enable_method_c24ko') ? 'checked="checked"' : '' ?>/></td>
              <td><input style="width: 350px;" type="text" name="method_c24ko_Name" value="<?php echo esc_html(get_option('method_c24ko_Name')); ?>" /></td>
              <td><input style="width: 80px;" type="text" name="fail_con_method_c24ko_price" value="<?php echo esc_html(get_option('fail_con_method_c24ko_price')); ?>" />&nbsp;₽</td>
            </tr>
          </table>
        </td>
        </tr>


        <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">Предоплаченная доставка IML до ПВЗ</th>
        <td>
          <table>
            <tr>
              <th style="width: 50px;">Включено</th>
              <th style="width: 350px;">Название в корзине</th>
              <th style="width: 150px;">Стоимость при неответе API</th>
            </tr>
            <tr>
              <td><input style="" type="checkbox" name="enable_method_c24" <?php echo get_option('enable_method_c24') ? 'checked="checked"' : '' ?>/></td>
              <td><input style="width: 350px;" type="text" name="method_c24_Name" value="<?php echo esc_html(get_option('method_c24_Name')); ?>" /></td>
              <td><input style="width: 80px;" type="text" name="fail_con_method_c24_price" value="<?php echo esc_html(get_option('fail_con_method_c24_price')); ?>" />&nbsp;₽</td>
            </tr>
          </table>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Время ожидания ответа от API, сек. (Рекомендуется 10 сек)</th>
        <td><input type="text" name="conIMLtimeout" value="<?php echo esc_html(get_option('conIMLtimeout')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Отображать доставку при неответе API</th>
        <td><input type="checkbox" name="showIMLDelWhenFailCon" <?php echo get_option('showIMLDelWhenFailCon') ? 'checked="checked"' : '' ?>/></td>
        </tr>
        <tr valign="top">
        <th scope="row">Отображать дату доставки</th>
        <td><input type="checkbox" name="showDateDelivery" <?php echo get_option('showDateDelivery') ? 'checked="checked"' : '' ?>/></td>
        </tr>
        <tr valign="top">
        <th scope="row">Доставка заказов только в ПВЗ (не в постаматы)</th>
        <td><input type="checkbox" name="deliveryInPVZOnly" <?php echo get_option('deliveryInPVZOnly') ? 'checked="checked"' : '' ?>/></td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Сохранить') ?>" />
    </p>

</form>
</div>
