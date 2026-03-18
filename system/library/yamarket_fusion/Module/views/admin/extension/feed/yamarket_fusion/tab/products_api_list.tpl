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
		<div id="aside-center">
			<div class="card" id="product_api_update">
            <div class="body-card">
				<!-- Start content-->
					<div class="panel-heading">
						<b>Обновленные по Api товары</b>
						<?php if ($updated_offers): ?> (список обновлен
						<?=$products_api_last_updated;?>)
						<?php endif; ?>
						<a class="btn btn-info btn-sm" href="<?=$url_get_updated_offers;?>">Обновить</a>
					</div>
					<div class="panel-body">
						<div class="well well-sm">
							<a href="#form-filter" data-toggle="collapse">ФИЛЬТР <span class="caret"></span></a>
							<form id="form-filter" class="collapse <?php echo (($filter_name || $filter_model) ? 'in' : '');?>" action="<?=$action_filter;?>" method="GET">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="input-filter-name">Название</label>
											<input class="form-control" type="text" name="filter_name" id="input-filter-name" value="<?=$filter_name;?>">
										</div>
										<div class="form-group">
											<label for="input-filter-status">Статус</label>
											<select class="form-control" name="filter_status">
												<option value=""><?=$text_select;?></option>
												<option value="1" <?= ($filter_status == '1' ? 'selected' : '');?>><?=$text_enabled;?></option>
												<option value="0" <?= ($filter_status == '0' ? 'selected' : '');?>><?=$text_disabled;?></option>
											</select>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="input-filter-model">Модель</label>
											<input class="form-control" type="text" name="filter_model" id="input-filter-model" value="<?=$filter_model;?>">
										</div>
										<div class="form-group">
											<label for="input-filter-model">Количество</label>
											<div class="input-group flex">
												<select name="filter_quantity_sign" class="form-control">
													<option value="<" <?= ($filter_quantity_sign == '<' ? 'selected' : '')?>>меньше</option>
													<option value=">" <?= ($filter_quantity_sign == '>' ? 'selected' : '')?>>больше</option>
													<option value="=" <?= ($filter_quantity_sign == '=' ? 'selected' : '')?>>равно</option>
												</select>
												<input type="text" name="filter_quantity" class="form-control" value="<?=$filter_quantity;?>">
											</div>
										</div>
										<div class="text-right">
											<button id="button-filter" type="submit" class="btn btn-info"><?=$button_filter;?></button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div>
							<input class="checkbox-inline" type="checkbox" onchange="$('input[name^=selected_offers]').prop('checked', this.checked);"
								data-toggle="tooltip" title="Выделить/снять все">
							<small class="text-muted">&nbsp;&nbsp;&nbsp;<span class="text-warning"> * </span>действуют ограничения на кол-во удаляемых
								товаров в минуту</small>
							<button type="submit" form="form-api-offers" class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Сбросить цены у выделенных товарах">Сбросить цены</button>
							<div class="pull-right" style="margin-right: 20px">
								<label>Показать:</label>
								<select name="updated_offers_limit" id="input-updated-offers-limit">
									<option value="20" <?php echo(20==$offers_limit ? 'selected' : '');?>>20</option>
									<option value="50" <?php echo(50==$offers_limit ? 'selected' : '');?>>50</option>
									<option value="100" <?php echo(100==$offers_limit ? 'selected' : '');?>>100</option>
								</select>
							</div>
						</div>
						<?php if (!empty($updated_offers)): ?>
						<br>
						Всего офферов: <?= count($updated_offers); ?>
						<form id="form-api-offers" action="<?=$action_delete;?>" method="post">
							<hr>
							<?php foreach ($updated_offers as $offer): ?>
							<div class="clearfix">
								<?php if (count($offer) == 1): ?>
								<div class="pull-left" style="margin-right:10px;">
									<input class="checkbox-inline" type="checkbox" name="selected_offers[]" value="<?=$offer[0]['offer_id'];?>">
									<img src="<?=$offer[0]['thumb']; ?>" alt="">
								</div>
								<div>
									<b><a href="<?=$offer[0]['href'];?>" target="_blank"><?=$offer[0]['product_name'];?></a></b>
								</div>
								<span class="pull-right text-muted">установленно <?=$offer[0]['date_modified'];?></span>
								<small class="text-muted">
									<?php if ($offer[0]['special_price']): ?>
									Основная цена - <?=$offer[0]['price'];?> RUB<br>
									Цена со скидкой - <?=$offer[0]['special_price'];?> RUB<br>
									<?php else: ?> Цена - <?=$offer[0]['price'];?> RUB
									<?php endif; ?>
								</small>
								<?php else: ?>
								<div class="pull-left" style="margin-right:10px;">
									<img src="<?=$offer[0]['thumb']; ?>" alt="">
								</div>
								<div>
									<b><a href="<?=$offer[0]['href'];?>" target="_blank"><?=$offer[0]['product_name'];?></a></b>
								</div>
								<div class="cur-pointer" data-toggle="collapse" data-target="#suboffers_<?=$offer[0]['product_id'];?>">Товары по опциям <div class="caret"></div></div>
								<div id="suboffers_<?=$offer[0]['product_id'];?>" class="collapse" style="clear:left">
									<?php foreach ($offer as $suboffer): ?>
									<div>
										<input class="checkbox-inline" type="checkbox" name="selected_offers[]" value="<?=$suboffer['offer_id'];?>">
										<b><?=$suboffer['name_compounded'];?></b>
									</div>
									<small class="text-muted">
										<?php if ($suboffer['special_price']): ?>
										Основная цена - <?=$suboffer['price'];?> RUB<br>
										Цена со скидкой - <?=$suboffer['special_price'];?> RUB<br>
										<?php else: ?>
										Цена - <?=$suboffer['price'];?> RUB
										<?php endif; ?>
									</small>
									<?php endforeach; ?>
								</div>
								<?php endif; ?>
							</div>
							<hr>
							<?php endforeach; ?>
						</form>
						<?=$pagination;?>
						<?php else: ?>
						<div><br>Нет товаров с обновлёнными ценами<hr></div>
						<?php endif; ?>
						<div>
							<div id="api-delete-limit"> * - Действуют ограничения на запросы к API, подробная информация по <a href="https://tech.yandex.ru/market/partner/doc/dg/reference/post-campaigns-id-offer-prices-updates-docpage/" target="_blank">ссылке</a> в разделе ограничения</div>
						</div>
					</div>
					<!-- lAst-->
				</div>
			</div>
		</div>
    </div>
</div>
<script>
jQuery(function() {
	$('#input-updated-offers-limit').change(function () {
		location = '<?=$url_module;?>' + '&' + this.name + '=' + this.value;
	});

	$('#form-filter').on('submit', function() {
		var url = this.action;

		$(this).find('input, select').each(function() {
			if (this.value.length)
				url += '&' + this.name + '=' + encodeURIComponent(this.value);
		});

		location = url;

		return false;
	});
});
</script>
