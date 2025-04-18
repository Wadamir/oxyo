<legend>Contact Page</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Map layout</label>
    <div class="col-sm-10">
    <select name="settings[oxyo][oxyo_map_style]" class="form-control">
    <option value="0"<?php if($oxyo_map_style == '0'){echo ' selected="selected"';} ?>>Disabled</option>
    <option value="full_width"<?php if($oxyo_map_style == 'full_width'){echo ' selected="selected"';} ?>>Full width</option>
    <option value="inline"<?php if($oxyo_map_style == 'inline'){echo ' selected="selected"';} ?>>Boxed</option>
	</select>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Location Latitude</label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[oxyo][oxyo_map_lat]" value="<?php echo isset($oxyo_map_lat) ? $oxyo_map_lat : ''; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Location Longitude</label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[oxyo][oxyo_map_lon]" value="<?php echo isset($oxyo_map_lon) ? $oxyo_map_lon : ''; ?>" />
    </div>                   
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">Map API key</label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[oxyo][oxyo_map_api]" value="<?php echo isset($oxyo_map_api) ? $oxyo_map_api : ''; ?>" />
    </div>                   
</div>

<div class="col-sm-offset-2">
<div class="bs-callout bs-callout-info bs-callout-sm">
<h4>How to find your Latitude & Longitude</h4>
<p>1. Navigate to your location at <a href="http://maps.google.com/" target="_blank">http://maps.google.com/</a></p>
<p>2. Right click on your location, and select What's Here?</p>
<p style="margin-bottom:20px;">3. You will see a box at the bottom of the map with your address along with it's latitude/longitudeb</p>

<h4>How to get the Map API key?</h4>
<p>Please follow the instructions here <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com/maps/documentation/javascript/get-api-key</a></p>
</div>
</div>