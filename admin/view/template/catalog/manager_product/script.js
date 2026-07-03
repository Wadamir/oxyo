<script type="text/javascript">
	<!--
	$('#products').delegate('.sort-name a, .sort-category a, .sort-manufacturer a, .sort-model a, .sort-sku a, .sort-upc a, .sort-ean a, .sort-jan a, .sort-isbn a, .sort-mpn a, .sort-location a, .sort-price a, .sort-tax a, .sort-quantity a, .sort-minimum a, .sort-subtract a, .sort-stock-status a, .sort-shipping a, .sort-date-available a, .sort-length-class a, .sort-weight a, .sort-weight-class a, .sort-sort-order a, .sort-status a', 'click', function(e) {
		e.preventDefault();
		var filter_value = $('#filter-value').val();
		$('#products').load(this.href + filter_value);
		$('#pagination-page').val(this.href);
	});
	$('#products').delegate('.pagination a', 'click', function(e) {
		e.preventDefault();
		var filter_value = $('#filter-value').val();
		$('#products').load(this.href + filter_value);
		$('#pagination-page').val(this.href);
	});
	$('#products').load('index.php?route=catalog/manager_product/product&user_token={{ user_token }}');
	$('#button-filter').on('click', function() {
		url = '';
		var filter_image = $('select[name=\'filter_image\']').val();
		if (filter_image != '') {
			url += '&filter_image=' + encodeURIComponent(filter_image);
		}
		var filter_name = $('input[name=\'filter_name\']').val();
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_category_id = $('input[name=\'filter_category_id\']').val();
		if (filter_category_id) {
			url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_manufacturer_id = $('input[name=\'filter_manufacturer_id\']').val();
		if (filter_manufacturer_id) {
			url += '&filter_manufacturer_id=' + encodeURIComponent(filter_manufacturer_id);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_model = $('input[name=\'filter_model\']').val();
		if (filter_model) {
			url += '&filter_model=' + encodeURIComponent(filter_model);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_sku = $('input[name=\'filter_sku\']').val();
		if (filter_sku) {
			url += '&filter_sku=' + encodeURIComponent(filter_sku);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_attribute_id = $('input[name=\'filter_attribute_id\']').val();
		if (filter_attribute_id) {
			url += '&filter_attribute_id=' + encodeURIComponent(filter_attribute_id);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_attribute_value = $('input[name=\'filter_attribute_value\']').val();
		if (filter_attribute_value) {
			url += '&filter_attribute_value=' + encodeURIComponent(filter_attribute_value);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_price = $('input[name=\'filter_price\']').val();
		if (filter_price) {
			url += '&filter_price=' + encodeURIComponent(filter_price);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_price_min = $('input[name=\'filter_price_min\']').val();
		if (filter_price_min) {
			url += '&filter_price_min=' + encodeURIComponent(filter_price_min);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_price_max = $('input[name=\'filter_price_max\']').val();
		if (filter_price_max) {
			url += '&filter_price_max=' + encodeURIComponent(filter_price_max);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_special = $('select[name=\'filter_special\']').val();
		if (filter_special != '') {
			url += '&filter_special=' + encodeURIComponent(filter_special);
		}
		var filter_special_price = $('input[name=\'filter_special_price\']').val();
		if (filter_special_price) {
			url += '&filter_special_price=' + encodeURIComponent(filter_special_price);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_special_price_min = $('input[name=\'filter_special_price_min\']').val();
		if (filter_special_price_min) {
			url += '&filter_special_price_min=' + encodeURIComponent(filter_special_price_min);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_special_price_max = $('input[name=\'filter_special_price_max\']').val();
		if (filter_special_price_max) {
			url += '&filter_special_price_max=' + encodeURIComponent(filter_special_price_max);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_quantity = $('input[name=\'filter_quantity\']').val();
		if (filter_quantity) {
			url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_quantity_min = $('input[name=\'filter_quantity_min\']').val();
		if (filter_quantity_min) {
			url += '&filter_quantity_min=' + encodeURIComponent(filter_quantity_min);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_quantity_max = $('input[name=\'filter_quantity_max\']').val();
		if (filter_quantity_max) {
			url += '&filter_quantity_max=' + encodeURIComponent(filter_quantity_max);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_location = $('input[name=\'filter_location\']').val();
		if (filter_location) {
			url += '&filter_location=' + encodeURIComponent(filter_location);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_filter_id = $('input[name=\'filter_filter_id\']').val();
		if (filter_filter_id) {
			url += '&filter_filter_id=' + encodeURIComponent(filter_filter_id);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_sort_order = $('input[name=\'filter_sort_order\']').val();
		if (filter_sort_order) {
			url += '&filter_sort_order=' + encodeURIComponent(filter_sort_order);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_sort_order_min = $('input[name=\'filter_sort_order_min\']').val();
		if (filter_sort_order_min) {
			url += '&filter_sort_order_min=' + encodeURIComponent(filter_sort_order_min);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_sort_order_max = $('input[name=\'filter_sort_order_max\']').val();
		if (filter_sort_order_max) {
			url += '&filter_sort_order_max=' + encodeURIComponent(filter_sort_order_max);
			var page = '';
		} else {
			var page = $('#pagination-page').val();
		}
		var filter_status = $('select[name=\'filter_status\']').val();
		if (filter_status != '') {
			url += '&filter_status=' + encodeURIComponent(filter_status);
		}
		var filter_discount = $('select[name=\'filter_discount\']').val();
		if (filter_discount != '') {
			url += '&filter_discount=' + encodeURIComponent(filter_discount);
		}
		$('#filter-value').val(url);
		if (page == '') {
			$('#products').load('index.php?route=catalog/manager_product/product&user_token= {{ user_token }}' + url);
		} else {
			$('#products').load(page + url);
		}
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	$('#button-act-filter').on('click', function() {
		$('#product-filter').slideToggle(300);
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	$('#button-clear-filter').on('click', function() {
		$('#product-filter input, #product-filter select').val('');
		$('#pagination-page').val('');
		$('#button-filter').trigger('click');
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	$('#product-filter select').on('change', function() {
		$('#pagination-page').val('');
		$('#button-filter').trigger('click');
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	var data_filter_input = $('#product-filter input').val();
	var data_filter_select = $('#product-filter select').val();
	if ((data_filter_input != '') || (data_filter_select != '')) {
		$('#product-filter').show();
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	$('input[name=\'filter_name\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete&user_token= {{ user_token }}&filter_name=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['product_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_name\']').val(item['label']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_category\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_category&user_token= {{ user_token }}&filter_name=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					json.unshift({
						category_id: '*',
						name: '{{ text_no_category }}'
					});
					json.unshift({
						category_id: 0,
						name: '{{ text_all_categories }}'
					});
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['category_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_category\']').val(item['label']);
			$('input[name=\'filter_category_id\']').val(item['value']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_manufacturer\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_manufacturer&user_token= {{ user_token }}&filter_name=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					json.unshift({
						manufacturer_id: '*',
						name: '{{ text_no_brand }}'
					});
					json.unshift({
						manufacturer_id: 0,
						name: '{{ text_all_brands }}'
					});
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['manufacturer_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_manufacturer\']').val(item['label']);
			$('input[name=\'filter_manufacturer_id\']').val(item['value']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_model\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_model&user_token= {{ user_token }}&filter_model=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['model'],
							value: item['product_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_model\']').val(item['label']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_sku\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_sku&user_token= {{ user_token }}&filter_sku=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['sku'],
							value: item['product_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_sku\']').val(item['label']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_attribute\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_attribute&user_token= {{ user_token }}&filter_name=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					json.unshift({
						attribute_id: '*',
						name: '{{ text_no_attribute }}'
					});
					json.unshift({
						attribute_id: 0,
						name: '{{ text_all_attributes }}'
					});
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['attribute_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_attribute\']').val(item['label']);
			$('input[name=\'filter_attribute_id\']').val(item['value']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_attribute_value\']').autocomplete({
		'source': function(request, response) {
			var filter_attribute_id = $('input[name=\'filter_attribute_id\']').val();
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_attribute_value&user_token= {{ user_token }}&filter_attribute_id=' + filter_attribute_id + '&filter_attribute_value=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['text'],
							value: item['text']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_attribute_value\']').val(item['label']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_location\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_location&user_token= {{ user_token }}&filter_location=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['location'],
							value: item['product_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_location\']').val(item['label']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	$('input[name=\'filter_filter\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/manager_product/autocomplete_filter&user_token= {{ user_token }}&filter_name=' + encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					json.unshift({
						filter_id: '*',
						name: '{{ text_no_filter }}'
					});
					json.unshift({
						filter_id: 0,
						name: '{{ text_all_filters }}'
					});
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['filter_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_filter\']').val(item['label']);
			$('input[name=\'filter_filter_id\']').val(item['value']);
			$('#pagination-page').val('');
			$('#button-filter').trigger('click');
		}
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	function getForm(product_id) {
		$('#modal-edit-product').modal('show');
		$('#modal-edit-product').on('hide.bs.modal', function() {
			$('#modal-edit-product').removeClass('modal-fullscreen');
			$('#modal-product-content').empty();
		});
		if (product_id == '') {
			$('#modal-product-content').load('index.php?route=catalog/manager_product/getForm&user_token= {{ user_token }}');
		} else {
			$('#modal-product-content').load('index.php?route=catalog/manager_product/getForm&user_token= {{ user_token }}&product_id=' + product_id);
		}
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function copyProduct() {
		$.ajax({
			url: 'index.php?route=catalog/manager_product/copyProduct&user_token= {{ user_token }}',
			type: 'post',
			dataType: 'json',
			data: $('#product-list input[type=\'checkbox\']:checked'),
			success: function(json) {
				if (json['error']) {
					$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-check-circle"></i> ' + json['error'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
				}
				if (json['success']) {
					$('.messages-body').html('<div class="alert alert-success alert-messages"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1000).fadeOut(500);
					$('#button-filter').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function deleteProduct() {
		if (!confirm('{{ text_confirm }}')) return;
		$.ajax({
			url: 'index.php?route=catalog/manager_product/deleteProduct&user_token= {{ user_token }}',
			type: 'post',
			dataType: 'json',
			data: $('#product-list input[type=\'checkbox\']:checked'),
			success: function(json) {
				if (json['error']) {
					$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-check-circle"></i> ' + json['error'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
				}
				if (json['success']) {
					$('.messages-body').html('<div class="alert alert-success alert-messages"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1000).fadeOut(500);
					$('#button-filter').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function editSetting() {
		$('#modal-edit-product').modal('show');
		$('#modal-edit-product').on('hide.bs.modal', function() {
			$('#modal-edit-product').removeClass('modal-fullscreen');
			$('#modal-product-content').empty();
		});
		$('#modal-product-content').load('index.php?route=extension/module/manager_product/edit&user_token= {{ user_token }}&type=manager_setting');
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function editData(product_id, type_data) {
		if (type_data == 'image') {
			var value_data = $('#product-current-image-' + product_id).val();
			if (value_data == '') {
				if (!confirm('{{ error_image }}')) return;
			}
		} else if (type_data == 'status') {
			var status = $('#product-' + type_data + '-' + product_id).find('input[name=\'statuses\']').val();
			if (status == 0) value_data = 1;
			else value_data = 0;
		} else if (type_data == 'subtract') {
			var subtract = $('#product-' + type_data + '-' + product_id).find('input[name=\'subtractes\']').val();
			if (subtract == 0) value_data = 1;
			else value_data = 0;
		} else if (type_data == 'shipping') {
			var shipping = $('#product-' + type_data + '-' + product_id).find('input[name=\'shippinges\']').val();
			if (shipping == 0) value_data = 1;
			else value_data = 0;
		} else if (type_data == 'dimension') {
			var length_data = $('#input-length-' + product_id).val();
			var width_data = $('#input-width-' + product_id).val();
			var height_data = $('#input-height-' + product_id).val();
		} else {
			var value_data = $('#input-' + type_data + '-' + product_id).val();
		}
		if (type_data == 'video') {
			var value_data = $('#input-video-' + product_id).val();
			$.ajax({
				url: 'index.php?route=catalog/manager_product/saveProductVideo&user_token= {{ user_token }}',
				type: 'post',
				dataType: 'json',
				data: {
					product_id: product_id,
					video: value_data
				},
				success: function(json) {
					if (json['error']) {
						$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-check-circle"></i> ' + json['error'] + '</div>');
						$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
					}
					if (json['success']) {
						$('.messages-body').html('<div class="alert alert-success alert-messages"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
						$('.maxy-backdrop, .messages-body').show().delay(1000).fadeOut(500);
						$('#button-filter').trigger('click');
						$('#close-' + type_data + '-' + product_id).trigger('click');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
			return;
		}
		$.ajax({
			url: 'index.php?route=extension/module/manager_product/edit&user_token= {{ user_token }}&product_id=' + product_id + '&type=product_list_data&type_data=' + type_data + '&value_data=' + value_data + '&length_data=' + length_data + '&width_data=' + width_data + '&height_data=' + height_data,
			type: 'post',
			dataType: 'json',
			success: function(json) {
				if (json['error']) {
					$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-check-circle"></i> ' + json['error'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
				}
				if (json['success']) {
					$('.messages-body').html('<div class="alert alert-success alert-messages"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					$('.maxy-backdrop, .messages-body').show().delay(1000).fadeOut(500);
					$('#button-filter').trigger('click');
					$('#close-' + type_data + '-' + product_id).trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function clearData(product_id, type_data) {
		if (type_data == 'image') {
			$('#product-image-' + product_id).find('a').find('img').attr('src', '{{ placeholder }}');
			$('#product-current-image-' + product_id).attr('value', '');
		} else if (type_data == 'video') {
			$('#product-video-preview-' + product_id).html('<img src=" {{ no_video_placeholder }}" class="img-thumbnail" data-placeholder=" {{ no_video_placeholder }}" style="max-width: 100px;" />');
			$('#input-video-' + product_id).val('');
		} else if (type_data == 'manufacturer_id') {
			$('#input-manufacturer-' + product_id).val('');
			$('#input-' + type_data + '-' + product_id).val('');
		} else if (type_data == 'tax_class_id' || type_data == 'quantity' || type_data == 'minimum') {
			$('#input-' + type_data + '-' + product_id).val(0);
		} else if (type_data == 'dimension') {
			$('#input-length-' + product_id).val(0);
			$('#input-width-' + product_id).val(0);
			$('#input-height-' + product_id).val(0);
		} else {
			$('#input-' + type_data + '-' + product_id).val('');
		}
	}

	function currentDate(product_id, type_data) {
		if (type_data == 'date_available') {
			Data = new Date();
			year = Data.getFullYear();
			month = Data.getMonth() + 1;
			day = Data.getDate();
			if (month < 10) month = '0' + month;
			if (day < 10) day = '0' + day;
			$('#input-' + type_data + '-' + product_id).val(year + '-' + month + '-' + day);
		}
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function linkImage(product_id, type_data) {
		type_data = type_data || 'image';
		var element = $('#product-' + type_data + '-' + product_id);
		var target = $(element).find('input').attr('id');
		var thumb = $(element).attr('id');
		if (type_data == 'video') {
			target = $('#input-video-' + product_id).attr('id');
			thumb = 'product-video-preview-' + product_id;
		}
		$('#modal-image').remove();
		$.ajax({
			url: 'index.php?route=common/filemanager&user_token= {{ user_token }}&target=' + target + '&thumb=' + thumb,
			dataType: 'html',
			success: function(html) {
				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
				$('#modal-image').modal('show');
			}
		});
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function editItem(product_id, type_data) {
		$('#product-more-' + product_id).popoverMaxy('hide');
		$('#modal-edit-product').modal('show');
		$('#modal-edit-product').on('hide.bs.modal', function() {
			$('#modal-edit-product').removeClass('modal-fullscreen');
			$('#modal-product-content').empty();
		});
		$('#modal-product-content').load('index.php?route=extension/module/manager_product/edit&user_token= {{ user_token }}&product_id=' + product_id + '&type=product_item_data&type_data=' + type_data);
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function massEdit(type_data) {
		var error_select_product = '{{ error_select_product }}';
		if ($('#product-list input[type=\'checkbox\']:checked').val()) {
			$('#option-mass-update').find('ul').removeClass('noclose');
			$('#option-mass-delete').find('ul').removeClass('noclose');
			$('#modal-edit-product').modal('show');
			$('#modal-edit-product').on('hide.bs.modal', function() {
				$('#modal-edit-product').removeClass('modal-fullscreen');
				$('#product-list-title input[type=\'checkbox\']').prop('checked', false);
				$('#product-list input[type=\'checkbox\']').prop('checked', false);
				$('#option-mass-update').find('a').addClass('disabled');
				$('#option-mass-update').find('ul').addClass('noclose');
				$('#option-mass-update .collapse').removeClass('in');
				$('#option-mass-delete').find('a').addClass('disabled');
				$('#option-mass-delete').find('ul').addClass('noclose');
				$('#option-mass-delete .collapse').removeClass('in');
				$('#modal-product-content').empty();
			});
			$('#modal-product-content').load('index.php?route=extension/module/manager_product/edit&user_token= {{ user_token }}&type=product_mass_edit&type_data=' + type_data);
		} else {;
			$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-exclamation-circle"></i> ' + error_select_product + '</div>');
			$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
			setTimeout(function() {
				$('#option-mass-update').find('a').addClass('disabled');
				$('#option-mass-update').find('ul').addClass('noclose');
				$('#option-mass-update .collapse').removeClass('in');
			}, 300)
		}
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	function massDelete(type_data) {
		var error_select_product = '{{ error_select_product }}';
		if ($('#product-list input[type=\'checkbox\']:checked').val()) {
			$('#option-mass-delete').find('ul').removeClass('noclose');
			$('#option-mass-update').find('ul').removeClass('noclose');
			if (!confirm('{{ text_confirm }}')) return;
			$.ajax({
				url: 'index.php?route=extension/module/manager_product/edit&user_token=' + getURLVar('user_token') + '&type_data=' + type_data + '&type=mass_delete',
				type: 'post',
				dataType: 'json',
				data: $('#product-list input[type=\'checkbox\']:checked'),
				success: function(json) {
					if (json['error']) {
						$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-check-circle"></i> ' + json['error'] + '</div>');
						$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
					}
					if (json['success']) {
						$('.messages-body').html('<div class="alert alert-success alert-messages"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
						$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
						if ((type_data == 'name') || (type_data == 'image') || (type_data == 'model') || (type_data == 'sku') || (type_data == 'price') || (type_data == 'quantity') || (type_data == 'status') || (type_data == 'sort_order') || (type_data == 'manufacturer') || (type_data == 'category') || (type_data == 'special')) {
							$('#button-filter').trigger('click');
							$('#product-list-title input[type=\'checkbox\']').prop('checked', false);
							$('#product-list-bottom input[type=\'checkbox\']').prop('checked', false);
							$('#product-list input[type=\'checkbox\']').prop('checked', false);
							$('#option-mass-delete').find('a').addClass('disabled');
							$('#option-mass-delete').find('ul').addClass('noclose');
							$('#option-mass-delete .collapse').removeClass('in');
							$('#option-mass-update').find('a').addClass('disabled');
							$('#option-mass-update').find('ul').addClass('noclose');
							$('#option-mass-update .collapse').removeClass('in');
						} else {
							$('#product-list-title input[type=\'checkbox\']').prop('checked', false);
							$('#product-list-bottom input[type=\'checkbox\']').prop('checked', false);
							$('#product-list input[type=\'checkbox\']').prop('checked', false);
							$('#option-mass-delete').find('a').addClass('disabled');
							$('#option-mass-delete').find('ul').addClass('noclose');
							$('#option-mass-delete .collapse').removeClass('in');
							$('#option-mass-update').find('a').addClass('disabled');
							$('#option-mass-update').find('ul').addClass('noclose');
							$('#option-mass-update .collapse').removeClass('in');
						}
					}
				}
			});
		} else {
			$('.messages-body').html('<div class="alert alert-danger alert-messages"><i class="fa fa-exclamation-circle"></i> ' + error_select_product + '</div>');
			$('.maxy-backdrop, .messages-body').show().delay(1500).fadeOut(500);
			setTimeout(function() {
				$('#option-mass-delete').find('a').addClass('disabled');
				$('#option-mass-delete').find('ul').addClass('noclose');
				$('#option-mass-delete .collapse').removeClass('in');
			}, 300)
		}
	}
	//-->
</script>
<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$(window).scroll(function() {
			if ($(window).scrollTop() <= 60) {
				if ($('.page-header').hasClass('fixed-page-header')) {
					$('.page-header').removeClass('fixed-page-header');
				}
				$('.panel-manager').css('margin-top', '0');
			} else {
				if (!$('.page-header').hasClass('fixed-page-header')) {
					$('.page-header').addClass('fixed-page-header');
				}
				$('.panel-manager').css('margin-top', '80px');
			}
		});
	});
	//-->
</script>
<script type="text/javascript">
	<!--
	function expand() {
		$('#modal-edit-product').addClass('modal-fullscreen');
		$('.btn-expand').addClass('hidden');
		$('.btn-compress').removeClass('hidden');
	}

	function compress() {
		$('#modal-edit-product').removeClass('modal-fullscreen');
		$('.btn-compress').addClass('hidden');
		$('.btn-expand').removeClass('hidden');;
	}
	//-->
</script>