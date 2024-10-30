<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('body').on('click', 'ul.payment_methods li', function(){
      // debugger;
      jQuery('body').trigger("update_checkout");
    });

});
</script>
