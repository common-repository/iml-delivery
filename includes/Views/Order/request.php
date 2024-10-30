
<style>
.request-iml
{
  font-size: 14px;;
}

.request-iml .title
{
  /* font-weight: 600; */
  font-size: 1.1em;
  text-transform: uppercase;
}

div.wrap.request-iml  div.ext_options div
/* .request-iml .ext_options div */
{
  margin-bottom: 10px;
}

.request-iml input[type="text"],.request-iml textarea, .request-iml select
{
  width: 100%;
  max-width: 600px;
}

.request-iml select[name="VAT"]
{
  width: 75px;
}

div.wrap.request-iml input.button-primary.select-pvz-on-map,
div.wrap.request-iml input.button-primary.recalc-delivery-cost
{
  margin-left: 50px;
}



.placesTable > tbody > tr >  th, .placesTable > tbody > tr > td
{
  width: 60px;
}

.disabled-button
{
  background: #80808091;
  cursor: unset;
}

</style>
<style media="screen">
.pvz_selector
{
  margin-top: 20px;
}
.iml-map-modal {
  display: none;
  position: fixed;
  z-index: 1;
  top: calc(50% - 275px + 10px);
  left: calc(50% - 325px);
  overflow: auto;
  border: 1px solid #b5aeae;
  z-index: 999999999;
}

.iml-map-modal .bar
{
  height: 20px;
  background: #b5aeae;
  color: white;
}
.iml-map-modal .bar .close-btn
{
  /* border: 1px solid gray; */
  float: right;
  height: 100%;
  width: 20px;
  cursor: pointer;
}
</style>
<div class="iml-map-modal">
  <div class="bar">
    <div class="close-btn">
✖
    </div>
  </div>
  <div style="clear: both;">
  </div>
  <div id="map-container">

  </div>
</div>



<?php
$order = wc_get_order( $order_id );
$order_data = $order->get_data(); // The Order data
// echo '<pre>';
// print_r($order_data);
// echo '</pre>';
$imlOrder = get_post_meta( $order_id, 'iml-ship-order-params', true );

$isChecked = function($value) use($imlOrder)
{
  echo ((isset($imlOrder->condMap[$value]) && $imlOrder->condMap[$value] === true) ? ' checked="checked" ' : '');
};

$readOnly = !empty($imlOrder->imlBarcode);


$isDisabled = function() use ($readOnly)
{
  echo ($readOnly === true) ? ' disabled="disabled" " ' : '';
};

// ___p($imlOrder);
?>
<?php settings_errors('iml-error'); ?>
<div class="wrap request-iml">
  <form method="post" name="request-iml" action="<?php echo esc_url(admin_url( 'options-general.php?page=create-order-iml&order_id='.$order_id));?>">
    <input type="hidden" name="action" value="request_iml_form">
    <input type="hidden" name="DeliveryPoint" value="<?php echo esc_html($imlOrder->DeliveryPoint)?>">
    <input type="hidden" name="Weight" value="<?php echo esc_html($imlOrder->Weight)?>">
    <input type="hidden" name="order_id" value="<?php echo esc_html($order_id)?>">
    <input type="hidden" name="StoreAmount" value="<?php echo esc_html($imlOrder->Amount)?>">
    <table class="form-table">


      <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">Заказ №</th>
        <td class="title"><?php echo esc_html($_GET['order_id'])?></td>
      </tr>

      <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">IML статус</th>
        <td><?php echo esc_html($this->getOrderStatusTitle($imlOrder->imlStatus))?></td>
      </tr>

      <tr valign="middle" style="border-bottom: 1px solid black;">
        <th scope="row" style="vertical-align: bottom;">IML штрих-код</th>
        <td><?php echo esc_html($imlOrder->imlBarcode)?></td>
      </tr>

<tr>
  <td colspan="2">
    <?php if(!$readOnly):?>
    <div style="padding: 10px; margin: 5px; border: 1px solid #CB4335; border-radius: 8px; display: inline-block; color: #CB4335;">
      Внимание! При редактировании заявки необходимо пересчитать стоимость доставки с помощью соответствующей кнопки
    </div>
  <?php endif;?>
  </td>
