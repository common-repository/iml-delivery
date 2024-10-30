<div class="wrap">
  <form method="post" action="options.php">
    <?php settings_fields( 'iml-sg-status' ); ?>
    <table class="form-table">

      <?php

      $wooStatuses = [
        '' => '',
        'wc-pending' => 'Ожидает оплаты',
        'wc-failed' => 'Неудачная доставка',
        'wc-processing' => 'Обрабатывается',
        'wc-completed' => 'Доставлен',
        'wc-on-hold' => 'Удерживается',
        'wc-cancelled' => 'Отменен',
        'wc-refunded' => 'Возмещен',
      ];

      $mapAr = [
        'StsAccepted' => 'Статус принятого заказа',
        'StsHand2Courier' => 'Статус заказа, переданного курьеру',
        'StsOnPickpont' => 'Статус заказа, доставленного на пункт самовывоза',
        'StsDelivered' => 'Статус доставленного заказа',
        'StsCanceled' => 'Статус заказа, от которого отказался клиент',
      ];
      foreach ($mapAr as $key => $value) {
        echo "<tr valign='top'>
        <th scope='row'>". esc_html($value) ."</th>";
        echo "<td><select id='". esc_html($key) ."' name='". esc_html($key) ."'>";
        $selectedValue = get_option($key);
        foreach($wooStatuses as $statusKey => $statusItem) {
          $selected = ($selectedValue == $statusKey) ? 'selected="selected"' : '';
          echo "<option value='". esc_html($statusKey) ."' ". esc_html($selected) .">". esc_html($statusItem) ."</option>";
        }
        echo "</select></td>";
        echo "</tr>";
      }
      ?>
    </table>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Сохранить') ?>" />
    </p>

  </form>
</div>
