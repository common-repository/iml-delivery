<div class="wrap">
  <form method="post" action="options.php">
    <?php settings_fields( 'iml-sg-main' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Тестовый режим</th>
        <td><input type="checkbox" name="testMode" <?php echo get_option('testMode') ? 'checked="checked"' : '' ?>/></td>
      </tr>
      <tr valign="top">
        <th scope="row">Расчет цен</th>
        <td>
          <?php $calcTypeVars = ['Сервер IML', 'Таблица'];
          foreach ($calcTypeVars as $value) {
            $checked = ($value == get_option('calcType')) ? ' checked="checked" ' : '';
            $value = esc_html($value);

            echo "<input ".$checked." value='$value' name='calcType' type='radio' />$value</label>&nbsp;
            &nbsp;
            &nbsp;";
          }
          ?>
        </td>
      </tr>
      <tr valign="top">
          <th scope="row">Метод расчета</th>
          <td>
            <select name="iml_calc_method">
              <option value="" <?php echo  get_option('iml_calc_method') == 'GetPlantCostOrder' ? 'selected' : '' ?>>GetPlantCostOrder</option>
              <option value="GetPrice" <?php echo  get_option('iml_calc_method') == 'GetPrice' ? 'selected' : '' ?>>GetPrice</option>
            </select>
          </td>
      </tr>
      <tr valign="top">
        <th scope="row">Курьер. Стоимость доставки в своем регионе</th>
        <td><input type="text"  class="mbdisabled" name="cdOwnRegionPrice" value="<?php echo esc_html(get_option('cdOwnRegionPrice')); ?>" />
          &nbsp;₽</td>
        </tr>
        <tr valign="top">
          <th scope="row">Курьер. Стоимость доставки в другие регионы</th>
          <td><input type="text" class="mbdisabled" name="cdOtherRegionPrice" value="<?php echo esc_html(get_option('cdOtherRegionPrice')); ?>" />
            &nbsp;₽</td>
          </tr>
          <tr valign="top">
            <th scope="row">Самовывоз. Стоимость доставки в своем регионе</th>
            <td><input type="text" class="mbdisabled" name="pkOwnRegionPrice" value="<?php echo esc_html(get_option('pkOwnRegionPrice')); ?>" />
              &nbsp;₽</td>
            </tr>

            <tr valign="top">
              <th scope="row">Самовывоз. Стоимость доставки в другие регионы</th>
              <td><input type="text" class="mbdisabled" name="pkOtherRegionPrice" value="<?php echo esc_html(get_option('pkOtherRegionPrice')); ?>" />
                &nbsp;₽</td>
              </tr>
              <tr>
                <td><input type="radio" name="packageCalcType" id="fix-pack" 
                  value="fix-pack" <?php echo get_option('packageCalcType') == 'fix-pack' ? ' checked ' : '' ?>>Фиксированная упаковка</td>
                <td><input type="radio" name="packageCalcType" id="pgood-pack" 
                  value="pgood-pack" <?php echo get_option('packageCalcType') == 'pgood-pack' ? ' checked ' : '' ?>>Каждый товар в своей упаковке (1 товар - 1 место)</td>
              </tr>
              <tr>
                <th colspan="2">
                  <fieldset class="fset-pack fix-pack">
                    <legend>Параметры</legend>
                    <label class="wpce-label"  for="defaultPlacesCount">Количество мест в заказе</label>
                    <select name="defaultPlacesCount" id="defaultPlacesCount">
                    <?php
                    // 9 - max places count
                    for($i = 1; $i <= 9 ; $i++)
                    {
                      $selected = get_option('defaultPlacesCount') == $i ? ' selected ' : '';
                      echo sprintf('<option %s value="%s">%s</option>', esc_html($selected), esc_html($i), esc_html($i));
                    }
                    ?>
                    </select>                    
                    <br>
                    <br>
                    <div>Габариты места по-умолчанию:</div><br>
                    <label class="wpce-label"  for="defaultWeightKg">Вес</label>
                    <input type="text" name="defaultWeightKg" value="<?php echo esc_html(get_option('defaultWeightKg')); ?>" />&nbsp;&nbsp;кг<br>
                    <label class="wpce-label"  for="defaultLength">Длина</label>
                    <input type="text" name="defaultLength" value="<?php echo esc_html(get_option('defaultLength')); ?>" />&nbsp;&nbsp;см<br>
                    <label class="wpce-label"  for="defaultWidth">Ширина</label>
                    <input type="text" name="defaultWidth" value="<?php echo esc_html(get_option('defaultWidth')); ?>" />&nbsp;&nbsp;см<br>
                    <label class="wpce-label"  for="defaultHeight">Высота</label>
                    <input type="text" name="defaultHeight" value="<?php echo esc_html(get_option('defaultHeight')); ?>" />&nbsp;&nbsp;см<br>
                  </fieldset>


                  <fieldset class="fset-pack pgood-pack">
                    <legend>Параметры</legend>                    
                    <div>Габариты товара по-умолчанию:</div><br>
                    <label class="wpce-label"  for="defaultGoodWeightKg">Вес</label>
                    <input type="text" name="defaultGoodWeightKg" value="<?php echo esc_html(get_option('defaultGoodWeightKg')); ?>" />&nbsp;&nbsp;кг<br>
                    <label class="wpce-label"  for="defaultGoodLength">Длина</label>
                    <input type="text" name="defaultGoodLength" value="<?php echo esc_html(get_option('defaultGoodLength')); ?>" />&nbsp;&nbsp;см<br>
                    <label class="wpce-label"  for="defaultGoodWidth">Ширина</label>
                    <input type="text" name="defaultGoodWidth" value="<?php echo esc_html(get_option('defaultGoodWidth')); ?>" />&nbsp;&nbsp;см<br>
                    <label class="wpce-label"  for="defaultGoodHeight">Высота</label>
                    <input type="text" name="defaultGoodHeight" value="<?php echo esc_html(get_option('defaultGoodHeight')); ?>" />&nbsp;&nbsp;см<br>
                  </fieldset>

                </td>
              </tr>
            <tr valign="top">
            <th scope="row">Дополнительный вес упаковки для каждого <span class="spn-lbl pgood-pack">товара</span><span class="spn-lbl fix-pack">места</span></th>
            <td><input type="text" name="addExtraWeightKg" value="<?php echo esc_html(get_option('addExtraWeightKg')); ?>" />&nbsp;&nbsp;кг</td>
            </tr>              
              <tr valign="top">
                <th scope="row">Включить комплектацию заказа</th>
                <td><input type="checkbox" name="enableFullfilment" <?php echo get_option('enableFullfilment') ? 'checked="checked"' : '' ?>/></td>
              </tr>

            </table>

            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e('Сохранить') ?>" />
            </p>

          </form>
        </div>
<script>

jQuery(document).ready(function($)
{

function checkCalcType()
{
  if($('input[name=calcType]:checked').val() == "Таблица")
  {
    $('.mbdisabled').removeAttr("disabled");
  }else {
    $('.mbdisabled').attr('disabled', 'disabled');
  }
}


function changePackageCalcType()
{

var id = $('input[name="packageCalcType"]:checked').first().attr('id');
$('.fset-pack').hide();
$('.spn-lbl').hide();
  if($('.'+id).length)
  {
   $('.'+id).show(); 
  }
}

$('input[name="packageCalcType"]').change(function()
{
  changePackageCalcType();
});





$('input[name=calcType]').change(
  function(){
    checkCalcType();
  }
);

checkCalcType();
changePackageCalcType();
});


</script>
<style type="text/css">
  
.wpce-label
{
  width: 100px;
  display: inline-block;
}

.wpce-label + input
{
  width: 100px;
}

fieldset.fset-pack
{
  padding: 20px;
  width: 400px;
  border: 1px solid gray;
}
fieldset.fset-pack > select
{
  width: 40px;
}


</style>