</tr>

      <tr>
        <th></th>
        <td class="title">Заявка на доставку с помощью IML</td>
      </tr>

      <tr>
        <th>Услуга</th>
        <td>
          <select name="Job" id="Job" <?php echo $isDisabled() ?> >
            <option <?php echo $imlOrder->Job == '24КО' ? 'selected' : '' ?>   value="24КО">IML доставка курьером (с наложенным платежом)</option>
            <option <?php echo $imlOrder->Job == '24' ? 'selected' : '' ?> value="24">IML доставка курьером (с предоплатой)</option>
            <option <?php echo $imlOrder->Job == 'С24КО' ? 'selected' : '' ?> value="С24КО">IML доставка до ПВЗ (с наложенным платежом)</option>
            <option <?php echo $imlOrder->Job == 'С24' ? 'selected' : '' ?> value="С24">IML доставка до ПВЗ (с предоплатой)</option>
          </select>


          <?php if(!$readOnly):?>
            <input type="button" style="display: none;" class="button-primary select-pvz-on-map" value="<?php _e('Выбрать ПВЗ на карте') ?>" />
          <?php endif;?>
        </td>
      </tr>
      <tr>
        <th>Регион отправления</th>
        <td>
          <select id='keyFrom' name='keyFrom' <?php echo esc_html($isDisabled()) ?> class="iml-ctl-select">
            <?php
            foreach($places as $placeKey => $placeItem) {
			  $placeKey = esc_html($placeKey);
              $selected = ($imlOrder->keyFrom == $placeKey) ? 'selected="selected"' : '';
              echo "<option value='{$placeKey}' $selected>{$placeItem['title']}</option>";
            }
            ?>
          </select>
        </tr>
        <tr>
          <th>Регион получения</th>
          <td>
            <select id='keyTo' name='keyTo' <?php echo esc_html($isDisabled()) ?> class="iml-ctl-select">
            </select>
          </td>
        </tr>
        <tr>
          <th>Дата доставки</th>
          <td>
            <input <?php echo esc_html($isDisabled()) ?> type="text" name="DeliveryDate"
            value="<?php echo esc_html($imlOrder->DeliveryDate) ?>"/>
          </td>
        </tr>
        <tr>
          <th>Стоимость доставки</th>
          <td>
            <input <?php echo esc_html($isDisabled()) ?> type="text" name="DeliveryCost"
            value="<?php echo esc_html($imlOrder->DeliveryCost) ?>" onkeypress="filterDigits(event, true)"/>

            <?php if(!$readOnly):?>
              <input type="button"  class="button-primary recalc-delivery-cost" value="<?php _e('Рассчитать стоимость доставки') ?>" />
            <?php endif;?>
          </td>
        </tr>
        <tr>
          <th>Сумма заказа</th>
          <td>
            <input readonly="readonly" type="text" name="Amount"
            value=""/>
          </td>
        </tr>
        <tr>
          <th>Оценочная стоимость заказа</th>
          <td>
            <input <?php echo esc_html($isDisabled()) ?> type="text" name="ValuatedAmount"
            value="<?php echo esc_html($imlOrder->ValuatedAmount) ?>" onkeypress="filterDigits(event, true)"/>
          </td>
        </tr>
        <tr style="display: none;" id="selPvzTr">
          <th>Адрес ПВЗ получения</th>
          <td>
            <select <?php echo esc_html($isDisabled()) ?> id='DeliveryPoint' name='DeliveryPoint' class="iml-ctl-select">
            </select>
          </td>
        </tr>
        <tr>
          <th>Комментарий</th>
          <td><textarea <?php echo esc_html($isDisabled()) ?> name='Comment' style="width: 100%;">
            <?php echo esc_html($imlOrder->Comment) ?>
          </textarea></td>
        </tr>

        <tr>
          <th></th>
          <td class="title">Получатель</td>
        </tr>


        <tr>
          <th>Контактное лицо</th>
          <td><input <?php echo esc_html($isDisabled()) ?> name="Contact"  type="text" value="<?php echo esc_html($imlOrder->Contact) ?>"/></td>
        </tr>

        <tr>
          <th>Телефон</th>
          <td><input <?php echo $isDisabled() ?> name='Phone'  type="text" value="<?php echo esc_html($imlOrder->Phone) ?>"/></td>
        </tr>

        <tr>
          <th>Email</th>
          <td><input <?php echo $isDisabled() ?> name='Email'  type="text" value="<?php echo esc_html($imlOrder->Email) ?>"/></td>
        </tr>

        <tr id="addressTr">
          <th>Адрес</th>
          <td><input <?php echo $isDisabled() ?> name='Address'  type="text" value="<?php echo esc_html($imlOrder->Address)  ?>"/></td>
        </tr>




        <tr>
          <th></th>
          <td class="title">Прочее</td>
        </tr>


        <tr>
          <th>НДС</th>
          <td>
            <select name="VAT" id="VAT" <?php echo $isDisabled() ?>>
              <?php
              foreach ($vatAr as $key => $value) {
              	$key = esc_html($key);
              	$value = esc_html($value);
                $selected = $imlOrder->VAT == $key ? ' selected ' : '';
                echo sprintf('<option %s value="%s">%s</option>', $selected, $key, $value);
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <div id="placesApp">
              <div v-if="readOnly == 0"><a v-bind:style="{ cursor: linksCursor}"
                v-on:click.stop.prevent="addNewPlace()">Добавить место</a></div>
              <div>
                <table class="placesTable">
                  <tr>
                    <th>Место №</th>
                    <th>Вес, кг</th>
                    <th>Длина, см</th>
                    <th>Ширина, см</th>
                    <th>Высота, см</th>
                    <th></th>
                  </tr>
                  <tr v-for="(placeItem,index) in placesAr">
                    <td>{{++index}}</td>
                    <td><input v-on:keypress="filterNCalcWeight($event, true)" :disabled="readOnly == 1" type="text" :name="'place['+index+'][Weight]'"  v-model="placeItem.Weight"></td>
                    <td><input v-on:keypress="filterDigits($event, false)" :disabled="readOnly == 1" type="text" :name="'place['+index+'][Length]'"  v-model="placeItem.Length"></td>
                    <td><input v-on:keypress="filterDigits($event, false)" :disabled="readOnly == 1" type="text" :name="'place['+index+'][Width]'"  v-model="placeItem.Width"></td>
                    <td><input v-on:keypress="filterDigits($event, false)" :disabled="readOnly == 1" type="text" :name="'place['+index+'][Height]'"  v-model="placeItem.Height"></td>
                    <td>
                      <a v-bind:style="{ cursor: linksCursor}" v-if="placesAr.length > 1 && readOnly == 0" v-on:click.stop.prevent="removePlace(index)">Удалить</a>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </td>
        </tr>
            <tr>
            <th>Включить комплектацию заказа</th>
            <td>
              <input  <?php echo $isDisabled() ?> type="checkbox" value='1'
              <?php checked($imlOrder->enableFullfilment, true) ?> name="enableFullfilment" />
            </td>
            </tr>
        <?php 
        include_once wp_iml_getView('parts/conditions');
        ?>
        </table>

        <p class="submit">
          <?php if($readOnly):?>
            <input type="button" class="button-primary print-barcode" value="<?php _e('Печать штрих-кода') ?>" />
            <span style="width: 300px; display: inline-block;"></span>
            <input type="button" class="button-primary status-check" value="<?php _e('Проверить статус') ?>" />
          <?php else:?>
            <input type="submit" class="button-primary" value="<?php _e('Отправить заказ') ?>" />
          <?php endif;?>
        </p>

      </form>
    </div>

    <script>
    window.imlBarCode = '<?php echo esc_html($imlOrder->imlBarcode)?>';
    window.initKeyTo = '<?php echo esc_html($imlOrder->keyTo)?>';
    window.initDeliveryPoint = <?php echo esc_html($imlOrder->DeliveryPoint)?>;
    var readOnly = <?php  echo !empty($imlOrder->imlBarcode) ? '1' : '0' ?>;
    var volumeAr = JSON.parse('<?php echo json_encode(array_values($imlOrder->volumeAr))?>');
    window.adminAjaxPath = "<?php echo admin_url( 'admin-ajax.php' )?>";
    var placeList = JSON.parse('<?php echo $placeList?>');
    window.deliveryInPVZOnly = <?php echo $deliveryInPVZOnly ? 'true' : 'false' ?>;
  </script>
