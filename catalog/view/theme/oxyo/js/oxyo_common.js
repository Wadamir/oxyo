document.addEventListener('DOMContentLoaded', function () {
	const container = document.querySelector('.input-plus-minus');
	if (!container) return;

	const input = container.querySelector('input[type="number"]');
	const minusBtn = container.querySelector('.button-minus');
	const plusBtn = container.querySelector('.button-plus');

	if (!input || !minusBtn || !plusBtn) return;

	const step = parseInt(input.getAttribute('step')) || 1;
	const min = parseInt(input.getAttribute('min')) || 1;

	minusBtn.addEventListener('click', function () {
		let value = parseInt(input.value) || min;
		if (value > min) {
			input.value = value - step;
			input.dispatchEvent(new Event('change'));
            if (window.jQuery) $(input).trigger('change');
		}
	});

	plusBtn.addEventListener('click', function () {
		let value = parseInt(input.value) || min;
		input.value = value + step;
		input.dispatchEvent(new Event('change'));
        if (window.jQuery) $(input).trigger('change');
    });
});

document.addEventListener("DOMContentLoaded", function () {
	/*** number input ****/
	const containers = document.querySelectorAll(".number-input");
	containers.forEach(function (container) {
		const input = container.querySelector("input");
		const btnDec = container.querySelector(".btn-decrement");
		const btnInc = container.querySelector(".btn-increment");

		const min = parseInt(input.min) || 1;
		const max = parseInt(input.max) || 999999;

		// Increase value
		btnInc.addEventListener("click", function () {
			let val = parseInt(input.value) || min;
			if (val < max) {
				input.value = val + 1;
				input.dispatchEvent(new Event("change"));
			}
		});

		// Decrease value
		btnDec.addEventListener("click", function () {
			let val = parseInt(input.value) || min;
			if (val > min) {
				input.value = val - 1;
				input.dispatchEvent(new Event("change"));
			}
		});

		// Validate manual input
		input.addEventListener("input", function () {
			let val = parseInt(input.value);
			if (isNaN(val) || val < min) {
				input.value = min;
			} else if (val > max) {
				input.value = max;
			}
		});
	});

	/*** cart update quantity ***/
});



function getURLVar(key) {
    var value = [];
    var query = String(document.location).split('?');
    if (query[1]) {
        var part = query[1].split('&');
        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }
        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
}

