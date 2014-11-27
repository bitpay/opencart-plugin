<?php
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */
?>

<div class="buttons">
  <div class="right"><a id="button-confirm" class="button"><span><?php echo $button_bitpay_confirm; ?></span></a></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		type: 'GET',
		url: 'index.php?route=payment/bitpay/send',
    timeout: (1000 * 45), // 45 seconds
    error: function() {
      alert('Error communicating with payment provider.');
    },
		success: function(msg) {
      try {
        var result = JSON.parse(msg);
        if(result.error) {
          alert(result.error);
        } else {
          location = result.url;
        }
      } catch(e) {
        alert('JSON parsing error: '+msg);
      }
		}
	});
});
//--></script>
