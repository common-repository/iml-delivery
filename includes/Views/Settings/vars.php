<style>

  div.iml-set-vars  div.ext_options div
  {
    margin-bottom: 10px;
  }

</style>
<div class="wrap iml-set-vars">
<form method="post" action="options.php">
    <?php
    settings_fields( 'iml-sg-vars' );
    ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Увеличить срок доставки</th>
        <td><input type="text" name="addDeliveryDateDays" value="<?php echo esc_html(get_option('addDeliveryDateDays')); ?>" />&nbsp;дней</td>
        </tr>
        <tr valign="top">
        <th scope="row">Изменить стоимость доставки</th>
        <td>
          <select id="dpExtraOper" name="dpExtraOper">
            <option value="+" <?php echo (get_option('dpExtraOper') == '+' ? ' selected="selected" ' : '')?> >+</option>
            <option value="-" <?php echo (get_option('dpExtraOper') == '-' ? ' selected="selected" ' : '')?> >-</option>
          </select>
          &nbsp;
          <input type="text" name="dpExtraNumber" value="<?php echo esc_html(get_option('dpExtraNumber')); ?>" />
          &nbsp;
          <select id="dpExtraMeasure" name="dpExtraMeasure">
            <option value="руб" <?php echo (get_option('dpExtraMeasure') == 'руб' ? ' selected="selected" ' : '')?>>руб</option>
            <option value="%" <?php  echo (get_option('dpExtraMeasure') == '%' ? ' selected="selected" ' : '')?>>%</option>
          </select>
        </td>
        </tr>


        <tr valign="top">
        <th scope="row">Округление стоимости доставки (после изменения)</th>
        <td>
          <select id="roundingOper" name="roundingOper">
            <option value="no" <?php echo (get_option('roundingOper') == 'no' ? ' selected="selected" ' : '')?> >
            не округлять</option>            
            <option value="+" <?php echo (get_option('roundingOper') == '+' ? ' selected="selected" ' : '')?> >
            в большую сторону</option>
            <option value="-" <?php echo (get_option('roundingOper') == '-' ? ' selected="selected" ' : '')?> >
            в меньшую сторону</option>
            <option value="math" <?php echo (get_option('roundingOper') == 'math' ? ' selected="selected" ' : '')?> >
            по правилам математики</option>            
          </select>
          &nbsp;
          <select id="roundingMeasure" name="roundingMeasure">
            <option value="unit" <?php echo (get_option('roundingMeasure') == 'unit' ? ' selected="selected" ' : '')?>>до единиц рублей</option>
            <option value="dozens" <?php  echo (get_option('roundingMeasure') == 'dozens' ? ' selected="selected" ' : '')?>>до десятков рублей</option>
            <option value="hundreds" <?php  echo (get_option('roundingMeasure') == 'hundreds' ? ' selected="selected" ' : '')?>>до сотен рублей</option>
          </select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Включать оценочную стоимость</th>
        <td><input type="checkbox" name="enableValuatedAmount" <?php echo get_option('enableValuatedAmount') ? 'checked="checked"' : '' ?>/></td>
        </tr>
        <?php 
        include_once wp_iml_getView('parts/conditions');
        ?>
      </table>
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Сохранить') ?>" />
      </p>

  </form>
  </div>
<script>

jQuery(document).ready(function($)
{

function checkRoundingOper()
{
  if($('select[name="roundingOper"]').find('option:selected').val() == "no")
  {
    $('select[name="roundingMeasure"]').attr('disabled', 'disabled');
  }else {
    $('select[name="roundingMeasure"]').removeAttr("disabled");
  }
}

$('select[name=roundingOper]').change(
  function(){
    checkRoundingOper();
  }
);

checkRoundingOper();
});

</script>
