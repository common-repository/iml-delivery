<div class="wrap">
<style media="screen">

.iml_load_btn
{
width: 200px;
height: 40px;
font-size: 14px;
background: #f3c500;
color: black;
font-weight: 700;
border-radius: 6px;
cursor: pointer;
}

.disabled-button
{
  background: #80808091;
  cursor: unset;
}

.form-table th
{
  width: 250px;
}

</style>
<form method="post" action="options.php">
    <?php settings_fields( 'iml-sg-dev' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Дата последнего обновления</th>
      <td>
        <div class="last-update-lists">
          <?php
          $lastUpdateLists = get_option('lastUpdateLists');
          echo esc_html($lastUpdateLists);
          ?>
        </div>
      </td>
      </tr>
      <tr valign="top">
      <th scope="row">Обновить справочники IML</th>
      <td>
        <button type="button" class= 'iml_load_btn' id='loadRegions' name="button">Обновить</button>
      </td>
      </tr>
    </table>
  </form>

  <?php add_thickbox(); ?>
  <div id="my-content-id" style="display:none;">
    <p id="tbInfo">

    </p>
  </div>
  <a href="#TB_inline?&width=300&height=120&inlineId=my-content-id" class="thickbox" style="display: none;">View my inline content!</a>

  <script type="text/javascript" >
	jQuery(document).ready(function($) {

    $('#loadRegions').click(function()
    {
    $(this).prop('disabled', true);
    $(this).addClass('disabled-button');
    var data = {
      'action': 'loadImlRegions',
      'whatever': 1234
    };
    btn = this;
    jQuery.post(ajaxurl, data, function(response) {
      if(response != 0)
      {
          $('.last-update-lists').html(response);
          $('#tbInfo').html('Справочники IML успешно обновлены');
          $('a[class="thickbox"]').click();
      }else {
        $('#tbInfo').html('Ошибка обновления справочников IML')
        $('a[class="thickbox"]').click();
      }
      $(btn).removeAttr("disabled");
      $(btn).removeClass('disabled-button');
    });
    });
	});
	</script>
