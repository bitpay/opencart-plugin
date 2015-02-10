<?php if ($testnet) { ?>
  <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <?php echo $warning_testnet; ?></div>
<?php } ?>
<div class="buttons">
  <div class="pull-right">
    <a class="btn btn-primary" href="<?php echo $url_redirect ?>"><?php echo $button_confirm ?></a>
  </div>
</div>