<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content" class="module-yamarket-fusion">
    <div class="page-header">
        <div class="container-fluid" style="position: relative;">
            <div class="pull-right">
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <p><?= $text_module_version; ?> <?= $module_version; ?> <span class="php-version">php version: <?= phpversion(); ?></span></p>
        <ul id="market_tabs" class="nav nav-tabs">
            <li><a href="#tab-market" data-toggle="tab"><?= $text_tab_market; ?></a></li>
            <li><a href="#tab-market-category" data-toggle="tab">Категории</a></li>
            <li><a href="#tab-market-addon" data-toggle="tab"><?= $text_tab_market_addon; ?></a></li>
            <li><a href="#tab-product-options" data-toggle="tab"><?= $text_tab_product_param; ?></a></li>
            <?php if ($module_user->hasPermission('api_offers_list_status')) { ?>
                <li><a href="#tab-market-products-api" data-toggle="tab"><?= $text_tab_market_products_api; ?></a></li>
            <?php } ?>
            <li><a href="#tab-list" data-toggle="tab">Инструкция</a></li>
            <li><a href="#tab-help" data-toggle="tab">Поддержка</a></li>
            <li><a href="#tab-news" data-toggle="tab">Новости</a></li>
        </ul>
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i>
                <?= $success; ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span class="pe-7s-close"></span></button>
            </div>
        <?php endif; ?>
        <div id="setting-profiles-list" class="setting-profiles">
            <label class="control-label"><i class="yamf-address-card"></i> Профили:</label>
            <select id="input-setting-profiles" class="form-control input-sm">
                <?php foreach ($setting_profiles as $p_id => $name) { ?>
                    <option value="<?= $p_id; ?>" <?php echo ($profile_id == $p_id ? 'selected' : ''); ?>><?= $name; ?></option>
                <?php } ?>
            </select>
            <a href="" data-toggle="modal" data-target="#modal-setting-profiles" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>
        </div>
        <div class="tab-content bootstrap">
            <div id="tab-product-options" class="tab-pane"><?= $tab_product_param; ?></div>
            <div id="tab-market" class="tab-pane active"><?= $tab_market; ?></div>
            <div id="tab-market-category" class="tab-pane"><?= $tab_market_category; ?></div>
            <div id="tab-market-addon" class="tab-pane"><?= $tab_market_addon; ?></div>
            <div id="tab-market-products-api" class="tab-pane"><?= $tab_products_api; ?></div>
            <div id="tab-list" class="tab-pane"><?= $tab_list; ?></div>
            <div id="tab-help" class="tab-pane"><?= $tab_help; ?></div>
            <div id="tab-news" class="tab-pane"><?= $tab_news; ?></div>
        </div>
    </div>
    <div id="modal-setting-profiles" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button data-dismiss="modal" type="button" class="close">&times;</button>
                    <h4><b>Профили настроек маркета</b></h4>
                </div>
                <div class="modal-body">
                    <input type="file" class="hidden" id="input-import-setting-profiles" name="import" accept="application/json">
                    <form id="setting-profiles" action="<?= $action_setting_profiles; ?>" method="post">
                        <output name="error" class="text-danger"></output>
                        <table class="table table-condensed">
                            <thead class="table-bordered">
                                <tr>
                                    <td><input type="checkbox" class="form-control" onclick="$('#setting-profiles input[name*=selected]').prop('checked', this.checked);"></td>
                                    <td>ID</td>
                                    <td>Название профиля</td>
                                    <td class="text-right">
                                        <button id="btn-import-setting-profiles" type="button" class="btn btn-sm btn-warning" data-toggle="tooltip" title="<?= $button_upload; ?>" data-loading-text="<i class='fa fa-spin fa-spinner'></i>"><i class="fa fa-download"></i></button>
                                        <button id="btn-export-setting-profiles" type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="<?= $button_save; ?>" data-loading-text="<i class='fa fa-spin fa-spinner'></i>"><i class="fa fa-floppy-o"></i></button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <button id="btn-add-setting-profile" type="button" class="btn btn-sm btn-info" data-toggle="tooltip" title="<?= $button_add; ?>"><i class="fa fa-plus"></i></button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($setting_profiles as $profile_id => $name): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected[]" value="<?= $profile_id; ?>" class="form-control"></td>
                                        <td><?= $profile_id; ?></td>
                                        <td><input class="form-control input-sm" type="text" name="profiles[<?= $profile_id; ?>]" value="<?= $name; ?>"></td>
                                        <td class="text-right"><button data-action="remove-setting-profile" type="button" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="form-group text-right">
                            <input type="submit" class="btn btn-primary" value="<?= $button_save; ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function() {
        var view = $.totalStorage('tab_yamarket_fusion'),
            tabWithProfiles = ['tab-market', 'tab-market-category', 'tab-market-addon', 'tab-market-products-api'];

        function loadFile(filename, data) {
            var a = document.createElement('a');
            a.href = window.URL.createObjectURL(new Blob([data], {
                type: 'application/json'
            }));
            a.download = filename;
            //a.style.display = 'none';
            //document.body.appendChild(a);
            a.click();
        }

        if (view == null)
            $.totalStorage('tab_yamarket_fusion', 'market');
        else
            $('.nav-tabs li a[href="#' + view + '"]').click();

        if (location.hash && $('#market_tabs').find('a[href="' + location.hash + '"]').length)
            $('#market_tabs').find('a[href="' + location.hash + '"]').click();
        else
            $('#market_tabs a:first').tab('show');

        $('#market_tabs li').click(function() {
            var view = $(this).find('a:first').attr('href').replace('#', '');
            $.totalStorage('tab_yamarket_fusion', view);
            $('#setting-profiles-list').css('display', (~tabWithProfiles.indexOf(view) ? 'block' : 'none'));
            //location.hash = view
        });

        $('[data-action="maximize"]').on('click', function() {
            $($(this).data('target')).toggleClass('fixed-h400');
        });

        // Change setting profile
        $('#input-setting-profiles').change(function() {
            location = '<?= $module_url; ?>&profile_id=' + this.value;
        });

        $('#setting-profiles').on('click', '[data-action=remove-setting-profile]', function() {
            $(this).parents('tr').remove();
        });

        var settingProfilesLastIndex = <?php echo max(array_keys($setting_profiles)); ?>;

        $('#btn-add-setting-profile').click(function() {
            settingProfilesLastIndex++;

            var html = '<tr>' +
                '<td><input class="form-control" type="checkbox" disabled></td>' +
                '<td>' + settingProfilesLastIndex + '</td>' +
                '<td><input class="form-control input-sm" name="profiles[' + settingProfilesLastIndex + ']"></td>' +
                '<td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm" data-action="remove-setting-profile" data-toggle="tooltip" title="<?= $button_remove; ?>"><i class="fa fa-ban"></i></button>' +
                '</td>' +
                '</tr>';

            $('#setting-profiles tbody').append(html);
        });

        $('#setting-profiles').submit(function() {
            var $output = $(this).find('output[name=error]');

            $.ajax({
                url: this.action,
                type: 'post',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $output.empty();
                },
                success: function(json) {
                    if (json['success']) {
                        location.reload(true);
                    }

                    if (json['error']) {
                        json['error'].forEach(function(el) {
                            $output.append('<div class="alert alert-danger">' + el + '</div>');
                        });
                    }
                }
            })

            return false;
        });

        $('#btn-export-setting-profiles').click(function() {
            var data = $('#setting-profiles').serialize(),
                $btn = $(this);

            if (!data)
                return false

            $.ajax({
                url: '<?= $action_export_setting_profiles; ?>',
                method: 'POST',
                data: data,
                type: 'json',
                beforeSend: function() {
                    $btn.button('loading');
                },
                success: function(json) {
                    if (json.success) {
                        loadFile('yamarket_fusion_settings.json', JSON.stringify(json.export));
                    }
                },
                complete: function() {
                    $btn.button('reset');
                }
            });

            return false
        });

        $('#btn-import-setting-profiles').click(function() {
            $('#input-import-setting-profiles').click();
        });

        $('#input-import-setting-profiles').change(function() {
            if (!this.files.length) return false;

            var data = new FormData(),
                $btn = $('#btn-import-setting-profiles'),
                $output = $('#setting-profiles').find('output[name=error]');

            data.append('import', this.files[0]);

            $.ajax({
                url: '<?= $action_import_setting_profiles; ?>',
                method: 'POST',
                data: data,
                type: 'json',
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $btn.button('loading');
                    $output.empty();
                },
                success: function(json) {
                    if (json.success) {
                        location.reload();
                    } else if (json.error) {
                        json.error.forEach(function(el) {
                            $output.append('<div class="alert alert-danger">' + el + '</div>');
                        });
                    }
                },
                complete: function() {
                    $btn.button('reset');
                }
            })
        });

        // bugfix opencart form submiting
        $('#form-category, #form-market, #form-addon, #form-api-offers, #form-filter').submit(function() {
            e.stopPropagation();
        });
    });
</script>
<script>
    $(function UpdateInfo() {
        // $.ajax({
        //     url: 'https://opencartmodul.ru/service/marketplace/update.php?version=<?= $version_oc; ?>',
        //     success: function(html) {
        //         if (html == 'актуально') {
        //             console.log('Модуль yamarket_fusion актуален');
        //         } else {
        //             $('#update').html(html = '<div class="alert alert-info" id="update"><i class="fa fa-exclamation-circle"></i>Вышло обновление модуля, свяжитесь с разработчиком для установки последней версии ПО.<button type="button" class="close" data-dismiss="alert"><span class="pe-7s-close"></span></button></div>');
        //         }
        //     }
        // })
    });
</script>
<script>
    setTimeout(function() {
        let alert = document.getElementById('alert alert-success');
        if (alert) alert.style.display = 'none';
    }, 5000);
</script>
<script>
    setTimeout(function() {
        let alert = document.getElementById('alert alert-danger alert-dismissible');
        if (alert) alert.style.display = 'none';
    }, 5000);
</script>