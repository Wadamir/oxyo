<?php foreach ($error as $error_text): ?>
    <div class="alert alert-danger alert-dismissible">
        <i class="fa fa-exclamation-circle"></i>
        <?= $error_text; ?>
        <button type="button" class="close" data-dismiss="alert">
            <span class="pe-7s-close"></span></button>
    </div>
<?php endforeach; ?>
<div class="container">
    <div class="content">
        <div id="aside-left">
            <ul class="menu">
                <li><a href="#menu_main"><span class="mini-number">1</span><?= $menu_main; ?></a></li>
                <li><a href="#menu_settings"><span class="mini-number">2</span><?= $menu_settings; ?></a></li>
                <li><a href="#menu_conditions"><span class="mini-number">3</span><?= $menu_conditions; ?></a></li>
                <li><a href="#menu_delivery"><span class="mini-number">4</span><?= $menu_delivery; ?></a></li>
                <li><a href="#menu_options_main"><span class="mini-number">5</span><?= $menu_options_main; ?></a></li>
                <li><a href="#menu_custom_tags"><span class="mini-number">6</span><?= $menu_custom_tags; ?></a></li>
                <li><a href="#menu_category_filter"><span class="mini-number">7</span><?= $menu_category_filter; ?></a></li>
                <li><a href="#menu_markup"><span class="mini-number">8</span><?= $menu_markup; ?></a></li>
                <li><a href="#menu_analytics"><span class="mini-number">9</span><?= $menu_analytics; ?></a></li>
                <li><a href="#menu_content"><span class="mini-number">10</span><?= $menu_content; ?></a></li>
                <li><a href="#menu_urls"><span class="mini-number">11</span><?= $menu_urls; ?></a></li>
            </ul>
        </div>
        <div id="aside-right">
            <form action="<?php echo $action; ?>" method="POST" id="form-market" class="form-horizontal">
                <div class="card" id="menu_main">
                    <div class="heading-card">
                        <div class="number">1</div>
                        <h2><?= $menu_main; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="well well-sm">
                            <a data-toggle="collapse" href="#catalog-type-init">Информация о каталоге:</a>
                            <span class="caret"></span>
                            <div id="catalog-type-init" class="collapse">
                                Информация о выбранном каталоге загружается...
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" id="input-market-type">Тип каталога</label>
                            <div class="col-sm-8">
                                <select class="form-control select_search_category" name="setting[<?= $profile_id; ?>][market_type]" id="market_type">
                                    <option value="Yandex" <?= ($market_type == 'Yandex' ? 'selected="selected"' : ''); ?>>Yandex YML</option>
                                    <option value="YandexADV" <?= ($market_type == 'YandexADV' ? 'selected="selected"' : ''); ?>>Яндекс ADV</option>
                                    <option value="YandexDBS" <?= ($market_type == 'YandexDBS' ? 'selected="selected"' : ''); ?>>Яндекс DBS</option>
                                    <option value="YandexFBS" <?= ($market_type == 'YandexFBS' ? 'selected="selected"' : ''); ?>>Яндекс FBS</option>
                                    <option value="YandexFBY" <?= ($market_type == 'YandexFBY' ? 'selected="selected"' : ''); ?>>Яндекс FBY</option>
                                    <option value="YandexSearch" <?= ($market_type == 'YandexSearch' ? 'selected="selected"' : ''); ?>>Яндекс Поиск</option>
                                    <option value="YandexLite" <?= ($market_type == 'YandexLite' ? 'selected="selected"' : ''); ?>>Yandex YML lite</option>
                                    <option value="YandexTurbo" <?= ($market_type == 'YandexTurbo' ? 'selected="selected"' : ''); ?>>Яндекс Турбо</option>
                                    <option value="Ozon" <?= ($market_type == 'Ozon' ? 'selected="selected"' : ''); ?>>Ozon YML update</option>
                                    <option value="Mailru" <?= ($market_type == 'Mailru' ? 'selected' : ''); ?>>Target.my.com(Mail.ru)</option>
                                    <option value="Ekatalog" <?= ($market_type == 'Ekatalog' ? 'selected' : ''); ?>>Ekatalog.ru/ua</option>
                                    <option value="Google" <?= ($market_type == 'Google' ? 'selected' : ''); ?>>Google Merchant</option>
                                    <option value="Priceru" <?= ($market_type == 'Priceru' ? 'selected' : ''); ?>>Price.ru</option>
                                    <option value="Farpost" <?= ($market_type == 'Farpost' ? 'selected="selected"' : ''); ?>>Farpost</option>
                                    <option value="Avito" <?= ($market_type == 'Avito' ? 'selected' : ''); ?>>Avito</option>
                                    <option value="Facebook" <?= ($market_type == 'Facebook' ? 'selected' : ''); ?>>Facebook rss</option>
                                    <option value="HotlineXML" <?= ($market_type == 'HotlineXML' ? 'selected' : ''); ?>>Формат Hotline</option>
                                    <option value="HotlineYML" <?= ($market_type == 'HotlineYML' ? 'selected' : ''); ?>>Hotline YML</option>
                                    <option value="Rozetka" <?= ($market_type == 'Rozetka' ? 'selected' : ''); ?>>Rozetka.ua</option>
                                    <option value="Promua" <?= ($market_type == 'Promua' ? 'selected="selected"' : ''); ?>>Prom.ua</option>
                                    <option value="CDEK" <?= ($market_type == 'CDEK' ? 'selected' : ''); ?>>СДЕК маркет</option>
                                    <option value="Goods" <?= ($market_type == 'Goods' ? 'selected="selected"' : ''); ?>>Goods маркетплейс</option>
                                    <option value="Beru" <?= ($market_type == 'Beru' ? 'selected="selected"' : ''); ?>>Маркетплейс Beru</option>
                                    <option value="Mobilluckcomua" <?= ($market_type == 'Mobilluckcomua' ? 'selected="selected"' : ''); ?>>Mobilluck.com.ua</option>
                                    <option value="Kaspikz" <?= ($market_type == 'Kaspikz' ? 'selected="selected"' : ''); ?>>Kaspi.kz</option>
                                    <option value="Yola" <?= ($market_type == 'Yola' ? 'selected="selected"' : ''); ?>>Юла - доска объявлений</option>
                                    <option value="Allo" <?= ($market_type == 'Allo' ? 'selected="selected"' : ''); ?>>Allo.ua</option>
                                    <option value="Sitemap" <?= ($market_type == 'Sitemap' ? 'selected="selected"' : ''); ?>>Sitemap товаров</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="input-market-shopname"><?= $entry_market_shopname; ?></label>
                            <div class="col-sm-8">
                                <input type="text" name="setting[<?= $profile_id; ?>][market_shopname]" value="<?= $market_shopname; ?>" id="input-market-shopname" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= $entry_market_simple_pricelist; ?></label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input name="setting[<?= $profile_id; ?>][market_simple_pricelist]" type="radio" <?php echo ($market_simple_pricelist ? 'checked' : ''); ?> value="1">
                                    <?php echo $text_enabled; ?>
                                </label>
                                <label class="radio-inline">
                                    <input name="setting[<?= $profile_id; ?>][market_simple_pricelist]" type="radio" <?php echo (!$market_simple_pricelist ? 'checked' : ''); ?>
                                        value="0">
                                    <?php echo $text_disabled; ?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= $entry_market_currency; ?></label>
                            <div class="col-sm-8">
                                <select name="setting[<?= $profile_id; ?>][market_currency]" class="form-control">
                                    <option value=""><?= $text_default; ?></option>
                                    <?php foreach ($currencies as $currency) { ?>
                                        <option value="<?= $currency['code']; ?>" <?= ($market_currency == $currency['code'] ? 'selected' : ''); ?>><?= $currency['title']; ?> (<?= $currency['code']; ?>) <?= ($currency['code'] == $config_store_currency ? "($text_store_currency)" : ''); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= $entry_store_currency; ?></label>
                            <div class="col-sm-8">
                                <select name="setting[<?= $profile_id; ?>][store_currency]" class="form-control">
                                    <option value=""><?= $text_default; ?></option>
                                    <?php foreach ($currencies as $currency) { ?>
                                        <option value="<?= $currency['code']; ?>" <?= ($store_currency == $currency['code'] ? 'selected' : ''); ?>><?= $currency['title']; ?> (<?= $currency['code']; ?>) <?= ($currency['code'] == $config_store_currency ? "($text_store_currency)" : ''); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_settings">
                    <div class="heading-card">
                        <div class="number">2</div>
                        <h2><?= $menu_settings; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_sales_note]" value="0">
                                    <input type="checkbox" <?php echo ($market_sales_note ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_sales_note]" value="1"> <?= $text_market_sales_note; ?>
                                </label>
                                <br>
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_sales_note_q]" value="0">
                                    <input type="checkbox" <?php echo ($market_sales_note_q ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_sales_note_q]" value="1"> <?= $text_market_sales_note_q; ?>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="input-market-sales_note_text"><?= $entry_market_sales_note_text; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="setting[<?= $profile_id; ?>][market_sales_note_text]" value="<?= $market_sales_note_text; ?>" id="input-market-sales_note_text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="input-market-sales_note_text_q"><?= $entry_market_sales_note_text_q; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="setting[<?= $profile_id; ?>][market_sales_note_text_q]" value="<?= $market_sales_note_text_q; ?>" maxlength="50" id="input-market-sales_note_text_q" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_available]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_available ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_available]" value="1"> <?= $text_export_product_available; ?>
                                </label>
                            </div>
                            <?php if ($module_user->hasPermission('raz')) { ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="hidden" name="setting[<?= $profile_id; ?>][market_raz]" value="0">
                                        <input type="checkbox" <?php echo ($market_raz ?
                                                                    'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_raz]" value="1">
                                        <?= $text_market_raz; ?>
                                        <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                    </label>
                                </div>
                            <?php } ?>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_options_combination]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_options_combination ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_options_combination]" value="1"> <?= $text_export_options_combination; ?>
                                </label>
                                <?php if ($module_user->hasPermission('options_zero')) { ?>
                                    <br>
                                    <label style="margin-left: 30px; margin-top: 10px;">
                                        <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_options_zero_quantity]" value="0">
                                        <input type="checkbox" <?php echo ($market_product_options_zero_quantity ?
                                                                    'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_options_zero_quantity]" value="1"> <?= $text_export_options_zero_quantity; ?>
                                    </label>
                                    <br>
                                    <label style="margin-left: 30px">
                                        <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_options_quantity_available]" value="0">
                                        <input type="checkbox" <?php echo ($market_product_options_quantity_available ?
                                                                    'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_options_quantity_available]" value="1"> <?= $text_export_options_quantity_available; ?>
                                    </label>
                                    <br>
                                    <label style="margin-left: 30px">
                                        <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_quantity_from_options]" value="0">
                                        <input type="checkbox" <?php echo ($market_product_quantity_from_options ?
                                                                    'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_quantity_from_options]" value="1"> <?= $text_market_product_quantity_from_options; ?>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_attributes]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_attributes ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_attributes]" value="1"> <?= $text_export_product_attributes; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_dimensions]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_dimensions ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_dimensions]" value="1">
                                    <?= $text_export_product_dimensions; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_all_currencies]" value="0">
                                    <input type="checkbox" <?php echo ($market_all_currencies ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_all_currencies]" value="1">
                                    <?= $text_export_all_currencies; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_condition_likenew]" value="0">
                                    <input type="checkbox" <?php echo ($market_condition_likenew ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_condition_likenew]" value="1">
                                    <?= $text_market_condition_likenew; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_condition_used]" value="0">
                                    <input type="checkbox" <?php echo ($market_condition_used ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_condition_used]" value="1">
                                    <?= $text_market_condition_used; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_oldprice]" value="0">
                                    <input type="checkbox" <?php echo ($market_oldprice ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_oldprice]" value="1">
                                    <?= $text_market_oldprice; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_weight]" value="0">
                                    <input type="checkbox" <?php echo ($market_weight ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_weight]" value="1">
                                    <?= $text_market_weight; ?>
                                    <span data-toggle="tooltip" title="Выгружать тег веса. Нужен для передачи данных в раздел доставки."></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_price_with_minumun]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_price_with_minumun ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_price_with_minumun]" value="1">
                                    <?= $text_market_product_price_with_minumun; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_only_main_image]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_only_main_image ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_only_main_image]" value="1">
                                    <?= $text_market_product_only_main_image; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_picture]" value="0">
                                    <input type="checkbox" <?php echo ($market_picture ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_picture]" value="1">
                                    <?= $text_market_picture; ?>
                                    <span data-toggle="tooltip" title="Выгружать только для сервисов где нужно оригинальное, не сжатое изображение!"></span>
                                </label>
                            </div>
                            <?php if ($market_type == 'Farpost') { ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="hidden" name="setting[<?= $profile_id; ?>][market_preg_rep]" value="0">
                                        <input type="checkbox" <?php echo ($market_preg_rep ?
                                                                    'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_preg_rep]" value="1">
                                        <?= $text_market_preg_rep; ?>
                                        <span data-toggle="tooltip" title="Выгружать только для сервисa Farpost!"></span>
                                    </label>
                                </div>
                            <?php } ?>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_description]" value="0">
                                    <input type="checkbox" <?php echo ($market_description ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_description]" value="1">
                                    <?= $text_market_description; ?>
                                    <span data-toggle="tooltip" title="Выгружать описания товарных предложений в фид"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_marketplace_description]" value="0">
                                    <input type="checkbox" <?php echo ($market_marketplace_description ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_marketplace_description]" value="1">
                                    <?= $text_market_marketplace_description; ?>
                                    <span data-toggle="tooltip" title="Выгружать дополнительное описание из карточки товара"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_extra_description]" value="0">
                                    <input type="checkbox" <?php echo ($market_extra_description ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_extra_description]" value="1">
                                    <?= $text_market_extra_description; ?>
                                    <span data-toggle="tooltip" title="Выгружать шаблонный текст в описании"></span>
                                </label>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_conditions">
                    <div class="heading-card">
                        <div class="number">3</div>
                        <h2><?= $menu_conditions; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_var_1]" value="0">
                                    <input type="checkbox" <?php echo ($market_var_1 ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_var_1]" value="1">
                                    <?= $text_market_var_1; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_pickup]" value="0">
                                    <input type="checkbox" <?php echo ($market_pickup ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_pickup]" value="1">
                                    <?= $text_market_pickup; ?>
                                    <span data-toggle="tooltip" title="Это массовое добавление тега pickup"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_var_2]" value="0">
                                    <input type="checkbox" <?php echo ($market_var_2 ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_var_2]" value="1">
                                    <?= $text_market_var_2; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_store]" value="0">
                                    <input type="checkbox" <?php echo ($market_store ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_store]" value="1">
                                    <?= $text_market_store; ?>
                                    <span data-toggle="tooltip" title="Это массовое добавление тега store"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_var_3]" value="0">
                                    <input type="checkbox" <?php echo ($market_var_3 ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_var_3]" value="1">
                                    <?= $text_market_var_3; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_delivery]" value="0">
                                    <input type="checkbox" <?php echo ($market_delivery ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_delivery]" value="1">
                                    <?= $text_market_delivery; ?>
                                    <span data-toggle="tooltip" title="Это массовое добавление тега delivery"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][manufacturer_warranty_var]" value="0">
                                    <input type="checkbox" <?php echo ($manufacturer_warranty_var ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][manufacturer_warranty_var]" value="1">
                                    <?= $text_manufacturer_warranty_var; ?>
                                </label>
                                <br>
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_var_4]" value="0">
                                    <input type="checkbox" <?php echo ($market_var_4 ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_var_4]" value="1">
                                    <?= $text_market_var_4; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                                <br>
                                <label style="margin-left: 30px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_manufacturer_warranty]" value="0">
                                    <input type="checkbox" <?php echo ($market_manufacturer_warranty ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_manufacturer_warranty]" value="1">
                                    <?= $text_market_manufacturer_warranty; ?>
                                    <span data-toggle="tooltip" title="Это массовое добавление тега manufacturer_warranty"></span>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][adult_var]" value="0">
                                    <input type="checkbox" <?php echo ($adult_var ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][adult_var]" value="1">
                                    <?= $text_adult_var; ?>
                                </label>
                                <br>
                                <label style="margin-left: 30px; margin-top: 10px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_var_5]" value="0">
                                    <input type="checkbox" <?php echo ($market_var_5 ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_var_5]" value="1">
                                    <?= $text_market_var_5; ?>
                                    <span data-toggle="tooltip" title="Данный тег индивидуально редактируется в модуле Управление торговлей"></span>
                                </label>
                                <br>
                                <label style="margin-left: 30px;">
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_adult]" value="0">
                                    <input type="checkbox" <?php echo ($market_adult ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_adult]" value="1">
                                    <?= $text_market_adult; ?>
                                    <span data-toggle="tooltip" title="Это массовое добавление тега adult"></span>
                                </label>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_delivery">
                    <div class="heading-card">
                        <div class="number">4</div>
                        <h2><?= $menu_delivery; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <div class="checkbox" id="delivery-global">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_store_delivery_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_store_delivery_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_store_delivery_options]" value="1">
                                    <?= $text_export_market_delivery_options; ?>
                                </label>
                            </div>
                            <div class="checkbox" id="pickup-global">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_store_pickup_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_store_pickup_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_store_pickup_options]" value="1">
                                    <?= $text_export_market_pickup_options; ?>
                                </label>
                            </div>
                            <div class="checkbox" id="shipment-global" <?php if ($market_type == 'Goods') {
                                                                            echo 'style="display:run-in;"';
                                                                        } else {
                                                                            echo 'style="display:none;"';
                                                                        } ?>>
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_store_shipment_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_store_shipment_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_store_shipment_options]" value="1">
                                    <?= $text_export_market_shipment_options; ?>
                                </label>
                            </div>
                            <hr>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_delivery_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_delivery_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_delivery_options]" value="1">
                                    <?= $text_export_product_delivery_options; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_pickup_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_pickup_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_pickup_options]" value="1">
                                    <?= $text_export_product_pickup_options; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="setting[<?= $profile_id; ?>][market_product_shipment_options]" value="0">
                                    <input type="checkbox" <?php echo ($market_product_shipment_options ?
                                                                'checked' : ''); ?> name="setting[<?= $profile_id; ?>][market_product_shipment_options]" value="1">
                                    <?= $text_export_product_shipment_options; ?>
                                </label>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= $entry_market_pickup_time; ?></label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input type="radio" <?php echo ($market_product_set_available == 1 ? 'checked' : ''); ?>
                                        name="setting[<?= $profile_id; ?>][market_product_set_available]"
                                        value="1">
                                    <?= $text_before_2_days_all_products; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" <?php echo ($market_product_set_available == 2 ? 'checked' : ''); ?>
                                        name="setting[<?= $profile_id; ?>][market_product_set_available]"
                                        value="2">
                                    <?= $text_before_2_days_available_products; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" <?php echo ($market_product_set_available == 3 ? 'checked' : ''); ?>
                                        name="setting[<?= $profile_id; ?>][market_product_set_available]"
                                        value="3">
                                    <?= $text_exec_individual; ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" <?php echo ($market_product_set_available == 4 ? 'checked' : ''); ?>
                                        name="setting[<?= $profile_id; ?>][market_product_set_available]"
                                        value="4">
                                    <?= $text_not_pickup; ?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group --no-offset" <?php if ($market_type == 'Goods') {
                                                                echo 'style="display:none;"';
                                                            } else {
                                                                echo 'style="display:run-in;"';
                                                            } ?>>
                            <div class="col-sm-12">
                                <div class="group-title">
                                    <label><?= $entry_market_delivery_global; ?><span data-toggle="tooltip" title="Поддерживается мультизначения через символ ';'"></span></label>
                                </div>
                                <table class="table table-delivery-in-stock">
                                    <thead>
                                        <tr>
                                            <td>Тип</td>
                                            <td><?= $entry_market_product_localcoast; ?></td>
                                            <td><?= $entry_market_product_localdays; ?></td>
                                            <td><?= $entry_market_product_localtimes; ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $entry_market_delivery; ?></td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_localcoast]" value="<?= $market_localcoast; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_localdays]" value="<?= $market_localdays; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_localtimes]" value="<?= $market_localtimes; ?>" class="form-control input-sm">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= $entry_market_pickup; ?></td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_pickupcoast]" value="<?= $market_pickupcoast; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_pickupdays]" value="<?= $market_pickupdays; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_pickuptimes]" value="<?= $market_pickuptimes; ?>" class="form-control input-sm">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group --no-offset" <?php if ($market_type == 'Goods') {
                                                                echo 'style="display:run-in;"';
                                                            } else {
                                                                echo 'style="display:none;"';
                                                            } ?>> <!--shipment for goods-->
                            <div class="col-sm-12">
                                <div class="group-title">
                                    <label><?= $entry_market_delivery_global; ?><span data-toggle="tooltip" title="Поддерживается мультизначения через символ ';'"></span></label>
                                </div>
                                <table class="table table-delivery-in-stock">
                                    <thead>
                                        <tr>
                                            <td>Тип</td>
                                            <td><?= $entry_market_product_shipmentdays; ?></td>
                                            <td><?= $entry_market_product_shipmenttimes; ?></td>
                                            <td><?= $entry_market_product_shipmentid; ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $entry_market_delivery; ?></td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_shipmentdays]" value="<?= $market_shipmentdays; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_shipmenttimes]" value="<?= $market_shipmenttimes; ?>" class="form-control input-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="setting[<?= $profile_id; ?>][market_shipmentid]" value="<?= $market_shipmentid; ?>" class="form-control input-sm">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group --no-offset --collapsible">
                            <div class="col-sm-12">
                                <div class="group-title">
                                    <label class=""><?php echo $text_market_delivery_variables; ?>
                                        (<?= count($market_delivery_options); ?>)
                                        <span data-toggle="tooltip" title="Поддерживается мультизначения через символ ','"></span></label>
                                    <i class="toggle-icon collapsed" data-toggle="collapse" data-target="#collapse-table-delivery-options"></i>
                                </div>
                                <div id="collapse-table-delivery-options" class="collapse">
                                    <table id="table-delivery-options" class="table table-delivery-options">
                                        <thead>
                                            <tr>
                                                <th><?= $entry_market_delivery_type; ?></th>
                                                <th class="col-stock-status"><?= $entry_market_product_stock_status; ?></th>
                                                <th class="col-compact"><?= $entry_market_delivery_coast; ?></th>
                                                <th class="col-compact"><?= $entry_market_delivery_time; ?></th>
                                                <th class="col-compact"><?= $entry_market_delivery_time_before; ?></th>
                                                <th class="text-center"><?= $entry_market_delivery_weight; ?></th>
                                                <th class="text-center"><?= $entry_market_delivery_category; ?></th>
                                                <th class="col-compact"><?= $entry_market_delivery_stock; ?></th>
                                                <th class="text-right">
                                                    <button type="button" id="btn-add-delivery-option" class="btn btn-sm btn-info" data-toggle="tooltip" title="<?= $button_add; ?>"><i class="fa fa-plus"></i></button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_options_main">
                    <div class="heading-card">
                        <div class="number">5</div>
                        <h2><?= $menu_options_main; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group --no-offset --collapsible">
                            <div class="col-xs-12">
                                <div class="group-title">
                                    <label>Опции товаров (<?= count($market_product_options); ?>)</label>
                                    <i class="toggle-icon collapsed" data-toggle="collapse" data-target="#collapse-table-market-product-options"></i>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 control-label">Тип опций оффера</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="setting[<?= $profile_id; ?>][market_product_option_type]" value="option" <?php echo ($market_product_option_type == 'option' ? 'checked' : ''); ?>>
                                            Опции товара
                                        </label>
                                        <?php if ($module_user->hasPermission('atribute_offer')) { ?>
                                            <label class="radio-inline">
                                                <input type="radio" name="setting[<?= $profile_id; ?>][market_product_option_type]" value="attribute" <?php echo ($market_product_option_type == 'attribute' ? 'checked' : ''); ?>>
                                                Атрибуты как опции товара <span id="popover-attribute-option-type" data-toggle="popover" class="icon-info"></span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <label class="col-sm-4 control-label">Изображения из опций</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="setting[<?= $profile_id; ?>][market_product_images_from_option]" value="0" <?php echo ($market_product_images_from_option == 0 ? 'checked' : ''); ?>>
                                            <?= $text_no; ?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="setting[<?= $profile_id; ?>][market_product_images_from_option]" value="1" <?php echo ($market_product_images_from_option == 1 ? 'checked' : ''); ?>>
                                            <?= $text_yes; ?>
                                        </label>
                                    </div>
                                </div>
                                <br>
                                <div id="collapse-table-market-product-options" class="collapse">
                                    <table id="market-product-options" class="table table-condensed table-market-options">
                                        <thead class="table-bordered">
                                            <tr>
                                                <td>Название групы опции</td>
                                                <td>Опции</td>
                                                <td class="text-right">
                                                    <button id="btn-add-market-options" type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="Добавить опции"><i class="fa fa-plus"></i></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($market_product_options as $i => $option_row): ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-control input-sm" type="text" name="setting[<?= $profile_id; ?>][market_product_options][<?= $i; ?>][name]" value="<?= $option_row['name']; ?>">
                                                    </td>
                                                    <td>
                                                        <div style="height: 100px; overflow-x: auto; width: 100%;">
                                                            <?php foreach ($catalog_options as $option) { ?>
                                                                <div class="">
                                                                    <?php if (in_array($option['option_id'], $option_row['options'])) { ?>
                                                                        <input type="checkbox" name="setting[<?= $profile_id; ?>][market_product_options][<?= $i; ?>][options][]" value="<?= $option['option_id']; ?>" checked>
                                                                        <?= $option['name']; ?>
                                                                    <?php } else { ?>
                                                                        <input type="checkbox" name="setting[<?= $profile_id; ?>][market_product_options][<?= $i; ?>][options][]" value="<?= $option['option_id']; ?>">
                                                                        <?= $option['name']; ?>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $button_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $button_unselect_all; ?></a>
                                                    </td>
                                                    <td class="text-right">
                                                        <button type="button" class="btn btn-danger btn-sm" data-action="remove-market-option" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_custom_tags">
                    <div class="heading-card">
                        <div class="number">6</div>
                        <h2><?= $menu_custom_tags; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group --no-offset --collapsible">
                            <div class="col-xs-12">
                                <div class="group-title">
                                    <label>Кастомные теги (<?= count($custom_market_tags); ?>)</label>
                                    <i class="toggle-icon collapsed" data-toggle="collapse" data-target="#collapse-custom-market-tags"></i>
                                </div>
                                <div id="collapse-custom-market-tags" class="collapse">
                                    <table id="custom-market-tags" class="table table-condensed table-custom-tags">
                                        <thead class="table-bordered">
                                            <tr>
                                                <td>Название тега</td>
                                                <td class="col-tag-value">Значение тега</td>
                                                <td>Соответствующее поле товара</td>
                                                <td class="col-delimiter">Разделитель <span data-toggle="tooltip" title="<?= $help_custom_tag_delemeter; ?>"></span></td>
                                                <td class="col-unit">Unit</td>
                                                <td>Фильтр по категории</td>
                                                <td>Статус на складе</td>
                                                <td>Наличие</td>
                                                <td class="text-right">
                                                    <button id="btn-add-custom-market-tag" type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="Добавить тег"><i class="fa fa-plus"></i></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_category_filter">
                    <div class="heading-card">
                        <div class="number">7</div>
                        <h2><?= $menu_category_filter; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group --no-offset --collapsible">
                            <div class="col-xs-12">
                                <div class="group-title">
                                    <label>Настройки категорий (<?= count($market_category_additional); ?>)</label>
                                    <i class="toggle-icon collapsed" data-toggle="collapse" data-target="#collapse-category-additional"></i>
                                </div>
                                <div id="collapse-category-additional" class="collapse">
                                    <table id="category-additional" class="table">
                                        <thead class="table-bordered">
                                            <tr>
                                                <td>Категория</td>
                                                <?php if ($module_user->hasPermission('category_for_yml')) { ?>
                                                    <td>Название</td>
                                                <?php } ?>
                                                <td>Фильтр по цене</td>
                                                <td class="text-right">
                                                    <button id="btn-add-category-additional-row" class="btn btn-info btn-sm" type="button"><i class="fa fa-plus"></i></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($market_category_additional as $i => $category_data): ?>
                                                <tr>
                                                    <td>
                                                        <select class="form-control input-sm" name="setting[<?= $profile_id; ?>][market_category_additional][<?= $i; ?>][category_id]" data-value="<?= $category_data['category_id']; ?>" id=""><?= $cat_tree_select; ?></select>
                                                    </td>
                                                    <?php if ($module_user->hasPermission('category_for_yml')) { ?>
                                                        <td>
                                                            <input type="text" name="setting[<?= $profile_id; ?>][market_category_additional][<?= $i; ?>][name]" class="form-control input-sm" value="<?= $category_data['name']; ?>">
                                                        </td>
                                                    <?php } ?>
                                                    <td>
                                                        <select name="setting[<?= $profile_id; ?>][market_category_additional][<?= $i; ?>][price_filter_sign]" class="input-sm col-sm-5">
                                                            <option value=""><?= $text_select; ?></option>
                                                            <option value=">" <?= ($category_data['price_filter_sign'] == '>' ? 'selected' : ''); ?>>&gt; (больше)</option>
                                                            <option value="<" <?= ($category_data['price_filter_sign'] == '<' ? 'selected' : ''); ?>>&lt; (меньше)</option>
                                                            <option value=">=" <?= ($category_data['price_filter_sign'] == '>=' ? 'selected' : ''); ?>>&ge; (больше или равно)</option>
                                                            <option value="<=" <?= ($category_data['price_filter_sign'] == '<=' ? 'selected' : ''); ?>>&le; (меньше или равно)</option>
                                                        </select>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="setting[<?= $profile_id; ?>][market_category_additional][<?= $i; ?>][price_filter_value]" class="form-control input-sm" value="<?= $category_data['price_filter_value']; ?>" placeholder="цена">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="<?= $button_remove; ?>" data-action="remove-category-additional-row"><i class="fa fa-ban"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_markup">
                    <div class="heading-card">
                        <div class="number">8</div>
                        <h2><?= $menu_markup; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group --no-offset --collapsible">
                            <div class="col-xs-12">
                                <div class="group-title">
                                    <label><?= $text_price_markup_options; ?> (<?= count($market_purchase_price_markup_options); ?>)</label>
                                    <i class="toggle-icon collapsed" data-toggle="collapse" data-target="#collapse-product-price-markup"></i>
                                </div>
                                <div id="collapse-product-price-markup" class="collapse">
                                    <table id="product-purchase-price-markup" class="table table-price-markup-option">
                                        <thead class="table-bordered">
                                            <tr>
                                                <td class="col-price"><?= $column_option_markup_price; ?> (<?= $market_product_purchase_markup_field; ?>)</td>
                                                <td><?= $column_option_markup_category; ?></td>
                                                <td><?= $column_option_markup_type; ?></td>
                                                <td><?= $column_option_markup_action; ?></td>
                                                <td><?= $column_option_markup_value; ?></td>
                                                <td class="text-right">
                                                    <button id="btn-add-product-price-markup-row" class="btn btn-info btn-sm" type="button"><i class="fa fa-plus"></i></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_analytics">
                    <div class="heading-card">
                        <div class="number">9</div>
                        <h2><?= $menu_analytics; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <input type="hidden" name="setting[<?= $profile_id; ?>][market_yandex_metrika]" value="">
                            <label for="input-yandex_metrika" class="col-sm-4 control-label">Yandex Metrika Code</label>
                            <div class="col-sm-8">
                                <textarea name="setting[<?= $profile_id; ?>][market_yandex_metrika]" id="input-yandex_metrika" rows="4" class="form-control"><?= $market_yandex_metrika; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="setting[<?= $profile_id; ?>][market_google_analytics]" value="">
                            <label for="input-google_analytics" class="col-sm-4 control-label">Google Analytics</label>
                            <div class="col-sm-8">
                                <textarea name="setting[<?= $profile_id; ?>][market_google_analytics]" id="input-google_analytics" rows="4" class="form-control"><?= $market_google_analytics; ?></textarea>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_content">
                    <div class="heading-card">
                        <div class="number">10</div>
                        <h2><?= $menu_content; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <input type="hidden" name="setting[<?= $profile_id; ?>][market_custom_content]" value="">
                            <label for="input-custom-content" class="col-sm-4 control-label">Произвольный XML контент</label>
                            <div class="col-sm-8">
                                <textarea name="setting[<?= $profile_id; ?>][market_custom_content]" id="input-custom-content" rows="4" class="form-control"><?= $market_custom_content; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="setting[<?= $profile_id; ?>][market_description_template]" value="">
                            <label for="input-custom-content" class="col-sm-4 control-label">Шаблонный текст в описании</label>
                            <div class="col-sm-8">
                                <textarea name="setting[<?= $profile_id; ?>][market_description_template]" id="input-custom-content" rows="4" class="form-control"><?= $market_description_template; ?></textarea>
                            </div>
                        </div>
                        <!-- End content -->
                    </div>
                </div>
                <div class="card" id="menu_urls">
                    <div class="heading-card">
                        <div class="number">11</div>
                        <h2><?= $menu_urls; ?></h2>
                    </div>
                    <div class="body-card">
                        <!-- Start content -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="input-yamarket-fusion-yml-url-key"><?= $entry_market_price_url_key; ?></label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="setting[<?= $profile_id; ?>][yml_url_key]" value="<?= $yml_url_key; ?>"
                                    id="input-yamarket-fusion-yml-url-key">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="ya_market_dynamic"><?php echo $entry_market_export_url; ?></label>
                            <div class="col-sm-8">
                                <input type="text" value="<?= $pricelist_url; ?>" class="form-control" readonly onclick="this.select()">
                            </div>
                        </div>
                        <div class="form-group market-pricelist-update-url">
                            <label class="col-sm-4 control-label"><span data-toggle="tooltip" title="<?= $help_yml_upadate_url; ?>"><?= $entry_yml_upadate_url; ?></span></label>
                            <div class="col-sm-8">
                                <input type="text" value="<?= $pricelist_update_url; ?>" class="form-control" readonly onclick="this.select()">
                                <div class="form-group">
                                    <input type="checkbox" name="setting[<?= $profile_id; ?>][yml_caching]" value="1" <?php echo ($yml_caching ? 'checked' : ''); ?>>
                                    <?= $entry_yml_caching; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($module_user->hasPermission('api_cron')) { ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $entry_yml_update_cron_url; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" value="<?= $pricelist_update_cron_url; ?>" class="form-control" readonly onclick="this.select()">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $entry_yandex_api_list_update; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" value="<?= $yandex_api_list_update; ?>" class="form-control" readonly onclick="this.select()">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><span data-toggle="tooltip" title="<?= $help_yandex_api_delete_out_stock_products; ?>"><?php echo $entry_yandex_api_delete_out_stock_products; ?></span></label>
                                <div class="col-sm-8">
                                    <input type="text" value="<?= $yandex_api_delete_out_stock_products; ?>" class="form-control" readonly onclick="this.select()">
                                </div>
                            </div>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
                        <!-- End content -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- some hidden data -->
<div id="popover-attribute-option-type-content" class="hidden">
    <div class="attribute-option-type-desc clearfix">
        <strong>Расширенный формат атрибутов</strong><br>
        <div class="format">
            <span class="attr-val">[значение]</span>
            <span class="attr-price-delimiter">[<?= $attribute_option_type_price_delimiter; ?>]</span>
            <span class="attr-price-sign">[знак]</span>
            <span class="attr-price">[цена]</span>
            <span class="attr-delimiter">[разделитель]</span>
            <span class="attr-val">[значение2]</span>
            <span class="attr-price-delimiter">[<?= $attribute_option_type_price_delimiter; ?>]</span>
            <span class="attr-price-sign">[знак]</span>
            <span class="attr-price">[цена2]</span>
        </div>
        <br><br>
        <div><span class="attr-val">[значение]</span> - значение атрибута</div>
        <div><span class="attr-price-delimiter">[<?= $attribute_option_type_price_delimiter; ?>]</span> - символ <b><?= $attribute_option_type_price_delimiter; ?></b> разделителя для цены(из файла конфигурации модуля)</div>
        <div>
            <span class="attr-price-sign">[знак]</span> - знак цены
            <ul>
                <li><b>+</b> - прибавить к основной цене</li>
                <li><b>&ndash;</b> - отнять от основной цены</li>
                <li>без знака - установить новую цену</li>
            </ul>
        </div>
        <div><span class="attr-price">[цена]</span> - цена</div>
        <div><span class="attr-delimiter">[разделитель]</span> - разделитель для атрибутов(из настроек модуля)</div>
    </div>
</div>
<select id="select-category-additional" class="form-control input-sm" style="display: none"><?= $cat_tree_select; ?></select>
<select id="select-category-additional-price-filter" class="input-sm col-sm-5" style="display: none">
    <option value=""><?= $text_select; ?></option>
    <option value=">">&gt; (больше)</option>
    <option value="<">&lt; (меньше)</option>
    <option value=">=">&ge; (больше или равно)</option>
    <option value="<=">&le; (меньше или равно)</option>
</select>
<script>
    jQuery(function() {
        // product options
        $('#btn-add-market-options').click(function() {
            var
                $table = $('#market-product-options'),
                index = ($table.find('tbody > tr').length + 1);

            html = '<tr>' +
                '<td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][market_product_options][' + index + '][name]">' +
                '</td><td>' +
                '<div class="/scrollbox" style="height: 100px; overflow-x: auto; width: 100%;">'
            <?php foreach ($catalog_options as $option) { ?>
                    +
                    '<div class="">' +
                    '<input type="checkbox" name="setting[<?= $profile_id; ?>][market_product_options][' + index + '][options][]" value="<?= $option['option_id']; ?>">' +
                    ' <?= $option['name']; ?>' +
                    '</div>'
            <?php } ?>
                +
                '</div>' +
                '<a onclick="$(this).parent().find(\':checkbox\').prop(\'checked\', true);"><?php echo $button_select_all; ?></a> / <a onclick="$(this).parent().find(\':checkbox\').prop(\'checked\', false);"><?php echo $button_unselect_all; ?></a>' +
                '</td><td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="<?= $button_remove; ?>" data-action="remove-market-option"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            $table.find('tbody').append(html);
        });

        $('#market-product-options').on('click', '[data-action=remove-market-option]', function() {
            $(this).parents('tr').remove();
        });

        $('.module-yamarket-fusion').on('click', '[data-action=remove-table-row]', function() {
            $(this).parents('tr').remove();
        });

        // Custom tags
        function addCustomTag(data) {
            var
                $table = $('#custom-market-tags'),
                index = ($table.find('tbody > tr').length + 1),
                tag = data.tag || '',
                value = data.value || '',
                delimiter = data.delimiter || '',
                unit = data.unit || '',
                filter_catefory_id = data.filter_catefory_id || '',
                html = '<tr>' +
                '<td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][tag]" value="' + tag + '">' +
                '</td><td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][value]" value="' + value + '">' +
                '</td><td>' +
                '<select class="form-control input-sm" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][field]" data-select="field">' +
                '<option value=""><?= $text_select; ?></option>'
            <?php foreach ($custom_market_tags_product_fields as $key => $name) { ?>
                    +
                    '<option value="<?= $key; ?>"><?= $name; ?></option>'
            <?php } ?>
                +
                '</select>' +
                '</td><td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][delimiter]" value="' + delimiter + '">' +
                '</td><td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][unit]" value="' + unit + '">' +
                '</td><td>' +
                '<select class="form-control input-sm select_search_category" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][filter_category_id]" data-select="filter_category_id">' +
                '<option value=""><?= $text_select; ?></option>' +
                '<?= $cat_tree_select; ?>' +
                '</select>' +
                '<input id="custom-market-tag-category-inherit-' + index + '" type="checkbox" name=setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][category_inherit]" value="1" data-checkbox="category_inherit"> <label for="custom-market-tag-category-inherit-' + index + '"><?= $text_category_children; ?><label>' +
                '</td><td>' +
                '<select class="form-control input-sm" name=setting[<?= $profile_id; ?>][custom_market_tags][' + index +
                '][stock_status]" data-select="stock_status">' +
                '<option value=""><?= $text_select; ?></option>'
            <?php foreach ($stock_statuses as $stock_status) { ?>
                    +
                    '<option value="<?= $stock_status['stock_status_id']; ?>"><?= $stock_status['name']; ?></option>'
            <?php } ?>
                +
                '</select>' +
                '</td><td>' +
                '<div class="checkbox"><input type="checkbox" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][stock_in]" value="1" data-checkbox="stock_in"> <?= $text_yes; ?></div>' +
                '<div class="checkbox"><input type="checkbox" name="setting[<?= $profile_id; ?>][custom_market_tags][' + index + '][stock_out]" value="1" data-checkbox="stock_out"> <?= $text_no; ?></div>' +
                '</td><td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-action="remove-table-row" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            html = $(html);

            var checkbocks = ['category_inherit', 'stock_in', 'stock_out'],
                selects = ['field', 'filter_category_id', 'stock_status'];

            for (var i = 0; i < checkbocks.length; i++) {
                if (data[checkbocks[i]])
                    html.find('[data-checkbox="' + checkbocks[i] + '"]').prop('checked', true);
            }

            for (var i = 0; i < selects.length; i++) {
                if (data[selects[i]])
                    html.find('[data-select="' + selects[i] + '"]').val(data[selects[i]]);
            }

            $table.find('tbody').append(html);
        }

        $('#btn-add-custom-market-tag').click(addCustomTag);

        var customTags = JSON.parse('<?= json_encode($custom_market_tags); ?>');
        for (var key in customTags) {
            addCustomTag(customTags[key]);
        }

        // Custom category names
        $('#btn-add-category-additional-row').click(function() {
            var
                $table = $('#category-additional'),
                index = ($table.find('tbody > tr').length + 1),
                html = '<tr>' +
                '<td>' +
                $('#select-category-additional').clone().attr('name', 'setting[<?= $profile_id; ?>][market_category_additional][' + index + '][category_id]').removeAttr('id').show()[0].outerHTML +
                '</td><td>' +
                '<input type="text" class="form-control input-sm" name="setting[<?= $profile_id; ?>][market_category_additional][' + index + '][name]" value="">' +
                '</td><td>' +
                $('#select-category-additional-price-filter').clone().attr('name', 'setting[<?= $profile_id; ?>][market_category_additional][' + index + '][price_filter_sign]').removeAttr('id').show()[0].outerHTML +
                '<div class="col-sm-5">' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_category_additional][' + index + '][price_filter_value]" class="form-control input-sm" placeholder="цена">' +
                '</div></td><td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-action="remove-category-additional-row" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            $table.find('tbody').append(html);
        });

        $('#category-additional select').each(function() {
            $(this).find('option[value=' + $(this).data('value') + ']').prop('selected', true);
        });

        $('#category-additional').on('click', '[data-action=remove-category-additional-row]', function() {
            $(this).parents('tr').remove();
        });

        // Popovers
        $('#popover-attribute-option-type').popover({
            html: true,
            placement: 'top',
            content: $('#popover-attribute-option-type-content').html()
        });

        function addDeliveryOption(data) {
            var
                $table = $('#table-delivery-options'),
                index = ($table.find('tbody > tr').length + 1),
                cost = data.cost || '',
                days = data.days || '',
                times = data.times || '',
                weight_from = data.weight_from || '',
                weight_to = data.weight_to || '',
                html = '<tr>' +
                '<td>' +
                '<select class="form-control input-sm" name=setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][type]" data-select-type>' +
                '<option value="delivery"><?= $entry_market_delivery; ?></option>' +
                '<option value="pickup"><?= $entry_market_pickup; ?></option>' +
                '</select>' +
                '</td><td>' +
                '<select class="form-control input-sm" name=setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][stock_status]" data-select-stock-status>' +
                '<option value=""><?= $text_select; ?></option>'
            <?php foreach ($stock_statuses as $stock_status) { ?>
                    +
                    '<option value="<?= $stock_status['stock_status_id']; ?>"><?= $stock_status['name']; ?></option>'
            <?php } ?>
                +
                '</select>' +
                '</td><td>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][cost]" class="form-control input-sm" value="' + cost + '">' +
                '</td><td>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][days]" class="form-control input-sm" value="' + days + '">' +
                '</td><td>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][times]" class="form-control input-sm" value="' + times + '">' +
                '</td><td class="col-weight">' +
                '<div class="input-block"><?= $text_range_from; ?> <input type="text" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][weight_from]" class="form-control input-sm" value="' + weight_from + '"></div>' +
                '<div class="input-block"><?= $text_range_to; ?> <input type="text" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][weight_to]" class="form-control input-sm" value="' + weight_to + '"></div>' +
                '<div class="input-block">Unit: <select class="form-control input-sm" name=setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][weight_class]" data-select-weight-class>'
            <?php foreach ($weight_classes as $weight_class) { ?>
                    +
                    '<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>'
            <?php } ?>
                +
                '</select></div>' +
                '</td><td>' +
                '<select id="delivery-options-category" class="form-control input-sm select_search_category" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][category]" data-select-category>' +
                '<option value=""><?= $text_select; ?></option>' +
                '<?= $cat_tree_select; ?>' +
                '</select>' +
                '<input id="market-delivery-options-category-inherit-' + index + '" type="checkbox" name=setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][category_inherit]" value="1" data-checkbox-category-inherit> <label for="market-delivery-options-category-inherit-' + index + '"><?= $text_category_children; ?><label>' +
                '</td><td>' +
                '<div class="checkbox"><input type="checkbox" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][stock_in]" value="1" data-checkbox-stock-in> <?= $text_yes; ?></div>' +
                '<div class="checkbox"><input type="checkbox" name="setting[<?= $profile_id; ?>][market_delivery_options][' + index + '][stock_out]" value="1" data-checkbox-stock-out> <?= $text_no; ?></div>' +
                '</td><td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-action="remove-table-row" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            html = $(html);

            if (data.type)
                html.find('[data-select-type] option[value=' + data.type + ']').prop('selected', true);

            if (data.stock_status)
                html.find('[data-select-stock-status] option[value=' + data.stock_status + ']').prop('selected', true);

            html.find('[data-select-weight-class] option[value=' + data.weight_class + ']').prop('selected', true);

            if (data.category)
                html.find('[data-select-category] option[value=' + data.category + ']').prop('selected', true);

            if (data.category_inherit)
                html.find('[data-checkbox-category-inherit]').prop('checked', true);

            if (data.stock_in)
                html.find('[data-checkbox-stock-in]').prop('checked', true);

            if (data.stock_out)
                html.find('[data-checkbox-stock-out]').prop('checked', true);

            $table.find('tbody').append(html);
        }

        $('#btn-add-delivery-option').click(addDeliveryOption);

        var deliveryOptions = JSON.parse('<?= json_encode($market_delivery_options); ?>');
        for (var key in deliveryOptions) {
            addDeliveryOption(deliveryOptions[key]);
        }

        // Product price markup
        function addProductPriceMarkupOption(data) {
            var
                $table = $('#product-purchase-price-markup'),
                index = ($table.find('tbody > tr').length + 1),
                price_from = data.price_from || '',
                price_to = data.price_to || '',
                price_to = data.price_to || '',
                markup_value = data.markup_value || '',
                markup_type = data.markup_type || '',
                markup_sign = data.markup_sign || '',
                html = '<tr>' +
                '<td>' +
                '<div class="input-group input-group-sm">' +
                '<div class="input-group-addon"><?= $text_range_from; ?></div>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][price_from]" class="form-control input-sm" value="' + price_from + '">' +
                '<div class="input-group-addon"><?= $text_range_to; ?></div>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][price_to]" class="form-control input-sm" value="' + price_to + '">' +
                '</div>' +
                '</td><td>' +
                '<select id="markup_category" class="form-control input-sm" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][category]" data-select-category>' +
                '<option value=""><?= $text_select; ?>' +
                '<?= $cat_tree_select; ?>' +
                '</select>' +
                '<input id="market-price-markup-options-category-inherit-' + index + '" type="checkbox" name=setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][category_inherit]" value="1" data-checkbox-category-inherit> <label for="market-price-markup-options-category-inherit-' + index + '"><?= $text_markup_category_children; ?></label>' +
                '</td><td>' +
                '<select class="form-control input-sm select_search_category" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][markup_type]" data-select-markup-type>' +
                '<option value=""><?= $text_select; ?></option>' +
                '<option value="percent"><?= $text_percent; ?></option>' +
                '<option value="sum"><?= $text_fixed_sum; ?></option>' +
                '</select>' +
                '</td><td>' +
                '<select class="form-control input-sm" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][markup_sign]" data-select-markup-sign>' +
                '<option value="+"><?= $text_add; ?></option>' +
                '<option value="-"><?= $text_substract; ?></option>' +
                '</select>' +
                '</td><td>' +
                '<input type="text" name="setting[<?= $profile_id; ?>][market_purchase_price_markup_options][' + index + '][markup_value]" class="form-control input-sm" value="' + markup_value + '">' +
                '</td><td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-action="remove-table-row" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            html = $(html);

            if (data.category)
                html.find('[data-select-category] option[value=' + data.category + ']').prop('selected', true);

            if (data.category_inherit)
                html.find('[data-checkbox-category-inherit]').prop('checked', true);

            if (data.markup_type)
                html.find('select[data-select-markup-type] option[value="' + data.markup_type + '"]').prop('selected', true);

            if (data.markup_sign)
                html.find('select[data-select-markup-sign] option[value="' + data.markup_sign + '"]').prop('selected', true);

            $table.find('tbody').append(html);
            $('#markup_category').select2();
        }

        $('#btn-add-product-price-markup-row').click(addProductPriceMarkupOption);

        var priceMarkupOptions = JSON.parse('<?= json_encode($market_purchase_price_markup_options); ?>');
        for (var key in priceMarkupOptions) {
            addProductPriceMarkupOption(priceMarkupOptions[key]);
        }
    });
</script>
<script>
    $(document).ready(function Getinfo() {
        $('.select_search_category').select2();
        $("#markets-type").change(function() {
            var valOpt = $(this).find('option:selected').val();
            $(function Information() {
                // $.ajax({
                //     url: 'https://opencartmodul.ru/service/marketplace/pricelist.php?info_pricelist=' + valOpt,
                //     success: function(html) {
                //         $('#catalog-type-init').html(html);
                //     }
                // })
            });
        });
    });
    $(function UpdateInfo() {
        // $.ajax({
        //     url: 'https://opencartmodul.ru/service/marketplace/pricelist.php?info_pricelist=<?= $market_type; ?>',
        //     success: function(html) {
        //         $('#catalog-type-init').html(html);
        //     }
        // })
    });
</script>