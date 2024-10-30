function filterDigits(e, allowFloat) {
  var code = (e.charCode == 0) ? e.keyCode : e.charCode;
  if (((code != 44 && code != 46) || !allowFloat) && (code < 48 || code > 57)) {
    e.preventDefault();
    return false;
  }
  return true;
  // return false;
}


// console.log(volumeAr);
var placesApp = new Vue({
  el: '#placesApp',
  data: {
    readOnly: readOnly,
    placesAr: volumeAr
  },
  computed: {
    linksCursor: function() {
      if (this.readOnly) {
        return 'default';
      } else {
        return 'pointer';
      }
    }
  },
  methods: {
    filterNCalcWeight: function(event, allowFloat) {
      if (!this.filterDigits(event, allowFloat))
        return false;
    },
    filterDigits: function(e, allowFloat) {

      return filterDigits(e, allowFloat);
    },
    removePlace: function(index) {
      // return confirm('Удалить грузовое  место?');
      if (index > -1) {
        this.placesAr.splice(--index, 1);
      }
      // this.calcTotalWeight();
    },
    addNewPlace: function() {
      if (this.placesAr.length == 9) {
        alert('Максимальное количество мест - 9');
      } else {
        var nobj = {
          Weight: '1',
          Length: '1',
          Width: '1',
          Height: '1'
        };
        this.placesAr.push(nobj);
      }
      // this.calcTotalWeight();
    },
  }
});

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function showMessage(msg) {
  alert(msg);
}