function addCookie(name, value, days) {
    var date, expires;
    if (days) {
        date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

$(document).ready(function () {

    // Add body ready class //
    $('body').addClass('document_ready');

    // Set page title position
    if ($(".title_in_bc").length) {
        $('#page-title').appendTo($("#title-holder").empty());
    }

    // Move breadcrumb to header //
    $('ul.breadcrumb').appendTo($('.links-holder span').empty());

    // Sticky header
    var sticky_to_top = $('.sticky-header').offset().top;
    var stickyheader = function () {
        var window_to_top = $(window).scrollTop();
        if (window_to_top > sticky_to_top) {
            $('body').addClass('sticky-active');
        } else {
            $('body').removeClass('sticky-active');
        };
        if (window_to_top > (sticky_to_top + 40)) {
            $('.sticky-header').addClass('short');
        } else {
            $('.sticky-header').removeClass('short');
        }
        if (window_to_top > 250) {
            $('body').addClass('offset250');
        } else {
            $('body').removeClass('offset250');
        };
    };
    $(window).scroll(function () {
        stickyheader();
    });

    // Mobile menu open
    $(".menu-trigger").click(function () {
        $('html').toggleClass('no-scroll mobile-menu-open');
        // $('.body-cover').addClass('active');
    });

    // Mobile menu close
    $(".menu-closer").click(function () {
        $('.body-cover').removeClass('active');
        $('html').removeClass('no-scroll mobile-menu-open side-filter-open');
    });

    // Mobile menu navigation
    $('.main-menu-wrapper > li.dropdown-wrapper > a .fa').click(function (e) {
        if ($(window).width() < 991) {
            e.preventDefault();
            $(this).parent().parent().siblings().find('>a').removeClass("open");
            $(this).parent().toggleClass("open").parent().find('>.dropdown-content').stop(true, true).slideToggle(350)
                .end().siblings().find('>.dropdown-content').slideUp(350);
        }
    });

    $('.main-menu-wrapper ul > li.has-sub > a .fa').click(function (e) {
        if ($(window).width() < 991) {
            e.preventDefault();
            $(this).parent().parent().siblings().find('>a').removeClass("open");
            $(this).parent().toggleClass("open").parent().find('>.sub-holder').stop(true, true).slideToggle(350)
                .end().siblings().find('>.sub-holder').slideUp(350);
        }
    });

    // Click drop down
    $(".dropdown-wrapper-click .clicker").click(function () {
        if ($(this).parent().hasClass('opened')) {
            $(this).parent().removeClass('opened');
        } else {
            $(".dropdown-wrapper-click").removeClass('opened');
            $(this).parent().addClass('opened');
        }
    });

    // Open external links in new tab //
    $('a.external').on('click', function (e) {
        e.preventDefault();
        window.open($(this).attr('href'));
    });

    // Highlight any found errors
    $('.text-danger').each(function () {
        var element = $(this).parent().parent();
        if (element.hasClass('form-group')) {
            element.addClass('has-error');
        }
    });

    /* Search */
    $('.search-trigger').on('click', function () { setTimeout(function (object) { $('.main-search-input').focus(); }, 500); });

    $(".main-search-input").focus(function () { $(this).parent().parent().addClass('focus'); }).blur(function () { $(this).parent().parent().removeClass('focus'); })

    $('.do-search').on('click', function () {
        var url = $('base').attr('href') + 'index.php?route=product/search';
        var value = $('.search-field input[name=\'search\']').val();
        if (value) { url += '&search=' + encodeURIComponent(value); }
        var category_id = $('select[name=\'category_id\']').prop('value');
        if (category_id > 0) {
            url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
        }
        location = url;
    });
    $('.main-search-input').on('keydown', function (e) {
        if (e.keyCode == 13) {
            $('.do-search.main').trigger('click');
        }
    });
    /* Mobile Search */
    $('.search-holder-mobile input[name=\'search-mobile\']').parent().find('.fa-search').on('click', function () {
        var url = $('base').attr('href') + 'index.php?route=product/search';
        var value = $('.search-holder-mobile input[name=\'search-mobile\']').val();
        if (value) { url += '&search=' + encodeURIComponent(value); }
        location = url;
    });
    $('.search-holder-mobile input[name=\'search-mobile\']').on('keydown', function (e) {
        if (e.keyCode == 13) { $('.search-holder-mobile input[name=\'search-mobile\']').parent().find('.fa-search').trigger('click'); }
    });

    // Keep Menu In Viewport
    // var menu_viewport = function () {
    //     if ($(window).width() > 992) {
    //         $('.main-menu .dropdown-content').each(function () {
    //             var menu = $('.container').offset();
    //             var dropdown = $(this).parent().offset();
    //             var dropdown_wrapper = $(this).offset();

    //             // LTR Version
    //             var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('.container').outerWidth());
    //             if (i > 0) {
    //                 $(this).css('margin-left', '-' + (i + 15) + 'px');
    //             } else {
    //                 $(this).css('margin-left', '0px');
    //             }

    //             // RTL Version		
    //             var r = (menu.left - dropdown_wrapper.left);
    //             if (r > 0) {
    //                 $(this).css('margin-right', '-' + (r + 15) + 'px');
    //             } else {
    //                 $(this).css('margin-right', '0px');
    //             }
    //         });
    //     }
    // };
    // $(window).on("load resize", function (e) { menu_viewport(); });

    // Language and currency select
    $('#language-select').on('change', function () {
        $('#lang-code').attr('value', this.value); $('#form-language').submit();
    });

    $('#currency-select').on('change', function () {
        $('#curr-code').attr('value', this.value); $('#form-currency').submit();
    });

    // Tooltip position on product style 2
    $('.product-style2 .single-product .icon').attr('data-placement', 'top');

    // tooltips on hover
    // $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

    // Makes tooltips work on ajax generated content
    $(document).ajaxStop(function () {
        $('[data-toggle=\'tooltip\']').tooltip({ container: 'body' });
    });

    // Banner module 
    $('.cm_content .type-img .cm_item > *').click(function (e) {
        e.stopPropagation();
    });
    $('.banner_wrap').has('.hover-zoom').addClass('hover-zoom');
    $('.banner_wrap').has('.hover-darken').addClass('hover-darken');
    $('.banner_wrap').has('.hover-up').addClass('hover-up');
    $('.banner_wrap').has('.hover-down').addClass('hover-down');
    $('.banner_wrap').has('.hover-border').addClass('hover-border');

    // Product List/Grid
    $('#list-view').click(function () {
        $('#product-view').attr('class', 'notransition list'); setTimeout(function () { $('#product-view').removeClass('notransition'); }, 100);
        localStorage.setItem('display', 'list');
    });
    $('#grid-view').click(function () {
        $('#product-view').attr('class', 'notransition grid'); setTimeout(function () { $('#product-view').removeClass('notransition'); }, 100);
        localStorage.setItem('display', 'grid');
    });
    if (localStorage.getItem('display') == 'list') {
        $('#list-view').trigger('click');
    } else {
        $('#grid-view').trigger('click');
    }

    // Checkout
    $(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function (e) {
        if (e.keyCode == 13) {
            $('#collapse-checkout-option #button-login').trigger('click');
        }
    });

});

// Quickview
// var quickview = function (product_id) {
//     $.ajax({
//         url: 'index.php?route=extension/oxyo/quickview&product_id=' + product_id,
//         type: 'post',
//         dataType: 'html',
//         beforeSend: function () {
//             $('body').append('<span class="oxyo-spinner ajax-call"></span>');
//         },
//         success: function (html) {
//             $('.oxyo-spinner.ajax-call').remove();
//             $('[data-toggle=\'tooltip\']').tooltip('hide');
//             // $.featherlight(html);
//         }
//     });
// }
const quickview = function (product_id) {
	const modalEl = document.getElementById('quickviewModal');
	const contentEl = modalEl.querySelector('.quickview-content');

	// Clear previous content and show loader
	contentEl.innerHTML = `
		<div class="quickview-loader">
			<i class="fa fa-spinner fa-spin"></i>
		</div>
	`;

	// Show the modal
	const modal = new bootstrap.Modal(modalEl);
	modal.show();

	// Send AJAX request to fetch quickview content
	fetch('index.php?route=extension/oxyo/quickview&product_id=' + product_id, {
		method: 'POST',
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		}
	})
	.then(response => {
		if (!response.ok) throw new Error('Network error');
		return response.text();
	})
	.then(html => {
		contentEl.innerHTML = html;

		// Re-init tooltips (Bootstrap 5)
		const tooltips = contentEl.querySelectorAll('[data-bs-toggle="tooltip"]');
		tooltips.forEach(el => new bootstrap.Tooltip(el));

		// Init slick (still jQuery-based)
		if (window.$ && $.fn.slick) {
			const sliderEl = $('.qv_image');
			sliderEl.css('opacity', '0');

			sliderEl.on('init', function () {
				sliderEl.css('opacity', '1');
			});
            setTimeout(() => {
                sliderEl.slick({
                    prevArrow: '<a class="arrow-left within icon-arrow-left"></a>',
                    nextArrow: '<a class="arrow-right within icon-arrow-right"></a>',
                    arrows: true
                })
            }, 100);
		} else {
			console.warn('Slick slider or jQuery is not available');
		}
	})
	.catch(error => {
		console.error('Quickview error:', error);
		contentEl.innerHTML = `<div class="p-3 text-danger">Ошибка загрузки быстрого просмотра</div>`;
	});
};

function addToCartQuickview() {
	console.log('Adding to cart from quickview');

	const form = document.getElementById('product');
	if (!form) {
		console.warn('Form #product not found.');
		return;
	}

	// Prepare form data
	const formData = new FormData();
	form.querySelectorAll('input, select, textarea').forEach(input => {
		if ((input.type === 'radio' || input.type === 'checkbox') && !input.checked) return;
		if (input.name) formData.append(input.name, input.value);
	});

	// Show spinner
	const spinner = document.createElement('span');
	spinner.className = 'oxyo-spinner ajax-call';
	document.body.appendChild(spinner);

	// Send AJAX request
	fetch('index.php?route=extension/oxyo/oxyo_features/add_to_cart', {
		method: 'POST',
		body: formData
	})
	.then(async response => {
		const text = await response.text();
		try {
			return JSON.parse(text);
		} catch (e) {
			throw new Error('Invalid JSON response: ' + text);
		}
	})
	.then(json => {
		document.querySelectorAll('.alert, .text-danger, .popup-note').forEach(e => e.remove());
		document.querySelectorAll('.table-cell, .has-error').forEach(e => e.classList.remove('has-error'));
		spinner.remove();

		// Handle errors
		if (json.error) {
			if (json.error.option) {
				for (const key in json.error.option) {
					const input = document.getElementById('input-option' + key.replace('_', '-'));
					if (!input) continue;

					const errorDiv = document.createElement('div');
					errorDiv.className = 'text-danger';
					errorDiv.textContent = json.error.option[key];

					if (input.parentElement.classList.contains('input-group')) {
						input.parentElement.insertAdjacentElement('afterend', errorDiv);
					} else {
						input.insertAdjacentElement('afterend', errorDiv);
					}
				}
			}

			if (json.error.recurring) {
				const recurringSelect = form.querySelector('select[name="recurring_id"]');
				if (recurringSelect) {
					const recurringError = document.createElement('div');
					recurringError.className = 'text-danger';
					recurringError.textContent = json.error.recurring;
					recurringSelect.insertAdjacentElement('afterend', recurringError);
				}
			}

			document.querySelectorAll('.text-danger').forEach(e => {
				e.parentElement.classList.add('has-error');
			});

			return;
		}

		// Handle redirect
		if (json.success_redirect) {
			location.href = json.success_redirect;
			return;
		}

		// Success popup
		if (json.success) {
			const note = document.createElement('div');
			note.className = 'popup-note';
			note.innerHTML = `
				<div class="inner">
					<a class="popup-note-close" onclick="this.closest('.popup-note').remove()">&times;</a>
					<div class="table">
						<div class="table-cell v-top img"><img src="${json.image}" alt=""/></div>
						<div class="table-cell v-top">${json.success}</div>
					</div>
				</div>
			`;
			document.body.appendChild(note);

			setTimeout(() => {
				note.remove();
			}, 8100);

			// Update cart totals
			setTimeout(() => {
				const items = document.querySelector('.cart-total-items');
				const amount = document.querySelector('.cart-total-amount');
				if (items) items.innerHTML = json.total_items;
				if (amount) amount.innerHTML = json.total_amount;
			}, 100);

			// Reload cart content
			const cartContent = document.getElementById('cart-content');
			if (cartContent) {
				fetch('index.php?route=common/cart/info')
					.then(response => response.text())
					.then(html => {
						const temp = document.createElement('div');
						temp.innerHTML = html;
						const newContent = temp.querySelector('#cart-content');
						if (newContent) {
							cartContent.innerHTML = newContent.innerHTML;
						}
					})
					.catch(err => console.error('Cart update error:', err));
			}
		}
	})
	.catch(error => {
		alert(error.message);
		console.error('Add to cart error:', error);
	});
}



// Newsletter Subscribe
var subscribe = function (module) {
    $.ajax({
        url: 'index.php?route=extension/oxyo/oxyo_features/subscribe&module=' + module,
        type: 'post',
        dataType: 'json',
        data: 'email=' + encodeURIComponent($('input[id=\'subscribe-module' + module + '\']').val()),
        success: function (json) {
            if (json['error']) {
                $('#subscribe-respond' + module + '').html('<span>' + json['error'] + '</span>');
                setTimeout(function () { $('#subscribe-respond' + module + ' span').fadeOut(500); }, 3000);
            }
            if (json['success']) {
                $('#subscribe-respond' + module + '').html('<span>' + json['success'] + '</span>');
                setTimeout(function () { $('#subscribe-respond' + module + ' span').fadeOut(500); }, 5000);
                $('input[id=\'subscribe-module' + module + '\']').val('');
            }
        }
    });
}
// Newsletter Unsubscribe
var unsubscribe = function (module) {
    $.ajax({
        url: 'index.php?route=extension/oxyo/oxyo_features/unsubscribe&module=' + module,
        type: 'post',
        dataType: 'json',
        data: 'email=' + encodeURIComponent($('input[id=\'subscribe-module' + module + '\']').val()),
        success: function (json) {
            if (json['error']) {
                $('#subscribe-respond' + module + '').html('<span>' + json['error'] + '</span>');
                setTimeout(function () { $('#subscribe-respond' + module + ' span').fadeOut(500); }, 3000);
            }
            if (json['success']) {
                $('#subscribe-respond' + module + '').html('<span>' + json['success'] + '</span>');
                setTimeout(function () { $('#subscribe-respond' + module + ' span').fadeOut(500); }, 5000);
                $('input[id=\'subscribe-module' + module + '\']').val('');
            }
        }
    });
}

// Cart add remove functions
var cart = {
    'add': function (product_id, quantity, source) {
        $.ajax({
            url: 'index.php?route=extension/oxyo/oxyo_features/add_to_cart',
            type: 'post',
            data: 'product_id=' + product_id + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',

            beforeSend: function (json) {
                $('body').append('<span class="oxyo-spinner ajax-call"></span>');
            },

            success: function (json) {
                $('[data-toggle=\'tooltip\']').tooltip('hide');

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success_redirect']) {

                    location = json['success_redirect'];

                } else if (json['success']) {

                    $('.alert, .popup-note, .oxyo-spinner.ajax-call, .text-danger').remove();
                    html = '<div class="popup-note">';
                    html += '<div class="inner">';
                    html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
                    html += '<div class="table">';
                    html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
                    html += '<div class="table-cell v-top">' + json['success'] + '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('body').append(html);
                    setTimeout(function () { $('.popup-note').hide(); }, 8100);
                    // Need to set timeout otherwise it wont update the total
                    setTimeout(function () {
                        $('.cart-total-items').html(json['total_items']);
                        $('.cart-total-amount').html(json['total_amount']);
                    }, 100);

                    $('#cart-content').load('index.php?route=common/cart/info #cart-content > *');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'update': function (key, quantity) {
        $.ajax({
            url: 'index.php?route=checkout/cart/edit',
            type: 'post',
            data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',

            success: function (json) {
                location = 'index.php?route=checkout/cart';
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function (key) {
        $.ajax({
            url: 'index.php?route=extension/oxyo/oxyo_features/remove_from_cart',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',
            beforeSend: function () {
                $('#cart > button').button('loading');
            },
            complete: function () {
                $('#cart > button').button('reset');
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $('.cart-total-items').html(json['total_items']);
                    $('.cart-total-amount').html(json['total_amount']);
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart-content').load('index.php?route=common/cart/info #cart-content > *');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}

var voucher = {
    'add': function () {
    },
    'remove': function (key) {
        $.ajax({
            url: 'index.php?route=checkout/cart/remove',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',

            success: function (json) {
                location = 'index.php?route=checkout/cart';
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}

var wishlist = {
    'add': function (product_id) {
        $.ajax({
            url: 'index.php?route=extension/oxyo/oxyo_features/add_to_wishlist',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function (json) {
                $('[data-toggle=\'tooltip\']').tooltip('hide');
                if (json['success_redirect']) {
                    location = json['success_redirect'];
                } else if (json['success']) {
                    $('.alert, .popup-note, .oxyo-spinner.ajax-call, .text-danger').remove();
                    html = '<div class="popup-note">';
                    html += '<div class="inner">';
                    html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
                    html += '<div class="table">';
                    html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
                    html += '<div class="table-cell v-top">' + json['success'] + '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('body').append(html);
                    setTimeout(function () { $('.popup-note').hide(); }, 8100);
                }
                $('.wishlist-total span').html(json['total']);
                $('.wishlist-counter').html(json['total_counter']);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function () {

    }
}

var compare = {
    'add': function (product_id) {
        $.ajax({
            url: 'index.php?route=extension/oxyo/oxyo_features/add_to_compare',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function (json) {
                $('[data-toggle=\'tooltip\']').tooltip('hide');
                if (json['success_redirect']) {
                    location = json['success_redirect'];
                } else if (json['success']) {
                    $('.alert, .popup-note, .oxyo-spinner.ajax-call, .text-danger').remove();
                    html = '<div class="popup-note">';
                    html += '<div class="inner">';
                    html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
                    html += '<div class="table">';
                    html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
                    html += '<div class="table-cell v-top">' + json['success'] + '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('body').append(html);
                    setTimeout(function () { $('.popup-note').hide(); }, 8100);
                    $('#compare-total').html(json['total']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function () {
    }
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function (e) {
    e.preventDefault();
    $('#modal-agree').remove();
    var element = this;
    $.ajax({
        url: $(element).attr('href'),
        type: 'get',
        dataType: 'html',
        success: function (data) {
            html = '<div id="modal-agree" class="modal">';
            html += '  <div class="modal-dialog">';
            html += '    <div class="modal-content">';
            html += '      <div class="modal-header">';
            html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
            html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
            html += '      </div>';
            html += '      <div class="modal-body">' + data + '</div>';
            html += '    </div';
            html += '  </div>';
            html += '</div>';
            $('body').append(html);
            $('#modal-agree').modal('show');
        }
    });
});
// Contact Form
var contact_form_send = function (form_id) {
    $.ajax({
        url: 'index.php?route=extension/oxyo/oxyo_features/oxyo_send_message',
        type: 'post',
        dataType: 'json',
        data: $('#mail_form' + form_id + '').serialize(),
        complete: function () {
            $('#mail_form' + form_id + ' .captchaimg').attr('src', 'index.php?route=extension/oxyo/oxyo_features/oxyo_captcha#' + new Date().getTime());
            $('#mail_form' + form_id + ' input[name=\'captcha\']').val('');
        },
        success: function (json) {
            $('#mail_form' + form_id + ' .respond').html('');
            if (json['error']) {
                $('#mail_form' + form_id + ' .respond').html('<p class="alert alert-danger">' + json['error'] + '</p>');
            }
            if (json['success']) {
                $('#mail_form' + form_id + ' .respond').html('<p class="alert alert-success">' + json['success'] + '</p>');
                $('#mail_form' + form_id + ' input').val('');
                $('#mail_form' + form_id + ' textarea').val('');
            }
        }
    });
};
// Autocomplete */
(function ($) {
    $.fn.autocomplete = function (option) {
        return this.each(function () {
            this.timer = null;
            this.items = new Array();
            $.extend(this, option);
            $(this).attr('autocomplete', 'off');
            // Focus
            $(this).on('focus', function () {
                this.request();
            });
            // Blur
            $(this).on('blur', function () {
                setTimeout(function (object) {
                    object.hide();
                }, 200, this);
            });
            // Keydown
            $(this).on('keydown', function (event) {
                switch (event.keyCode) {
                    case 27: // escape
                        this.hide();
                        break;
                    default:
                        this.request();
                        break;
                }
            });
            // Click
            this.click = function (event) {
                event.preventDefault();
                value = $(event.target).parent().attr('data-value');
                if (value && this.items[value]) {
                    this.select(this.items[value]);
                }
            }
            // Show
            this.show = function () {
                var pos = $(this).position();
                $(this).siblings('ul.dropdown-menu').css({
                    top: pos.top + $(this).outerHeight(),
                    left: pos.left
                });
                $(this).siblings('ul.dropdown-menu').show();
            }
            // Hide
            this.hide = function () {
                $(this).siblings('ul.dropdown-menu').hide();
            }
            // Request
            this.request = function () {
                clearTimeout(this.timer);
                this.timer = setTimeout(function (object) {
                    object.source($(object).val(), $.proxy(object.response, object));
                }, 200, this);
            }
            // Response
            this.response = function (json) {
                html = '';
                if (json.length) {
                    for (i = 0; i < json.length; i++) {
                        this.items[json[i]['value']] = json[i];
                    }
                    for (i = 0; i < json.length; i++) {
                        if (!json[i]['category']) {
                            html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
                        }
                    }
                    // Get all the ones with a categories
                    var category = new Array();
                    for (i = 0; i < json.length; i++) {
                        if (json[i]['category']) {
                            if (!category[json[i]['category']]) {
                                category[json[i]['category']] = new Array();
                                category[json[i]['category']]['name'] = json[i]['category'];
                                category[json[i]['category']]['item'] = new Array();
                            }
                            category[json[i]['category']]['item'].push(json[i]);
                        }
                    }
                    for (i in category) {
                        html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

                        for (j = 0; j < category[i]['item'].length; j++) {
                            html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
                        }
                    }
                }
                if (html) {
                    this.show();
                } else {
                    this.hide();
                }
                $(this).siblings('ul.dropdown-menu').html(html);
            }
            $(this).after('<ul class="dropdown-menu"></ul>');
            $(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
        });
    }
})(window.jQuery);