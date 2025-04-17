<legend>Status</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Main setting to Enable/Disable Oxyo theme.">Theme Status</span></label>
    <div class="col-sm-10 toggle-btn">
        <label><input type="radio" name="settings[theme_default][theme_default_directory]" value="default" <?php if ($theme_default_directory == 'default') {
                                                                                                                echo ' checked="checked"';
                                                                                                            } ?> /><span>Disabled</span></label>
        <label><input type="radio" name="settings[theme_default][theme_default_directory]" value="oxyo" <?php if ($theme_default_directory == 'oxyo') {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?> /><span>Enabled</span></label>
    </div>
    <input type="hidden" name="settings[config][config_theme]" value="theme_default" />
    <input type="hidden" name="settings[oxyo_version][oxyo_theme_version]" value="1.2.8.0" />
</div>