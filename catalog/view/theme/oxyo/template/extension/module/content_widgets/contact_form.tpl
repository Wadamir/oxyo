<div class="contact_form">
<form class="form-horizontal" id="mail_form<?php echo $module; ?>">
<div class="form-group">
  <div class="col-sm-12">
  <label><?php echo $oxyo_text_name; ?></label>
    <input type="text" name="name" value="" class="form-control" />
  </div>
</div>
<div class="form-group required">
  <div class="col-sm-12">
  <label><?php echo $oxyo_text_email; ?></label>
    <input type="text" name="email" value="" class="form-control" />
  </div>
</div>
<div class="form-group required">
  <div class="col-sm-12">
  <label><?php echo $oxyo_text_message; ?></label>
    <textarea name="text" rows="4" class="form-control"></textarea>
  </div>
</div>
  <div class="form-group required">
  <div class="col-sm-12">
  <label><?php echo $oxyo_text_captcha; ?></label>
    <div class="input-group">
    <span class="input-group-addon captcha_addon"><img src="index.php?route=extension/oxyo/oxyo_features/oxyo_captcha" alt="" class="captchaimg" /></span>
    <input type="text" name="captcha" value="" class="form-control" />
    </div>
  </div>
</div>
<div class="form-group">
<div class="col-sm-12">
  <button type="button" class="btn btn-contrast" onclick="contact_form_send(<?php echo $module; ?>);"><?php echo $oxyo_text_submit; ?></button>
  </div>
</div>
<div class="respond"></div>
</form>
</div>