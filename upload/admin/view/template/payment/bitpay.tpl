<?php

/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

echo $header;

?>
<style>
  @import url(http://fonts.googleapis.com/css?family=Ubuntu);
  .bp_banner {
    color: #b3bfcc;
    font-family: 'Ubuntu', sans-serif;
    background: #002855;
  }
  .bp_banner > td {
    border-bottom: none !important;
  }
  .bp_banner p {
    color: #b3bfcc;
    text-decoration: none;
  }
  .bp_banner a, .bp_banner strong{
    color: #fff;
  }
  .bp_banner a:hover {
    text-decoration: underline;
  }
</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?= $breadcrumb['separator']; ?><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?= $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
  <div class="success"><?= $success; ?></div>
<?php } ?>
<div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?= $button_save; ?></span></a><a onclick="location = '<?= $cancel; ?>';" class="button"><span><?= $button_cancel; ?></span></a></div>
    </div>
  <div class="content">
    <div id="htabs" class="htabs"><a href="#tab-settings"><?= $text_settings; ?></a><a href="#tab-log"><?= $text_log; ?></a></div>
    <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <div id="tab-settings">
        <div id="vtabs" class="vtabs">
          <a href="#tab-general"><?php if ($error_api_key) { ?><img src="view/image/warning.png"> <?php } ?><?= $text_general; ?></a>
          <a href="#tab-statuses"><?= $text_statuses; ?></a>
          <a href="#tab-advanced"><?php if ($error_notify_url || $error_return_url) { ?><img src="view/image/warning.png"> <?php } ?><?= $text_advanced; ?></a>
        </div>
        <div id="tab-general" class="vtabs-content">
          <table class="form">
            <tr class="bp_banner">
              <td style="text-align: center;"><a href="https://bitpay.com" target="_blank" style="display: inline-block; margin: 0 auto;"><img src="view/image/payment/bitpay_banner.png" style="margin: .5em 0 .3em"/></a></td>
              <td><p><?= $text_bitpay_support; ?></p></td>
            </tr>
            <tr>
              <td><label for="bitpay_api_key"><span class="required">*</span> API Token</label><span class="help" id="api_key_help"><?php if (isset($bitpay_api_server) && $bitpay_api_server == 'live') { echo $help_api_key; }else{ echo $help_api_key_test; } ?></span></td>
              <td><input type="text" name="bitpay_api_key" value="<?php echo $bitpay_api_key; ?>" style="width:300px;" />
            <?php if ($error_api_key) { ?>
            <span class="error"><?php echo $error_api_key; ?></span>
            <?php } ?></td>

            </tr>
            <tr>
              <td><label for="bitpay_api_server"><?php echo $entry_api_server; ?></label><span class="help"><?= $help_api_server; ?></span></td>
              <td>
                <select name="bitpay_api_server" id="bitpay_api_server">
                  <option value="live"<?php if (isset($bitpay_api_server) && $bitpay_api_server == 'live') { ?> selected="selected"<?php } ?>><?= $text_live; ?></option>
                  <option value="test"<?php if (isset($bitpay_api_server) && $bitpay_api_server == 'test') { ?> selected="selected"<?php } ?>><?= $text_test; ?></option>
                </select>
              </td>
            </tr>
            <tr>
              <td><label for="bitpay_risk_speed"><?php echo $entry_risk_speed; ?></label><span class="help"><?= $help_risk_speed; ?></span></td>
              <td>
                <select name="bitpay_risk_speed" id="bitpay_risk_speed">
                  <option value="high"<?php if (isset($bitpay_risk_speed) && $bitpay_risk_speed == 'high') { ?> selected="selected"<?php } ?>><?= $text_high; ?></option>
                  <option value="medium"<?php if (isset($bitpay_risk_speed) && $bitpay_risk_speed == 'medium') { ?> selected="selected"<?php } ?>><?= $text_medium; ?></option>
                  <option value="low"<?php if (isset($bitpay_risk_speed) && $bitpay_risk_speed == 'low') { ?> selected="selected"<?php } ?>><?= $text_low; ?></option>
                </select>
              </td>
            </tr>
            <tr>
              <td><label for="bitpay_geo_zone"><?= $entry_geo_zone ?></label></td>
              <td>
                <select name="bitpay_geo_zone" id="bitpay_geo_zone">
                      <option value="0"<?php if (isset($bitpay_geo_zone) && $bitpay_geo_zone == 0) { ?> selected="selected"<?php } ?>><?= $text_all_geo_zones ?></option>
                  <?php foreach ($geo_zones as $geo_zone): ?>
                      <option value="<?= $geo_zone['geo_zone_id'] ?>"<?php if (isset($bitpay_geo_zone) && $bitpay_geo_zone == $geo_zone['geo_zone_id']) { ?> selected="selected"<?php } ?>><?= $geo_zone['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label for="bitpay_status"><?= $entry_status; ?></label></td>
              <td><select name="bitpay_status" id="bitpay_status">
                  <?php if ($bitpay_status) { ?>
                  <option value="1" selected="selected"><?= $text_enabled; ?></option>
                  <option value="0"><?= $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?= $text_enabled; ?></option>
                  <option value="0" selected="selected"><?= $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label for="bitpay_sort_order"><?= $entry_sort_order; ?></label></td>
              <td><input type="text" name="bitpay_sort_order" id="bitpay_sort_order" value="<?= $bitpay_sort_order; ?>" size="1" /></td>
            </tr>
          </table>
        </div>
        <div id="tab-statuses" class="vtabs-content">
          <table class="form">
            <tr>
              <td><label for="bitpay_paid_status_id"><?= $entry_paid_status; ?></label><span class="help"><?= $help_paid_status; ?></span></td>
              <td><select name="bitpay_paid_status_id" id="bitpay_paid_status_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <option value="<?= $order_status['order_status_id']; ?>"<?php if (isset($bitpay_paid_status_id) && $bitpay_paid_status_id == $order_status['order_status_id']) { ?> selected="selected"<?php } ?>><?= $order_status['name'] ?></option>
                  <?php endforeach; ?>
                </select></td>
            </tr>
            <tr>
              <td><label for="bitpay_confirmed_status_id"><?= $entry_confirmed_status; ?></label><span class="help"><?= $help_confirmed_status; ?></span></td>
              <td><select name="bitpay_confirmed_status_id" id="bitpay_confirmed_status_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <option value="<?= $order_status['order_status_id']; ?>"<?php if (isset($bitpay_confirmed_status_id) && $bitpay_confirmed_status_id == $order_status['order_status_id']) { ?> selected="selected"<?php } ?>><?= $order_status['name'] ?></option>
                  <?php endforeach; ?>
                </select></td>
            </tr>
            <tr>
              <td><label for="bitpay_complete_status_id"><?= $entry_complete_status; ?></label><span class="help"><?= $help_complete_status; ?></span></td>
              <td><select name="bitpay_complete_status_id" id="bitpay_complete_status_id">
                  <?php foreach ($order_statuses as $order_status): ?>
                      <option value="<?= $order_status['order_status_id']; ?>"<?php if (isset($bitpay_complete_status_id) && $bitpay_complete_status_id == $order_status['order_status_id']) { ?> selected="selected"<?php } ?>><?= $order_status['name'] ?></option>
                  <?php endforeach; ?>
                </select></td>
            </tr>
          </table>
        </div>
        <div id="tab-advanced" class="vtabs-content">
          <table class="form">
            <tr>
              <td><label for="bitpay_notify_url"><span class="required">*</span> <?= $entry_notify_url; ?></label><span class="help"><?= $help_notify_url; ?></span></td>
              <td><input type="url" name="bitpay_notify_url" id="bitpay_notify_url" value="<?= $bitpay_notify_url; ?>" style="width:300px;" /><span class="help"><?= "<strong>$entry_default</strong> $notify_default"; ?></span>
                <?php if ($error_notify_url) { ?>
                <span class="error"><?php echo $error_notify_url; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><label for="bitpay_return_url"><span class="required">*</span> <?= $entry_return_url; ?></label><span class="help"><?= $help_return_url; ?></span></td>
              <td><input type="url" name="bitpay_return_url" id="bitpay_return_url" value="<?= $bitpay_return_url; ?>" style="width:300px;" /><span class="help"><?= "<strong>$entry_default</strong> $return_default"; ?></span>
                <?php if ($error_return_url) { ?>
                <span class="error"><?php echo $error_return_url; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><label for="bitpay_debug_mode"><?= $entry_debug_mode; ?></label><span class="help"><?= $help_debug_mode; ?></td>
              <td><select name="bitpay_debug_mode" id="bitpay_debug_mode">
                  <?php if ($bitpay_debug_mode) { ?>
                  <option value="1" selected="selected"><?= $text_enabled; ?></option>
                  <option value="0"><?= $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?= $text_enabled; ?></option>
                  <option value="0" selected="selected"><?= $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div id="tab-log">
        <table class="form">
          <tr>
            <td><textarea id="bitpay_log" wrap="off" style="width: 98%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;" readonly><?= $log ?></textarea></td>
          </tr>
          <tr>
            <td style="text-align: right;"><a href="<?= $clear; ?>" class="button" id="clear_log"><?php echo $button_clear ?></a></td>
          </tr>
        </table>
      </div>

    </form>
  </div>
</div>
</div>
<script type="text/javascript"><!--
$('#htabs a').tabs();
$('#vtabs a').tabs();
$('#bitpay_api_server').on("change", function changeApiHelpText(e){
  if ($('#bitpay_api_server').val() === "live") {
    $('#api_key_help').html('<?= $help_api_key; ?>');
  }else{
    $('#api_key_help').html('<?= $help_api_key_test; ?>');
  }
});
function getClear(){
  $.get($('#clear_log').attr("href"), function clearLog(){
    $("#bitpay_log").html('');
  });
}
$('#clear_log').on('click', function (e) {
  e.preventDefault();
  getClear();
});
$('#clear_log').on('keypress', function (e) {
  e.preventDefault();
  if(e.keyCode === 13) { getClear() };
});

(function(){

  var changes = false;
  $('input, select').on("change", function(){
    changes = true;
  });

  $(window).bind('beforeunload', function(){
    if(changes) {
      return '<?= $text_changes; ?>';
    }

  });

  $('#form').on('submit', function (e) {
    changes = false;
  });

})();
//--></script>
<?php echo $footer; ?>