jQuery(document).ready(function($) {

  window.selRealCityName = '';
  window.selRealRegionName = '';



  $('.recalc-delivery-cost').click(function(e) {
    e.preventDefault();
    $(this).prop('disabled', true);
    $(this).addClass('disabled-button');



    var data = {
      'action': 'recalcDeliveryCost',
      'formData': jQuery("form[name='request-iml']").serialize()
    };
    btn = this;
    $('input[name="DeliveryDate"]').prop('disabled', true);
    $('input[name="DeliveryCost"]').prop('disabled', true);
    jQuery.post(ajaxurl, data, function(response) {
      // debugger;
      response = JSON.parse(response);

      if (response.hasOwnProperty('error')) {
        showMessage(response.error);
        console.log(response.console);
      } else {
        $('input[name="DeliveryCost"]').val(response.cost);
        $('input[name="DeliveryDate"]').val(response.date);
      }

      $(btn).removeAttr("disabled");
      $(btn).removeClass('disabled-button');
      $('input[name="DeliveryDate"]').removeAttr("disabled");
      $('input[name="DeliveryCost"]').removeAttr("disabled");
    });


  });


  jQuery('.select-pvz-on-map').click(function(e) {
    e.preventDefault();
    var callback = function(pvz) {

      console.log(pvz);
      jQuery('.iml-map-modal').hide(100);

      var RequestCode = pvz.RequestCode;
      var data = {
        'action': 'getPlaceKeyByPvzID',
        'ID': pvz.ID
      };
      jQuery.post(window.adminAjaxPath, data, function(response) {
        response = JSON.parse(response);
        console.log(response);
        if (response.hasOwnProperty('result')) {
          // debugger;

          if (jQuery('select[name="keyTo"] option[value="' + response.result + '"]').length == 0) {
            showMessage('В списке городов для указанной услуги не найден город, выбранный на карте');
            return false;
          }


          jQuery('select[name="keyTo"]').val(response.result);
          getPvzList(RequestCode, function(msg) {
            alert(msg);
          });
          // jQuery("select[name='keyTo']").trigger('change');
        } else {
          console.log(response.error);
        }
      });
    }

    var wurl = 'https://iml.ru';
    // var wurl = 'https://localhost';
    if (!jQuery('iframe[src^="' + wurl + '"]').length) {

    var params = {
      isselectpvz: 1,
      isc2cpage: 0,
      shwgetmapcode: 0,
      copydesc: 1,
      city: window.selRealCityName,
      region: window.selRealRegionName
    };

      if(window.deliveryInPVZOnly)
      {
        params['sdtype'] = 1;
      }

      window.iml_map.init('750px', '530px', params, callback, wurl);
      jQuery('iframe[src^="' + wurl + '"]').appendTo(jQuery('#map-container'));
    }
    jQuery('.iml-map-modal').show(100);
  });


  jQuery('.iml-map-modal .bar .close-btn').click(function() {
    jQuery('.iml-map-modal').hide(100);
  });

  $('form[name="request-iml"]').submit(function() {
    var sumWeight = 0;
    placesApp.$data.placesAr.forEach(
      function(item) {
        sumWeight += parseFloat(item.Weight);
      });

    jQuery('input[name="Weight"]').val(sumWeight);
    return true;
  });


  jQuery('input[name="DeliveryDate"]').datepicker({
    dateFormat: 'dd.mm.yy'
  });

  jQuery('.status-check').click(function(e) {
    e.preventDefault();
    if (!window.imlBarCode) {
      return;
    }
    var order_id = jQuery('input[name="order_id"]').val();

    var data = {
      'action': 'updateImlRequestStatus',
      'imlBarCode': window.imlBarCode,
      'order_id': order_id
    };
    jQuery.post(ajaxurl, data, function(response) {
      // debugger;
      response = JSON.parse(response);
      if (response.error) {
        alert(response.error);
      } else {
        window.location.reload();
      }
    });
    return false;
  });

  jQuery('.print-barcode').click(function(e) {
    e.preventDefault();
    if (!window.imlBarCode) {
      return;
    }

    var data = {
      'action': 'printBar4Order',
      'barcode': window.imlBarCode
    };
    jQuery.post(ajaxurl, data, function(response) {
      // debugger;
      response = JSON.parse(response);
      if (response.error) {
        alert(response.error);
      } else {
        window.location.href = response.url;
      }
    });
    return false;
  });





  // доставка на ПВЗ
  function isPvzMode() {
    var courierDeliveryJobs = ['24КО', '24'];
    var selJob = jQuery('select[name="Job"] option:selected').val();
    return selJob && courierDeliveryJobs.indexOf(selJob) === -1;
  }

  // возможно КО
  function isCashMode() {
    var cashJobs = ['24КО', 'С24КО'];
    var selJob = jQuery('select[name="Job"] option:selected').val();
    return selJob && cashJobs.indexOf(selJob) !== -1;
  }



  function getPvzList(selRequestCode, checkSelectedCallback) {
    if (!isPvzMode()) {
      return;
    }
    var Job = jQuery('select[name="Job"] option:selected').val();
    var placeKey = jQuery('select[name="keyTo"] option:selected').val();



    var data = {
      'action': 'getPvzByPlaceKey',
      'placeKey': placeKey,
      'Job': Job
    };

    var reqCode4Select = (typeof selRequestCode !== 'undefined') ? selRequestCode : window.initDeliveryPoint;

    // btn = control;
    jQuery.post(ajaxurl, data, function(response) {
      // console.log(response);
      jQuery('select[name="DeliveryPoint"]').empty();
      var items = JSON.parse(response);
      var wasSelected = false;

      items.forEach(function(item) {
        // console.log(item);
        if (reqCode4Select && reqCode4Select == item.RequestCode) {
          jQuery('select[name="DeliveryPoint"]').append("<option selected value='" + item.RequestCode + "'>" + item.CmptdAddress + "</option>");
          wasSelected = true;
        } else {
          jQuery('select[name="DeliveryPoint"]').append("<option value='" + item.RequestCode + "'>" + item.CmptdAddress + "</option>");
        }
      });
      if (!wasSelected && (typeof checkSelectedCallback !== 'undefined')) {
        checkSelectedCallback('Выбранный на карте ПВЗ не поддерживает услугу кассового обслуживания.\n В списке "Адрес ПВЗ получения" выбран первый вариант');
      }
    });
  }


  function getPlaceList() {
    var Job = jQuery('select[name="Job"] option:selected').val();
    // debugger;
    var html = '';
    for (var key in placeList) {

      if (placeList[key]['Jobs'].indexOf(Job) != -1) {
        if (typeof window.initKeyTo !== 'undefined' && window.initKeyTo == key) {
          html += "<option selected value='" + key + "'>" + placeList[key].title + "</option>";
          window.selRealCityName = placeList[key].realCityName;
          window.selRealRegionName = placeList[key].realRegionName;
        } else {
          html += "<option value='" + key + "'>" + placeList[key].title + "</option>";
        }

      }
    }
    jQuery('select[name="keyTo"]').html(html);
  }

  jQuery("select[name='keyTo']").change(function() {
    jQuery('input[name="Address"]').val('');
    window.initKeyTo = jQuery('select[name="keyTo"]').val();
    getPvzList();
  });


  function toggleAddressFields() {
    if (isPvzMode()) {
      jQuery('#selPvzTr').show();
      jQuery('#addressTr').hide();
    } else {
      jQuery('#selPvzTr').hide();
      jQuery('#addressTr').show();
      jQuery('input[name="Address"]').val('');
    }
  }



  jQuery('select[name="Job"]').change(function() {
    toggleAddressFields();
    getPlaceList();
    updateAmount();
    togglePvzBtn();

    if (isPvzMode()) {
      getPvzList();
    }
  });

  function togglePvzBtn() {
    if (!readOnly) {
      if (isPvzMode()) {
        jQuery('.select-pvz-on-map').show();
      } else {
        jQuery('.select-pvz-on-map').hide();
      }
    }
  }


  function updateAmount() {
    if (isCashMode()) {
      $('input[name="Amount"]').val($('input[name="StoreAmount"]').val());
    } else {
      $('input[name="Amount"]').val(0);
    }
  }


  toggleAddressFields();
  getPlaceList();
  updateAmount();
  togglePvzBtn();
  if (isPvzMode()) {
    getPvzList();
  }

});
