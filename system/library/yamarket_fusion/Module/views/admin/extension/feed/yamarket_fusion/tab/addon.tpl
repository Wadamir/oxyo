<?php foreach ($error as $error_text): ?>
<div class="alert alert-danger alert-dismissible">
	<i class="fa fa-exclamation-circle"></i>
	<?=$error_text;?>
	<button type="button" class="close" data-dismiss="alert">
	<span class="pe-7s-close"></span></button>
</div>
<?php endforeach; ?>
<div class="container">
    <div class="content">
		<div id="aside-left">
			<ul class="menu">
				<li><a href="#menu_api_yandex"><span class="mini-number">1</span><?=$menu_api_yandex;?></a></li>
				<li><a href="#menu_timezone"><span class="mini-number">2</span><?=$menu_timezone;?></a></li>
				<li><a href="#menu_groups"><span class="mini-number">3</span><?=$menu_groups;?></a></li>
				<li><a href="#menu_kg_calcu"><span class="mini-number">4</span><?=$menu_kg_calcu;?></a></li>
				<li><a href="#menu_language"><span class="mini-number">5</span><?=$menu_language;?></a></li>
				<li><a href="#menu_add2cart"><span class="mini-number">6</span><?=$menu_add2cart;?></a></li>
				<li><a href="#menu_utm"><span class="mini-number">7</span><?=$menu_utm;?></a></li>
				<li><a href="#menu_integration"><span class="mini-number">8</span><?=$menu_integration;?></a></li>
			</ul>
		</div>
		<div id="aside-right">
		<form action="<?php echo $action; ?>" method="POST" id="form-addon" class="form-horizontal">
			<div class="card" id="menu_api_yandex">
				<div class="heading-card"><div class="number">1</div><h2><?=$menu_api_yandex;?></h2></div>
					<div class="body-card">
						<!-- Start content -->
						<div class="well well-sm">
							<a data-toggle="collapse" href="#yandex-api-init">Работа с API yandex</a>
							<span class="caret"></span>
							<div id="yandex-api-init" class="collapse">
								Для работы Api Yandex нужно:
								<br> 1. Зарегистрировать
								<a href="https://oauth.yandex.ru/client/new" target="_blank">приложение</a>. В настройках приложения
								<b>Callback URI</b> указать адрес
								<code><?=$url_api_callback;?></code> и разрешить Доступ к Яндекс.Маркет. Полученные
								<b>ID</b> и <b>Пароль</b> укажите в настройках ниже.
								<br> 2. Получите токен по
								<a href="<?=$url_api_token;?>" target="_blank" data-url-window>ссылке</a>.
								<?php if ($has_market_api_token): ?>
								<span class="text-success">(токен уже получен, осталось ~<?=$market_api_token_expired;?> дней)</span>
								<a href="<?=$url_refresh_token;?>" target="_blank" data-url-window>Обновить токен</a>
								<?php endif; ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-app-client-id"><?=$entry_app_client_id;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<input type="text" name="setting[<?=$profile_id;?>][market_api_client]" value="<?=$market_api_client;?>" id="input-app-client-id" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-app-client-secret"><?=$entry_app_secret;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<input type="text" name="setting[<?=$profile_id;?>][market_api_secret]" value="<?=$market_api_secret;?>" id="input-app-client-secret" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-shop-id"><?=$entry_market_compaing_id;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<input type="text" name="setting[<?=$profile_id;?>][market_shop_id]" value="<?=$market_shop_id;?>" id="input-market-shop-id" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-shop-feed-id"><?=$entry_market_feed_id?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<input type="text" name="setting[<?=$profile_id;?>][market_shop_feed_id]" value="<?=$market_shop_feed_id;?>" id="input-market-shop-feed-id" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-feed-id"><?=$entry_market_price_autoapdate?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-3">
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate]" value="1" <?php echo ($products_price_autoupdate ? 'checked' : ''); ?>>
									<?=$text_yes;?>
								</label>
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate]" value="0" <?php echo (!$products_price_autoupdate ?'checked' : '');?>>
									<?=$text_no;?>
								</label>
							</div>
							<label class="col-sm-4 control-label" for="input-market-feed-id">
								<div><?=$entry_market_price_autoapdate_cron; ?> <i class="icon-setting-profiles"></i></div>
								<br>
								<div><span data-toggle="tooltip" title="<?=$help_market_price_autoapdate_cron_type_direct;?>"><?=$entry_market_price_autoapdate_cron_type_direct; ?></span> <i class="icon-setting-profiles"></i></div>
							</label>
							<?php if ($module_user->hasPermission('api_cron')) { ?>
							<div class="col-sm-3">
								<div>
									<label class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate_cron]" value="1" <?php echo ($products_price_autoupdate_cron ? 'checked' : ''); ?>>
										<?=$text_yes;?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate_cron]" value="0" <?php echo (!$products_price_autoupdate_cron ?'checked' : '');?>>
										<?=$text_no;?>
									</label>
								</div>
								<div>
									<label class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate_cron_type_direct]" value="1" <?php echo ($products_price_autoupdate_cron_type_direct ? 'checked' : ''); ?>>
										<?=$text_yes;?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][products_price_autoupdate_cron_type_direct]" value="0" <?php echo (!$products_price_autoupdate_cron_type_direct ?'checked' : '');?>>
										<?=$text_no;?>
									</label>
								</div>
							</div>
							<?php } ?>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_timezone">
				<div class="heading-card"><div class="number">2</div><h2><?=$menu_timezone;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-compaing"><?=$entry_timezone;?></label>
							<div class="col-sm-9">
								<select class="form-control" name="setting[0][timezone]">
									<option value=""><?=$text_none;?></option>
									<?php foreach ($timezones as $timezone): ?>
									<option value="<?=$timezone;?>" <?php echo ($timezone == $market_timezone ? 'selected' : '');?>>
										<?=$timezone;?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_groups">
				<div class="heading-card"><div class="number">3</div><h2><?=$menu_groups;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-compaing"><?=$entry_customer_group_id;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<select class="form-control" name="setting[<?=$profile_id;?>][market_customer_group_id]">
									<option value=""><?=$text_default;?></option>
									<?php foreach ($customer_groups as $customer_group) { ?>
									<option value="<?=$customer_group['customer_group_id'];?>" <?=$market_customer_group_id == $customer_group['customer_group_id'] ? 'selected' : '';?>><?=$customer_group['name'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_kg_calcu">
				<div class="heading-card"><div class="number">4</div><h2><?=$menu_kg_calcu;?></h2></div>
					<div class="body-card">
						<!-- Start content -->
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-compaing"><?=$entry_weight_class;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<select class="form-control" name="setting[<?=$profile_id;?>][market_weight_class_id]">
									<option value=""><?=$text_none;?></option>
									<?php foreach ($weight_classes as $weight_class) { ?>
									<option value="<?=$weight_class['weight_class_id'];?>" <?=$market_weight_class_id == $weight_class['weight_class_id'] ? 'selected' : '';?>><?=$weight_class['title'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_language">
				<div class="heading-card"><div class="number">3</div><h2><?=$menu_language;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<div class="form-group">
							<label class="col-sm-3 control-label" for="input-market-compaing"><?=$entry_languages;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-9">
								<select class="form-control" name="setting[<?=$profile_id;?>][market_language_id]">
									<option value=""><?=$text_none;?></option>
									<?php foreach ($languages as $language) { ?>
									<option value="<?=$language['language_id'];?>" <?=$market_language_id == $language['language_id'] ? 'selected' : '';?>><?=$language['name'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_add2cart">
				<div class="heading-card"><div class="number">6</div><h2><?=$menu_add2cart;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?=$entry_product_url_add_to_cart;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-3">
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][market_product_url_add_to_cart]" value="1" <?php echo ($market_product_url_add_to_cart ? 'checked' : ''); ?>>
									<?=$text_yes;?>
								</label>
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][market_product_url_add_to_cart]" value="0" <?php echo (!$market_product_url_add_to_cart ? 'checked' : '');?>>
									<?=$text_no;?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?=$entry_product_watermark;?> <i class="icon-setting-profiles"></i></label>
							<div class="col-sm-4">
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][market_product_watermark]" value="1" <?php echo ($market_product_watermark ? 'checked' : ''); ?>>
									<?=$text_yes;?>
								</label>
								<label class="radio-inline">
									<input type="radio" name="setting[<?=$profile_id;?>][market_product_watermark]" value="0" <?php echo (!$market_product_watermark ? 'checked' : '');?>>
									<?=$text_no;?>
								</label>
								<br><br>
								<select class="form-control" name="setting[<?=$profile_id;?>][market_product_watermark_position]">
									<?php foreach ($product_watermark_positions as $watermark_position => $w_position_name) { ?>
									<option value="<?= $watermark_position ?>" <?= $market_product_watermark_position == $watermark_position ? 'selected' : '' ?>><?= $w_position_name ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm-4">
								<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?= $product_watermark_thumb ?>" data-placeholder="<?= $placeholder ?>" /></a>
								<input type="hidden" name="setting[<?=$profile_id;?>][market_product_watermark_image]" value="<?= $market_product_watermark_image ?>" id="input-image" >
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_utm">
				<div class="heading-card"><div class="number">7</div><h2><?=$menu_utm;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<div class="form-group">
							<div class="col-sm-12">
								<div class="form-inline market-utm-inputs">
									<legend><?=$text_yandex_utm;?> <i class="icon-setting-profiles"></i></legend>
									<label><?=$text_status;?>:</label>
									<div class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][market_yandex_utm_status]" value="1" <?= ($market_yandex_utm_status == 1 ? 'checked' : '');?>>
										<?=$text_enabled;?>
									</div>
									<div class="radio-inline">
										<input type="radio" name="setting[<?=$profile_id;?>][market_yandex_utm_status]" value="0" <?= ($market_yandex_utm_status == 0 ? 'checked' : '');?>>
										<?=$text_disabled;?>
									</div>
									&nbsp;
									<div class="input-group input-group-sm">
										<div class="input-group-addon"><?=$entry_market_yandex_utm_source;?></div>
										<input type="text" class="form-control" name="setting[<?=$profile_id;?>][market_yandex_utm_source]" value="<?=$market_yandex_utm_source;?>">
									</div>
									&nbsp;
									<div class="input-group input-group-sm">
										<div class="input-group-addon"><?=$entry_market_yandex_utm_medium;?></div>
										<input type="text" class="form-control" name="setting[<?=$profile_id;?>][market_yandex_utm_medium]" value="<?=$market_yandex_utm_medium;?>">
									</div>
								</div>
							</div>
						</div>
					<!-- end content -->	
				</div>
			</div>
			<div class="card" id="menu_integration">
				<div class="heading-card"><div class="number">8</div><h2><?=$menu_integration;?></h2></div>
				<div class="body-card">
					<!-- start content -->
						<legend><?=$text_module_integration;?></legend>
						<div class="form-group">
							<div class="col-sm-12">
								<div class="checkbox-inline">
									<label class="text-normal cur-pointer">
										<input type="checkbox" data-addon-name="productmanager" <?=($addon_productmanager_status == 1 ? 'checked' : '');?>>
										<b>Управление торговлей (не 1с)</b>
										<i class="fa fa-spinner fa-spin" style="display:none;"></i>
									</label>
								</div>
								<?php if ($module_user->hasPermission('super_options')) { ?>
								<div class="checkbox-inline">
									<label class="text-normal cur-pointer">
										<input type="checkbox" data-addon-name="related_options" <?=($addon_related_options_status == 1 ? 'checked' : '');?>>
										<b>Связанные опции</b>
										<i class="fa fa-spinner fa-spin" style="display:none;"></i>
									</label>
								</div>
								<?php } ?>
								<?php if ($module_user->hasPermission('options_image')) { ?>
								<div class="checkbox-inline">
									<label class="text-normal cur-pointer">
										<input type="checkbox" data-addon-name="option_image_change" <?=($addon_option_image_change_status == 1 ? 'checked' : '');?>>
										<b>Option Image Change</b>
										<i class="fa fa-spinner fa-spin" style="display:none;"></i>
									</label>
								</div>
								<br>
								<?php } ?>
								<?php if ($module_user->hasPermission('valute')) { ?>
								<div class="addon-with-setting">
									<div class="checkbox-inline">
										<label class="text-normal cur-pointer">
											<input type="checkbox" data-addon-name="currency_plus" <?=($addon_currency_plus_status == 1 ? 'checked' : '');?>>
											<b>Валюта плюс</b>
											<i class="fa fa-spinner fa-spin" style="display:none;"></i>
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_currency_plus][base_price]" value="1" <?=(isset($addon_currency_plus['base_price']) ? 'checked' : '');?>>
											Базовая цена товара
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_currency_plus][base_currency_code]" value="1" <?=(isset($addon_currency_plus['base_currency_code']) ? 'checked' : '');?>>
											Базовая валюта товара
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_currency_plus][special_base]" value="1" <?=(isset($addon_currency_plus['special_base']) ? 'checked' : '');?>>
											Базовая цена акции
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_currency_plus][option_base_price]" value="1" <?=(isset($addon_currency_plus['option_base_price']) ? 'checked' : '');?>>
											Базовая цена опции
										</label>
									</div>
									<div class="checkbox">
										└─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_currency_plus][discount_base]" value="1" <?=(isset($addon_currency_plus['discount_base']) ? 'checked' : '');?>>
											Базовая цена скидки
										</label>
									</div>
								</div>
								<?php } ?>
								<?php if ($module_user->hasPermission('group_price')) { ?>
								<div class="addon-with-setting">
									<div class="checkbox-inline">
										<label class="text-normal cur-pointer">
											<input type="checkbox" data-addon-name="group_price" <?=($addon_group_price_status == 1 ? 'checked' : '');?>>
											<b>Group Price</b>
											<i class="fa fa-spinner fa-spin" style="display:none;"></i>
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_group_price][markup_base_price]" value="1" <?=(isset($addon_group_price['markup_base_price']) ? 'checked' : '');?>>
											Учитывать основную настройку цены
										</label>
									</div>
									<div class="checkbox">
										├─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_group_price][markup_category]" value="1" <?=(isset($addon_group_price['markup_category']) ? 'checked' : '');?>>
											Учитывать настройку цены категории
											<span class="help-icon" data-toggle="tooltip" title="Только категория к которой принадлежит товар"></span>
										</label>
									</div>
									<div class="checkbox">
										└─ <label class="text-normal cur-pointer">
											<input type="checkbox" name="setting[0][addon_group_price][markup_manufacturer]" value="1" <?=(isset($addon_group_price['markup_manufacturer']) ? 'checked' : '');?>>
											Учитывать настройку цены производителя
										</label>
									</div>
								</div>
								<?php } ?>
								<div class="checkbox-inline">
									<label class="text-normal cur-pointer">
										<input type="checkbox" data-addon-name="podarki" <?=($addon_podarki_status == 1 ? 'checked' : '');?>>
										<b>Подарки</b>
										<i class="fa fa-spinner fa-spin" style="display:none;"></i>
									</label>
								</div>
								<div class="checkbox-inline">
									<label class="text-normal cur-pointer">
										<input type="checkbox" data-addon-name="multistore" <?=($addon_multistore_status == 1 ? 'checked' : '');?>>
										<b>МультиСклад</b>
										<i class="fa fa-spinner fa-spin" style="display:none;"></i>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> <?=$button_save;?></button>
							</div>
						</div>
						<!-- end content -->	
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
jQuery(function() {
	$('[data-addon-name]').change(function () {
		var $loader = $(this).parent().find('.fa.fa-spinner');
		$loader.show();
		$.get('<?=$url_toggle_addons;?>&addon_name=' + $(this).attr('data-addon-name') + '&addon_status=' + (+this.checked), function () {
			$loader.hide();
			$loader.after('<i class="fa fa-check"></i>');
			setTimeout(function () {
				$loader.parent().find('.fa-check').remove();
			}, 3000);
		});
	});
});
</script>
