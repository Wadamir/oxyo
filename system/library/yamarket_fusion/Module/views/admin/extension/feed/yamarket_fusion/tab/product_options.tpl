<div class="container">
	<div class="content">
		<div id="aside-left">
			<ul class="menu">
				<li><a href="#menu_options"><span class="mini-number">1</span><?=$menu_options;?></a></li>
				<li><a href="#menu_attributes"><span class="mini-number">2</span><?=$menu_attributes;?></a></li>
			</ul>
		</div>
		<div id="aside-right">
		<form id="product-options_form" action="<?php echo $action; ?>" method="POST">
			<div class="card" id="menu_options">
				<div class="heading-card"><div class="number">1</div><h2><?=$menu_options;?></h2></div>
				<div class="body-card">
					<!-- Start content -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<span class="cur-pointer" data-toggle="collapse" data-target="#collapse-form-options"><i class="icon-setting-profiles"></i> Опции <span class="caret"></span></span>
								<i class="yamf yamf-window-maximize pull-right cur-pointer" data-action="maximize" data-target="#collapse-form-options"></i>
							</div>
							<div class="collapse" id="collapse-form-options">
							
								<table class="table table-condensed table-bordered">
									<thead>
										<tr>
											<td><?=$column_options_name;?></td>
											<td><?=$column_options_unit;?></td>
											<td><?=$column_options_custom_name;?></td>
											<td><?=$column_options_tag;?></td>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($product_options_data as $option_val): ?>
										<tr>
											<td>
												<label for="product_option_unit_<?=$option_val['id'];?>"><?=$option_val['name'];?></label>
											</td>
											<td>
												<input id="product_option_unit_<?=$option_val['id'];?>" class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_options_data][<?=$option_val['id'];?>][unit]" value="<?=$option_val['unit'];?>">
											</td>
											<td>
												<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_options_data][<?=$option_val['id'];?>][custom_name]" value="<?=$option_val['custom_name'];?>">
											</td>
											<td>
												<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_options_data][<?=$option_val['id'];?>][tag]" value="<?=$option_val['tag'];?>">
											</td>
										</tr>
										<?php endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					<!-- End content -->
				</div>
			</div>
			<div class="card" id="menu_attributes">
				<div class="heading-card"><div class="number">2</div><h2><?=$menu_attributes;?></h2></div>
				<div class="body-card">
					<!-- Start content -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<span class="cur-pointer" data-toggle="collapse" data-target="#collapse-form-attributes"><i class="icon-setting-profiles"></i> Аттрибуты <span class="caret"></span></span>
								<i class="yamf yamf-window-maximize pull-right cur-pointer" data-action="maximize" data-target="#collapse-form-attributes"></i>
							</div>
							<div class="collapse" id="collapse-form-attributes">
								<table class="table table-condensed table-bordered">
									<thead>
										<tr>
											<td><?=$column_attribute_name;?></td>
											<td><?=$column_attribute_unit;?></td>
											<td><?=$column_attribute_custom_name;?></td>
											<td><?=$column_attribute_tag;?></td>
											<td><?=$column_attribute_delimeter;?></td>
											<td><?=$column_attribute_status;?></td>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($attribute_groups as $attr_group): ?>
											<?php if (!empty($attr_group['attributes'])): ?>
												<tr>
													<td colspan="6"><b><?=$attr_group['name'];?></b></td>
												</tr>
												<?php foreach ($attr_group['attributes'] as $attribute): ?>
												<tr>
													<td><?=$attribute['name'];?></td>
													<td>
														<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_attribute_data][<?=$attribute['attribute_id'];?>][unit]" value="<?= isset($product_attribute_data[$attribute['attribute_id']]) ? $product_attribute_data[$attribute['attribute_id']]['unit'] : '';?>">
													</td>
													<td>
														<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_attribute_data][<?=$attribute['attribute_id'];?>][custom_name]" value="<?= isset($product_attribute_data[$attribute['attribute_id']]['custom_name']) ? $product_attribute_data[$attribute['attribute_id']]['custom_name'] : '';?>">
													</td>
													<td>
														<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_attribute_data][<?=$attribute['attribute_id'];?>][tag]" value="<?= isset($product_attribute_data[$attribute['attribute_id']]) ? $product_attribute_data[$attribute['attribute_id']]['tag'] : '';?>">
													</td>
													<td>
														<input class="form-control input-sm" type="text" name="setting[<?=$profile_id;?>][product_attribute_data][<?=$attribute['attribute_id'];?>][delimiter]" value="<?= isset($product_attribute_data[$attribute['attribute_id']]) ? $product_attribute_data[$attribute['attribute_id']]['delimiter'] : '';?>">
													</td>
													<td class="text-center">
														<div class="checkbox-inline">
															<input class="" type="checkbox" name="setting[<?=$profile_id;?>][product_attribute_data][<?=$attribute['attribute_id'];?>][disabled]" value="1" <?= empty($product_attribute_data[$attribute['attribute_id']]['disabled']) ? '' : 'checked';?>>
															<?=$text_yes;?>
														</div>
													</td>
												</tr>
												<?php endforeach;?>
											<?php endif; ?>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save;?></button>
					<!-- End content -->
				</div>
			</div>
		</form>
		</div>
	</div>
</div>