<div class="pvz_selector">
  <p style="display: inline;">
  <strong>Адрес IML ПВЗ: </strong>
<p class="iml-dest-address" style="display: inline;">
<?php
if(isset($_SESSION['iml-selected-pvz']) && isset($_SESSION['iml-pvz-City']) && isset($_SESSION['iml-pvz-Region']))
{
  echo  sprintf("%s, %s, %s", esc_html($_SESSION['iml-pvz-Region']), esc_html($_SESSION['iml-pvz-City']), esc_html($_SESSION['iml-selected-pvz']));
}
 ?>
  </p>
  <a id="selectPvz" href="">(выбрать другой)</a></p>
</div>
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
  /* width: 750px;
  height: 570px; */
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

<script type="text/javascript">
jQuery(document).ready(function($)
{


  jQuery('body').on( 'updated_checkout', function(){
    // debugger;
    if($('input[value*="iml_method_с24:"]').is(':checked') ||
      $('input[value*="iml_method_c24ko:"]').is(':checked'))
      {
        $('.pvz_selector').show();
      }else {
        $('.pvz_selector').hide();
      }
});




  jQuery('#selectPvz').click(function(event)
  {
    event.preventDefault();
    event.stopPropagation();
    var callback = function(pvz)
    {

      console.log(pvz);
      jQuery('.iml-map-modal').hide(100);
      var address = pvz.Region + ', ' +  pvz.City + ', ' + pvz.address;
      jQuery(".iml-dest-address").html(address);


      var data = {
        'action': 'setSelectedPvz',
        'address': pvz.address,
        'ID': pvz.ID,
        'RequestCode': pvz.RequestCode,
        'Special_Code' : pvz.Special_Code,
        'RegionCode' : pvz.RegionCode,
        'City' : pvz.City,
        'Region' : pvz.Region
      };
      jQuery.post("<?php echo admin_url( 'admin-ajax.php' )?>" , data, function(response) {
        console.log(response);
        $("input[name='billing_address_1']").val(pvz.address);
        $("input[name='billing_city']").val(pvz.City);
        $("input[name='billing_state']").val(pvz.Region);
        // // инициируем обновление корзины
        if(jQuery('button[name="update_cart"]').length > 0)
        {
          jQuery('button[name="update_cart"]').removeAttr("disabled");
          jQuery('button[name="update_cart"]').click();
        }else {
          jQuery(document.body).trigger("update_checkout");
        }
      });
    }

    var wurl = 'https://iml.ru';
    // var wurl = 'https://localhost';
    if(!jQuery('iframe[src^="'+wurl+'"]').length)
    {

      var params = {isselectpvz: 1, isc2cpage: 0, shwgetmapcode: 0,
        copydesc: 1,
         city: "<?php echo esc_html($city) ?>",
         region: "<?php echo esc_html($region) ?>"
      };
      if(<?php echo $deliveryInPVZOnly ? esc_html('true') : esc_html('false') ?>)
      {
        params['sdtype'] = 1;
      }

      window.iml_map.init('750px', '530px', params, callback, wurl);
      jQuery('iframe[src^="'+wurl+'"]').appendTo(jQuery('#map-container'));
    }
    jQuery('.iml-map-modal').show(100);
  });


  jQuery('.iml-map-modal .bar .close-btn').click(function()
  {
    jQuery('.iml-map-modal').hide(100);
  });

});
</script>
