<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-bitpay-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $url_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid" id="bitpay-page">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $url_action; ?>" method="post" enctype="multipart/form-data" id="form-bitpay-account" class="form-horizontal">
          <input type="hidden" name="action" value="save">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
            <li><a href="#tab-log" data-toggle="tab"><?php echo $tab_log ?></a></li>
            <li><a href="#tab-support" id="tab-support-tab" data-toggle="tab"><?php echo $tab_support ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-settings">
              <h3 class="col-sm-10 col-sm-offset-2"><?php echo $text_general; ?></h3>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-api-token"><span data-toggle="tooltip" title="<?php echo $help_api_access; ?>"><?php echo $entry_api_access; ?></span></label>
                <div class="col-sm-10">  
                  <div id="api-disconnected" class="btn-group <?php if ($bitpay_connection === 'connected') { echo ' hidden'; } ?>">
                    <a id="connect_to_bitpay" href="<?= $url_connect_livenet; ?>" class="btn btn-primary pair_url"><?php echo $text_connect_to_bitpay; ?></a>
                    <button id="connect_to_bitpay_dropdown" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?= $url_connect_livenet; ?>" class="pair_url"><?php echo $text_livenet; ?></a></li>
                      <li><a href="<?= $url_connect_testnet; ?>" class="pair_url"><?php echo $text_testnet; ?></a></li>
                    </ul>
                  </div>  
                  <div id="api-connected" class="input-group<?php if ($bitpay_connection !== 'connected') { echo ' hidden'; } ?>">
                    <p class="form-control-static text-primary"><i class="fa fa-plug"></i> <?php echo $text_connected; ?></p>
                    <span class="input-group-btn">
                        <a style="border-radius:3px;" href="<?php echo $url_disconnect; ?>" class="btn btn-danger"><i class="fa fa-unlink"></i> <?php echo $button_disconnect; ?></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-risk-speed"><span data-toggle="tooltip" title="<?php echo $help_risk_speed; ?>"><?php echo $entry_risk_speed; ?></span></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-bolt fa-fw"></i></span>
                    <select name="bitpay_risk_speed" id="input-risk-speed" class="form-control">
                      <option value="high"<?php if ($bitpay_risk_speed == 'high') { echo ' selected="selected"'; } ?>><?= $text_high; ?></option>
                      <option value="medium"<?php if ($bitpay_risk_speed == 'medium') { echo ' selected="selected"'; } ?>><?= $text_medium; ?></option>
                      <option value="low"<?php if ($bitpay_risk_speed == 'low') { echo ' selected="selected"'; } ?>><?= $text_low; ?></option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_send_buyer_info; ?>"><?php echo $entry_send_buyer_info; ?></span></label>
                <div class="col-sm-10">
                  <label class="radio-inline">            
                    <input type="radio" name="bitpay_send_buyer_info" value="1" <?php if ($bitpay_send_buyer_info) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_yes; ?>
                  </label>
                  <label class="radio-inline">            
                    <input type="radio" name="bitpay_send_buyer_info" value="0" <?php if (!$bitpay_send_buyer_info) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_no; ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
                    <select name="bitpay_geo_zone_id" id="input-geo-zone" class="form-control">
                      <option value="0"><?php echo $text_all_zones; ?></option>
                      <?php foreach ($geo_zones as $geo_zone) { ?>
                      <?php if ($geo_zone['geo_zone_id'] == $bitpay_geo_zone_id) { ?>
                      <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="bitpay_status" id="input-status" class="form-control">
                    <?php if ($bitpay_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                  <?php if ($error_status) { ?>
                  <div class="text-danger"><?php echo $error_status; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="bitpay_sort_order" value="<?php echo $bitpay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <br>
              <h3 class="col-sm-10 col-sm-offset-2"><?php echo $text_statuses; ?></h3>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_paid_status; ?>"><?php echo $entry_paid_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bitpay_paid_status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bitpay_paid_status) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_confirmed_status; ?>"><?php echo $entry_confirmed_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bitpay_confirmed_status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bitpay_confirmed_status) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_complete_status; ?>"><?php echo $entry_complete_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="bitpay_complete_status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $bitpay_complete_status) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <br>
              <h3 class="col-sm-10 col-sm-offset-2<?php if ($error_notify_url || $error_return_url) { ?> text-danger<?php } ?>"><?php echo $text_advanced; ?></h3>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-notify-url"><span data-toggle="tooltip" title="<?php echo $help_notify_url; ?>"><?php echo $entry_notify_url; ?></span></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                    <input type="url" name="bitpay_notify_url" id="input-notify-url" value="<?php echo $bitpay_notify_url; ?>" placeholder="<?php echo $default_notify_url; ?>" class="form-control" />
                  </div>
                  <?php if ($error_notify_url) { ?>
                  <div class="text-danger"><?php echo $error_notify_url; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-return-url"><span data-toggle="tooltip" title="<?php echo $help_return_url; ?>"><?php echo $entry_return_url; ?></span></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                    <input type="url" name="bitpay_return_url" id="input-return-url" value="<?php echo $bitpay_return_url; ?>" placeholder="<?php echo $default_return_url; ?>" class="form-control" />
                  </div>
                  <?php if ($error_return_url) { ?>
                  <div class="text-danger"><?php echo $error_return_url; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                <div class="col-sm-10">
                  <select name="bitpay_debug" id="input-debug" class="form-control">
                    <?php if ($bitpay_debug) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-log">
              <p>
                <pre id="bitpay_logs" class="form-control"><?php echo $log; ?></pre>
              </p>
              <div class="text-right"><a href="<?php echo $url_clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $button_clear; ?></a></div>
            </div>
            <div class="tab-pane" id="tab-support">
              <div class="col-sm-10 col-sm-offset-2">
                <h3><i class="fa fa-comments"></i> <?php echo $text_forum; ?></h3>
                <p>
                  <?php echo $text_bitpay_labs; ?>
                </p>
                <br>
              </div>
              <div class="col-sm-10 col-sm-offset-2">
                <h3><i class="fa fa-support"></i> <?php echo $text_send_request; ?></h3>
                <p>
                  <?php echo $text_support; ?>
                </p>
                <br>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                    <input type="text" name="request_name" id="input-name" value="<?php echo $request_name; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
                  </div>
                  <?php if ($error_request_name) { ?>
                  <div class="text-danger"><?php echo $error_request_name; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-email-address"><?php echo $entry_email_address; ?></label>
                <div class="col-sm-10">
                  <div class="input-group"> <span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
                    <input type="email" name="request_email_address" id="input-email-address" value="<?php echo $request_email_address; ?>" placeholder="<?php echo $entry_email_address; ?>" class="form-control" autocorrect="off" autocapitalize="off" spellcheck="false" />
                  </div>
                  <?php if ($error_request_email_address) { ?>
                  <div class="text-danger"><?php echo $error_request_email_address; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_send_logs; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <input type="radio" name="request_send_logs" value="1" <?php if ($request_send_logs) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_yes; ?>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="request_send_logs" value="0" <?php if (!$request_send_logs) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_no; ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_send_server_info; ?>"><?php echo $entry_send_server_info; ?></span></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <input type="radio" name="request_send_server_info" value="1" <?php if ($request_send_server_info) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_yes; ?>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="request_send_server_info" value="0" <?php if (!$request_send_server_info) { ?>checked="checked" <?php } ?>/>
                    <?php echo $text_no; ?>
                  </label>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="request_subject" id="input-subject" value="<?php echo $request_subject; ?>" placeholder="<?php echo $entry_subject; ?>" class="form-control" />
                  <?php if ($error_request_subject) { ?>
                  <div class="text-danger"><?php echo $error_request_subject; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
                <div class="col-sm-10">
                  <textarea name="request_description" id="input-description" class="form-control" rows="5"><?php echo $request_description; ?></textarea>
                  <?php if ($error_request_description) { ?>
                  <div class="text-danger"><?php echo $error_request_description; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="text-right"><button class="btn btn-primary" name="action" value="send"><i class="fa fa-paper-plane"></i> <?php echo $button_send; ?></button></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<style>
  .btn-bitpay {
    color: #fff;
    background-color: #002855;
    border-color: #000F3C;
  }

  .btn-bitpay:hover, .btn-bitpay:focus, .btn-bitpay:active {
    color: #fff;
    background-color: #000F3C;
    border-color: #000023;
  }

  #bitpay_logs {
    overflow: scroll; 
    white-space: nowrap;
    height: 15em;
  }

  .bp-log-date {
    font-size: 12px;
  }
  .bp-log-level {
    font-weight: bold;
  }
  .bp-log-locale {
    font-weight: bold;
  }
  .bp-log-locale > span {
    color: #888;
    font-weight: normal;
    font-style: italic;
  }
  .bp-log-locale > span > span {
    color: #c55;
  }
  .bp-log-error > .bp-log-level > span {
    color: #a94442;
  }
  .bp-log-warn > .bp-log-level > span {
    color: #aa6708;
  }
  .bp-log-info > .bp-log-level > span {
    color: #31708f;
  }
  .bp-log-trace > .bp-log-level > span {
    color: #777;
  }

  #bitpay_disconnect {
    border-radius: 3px;
  }


</style>
<script type="text/javascript"><!--
  (function(){
    $('.pair_url').on('click', function(e) {
      $('#connect_to_bitpay').addClass('disabled');
      $('#connect_to_bitpay_dropdown').addClass('disabled');
    });

    <?php if ($bitpay_connection === 'connected') { ?>
  
    <?php } ?>

    <?php if ($error_request) { ?>
    $('#tab-support-tab').tab('show');
    <?php } ?>
  }())
//--></script>
<?php echo $footer; ?> 