<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">

      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div id="settings-not-save" style="display:none">
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Настройки не сохранены!
      </div>
    </div>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-1" data-toggle="tab">Синхронизация Мой Склад &#8658; Opencart</a></li>
          <li><a href="#tab-2" data-toggle="tab">Синхронизация Opencart &#8658; Мой Склад</a></li>
          <li><a href="#tab-3" data-toggle="tab">Настройки</a></li>
          <li><a href="#tab-4" data-toggle="tab">Лог</a></li>
        </ul>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
        <div class="tab-content">

          <div class="tab-pane active" id="tab-1">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация категорий</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;">Синхронизировать только часть категорий: </label>
                  <div class="col-sm-3">
                    <select name="syncms_cat_offset" id="cat-offset" class="form-control offset">
                      <?php foreach ($cat_offset as $key => $value) { ?>
                        <?php if ($key != $cat_offset_key) { ?>
                          <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-6 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Работает только для запуска по кнопкам. Можно синхронизировать не все категории разом, а по 1000 штук за раз. Эта настройка сохраняется автоматически при изменении.
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $category_add_href; ?>" style="width: 100%" id="category-add" class="btn btn-primary" type="submit" form = "form-module">Добавить категории в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос категорий (групп) из Моего Склада в Opencart. Переносятся следующие данные: Наименование, Родительская категория. Можно включить заполнение следующих полей: URL категории, Meta-title, Meta-description, Meta-keyword.
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $category_update_href; ?>" style="width: 100%" id="category-update" class="btn btn-primary" type="submit">Обновить категории в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление данных у категорий в Opencart в соответствии с группами в Моем Складе. Обновляются следующие данные категорий: Наименование, Родительская категория, Meta-title, Meta-description, Meta-keyword.
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $absence_category_href; ?>" style="width: 100%" id="absence-category" class="btn btn-primary" type="submit">Удалить лишние категории Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Удаление категорий в Opencart, которые не были найдены в Моем Складе (поиск осуществляется исходя из выбранного вида связывания категорий).
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация товаров</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать только часть товаров: </label>
                  <div class="col-sm-3">
                    <select name="syncms_product_offset" id="product-offset" class="form-control offset">
                      <?php foreach ($product_offset as $key => $value) { ?>
                        <?php if ($key != $product_offset_key) { ?>
                          <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-6 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Работает только для запуска по кнопкам. Можно синхронизировать не все товары разом, а по 1000 штук за раз. Эта настройка сохраняется автоматически при изменении.
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать только часть комплектов: </label>
                  <div class="col-sm-3">
                    <select name="syncms_bundle_offset" id="bundle-offset" class="form-control offset">
                      <?php foreach ($bundle_offset as $key => $value) { ?>
                        <?php if ($key != $bundle_offset_key) { ?>
                          <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-6 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Работает только для запуска по кнопкам. Можно синхронизировать не все комплекты разом, а по 1000 штук за раз. Эта настройка сохраняется автоматически при изменении.
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать только часть модификаций: </label>
                  <div class="col-sm-3">
                    <select name="syncms_modif_offset" id="modif-offset" class="form-control offset">
                      <?php foreach ($modif_offset as $key => $value) { ?>
                        <?php if ($key != $modif_offset_key) { ?>
                          <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-6 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Работает только для запуска по кнопкам. Можно синхронизировать не все модификации разом, а по 1000 штук за раз. Эта настройка сохраняется автоматически при изменении.
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $product_add_href; ?>" style="width: 100%" id="product-add" class="btn btn-primary" type="submit">Добавить товары в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос товаров (комплектов) из Моего Склада в Opencart. Из Моего Склада переносятся только Товары и Комплекты, Услуги не переносятся. Комплекты переносятся как обычные товары. Переносятся следующие данные: Цена, Количество и поля, выбранные для синхронизации в настройках.
                  </div>
                </div>  
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $product_update_href; ?>" style="width: 100%" id="product-update" class="btn btn-primary" type="submit">Обновить товары в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление данных у товаров в Opencart в соответствии с товарами (комплектами) в Моем Складе. Обновляются поля, выбранные для синхронизации в настройках.
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $stock_update_href; ?>" style="width: 100%" id="stock-update" class="btn btn-primary" type="submit">Обновить остатки товаров в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление остатков у товаров в Opencart в соответствии с остатками товаров (комплектов) в Моем Складе. Остатки из Моего Склада берутся с учетом резерва, то есть из поля Доступно. Остатки Комплектов вычисляются на основании остатков Компонентов (товаров входящих в Комплект).
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $price_update_href; ?>" style="width: 100%" id="price-update" class="btn btn-primary" type="submit">Обновить цены товаров в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление цен у товаров в Opencart в соответствии с ценами товаров (комплектов) в Моем Складе.
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $absence_product_href; ?>" style="width: 100%" id="absence-products" class="btn btn-primary" type="submit">Удалить/обновить лишние<br>товары  Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Удаление или обновление товаров в Opencart, которые не были найдены в Моем Складе (поиск осуществляется исходя из выбранного вида связывания товаров).
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $image_sync_href; ?>" style="width: 100%" id="image-sync" class="btn btn-primary" type="submit">Добавить/обновить изображения<br>товаров в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос и обновление изображений у товаров Opencart в соответствии с изображениями товаров в Моем Складе. Синхронизируются все изображения товаров, а не только первые. Изображения скачиваются и хранятся в директории /image/catalog/demo/syncms
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $modification_sync_href; ?>" style="width: 100%" id="modification-sync" class="btn btn-primary" type="submit" >Добавить/обновить атрибуты и опции<br>товаров в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос или обновление опций и атрибутов товаров в Opencart в соответствии с модификациями и дополнительными полями товаров в Моем Складе. При обновлении происходит обновление цен и остатков опций и  значений атрибутов. Связывание опций и атрибутов с модификациями осуществляется только по наименованию.
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация заказов</h3>
              </div>
              <div class="panel-body">
                
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $order_update_oc_href; ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Обновить заказы в Opencart</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление статусов заказов в Opencart в соответствии со статусами заказов в Моем Складе. Если статус заказа из Моего Склада будет отсутствовать в Opencart, то он будет создан.
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Cron (Планировщик заданий)</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <div class="col-sm-12">
                    <label class="control-label" style="text-align: left;">Планировщик Cron позволяет выполнять все вышеперечисленные действия автоматически в определенное время. В случае использования Cron синхронизироваться будут все товары и категории. Рекомендуется не ставить выполнение большого количества команд в одно и то же время. Ниже перечислены команды для каждого действия, которые необходимо внести в Cron:<br>
                    <br>Добавление категорий в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_category_add; ?></i>
                    <br>Обновление категорий в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_category_update; ?></i>
                    <br>Удаление лишних категорий Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_sync_absence_category; ?></i>
                    <br>Добавление товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_product_add; ?></i>
                    <br>Обновление товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_product_update; ?></i>
                    <br>Обновление остатков товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_stock_update; ?></i>
                    <br>Обновление цен товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_price_update; ?></i>
                    <br>Удаление/обновление лишних товаров Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_sync_absence_products; ?></i>
                    <br>Добавление/обновление изображений товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_image_sync; ?></i>
                    <br>Добавление/обновление атрибутов и опций товаров в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_modification_sync; ?></i>
                    <br>Обновление заказов в Opencart: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_order_update_oc; ?></i>
                    </label>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <div class="tab-pane" id="tab-2">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация заказов</h3>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $order_add_href; ?>" style="width: 100%" id="order-add" class="btn btn-primary" type="submit" >Добавить заказы в Мой Склад</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос заказов из Opencart в Мой Склад. Если в заказе Opencart будут присутствовать товары, которые не будут найдены в Моем Складе, то этот заказ не будет добавлен в Мой Склад.
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $order_update_href; ?>" style="width: 100%" id="order-update" class="btn btn-primary" type="submit" >Обновить заказы в Моем Складе</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление статусов заказов в Моем Складе в соответствии со статусами заказов в Opencart. Если статус заказа из Opencart будет отсутствовать в Моем Складе, то он будет создан.
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация категорий</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $category_add_ms_href; ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Добавить категории в Мой Склад</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос категорий из Opencart в Мой Склад. Переносятся следующие данные: Наименование, Родительская категория
                  </div>
                </div>
                
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация товаров</h3>
              </div>
              <div class="panel-body">
                
                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;">Добавить товары с порядковыми номерами: </label>
                  <div class="col-sm-3">
                    <select name="syncms_product_sql_offset" id="product_sql-offset" class="form-control offset">
                      <?php foreach ($product_sql_offset as $key => $value) { ?>
                        <?php if ($key != $product_sql_offset_key) { ?>
                          <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $product_add_ms_href; ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Добавить товары в Мой Склад</a>
                  </div>
                  <div class="col-sm-9 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Перенос товаров из Opencart в Мой Склад. Переносятся данные, выбранные для выгрузки.
                  </div>
                </div>
                
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Cron (Планировщик заданий)</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <div class="col-sm-12">
                    <label class="control-label" style="text-align: left;">Планировщик Cron позволяет выполнять все вышеперечисленные действия автоматически в определенное время. Рекомендуется не ставить выполнение большого количества команд в одно и то же время. Ниже перечислены команды для каждого действия, которые необходимо внести в Cron:<br>
                    <br>Добавление заказов в Мой Склад: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_order_add; ?></i>
                    <br>Обновление заказов в Мой Склад: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_order_update; ?></i>
                    <br>Добавление категорий в Мой Склад: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_category_add_ms; ?></i>
                    <br>Добавление товаров в Мой Склад: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_product_add_ms; ?></i>
                    </label>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <div class="tab-pane" id="tab-3">

            <ul class="nav nav-tabs">
              <li class="active"><a href="#setting-tab-1" data-toggle="tab">Общие</a></li>
              <li><a href="#setting-tab-2" data-toggle="tab">Синхронизация Мой Склад &#8658; Opencart</a></li>
              <li><a href="#setting-tab-3" data-toggle="tab">Синхронизация Opencart &#8658; Мой Склад</a></li>
            </ul>

            <div class="tab-content">

              <div class="tab-pane active" id="setting-tab-1">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Основные настройки</h3>
                  </div>
                  <div class="panel-body">

                    <input name="syncms_admin_url" type="text" id="admin-url" class="form-control hide" value="<?php echo $admin_url; ?>">
                    
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" ><?php echo $entry_status; ?></label>
                      <div class="col-sm-2">    
                        <select name="syncms_status" id="module-status" class="form-control">
                          <?php if ($status) { ?>
                          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                          <?php } else { ?>
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Логин в Моем Складе</label>
                      <div class="col-sm-3">
                        <input name="syncms_login" type="text" id="login" class="form-control" value="<?php echo $login; ?>">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Пароль в Моем Складе</label>
                      <div class="col-sm-3">
                        <input name="syncms_password" type="password" id="password" class="form-control" value="<?php echo $password; ?>">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Токен из Моего Склада</label>
                      <div class="col-sm-3">
                        <input name="syncms_token" type="password" id="token" class="form-control" value="<?php echo $token; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info mt-1">
                        <i class="fa fa-info-circle"></i>
                        Вместо логина и пароля можно использовать токен доступа к JSON API (можно создать в настройках Моего Склада).
                      </div>
                    </div>
                  </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация категорий</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Связывать категории по:</label>
                      <div class="col-sm-3">
                        <select name="syncms_category_binding" id="product-binding" class="form-control">
                          <?php if ($category_binding == 'ms_id') { ?>
                            <option value="name">Наименованиям</option>
                            <option value="ms_id" selected="selected">ID из Моего Склада</option>
                          <?php } else { ?>
                            <option value="name" selected="selected">Наименованиям</option>
                            <option value="ms_id">ID из Моего Склада</option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        При выборе "ID из Моего Склада" категории добавленные не через модуль синхронизироваться не будут!
                      </div>
                    </div>
                  </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация товаров</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Связывать товары по:</label>
                      <div class="col-sm-3">
                        <select name="syncms_binding" id="product-binding" class="form-control">
                          <?php if ($binding == 'model_article') { ?>
                            <option value="model_code">Модель OC - Код МС</option>
                            <option value="model_externalCode">Модель OC - Внешний код МС</option>
                            <option value="model_article" selected="selected">Модель OC - Артикул МС</option>
                            <option value="sku_article">SKU OC - Артикул МС</option>
                            <option value="sku_code">SKU OC - Код МС</option>
                            <option value="sku_externalCode">SKU OC - Внешний код МС</option>
                          <?php } elseif ($binding == 'model_externalCode') { ?>
                            <option value="model_code">Модель OC - Код МС</option>
                            <option value="model_externalCode" selected="selected">Модель OC - Внешний код МС</option>
                            <option value="model_article">Модель OC - Артикул МС</option>
                            <option value="sku_article">SKU OC - Артикул МС</option>
                            <option value="sku_code">SKU OC - Код МС</option>
                            <option value="sku_externalCode">SKU OC - Внешний код МС</option>
                          <?php } elseif ($binding == 'sku_article') { ?>
                            <option value="model_code">Модель OC - Код МС</option>
                            <option value="model_externalCode">Модель OC - Внешний код МС</option>
                            <option value="model_article">Модель OC - OC Артикул МС</option>
                            <option value="sku_article" selected="selected">SKU OC - Артикул МС</option>
                            <option value="sku_code">SKU OC - Код МС</option>
                            <option value="sku_externalCode">SKU OC - Внешний код МС</option>
                          <?php } elseif ($binding == 'sku_code') { ?>
                            <option value="model_code">Модель OC - Код МС</option>
                            <option value="model_externalCode">Модель OC - Внешний код МС</option>
                            <option value="model_article">Модель OC - Артикул МС</option>
                            <option value="sku_article">SKU OC - Артикул МС</option>
                            <option value="sku_code" selected="selected">SKU OC - Код МС</option>
                            <option value="sku_externalCode">SKU OC - Внешний код МС</option>
                          <?php } elseif ($binding == 'sku_externalCode') { ?>
                            <option value="model_code">Модель OC - Код МС</option>
                            <option value="model_externalCode">Модель OC - Внешний код МС</option>
                            <option value="model_article">Модель OC - Артикул МС</option>
                            <option value="sku_article">SKU OC - Артикул МС</option>
                            <option value="sku_code">SKU OC - Код МС</option>
                            <option value="sku_externalCode" selected="selected">SKU OC - Внешний код МС</option>
                          <?php } else { ?>
                            <option value="model_code" selected="selected">Модель OC - Код МС</option>
                            <option value="model_externalCode">Модель OC - Внешний код МС</option>
                            <option value="model_article">Модель OC - Артикул МС</option>
                            <option value="sku_article">SKU OC - Артикул МС</option>
                            <option value="sku_code">SKU OC - Код МС</option>
                            <option value="sku_externalCode">SKU OC - Внешний код МС</option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Дополнительно связывать по наименованию:</label>
                      <div class="col-sm-2">
                        <?php if ($binding_name == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_binding_name" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_binding_name" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_binding_name" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_binding_name" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Необходимо включить если выбранный параметр связки товаров (код/артикул/внешний код) не является уникальным в Моем Складе! При включении дополнительной связки у товаров не будет обновляться наименование и URL, вместо этого будут создаваться новые товары!
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Название доп. поля с производителями</label>
                      <div class="col-sm-3">
                          <input placeholder="Производитель" name="syncms_manufacturer" type="text" id="manufacturer" class="form-control" value="<?php echo $manufacturer; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          В это поле нужно вписать название (без кавычек) дополнительного поля с типом "Текст" или "Справочник" в Моем Складе, в которое занесены производители товара.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Цена продажи</label>
                      <div class="col-sm-3">
                        <select name="syncms_sale_price" id="sale-price" class="form-control">
                          <?php foreach ($sale_prices as $key => $value) { ?>
                            <?php if ($key != $sale_price_key) { ?>
                              <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация заказов</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Связывать заказы по:</label>
                      <div class="col-sm-3">
                        <select name="syncms_order_binding" id="order-binding" class="form-control">
                          <?php if ($order_binding == 'number') { ?>
                            <option value="number" selected="selected">№ заказа ОС - № заказа МС</option>
                            <option value="comment">№ заказа ОС - Комментарий МС</option>
                          <?php } else { ?>
                            <option value="number">№ заказа ОС - № заказа МС</option>
                            <option value="comment" selected="selected">№ заказа ОС - Комментарий МС</option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Если выбрана связь через комментарий, то в комментарий заказа можно дописывать текст через пробел или перенос строки после номера заказа. Н-р: OC №154 Текст или OC №154[перенос строки]Текст
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать заказы, созданные за последние дни:</label>
                      <div class="col-sm-2">
                          <input name="syncms_order_day_limit" type="number" id="order-date-limit" class="form-control" placeholder="Кол-во дней" value="<?php echo $order_day_limit; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Например, если указано 5 дней, то будут синхронизироваться заказы созданные за последние 5 дней. Оставьте это поле пустым, чтобы синхронизирвоать все заказы
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Префикс заказов</label>
                      <div class="col-sm-2">
                          <input name="syncms_order_prefix" type="text" id="order-prefix" class="form-control" value="<?php echo $order_prefix; ?>">
                      </div>
                      <div class="col-sm-8 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Вы можете дополнить номер заказа, который будет передан в номер заказа/комментарий к заказу в Моем Складе префиксом. Уникальный префикс ускоряет синхронизацию заказов.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Двусторонняя синхронизация статусов заказов:</label>
                      <div class="col-sm-2">
                        <?php if ($two_side_status == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_two_side_status" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_two_side_status" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_two_side_status" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_two_side_status" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        При включении будет происходить проверка по дате изменения статуса. Если статус был изменен в МойСклад, то он будет передан в Opencart. Если статус был изменен в Opencart, то он будет передан в МойСклад.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="setting-tab-2">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация категорий</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Генерировать URL для категорий при добавлении</label>
                      <div class="col-sm-2">
                        <?php if ($cat_url_update == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_cat_url_update" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_cat_url_update" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_cat_url_update" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_cat_url_update" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        URL генерируется из наименований категорий. Если у нескольких категорий одинаковые наименования, то в URL будет добавлен ID категории из Opencart.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать категории, измененные в МойСклад за последние минуты:</label>
                      <div class="col-sm-2">
                          <input name="syncms_category_time_limit" type="number" id="order-date-limit" class="form-control" placeholder="Кол-во минут" value="<?php echo $category_time_limit; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Например, если указано 5, то будут синхронизироваться только категории, которые были изменены в МойСклад за последние 5 минут. Оставьте это поле пустым, чтобы синхронизирвоать все категории.
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация товаров</h3>
                  </div>
                  <div class="panel-body">

                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Генерировать URL для товаров при добавлении</label>
                      <div class="col-sm-2">
                        <?php if ($url_update == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_url_update" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_url_update" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_url_update" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_url_update" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        URL генерируется из наименований товаров. Если у нескольких товаров одинаковые наименования, то в URL будет добавлен ID товара из Opencart.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;">Синхронизировать следующие поля товаров:</label>
                      <div class="col-sm-4">
                        <div class="well well-sm" style="height: auto; overflow: auto;">
                            <div class="checkbox">
                              <?php if ($name_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_name_update" checked="checked"> Наименование
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_name_update"> Наименование
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($desc_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_desc_update" checked="checked"> Описание
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_desc_update"> Описание
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($cat_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_cat_update" checked="checked"> Категория
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_cat_update"> Категория
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($sku_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_sku_update" checked="checked"> Артикул (SKU)
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_sku_update"> Артикул (SKU)
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($weight_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_weight_update" checked="checked"> Вес
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_weight_update"> Вес
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($manufacturer_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_manufacturer_update" checked="checked"> Производитель (нужно доп. поле в МС)
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_manufacturer_update"> Производитель (нужно доп. поле в МС)
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($stock_status_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_stock_status_update" checked="checked"> Статус при отсутствии на складе
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_stock_status_update"> Статус при отсутствии на складе
                                </label>
                              <?php } ?> 
                            </div>
                            <div class="checkbox">
                              <?php if ($subtract_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_subtract_update" checked="checked"> Вычитать со склада
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_subtract_update"> Вычитать со склада
                                </label>
                              <?php } ?> 
                            </div>
                            <div class="checkbox">
                              <?php if ($ean_update == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_ean_update" checked="checked"> EAN
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_ean_update"> EAN
                                </label>
                              <?php } ?> 
                            </div>
                         </div>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        При добавлении товаров отключенным полям будут присваиваться значения по умолчанию.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Единица измерения веса:</label>
                      <div class="col-sm-3">
                        <select name="syncms_weight_unity" id="stock-status" class="form-control">
                          <?php foreach ($weight_unity as $key => $value) { ?>
                            <?php if ($key != $weight_unity_key) { ?>
                              <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;">Выгружать остатки из:</label>
                      <div class="col-sm-4">
                        <div class="well well-sm" style="height: 150px; overflow: auto;">
                          <?php foreach ($stock_store as $key => $value) { ?>
                            <div class="checkbox">
                            <?php if (isset($stock_store_key) && in_array($key, $stock_store_key)) { ?>
                              <label>
                                <input type="checkbox" name="syncms_stock_store[]" checked="checked" value="<?php echo $key; ?>"> <?php echo $value; ?>
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_stock_store[]" value="<?php echo $key; ?>"> <?php echo $value; ?>
                              </label>
                            <?php } ?>
                            </div>
                          <?php } ?>

                         </div>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Если ни один склад не выбран, то остатки будут выгружаться из всех складов
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Статус товаров при отсутствии на складе:</label>
                      <div class="col-sm-3">
                        <select name="syncms_stock_status" id="stock-status" class="form-control">
                          <?php foreach ($stock_status as $key => $value) { ?>
                            <?php if ($key != $stock_status_key) { ?>
                              <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Синхронизировать товары и категории из групп:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Название группы1; Название группы2; Название группы3" class="form-control" name="syncms_from_group" rows="3"><?php echo $from_group; ?></textarea>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Товары и категории также будут синхронизироваться из подгрупп указанных групп! Разделитель категорий - точка с запятой. Оставьте это поле пустым, чтобы синхронизировать товары и категории со всех групп.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Не синхронизировать товары и категории из групп:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Название группы1; Название группы2; Название группы3" class="form-control" name="syncms_not_from_group" rows="3"><?php echo $not_from_group; ?></textarea>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Товары и категории также не будут синхронизироваться из подгрупп указанных групп! Разделитель категорий - точка с запятой. Оставьте это поле пустым, чтобы синхронизировать товары и категории со всех групп.
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Добавлять товар в его родительские категории</label>
                      <div class="col-sm-2">
                        <?php if ($prod_parent_cat == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_prod_parent_cat" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_prod_parent_cat" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_prod_parent_cat" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_prod_parent_cat" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        По умолчанию товар добавляется только в одну категорию - та, в которой он находится в Моем Складе. При включении товар также будет добавляться во все свои родительские категории.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Вычитать со склада</label>
                      <div class="col-sm-3">
                        <select name="syncms_subtract" id="subtract" class="form-control">
                            <?php if ($subtract == "0") { ?>
                              <option value="1">Да</option>
                              <option value="0" selected="selected">Нет</option>
                            <?php } else { ?>
                              <option value="1" selected="selected">Да</option>
                              <option value="0">Нет</option>
                            <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Отдельные цены для групп клиентов:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Группа клиентов1 - Цена1; Группа клиентов2 - Цена2; Группа клиентов3 - Цена3" class="form-control" name="syncms_client_price" rows="3"><?php echo $client_price; ?></textarea>
                      </div>
                      <div class="col-sm-5 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Формат: [Название группы клиентов] - [Название цены]. Разделить нескольких значений - точка с запятой ";"
                      </div>
                    </div> 

                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Цены по акции для групп клиентов:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Группа клиентов1 - Цена по акции 1; Группа клиентов2 - Цена по акции 2; Группа клиентов3 - Цена по акции 3" class="form-control" name="syncms_special_price" rows="3"><?php echo $special_price; ?></textarea>
                      </div>
                      <div class="col-sm-5 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Формат: [Название группы клиентов] - [Название цены по акции]. Разделить нескольких значений - точка с запятой ";". Если цена по акции равна 0, то акция не будет добавляться, а в случае если акция уже создана она удалится. 
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать товары с незаполненным полем для связи</label>
                      <div class="col-sm-2">
                        <?php if ($empty_field == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_empty_field" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_empty_field" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_empty_field" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_empty_field" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        По умолчанию товары, у которых не заполнено поле для связи в Моем Складе (Код/Внешний код/Артикул) не будут синхронизироваться.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Не добавлять товары с нулевым остатком</label>
                      <div class="col-sm-2">
                        <?php if ($add_out_of_stock == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_add_out_of_stock" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_add_out_of_stock" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_add_out_of_stock" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_add_out_of_stock" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Название доп. поля для выгрузки определенных товаров</label>
                      <div class="col-sm-3">
                          <input placeholder="Выгружать на сайт" name="syncms_certain_products" type="text" id="certain_products" class="form-control" value="<?php echo $certain_products; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          В это поле нужно вписать название (без кавычек) дополнительного поля с типом "Флажок". Товар будет добавляться на сайт только, если будет стоять галочка в указанном поле. По умолчанию добавляются все товары.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать товары, измененные в МойСклад за последние минуты:</label>
                      <div class="col-sm-2">
                          <input name="syncms_product_time_limit" type="number" id="order-date-limit" class="form-control" placeholder="Кол-во минут" value="<?php echo $product_time_limit; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Например, если указано 5, то будут синхронизироваться только товары, которые были изменены в МойСклад за последние 5 минут. Не работает с обновлением остатков товаров. Оставьте это поле пустым, чтобы синхронизирвоать все товары.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Что делать с отсутствующими в Моем Складе товарами:</label>
                      <div class="col-sm-3">
                        <select name="syncms_absence_products" id="absence-setting" class="form-control">
                          <?php if ($absence_products == "0") { ?>
                            <option value="0" selected="selected">Удалять</option>
                            <option value="1">Обнулять остатки</option>
                            <option value="2">Обнулять остатки и обновлять статус</option>
                          <?php } elseif ($absence_products == "1") { ?>
                            <option value="0">Удалять</option>
                            <option value="1" selected="selected">Обнулять остатки</option>
                            <option value="2">Обнулять остатки и обновлять статус</option>
                          <?php } else { ?>
                            <option value="0">Удалять</option>
                            <option value="1">Обнулять остатки</option>
                            <option value="2" selected="selected">Обнулять остатки и обновлять статус</option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Отстутствующие товары - товары, которые не нашлись в Моем Складе. "Обновлять статус товара" означает менять статусы товаров на "Нет в наличии" вместе с обнулением остатков товаров.
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация изображений</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Удалять изображения у товаров на сайте, если они отсутствуют в Моем Складе</label>
                      <div class="col-sm-2">
                        <?php if ($delete_img == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_delete_img" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_delete_img" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_delete_img" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_delete_img" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Если выбрано "Нет", то изображения, которые есть у товаров в Opencart, но отсутствуют в МС удалятся не будут!
                      </div>
                    </div>
                  </div> 
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация опций и атрибутов</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Присваивать товарам суммы остатков их опций:</label>

                        <div class="col-sm-3">
                          <?php if ($sum_option == "1") { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_sum_option" value="1" checked="checked"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_sum_option" value="0"> Нет
                            </label>
                          <?php } else { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_sum_option" value="1"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_sum_option" value="0" checked="checked"> Нет
                            </label>
                          <?php } ?>   
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Рассчитывать цену опций как разницу между ценой модификации и ценой товара:</label>

                        <div class="col-sm-3">
                          <?php if ($diff_option_price == "1") { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_diff_option_price" value="1" checked="checked"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_diff_option_price" value="0"> Нет
                            </label>
                          <?php } else { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_diff_option_price" value="1"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_diff_option_price" value="0" checked="checked"> Нет
                            </label>
                          <?php } ?>   
                        </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Названия дополнительных полей с атрибутами:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Атрибут1; Атрибут2; Атрибут3" class="form-control" name="syncms_attr_from_fields" rows="3"><?php echo $attr_from_fields; ?></textarea>
                      </div>
                      <div class="col-sm-5 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Указанные дополнительные поля (через "; ") будут заноситься как атрибуты товаров при синхрониации опций/атрибутов.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Выгружать следующие характеристики модификаций как атрибуты:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Атрибут1; Атрибут2; Атрибут3" class="form-control" name="syncms_attr_from_modif" rows="3"><?php echo $attr_from_modif; ?></textarea>
                      </div>
                      <div class="col-sm-5 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Указанные характеристики (через "; ") будут заноситься как атрибуты товаров, а не как опции при синхрониации опций/атрибутов.
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Название группы арибутов:</label>
                      <div class="col-sm-3">
                          <input placeholder="Характеристики" name="syncms_attr_group" type="text" id="manufacturer" class="form-control" value="<?php echo $attr_group; ?>">
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Атрибуты будут добавляться в группу названием, которое указано в этом поле. Название группы по умолчанию - "Характеристики".
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Вычитать со склада</label>
                      <div class="col-sm-3">
                        <select name="syncms_subtract_option" id="subtract_option" class="form-control">
                            <?php if ($subtract_option == "0") { ?>
                              <option value="1">Да</option>
                              <option value="0" selected="selected">Нет</option>
                            <?php } else { ?>
                              <option value="1" selected="selected">Да</option>
                              <option value="0">Нет</option>
                            <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Удалять у товаров опции, которых нет в МойСклад:</label>
                        <div class="col-sm-3">
                          <?php if ($delete_option == "1") { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_option" value="1" checked="checked"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_option" value="0"> Нет
                            </label>
                          <?php } else { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_option" value="1"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_option" value="0" checked="checked"> Нет
                            </label>
                          <?php } ?>   
                        </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Удалять у товаров атрибуты, которых нет в МойСклад:</label>
                        <div class="col-sm-3">
                          <?php if ($delete_attribute == "1") { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_attribute" value="1" checked="checked"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_attribute" value="0"> Нет
                            </label>
                          <?php } else { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_attribute" value="1"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_delete_attribute" value="0" checked="checked"> Нет
                            </label>
                          <?php } ?>   
                        </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" style="text-align: left;" >Выгружать каждую модификацию товара как одно значение опции:</label>
                        <div class="col-sm-2">
                          <?php if ($group_modification == "1") { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_group_modification" value="1" checked="checked"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_group_modification" value="0"> Нет
                            </label>
                          <?php } else { ?>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_group_modification" value="1"> Да
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="syncms_group_modification" value="0" checked="checked"> Нет
                            </label>
                          <?php } ?>   
                        </div>
                        <div class="col-sm-7 alert alert-info">
                          <i class="fa fa-info-circle"></i>
                            В Opencart у каждого товара будет создана только одна опция с названием "Опции", а в качестве значений этой опции через запятую будут перчислены характеристики каждой модификации в формате [Название]: [Значение]. Одна модификация = Одно значение опции.
                        </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Тип создаваемых опций</label>
                      <div class="col-sm-3">
                        <select name="syncms_option_type" id="option_type" class="form-control">
                            <?php if ($option_type == "select") { ?>
                              <option value="select" selected="selected">Список</option>
                              <option value="checkbox">Флажок</option>
                              <option value="radio" selected="selected">Переключатель</option>
                            <?php } elseif ($option_type == "checkbox") { ?>
                              <option value="select">Список</option>
                              <option value="checkbox" selected="selected">Флажок</option>
                              <option value="radio">Переключатель</option>
                            <?php } else { ?>
                              <option value="select">Список</option>
                              <option value="checkbox">Флажок</option>
                              <option value="radio" selected="selected">Переключатель</option>
                            <?php } ?>
                        </select>
                      </div>
                    </div>

                  </div> 
                  
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Meta-теги товаров</h3>
                  </div>
                  <div class="panel-body">
                  <div class="form-group">
                  <div class="col-sm-12 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                      Доступные переменные: [name] - наименование товара, [price] - цена товара
                  </div>
                    <label class="col-sm-2 control-label" style="text-align: left;" >Обновлять мета-теги товаров:</label>

                      <div class="col-sm-3">
                        <?php if ($meta_prod_update == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_prod_update" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_prod_update" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_prod_update" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_prod_update" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег title:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_title" rows="2"><?php echo $prod_meta_title; ?></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег description:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_desc" rows="3"><?php echo $prod_meta_desc; ?></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег keywords:</label>
                      <div class="col-sm-4">
                       <textarea placeholder="купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_keyword" rows="2"><?php echo $prod_meta_keyword; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Meta-теги категорий</h3>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <div class="col-sm-12 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                          Доступные переменные: [name] - наименование категории
                      </div>
                      <label class="col-sm-2 control-label" style="text-align: left;" >Обновлять мета-теги категорий:</label>

                      <div class="col-sm-3">
                        <?php if ($meta_cat_update == "1") { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_cat_update" value="1" checked="checked"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_cat_update" value="0"> Нет
                          </label>
                        <?php } else { ?>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_cat_update" value="1"> Да
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="syncms_meta_cat_update" value="0" checked="checked"> Нет
                          </label>
                        <?php } ?>   
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег title:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_title" rows="2"><?php echo $cat_meta_title; ?></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег description:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_desc" rows="3"><?php echo $cat_meta_desc; ?></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег keywords:</label>
                      <div class="col-sm-4">
                        <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_keyword" rows="2"><?php echo $cat_meta_keyword; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="setting-tab-3">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Синхронизация заказов</h3>
                  </div>
                  <div class="panel-body">

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Организация</label>
                    <div class="col-sm-3">
                      <select name="syncms_organization" id="organization" class="form-control">
                        <?php foreach ($organization as $key => $value) { ?>
                          <?php if ($key != $organization_key) { ?>
                            <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                          <?php } else { ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-7 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      Организация, которая будет указана в заказах.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Склад</label>
                    <div class="col-sm-3">
                      <select name="syncms_store" id="store" class="form-control">
                        <?php foreach ($store as $key => $value) { ?>
                          <?php if ($key != $store_key) { ?>
                            <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                          <?php } else { ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-7 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      Склад, который будет указан в заказах.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Канал продаж</label>
                    <div class="col-sm-3">
                      <select name="syncms_saleschannel" id="saleschannel" class="form-control">
                        <?php foreach ($saleschannel as $key => $value) { ?>
                          <?php if ($key != $saleschannel_key) { ?>
                            <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                          <?php } else { ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-7 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      Канал продаж, который будет указан в заказах.
                    </div>
                  </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >НДС в процентах:</label>
                      <div class="col-sm-2">
                          <input name="syncms_nds" type="number" id="nds" class="form-control" placeholder="20" value="<?php echo $nds; ?>">
                      </div>
                      <div class="col-sm-8 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        При добавлении заказа в Мой Склад введенное значение НДС будет указано для каждого товара в заказе. Если это поле не заполнено, то значение НДС будет "без НДС".
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Тип контрагента</label>
                      <div class="col-sm-3">
                        <select name="syncms_agent_type" id="agent-type" class="form-control">
                          <?php if ($agent_type == 'legal') { ?>
                            <option value="legal" selected="selected">Юридическое лицо</option>
                            <option value="entrepreneur">Индивидуальный предприниматель</option>
                            <option value="individual">Физическое лицо</option>
                          <?php } elseif ($agent_type == 'entrepreneur') { ?>
                            <option value="legal" selected="selected">Юридическое лицо</option>
                            <option value="entrepreneur" selected="selected">Индивидуальный предприниматель</option>
                            <option value="individual">Физическое лицо</option>
                          <?php } else { ?>
                            <option value="legal">Юридическое лицо</option>
                            <option value="entrepreneur">Индивидуальный предприниматель</option>
                            <option value="individual" selected="selected">Физическое лицо</option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-sm-7 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        При создании контрагента будет указываться выбранный тип.
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Email по умолчанию (если не был заполнен):</label>
                      <div class="col-sm-4">
                        <textarea placeholder="localhost@localhost.com; empty@empty.ru" class="form-control" name="syncms_default_email" rows="3"><?php echo $default_email; ?></textarea>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        В данном поле нужно указать значения email, которые проставляются в случае, когда email не был заполнен покупателем, чтобы поиск и создание контрагента в Моем Складе работали правильно. Разделитель нескольких значений точка с запятой ";".
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;" >Связывать способы доставки по:</label>
                      <div class="col-sm-3">
                        <select name="syncms_shipping_binding" id="shipping-binding" class="form-control">
                          <?php if ($shipping_binding == 'name') { ?>
                            <option value="name" selected="selected">Название ОС - Название МС</option>
                            <option value="code">Код ОС - Код МС</option>
                          <?php } else { ?>
                            <option value="name">Название ОС - Название МС</option>
                            <option value="code" selected="selected">Код ОС - Код МС</option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label" style="text-align: left;">При добавлении заказов:</label>
                      <div class="col-sm-4">
                        <div class="well well-sm" style="height: auto; overflow: auto;">
                            <div class="checkbox">
                              <?php if ($product_reserve == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_product_reserve" checked="checked"> Ставить товары в резерв
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_product_reserve"> Ставить товары в резерв
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($shipping_add == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_shipping_add" checked="checked"> Добавлять услугу доставки
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_shipping_add"> Добавлять услугу доставки
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($conduct_order == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_conduct_order" checked="checked"> Проводить заказ
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_conduct_order"> Проводить заказ
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($quick_add == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_quick_add" checked="checked"> Использовать оперативный режим
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_quick_add"> Использовать оперативный режим
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($comment_info == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_comment_info" checked="checked"> Выводить информацию в комментарий к заказу
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_comment_info"> Выводить информацию в комментарий к заказу
                                </label>
                              <?php } ?>
                            </div>
                            <div class="checkbox">
                              <?php if ($not_skip_order == 'on') { ?>
                                <label>
                                  <input type="checkbox" name="syncms_not_skip_order" checked="checked"> Выгружать заказ, если товар из заказа не был найден в МойСклад
                                </label>
                              <?php } else { ?>
                                <label>
                                  <input type="checkbox" name="syncms_not_skip_order"> Выгружать заказ, если товар из заказа не был найден в МойСклад
                                </label>
                              <?php } ?>
                            </div>
                         </div>
                      </div>
                      <div class="col-sm-6 alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Оперативный режим ускоряет добавление заказов в случае если заказы выгружаются регулярно в небольших количествах. Если необходимо за раз перенести большое количество заказов, то оперативный режим замедлит выгрузку.
                        <br><i class="fa fa-info-circle"></i>
                        В комментарий к заказу будет выводиться следующая информация: номер заказа, имя, телефон, email, адрес, способ доставки, способ оплаты, комментарий.
                        <br><i class="fa fa-info-circle"></i>
                        Не найденные в МойСклад товары будут выводиться в комментарий заказа в МойСклад.
                      </div>
                    </div>

                  </div>
                </div>
                <div class="panel-heading">
                  <h3 class="panel-title">Синхронизация товаров</h3>
                </div>
                <div class="panel-body">
                  
                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;">Выгружать следующие данные товаров:</label>
                    <div class="col-sm-4">
                      <div class="well well-sm" style="height: auto; overflow: auto;">
                          <div class="checkbox">
                            <?php if ($desc_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_desc_update_ms" checked="checked"> Описание
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_desc_update_ms"> Описание
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($weight_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_weight_update_ms" checked="checked"> Вес
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_weight_update_ms"> Вес
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($unit_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_unit_update_ms" checked="checked"> Единица измерения
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_unit_update_ms"> Единица измерения
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($article_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_article_update_ms" checked="checked"> Артикул
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_article_update_ms"> Артикул
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($stock_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_stock_update_ms" checked="checked"> Остаток (нужно указать внешний код поставщика)
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_stock_update_ms"> Остаток (нужно указать внешний код поставщика)
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($cat_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_cat_update_ms" checked="checked"> Категория (группа)
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_cat_update_ms"> Категория (группа)
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($manufacturer_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_manufacturer_update_ms" checked="checked"> Производитель (нужно доп. поле в МС)
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_manufacturer_update_ms"> Производитель (нужно доп. поле в МС)
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($image_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_image_update_ms" checked="checked"> Изображения
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_image_update_ms"> Изображения
                              </label>
                            <?php } ?>
                          </div>
                          <div class="checkbox">
                            <?php if ($modif_update_ms == 'on') { ?>
                              <label>
                                <input type="checkbox" name="syncms_modif_update_ms" checked="checked"> Опции (Модификации)
                              </label>
                            <?php } else { ?>
                              <label>
                                <input type="checkbox" name="syncms_modif_update_ms"> Опции (Модификации)
                              </label>
                            <?php } ?>
                          </div>
                       </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Внешний код поставщика</label>
                    <div class="col-sm-3">
                        <input name="syncms_supplier" type="text" id="supplier" class="form-control" value="<?php echo $supplier; ?>">
                    </div>
                    <div class="col-sm-7 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      Поставщик с указанным внешним кодом будет заноситься в приемки при добавлении товаров в Мой Склад. Необходимо указать для выгрузки остатков.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Размер изображений при выгрузке в МойСклад</label>
                    <div class="col-sm-2">
                        <input name="syncms_image_size_ms" type="text" placeholder="500x500" id="image-size-ms" class="form-control" value="<?php echo $image_size_ms; ?>">
                    </div>
                    <div class="col-sm-8 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      Формат: [длина]x[ширина]. Оставьте эти поля пустыми, чтобы выгружать изображения исходных размеров.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Единица измерения товаров</label>
                    <div class="col-sm-1">
                        <input name="syncms_uom" type="text" placeholder="шт" id="uom" class="form-control" value="<?php echo $uom; ?>">
                    </div>
                    <div class="col-sm-9 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                      При добавлении товаров в Мой Склад у товаров будет задана указанная единица измерения.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" style="text-align: left;" >Добавить товары из категории</label>
                    <div class="col-sm-3">
                        <input name="syncms_from_group_sql" type="text" placeholder="Название категории" id="uom" class="form-control" value="<?php echo $from_group_sql; ?>">
                    </div>
                    <div class="col-sm-7 alert alert-info">
                      <i class="fa fa-info-circle"></i>
                        Оставьте это поле пустым, чтобы добавлять товары со всех категорий.
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>  
          <div class="tab-pane" id="tab-4">
            <p><textarea readonly class="form-control" id="log" rows="20"><?php echo $log; ?></textarea></p>
            
            <a href="<?php echo $log_clear_href; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> Очистить</a>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<style>
@media (max-width: 768px) {
  #content .alert-info {
    margin-top: 10px;
  }
}
</style>
<script type="text/javascript">
  $('input').change(function () {
    $('#settings-not-save').slideDown();
    $('.alert-success').slideUp();
    modificationForm(this);
  })
  $('textarea').change(function () {
    $('#settings-not-save').slideDown();
    $('.alert-success').slideUp();
    modificationForm(this);
  })
  $('select').change(function () {
    if ($(this).hasClass('offset')) {
      let select = $(this);
      $.ajax({
        url: 'index.php?route=<?php echo $path; ?>/syncms/UpdateOffset&token=<?php echo $admin_token; ?>&key=' + $(this).attr('name') + '&value=' + $(this).val(),
        success: function() {
          select.after('<p class="offset-alert alert alert-success" style="margin-bottom: 0; padding: 5px 10px;"><i class="fa fa-check"></i> Настройка сохранена</p>');
          setTimeout(function(){
            $('.offset-alert').slideUp();
          }, 1000);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    } else {
      $('#settings-not-save').slideDown();
      $('.alert-success').slideUp();
      modificationForm(this);
    }
  })
  $('.panel-body a.btn').click(function(e) {
    if (!confirm("Вы уверены?"))
      e.preventDefault();
  });
</script>