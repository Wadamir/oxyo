<legend>Custom CSS</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Custom CSS Status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[oxyo][oxyo_custom_css_status]" class="custom-css-select" value="0" <?php if($oxyo_custom_css_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[oxyo][oxyo_custom_css_status]" class="custom-css-select" value="1" <?php if($oxyo_custom_css_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div id="custom_css_holder"<?php if($oxyo_custom_css_status){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>
<div class="form-group">
    <label class="col-sm-2 control-label">CSS</label>
    <div class="col-sm-10">
    <textarea name="settings[oxyo][oxyo_custom_css]" class="form-control code"><?php echo isset($oxyo_custom_css) ? $oxyo_custom_css : ''; ?></textarea>
    </div>                   
</div>
</div>
