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
				<li><a href="#menu_category"><span class="mini-number">1</span><?=$menu_category;?></a></li>
				<li><a href="#menu_manufacture_filter"><span class="mini-number">2</span><?=$menu_manufacture_filter;?></a></li>
			</ul>
		</div>
		<div id="aside-right">
			<form action="<?php echo $action; ?>" method="POST" id="form-category" class="form-horizontal">
				<div class="card" id="menu_category">
				<div class="heading-card"><div class="number">1</div><h2><?=$menu_category;?></h2></div>
					<div class="body-card">
						<!-- Start content -->	
				<div class="form-group">
					<!--<label class="col-sm-4 control-label"><?php echo $entry_market_export; ?></label>-->
					<div class="col-sm-12">
						<label class="radio-inline">
							<input type="radio" <?php echo ($market_all_categories ? 'checked' : ''); ?>
								name="setting[<?=$profile_id;?>][market_all_categories]"
								value="1">
							<?php echo $text_all_products; ?>
						</label>
						<label class="radio-inline">
							<input type="radio" <?php echo (!$market_all_categories ? 'checked' : ''); ?>
								name="setting[<?=$profile_id;?>][market_all_categories]"
								value="0">
							<?php echo $text_selected_categories; ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4"><?php echo $entry_market_export_categories; ?></label>
					<div class="col-sm-12">
						<div class="panel panel-default">
							<div class="tree-panel-heading tree-panel-heading-controls clearfix">
								<div class="tree-actions pull-right">
									<a onclick="hidecatall($('#categoryBox')); return false;" id="collapse-all-categoryBox" class="btn btn-default btn-sm">
										<i class="icon-collapse-alt"></i>
										<?php echo $button_collapse_all; ?>
									</a>
									<a onclick="showcatall($('#categoryBox')); return false;" id="expand-all-categoryBox" class="btn btn-default btn-sm">
										<i class="icon-expand-alt"></i>
										<?php echo $button_expand_all; ?>
									</a>
									<a onclick="checkAllAssociatedCategories($('#categoryBox')); return false;" id="check-all-categoryBox" class="btn btn-default btn-sm">
										<i class="icon-check-sign"></i>
										<?php echo $button_select_all; ?>
									</a>
									<a onclick="uncheckAllAssociatedCategories($('#categoryBox')); return false;" id="uncheck-all-categoryBox"
										class="btn btn-default btn-sm">
										<i class="icon-check-empty"></i>
										<?php echo $button_unselect_all; ?>
									</a>
								</div>
							</div>
							<ul id="categoryBox" class="tree">
								<?php echo $cat_tree_html; ?>
							</ul>
						</div>
					</div>
				</div>
						<!-- End content -->
					</div>
				</div>
				<div class="card" id="menu_manufacture_filter">
					<div class="heading-card"><div class="number">2</div><h2><?=$menu_manufacture_filter;?></h2></div>
						<div class="body-card">
							<!-- Start content -->	
								<div class="form-group">
									<div class="col-sm-12">
										<div class="panel panel-default">
											<div class="panel-heading">
												<input type="checkbox" class="cur-pointer" onclick="$('input[type=checkbox][id^=manufacturer_]').prop('checked', this.checked);" data-toggle="tooltip" title="Выделить/снять все">
												<b><?=$entry_market_manufacturers_filter;?></b> <i class="yamf yamf-window-maximize pull-right cur-pointer" data-action="maximize" data-target="#manufacturer-container"></i>
											</div>
											<div class="panel-body">
												<div class="col-sm-12 /col-sm-push-4">
													<div id="manufacturer-container">
														<?php foreach ($manufacturers as $manufacturer): ?>
														<div class="checkbox">
															<label for="manufacturer_<?=$manufacturer['manufacturer_id'];?>">
																<input id="manufacturer_<?=$manufacturer['manufacturer_id'];?>" type="checkbox"
																	name="setting[<?=$profile_id;?>][market_manufacturers][]"
																	value="<?=$manufacturer['manufacturer_id'];?>" <?php echo (in_array($manufacturer[ 'manufacturer_id' ],
																	$market_manufacturers) ? 'checked' : '');?>
																>
																<?=$manufacturer['name'];?>
															</label>
														</div>
														<?php endforeach; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group text-right">
									<div class="col-sm-12">
										<input type="submit" class="btn btn-primary" value="<?=$button_save;?>">
									</div>
								</div>
						<!-- End content -->
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
function showcatall($tree) {
	$tree.find("ul.tree").each(
		function () {
			$(this).slideDown();
		}
	);
}

function hidecatall($tree) {
	$tree.find("ul.tree").each(
		function () {
			$(this).slideUp();
		}
	);
}
function checkAllAssociatedCategories($tree) {
	$tree.find(":input[type=checkbox]").each(
		function () {
			$(this).prop("checked", true);
			$(this).parent().addClass("tree-selected");
		}
	);
}

function uncheckAllAssociatedCategories($tree) {
	$tree.find(":input[type=checkbox]").each(
		function () {
			$(this).prop("checked", false);
			$(this).parent().removeClass("tree-selected");
		}
	);
}

$('.tree-item-name label').click(function () {
	$(this).parent().find('input').click();
});

$('.tree-folder-name input').change(function () {
	if ($(this).prop("checked")) {
		$(this).parent().addClass("tree-selected");
		$(this).parents('.tree-folder').first().find('ul input').prop("checked", true).parent().addClass("tree-selected");
	}
	else {
		$(this).parent().removeClass("tree-selected");
		$(this).parents('.tree-folder').first().find('ul input').prop("checked", false).parent().removeClass("tree-selected");
	}
});

$('.tree-toggler').click(function () {
	$(this).parents('.tree-folder').first().find('ul').slideToggle('slow');
});

$('.tree input').change(function () {
	if ($(this).prop("checked")) {
		$(this).parent().addClass("tree-selected");
	}
	else {
		$(this).parent().removeClass("tree-selected");
	}
});

</script>
