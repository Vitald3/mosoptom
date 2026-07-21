$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function modal_show(id, t) {
    if (typeof id !== undefined && id && $(id).length) {
        $(id).addClass('active').fadeIn();
        if (typeof t == "undefined") $('body').css('overflow', 'hidden');
    }
}

if (location.hash) {
    modal_show(location.hash);
}

function selectize(element) {
    if (typeof element !== undefined && element && $(element).length) {
        $(element).each(function(i, e){
            $(e).fadeOut(0);

            var select = '<div class="selectize input">';
            select += '<div class="select_value pointer" onclick="selectize_init(this, ' + i + ');">' + (typeof $(e).attr('data-text') != "undefined" ? $(e).attr('data-text') : '') + '</div>';
            select += '<svg onclick="selectize_init(this, ' + i + ');" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '    <path d="M5.99997 9.41846C5.7849 9.41846 5.56987 9.33635 5.4059 9.17246L0.24617 4.01268C-0.0820565 3.68445 -0.0820565 3.15229 0.24617 2.82419C0.574263 2.4961 1.10632 2.4961 1.43457 2.82419L5.99997 7.38986L10.5654 2.82435C10.8936 2.49626 11.4256 2.49626 11.7537 2.82435C12.0821 3.15245 12.0821 3.68461 11.7537 4.01284L6.59404 9.17262C6.42999 9.33653 6.21495 9.41846 5.99997 9.41846Z" fill="#484848"/>\n' +
                '</svg>';
            select += '<div class="overflow-y"><ul class="list-un-styled">'

            $(e).find('option').each(function(){
                select += '<li data-value="' + $(this).attr('value') + '" onclick="selectize_set(this);" class="pointer' + ($(this).attr('selected') ? ' active' : '') + '">' + $(this).text() + '</li>';
            });

            select += '</ul></div>';
            select += '</div>';

            $(e).before(select);

            var f = false;

            $(e).find('option').each(function(){
                if (typeof $(this).attr('selected') !== "undefined") {
                    $(e).parent().addClass('active');
                    $(e).parent().find('label').addClass('active');
                    $(e).parent().find('.select_value').text($(this).text());
                    f = true;
                }
            });

            if (!f && $(e).attr('data-text') == "undefined") {
                $(e).parent().find('.select_value').text($(e).find('option:first').text());
                $(e).val(0);
            }
        });
    }
}

function selectize_set(e) {
    $(e).closest('.selectize').find('li').removeClass('active');
    $(e).closest('.selectize').removeClass('active');
    $(e).closest('.form_group').removeClass('error');

    if (!$(e).hasClass('active')) {
        $(e).addClass('active');
        $(e).closest('.selectize').prev().val($(e).attr('data-value'));
        $(e).closest('.selectize').find('.select_value').text($(e).text());
        $(e).closest('.selectize').parent().addClass('active');
        $(e).closest('.selectize').next().val($(e).attr('data-value')).trigger('change');
    } else {
        $(e).removeClass('active');
        $(e).closest('.selectize').prev().val('');
        $(e).closest('.selectize').find('.select_value').text($(e).text(''));
        $(e).closest('.selectize').parent().removeClass('active');
        $(e).closest('.selectize').next().val('').trigger('change');
    }

    $(e).closest('.selectize').find('.overflow-y').slideToggle();
}

function selectize_init(e, i) {
    $('.selectize:not(:eq(' + i + '))').removeClass('active');
    $(e).parent().toggleClass('active');
    $(e).parent().parent().find('.required').toggleClass('active');
    $(e).parent().find('.overflow-y').slideToggle();
}
$(document).on('click', '.cart_item .change_quantity', function(){
    var self = $(this);
    var qw = self.attr('data-quantity');

    if (self.hasClass('active')) {
        self.parent().find('.change_quantity').removeClass('active');
        qw = self.closest('.cart_item').find('.product_name .quant input').attr('data-quantity');
    } else {
        self.parent().find('.change_quantity').removeClass('active');
        self.addClass('active');
        self.closest('.cart_item').find('.product_name .quant input').attr('data-quantity', self.closest('.cart_item').find('.product_name .quant input').val());
    }

    self.closest('.cart_item').find('.product_name .quant input').val(qw).trigger('input');
});
$(document).on('click', '.form_group .required', function() {
    if ($(this).parent().find('.selectize:not(.active)').length) {
        $(this).parent().find('.selectize:not(.active) .select_value').trigger('click');
        $(this).toggleClass('active');
    }
});
$(document).on('click', '.top_menu li:first a', function() {
    modal_show('#modal_minimal');
    return false;
});
$(document).on('click', '.link_top', function() {
    $('html, body').animate({ scrollTop: 0}, 'slow');
    return false;
});

var inpsToMonitor = document.querySelectorAll('.input');

for (var J = inpsToMonitor.length - 1; J >= 0; --J) {
    inpsToMonitor[J].addEventListener ("change",    adjustStyling, false);
    inpsToMonitor[J].addEventListener ("keyup",     adjustStyling, false);
    inpsToMonitor[J].addEventListener ("focus",     adjustStyling, false);
    inpsToMonitor[J].addEventListener ("blur",      adjustStyling, false);
    inpsToMonitor[J].addEventListener ("mousedown", adjustStyling, false);

    var evt = document.createEvent ("HTMLEvents");
    evt.initEvent ("change", false, true);
    inpsToMonitor[J].dispatchEvent (evt);
}

function adjustStyling (zEvent) {
    var inpVal = zEvent.target.value;
    var type = $(zEvent.target).attr('type');
    if (type == 'tel') return false;

    if (inpVal && inpVal.replace(/^\s+|\s+$/g, "")) {
        if (type == 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inpVal)) {
            $(zEvent.target).parent().addClass('error');
        } else {
            $(zEvent.target).parent().removeClass('error');
            $(zEvent.target).parent().addClass('active');
        }
    } else {
        $(zEvent.target).parent().removeClass('active error');
    }
}

let tooltipElem;

document.onmouseover = function(event) {
    let target = event.target;
    let tooltipHtml = target.dataset.tooltip;

    if (!tooltipHtml) return;

    tooltipElem = document.createElement('div');
    tooltipElem.className = 'tooltip';
    tooltipElem.innerHTML = tooltipHtml;
    document.body.append(tooltipElem);

    let coords = target.getBoundingClientRect();

    let left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
    if (left < 0) left = 0;

    let top = coords.top - tooltipElem.offsetHeight - 5;
    if (top < 0) {
        top = coords.top + target.offsetHeight + 5;
    }

    tooltipElem.style.left = left + 'px';
    tooltipElem.style.top = top + 'px';
}

document.onmouseout = function(e) {
    if (tooltipElem) {
        tooltipElem.remove();
        tooltipElem = null;
    }
}

$(document).on('click', '.addattach', function() {
    $('#write_attach').append('<div class="item_file flex-2 flex-start" style="display:none"><input type="file" name="file[]" accept=".pdf, image/jpg, image/png, image/gif, .docx, .xlsx" class="imagef" style="display: none"><a class="btn-remove" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">\n' +
        '<path d="M9.9375 1.5H7.875V1.125C7.875 0.50468 7.37032 0 6.75 0H5.25C4.62968 0 4.125 0.50468 4.125 1.125V1.5H2.0625C1.54556 1.5 1.125 1.92056 1.125 2.4375V3.75C1.125 3.95709 1.29291 4.125 1.5 4.125H1.70494L2.02891 10.9285C2.05753 11.5294 2.55112 12 3.15262 12H8.84737C9.4489 12 9.94249 11.5294 9.97109 10.9285L10.2951 4.125H10.5C10.7071 4.125 10.875 3.95709 10.875 3.75V2.4375C10.875 1.92056 10.4544 1.5 9.9375 1.5ZM4.875 1.125C4.875 0.918234 5.04323 0.75 5.25 0.75H6.75C6.95677 0.75 7.125 0.918234 7.125 1.125V1.5H4.875V1.125ZM1.875 2.4375C1.875 2.33412 1.95912 2.25 2.0625 2.25H9.9375C10.0409 2.25 10.125 2.33412 10.125 2.4375V3.375C10.0094 3.375 2.3539 3.375 1.875 3.375V2.4375ZM9.22193 10.8928C9.21239 11.0931 9.04786 11.25 8.84737 11.25H3.15262C2.95212 11.25 2.78759 11.0931 2.77807 10.8928L2.45578 4.125H9.54422L9.22193 10.8928Z" fill="#EB5757"/>\n' +
        '<path d="M6 10.5C6.20709 10.5 6.375 10.3321 6.375 10.125V5.25C6.375 5.04291 6.20709 4.875 6 4.875C5.79291 4.875 5.625 5.04291 5.625 5.25V10.125C5.625 10.3321 5.79288 10.5 6 10.5Z" fill="#EB5757"/>\n' +
        '<path d="M7.875 10.5C8.08209 10.5 8.25 10.3321 8.25 10.125V5.25C8.25 5.04291 8.08209 4.875 7.875 4.875C7.66791 4.875 7.5 5.04291 7.5 5.25V10.125C7.5 10.3321 7.66788 10.5 7.875 10.5Z" fill="#EB5757"/>\n' +
        '<path d="M4.125 10.5C4.33209 10.5 4.5 10.3321 4.5 10.125V5.25C4.5 5.04291 4.33209 4.875 4.125 4.875C3.91791 4.875 3.75 5.04291 3.75 5.25V10.125C3.75 10.3321 3.91788 10.5 4.125 10.5Z" fill="#EB5757"/>\n' +
        '</svg></a></div>');

    $('#write_attach > div:last-child').find('input').trigger('click');
});

$(document).on('change', '#write_attach input', function() {
    var fileName = $(this).val().split('/').pop().split('\\').pop();
    $(this).parent().append('<span class="file_name">' + fileName + '</span>');
    $(this).parent().fadeIn();
});

$(document).on('click', '.item_file .btn-remove', function() {
    $(this).parent().remove();
    return false;
});

if ($(window).width() < 960) {
    $(document).on('click', '.first_level a', function () {
        if ($(this).parent().find('> ul').length) {
            var ul = $(this).parent().find('> ul');

            if (!ul.find('.title_second').length) {
                var title = '<li class="title_second"><div class="flex-2 flex-start"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">\n' +
                    '<path d="M0 0H24C32.8366 0 40 7.16344 40 16C40 24.8366 32.8366 32 24 32H0V0Z" fill="#54B0AC"/>\n' +
                    '<g clip-path="url(#clip0_506_45445)">\n' +
                    '<path d="M17.1493 16C17.1493 15.8208 17.2178 15.6416 17.3543 15.5049L21.6541 11.2051C21.9277 10.9316 22.3711 10.9316 22.6446 11.2051C22.918 11.4786 22.918 11.9219 22.6446 12.1955L18.8398 16L22.6444 19.8045C22.9178 20.078 22.9178 20.5213 22.6444 20.7947C22.371 21.0684 21.9275 21.0684 21.654 20.7947L17.3542 16.495C17.2176 16.3583 17.1493 16.1791 17.1493 16Z" fill="white"/>\n' +
                    '</g>\n' +
                    '<defs>\n' +
                    '<clipPath id="clip0_506_45445">\n' +
                    '<rect width="10" height="10" fill="white" transform="translate(25 11) rotate(90)"/>\n' +
                    '</clipPath>\n' +
                    '</defs>\n' +
                    '</svg><a class="ssil" href="' + $(this).attr('href') + '"><span style="color: #54B0AC">' + $(this).text() + '</span></a></div></li>';

                ul.prepend(title);
            }

            $('.menu_drop').css('overflow', 'hidden');
            ul.addClass('overflow-y');
            fadeToggle(ul);
            $('.burger .svg_hid').fadeOut(0);
            $('.burger .svg_hid2').fadeIn(0);
            $('.burger span').addClass('active_x');

            return false;
        }
    });

    $(document).on('click', '.ssil', function (e) {
        e.preventDefault();
        location = $(this).attr('href');
        return false;
    });

    $(document).on('click', '.title_second', function (e) {
        if ($(this).parent().parent().hasClass('second_level')) {
            $(this).parent().parent().find('ul').fadeOut(0).removeClass('active');
        } else {
            $('.svg_hid2').fadeOut(0);
            $('.burger .svg_hid').fadeIn(0);
            $('.burger span').removeClass('active_x');
            $('.menu_drop .list-un-styled.active').fadeOut(0).removeClass('active');
            $('.menu_drop').css('overflow', 'auto');
            $('.menu_drop .list-un-styled.active').removeClass('overflow-y');
        }

        return false;
    });

    $(document).on('click', '.svg_hid2', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($('.menu_drop .second_level .list-un-styled.active').parent().hasClass('second_level')) {
            $('.menu_drop .second_level .list-un-styled.active').parent().find('ul').fadeOut(0).removeClass('active');
        } else {
            $(this).fadeOut(0);
            $('.burger .svg_hid').fadeIn(0);
            $('.burger span').removeClass('active_x');
            fadeToggle('.menu_drop .list-un-styled.active');
            $('.menu_drop').css('overflow', 'auto');
            $('.menu_drop .list-un-styled.active').removeClass('overflow-y');
        }
        return false;
    });
}

$(document).on('click', '.btn-shadow a', function() {
    var phone = $(this).parent().find('[name="phone"]');
    var self = $(this);

    if (self.prop('disabled')) return false;
    self.attr('disabled', true);

    if (phone.val() && phone.attr('data-mask').length && phone.attr('data-mask').length != phone.val().length) {
        self.parent().addClass('error');
    } else if(!phone.val() && $(this).parent().hasClass('active')) {
        self.parent().addClass('error');
    } else {
        self.parent().removeClass('error');

        if (phone.attr('data-mask').length == phone.val().length) {
            $.ajax({
                url: $('base').attr('href') + '/form-action',
                type: 'POST',
                dataType: 'json',
                data: {
                    phone: phone.val(),
                    type: 'callback'
                },
                success: function (json) {
                    if (json.success) {
                        self.removeAttr('disabled');
                        phone.val('');
                        phone.closest('.btn-shadow').removeClass('active');
                        $('#modal_success .modal-content').html('<a href="#" class="close" data-close-modal="#modal_success">\n' +
                            '            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">\n' +
                            '                <g clip-path="url(#clip0_506_47034)">\n' +
                            '                    <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>\n' +
                            '                </g>\n' +
                            '                <defs>\n' +
                            '                    <clipPath id="clip0_506_47034">\n' +
                            '                        <rect width="20" height="20" fill="white"/>\n' +
                            '                    </clipPath>\n' +
                            '                </defs>\n' +
                            '            </svg>\n' +
                            '        </a>' + json.success);
                        $('#modal_success').fadeIn().addClass('active');

                        setTimeout(function () {
                            $('#modal_success').fadeOut().removeClass('active');
                            $('body').css('overflow', 'hidden');
                        }, 3500);
                    } else {
                        self.parent().addClass('error');
                    }
                }
            });

            return false;
        }
    }

    $(this).parent().addClass('active');

    return false;
});

$(document).on('click', '[data-target-modal]', function () {
    var id = $(this).attr('data-target-modal');

    if (typeof id !== undefined && id && $(id).length) {
        $('.modal.active').fadeOut().removeClass('active');
        $(id).addClass('active').fadeIn();
        $('body').css('overflow', 'hidden');

        if (id === '#modal_cart' && $(window).width() > 960) {
            $(id).find('.cart_items').animate({ right: 0}, 'slow');
        }
    }

    return false;
});

$(document).on('click', '[data-close-modal]', function () {
    var id = $(this).attr('data-close-modal');

    if (typeof id !== undefined && id && $(id).length) {
        if (id === '#modal_cart') {
            $(id).find('.cart_items').animate({ right: '-100%'}, 'slow');
        }

        $(id).removeClass('active').fadeOut();
        $('body').css('overflow', 'unset');
    }

    return false;
});

function fixed_top() {
    var h = $('header').offset().top;
    var header_height = $('header').height();

    if ($(window).scrollTop() > 0) {
        $('body').css('padding-top', header_height + 'px');
        $('header').addClass('fixed');
    } else {
        $('header').removeClass('fixed');
        $('body').css('padding-top', 0);
    }
}
$(document).ready(function () {
    var h = $('header').offset().top;
    var header_height = $('header').height();

    $(window).scroll(function () {
        if ($(window).scrollTop() > 200) {
            $('.link_top').fadeIn();
        } else {
            $('.link_top').fadeOut();
        }
    });

    if ($(window).width() > 960) {
        $(window).scroll(function () {
            if ($(window).scrollTop() > h && $(window).scrollTop() > 0) {
                $('body').css('padding-top', header_height + 'px');
                $('header').addClass('fixed');
            } else {
                $('header').removeClass('fixed');
                $('body').css('padding-top', 0);
            }
        });

        fixed_top();
    }

    wishlist.getList();
    selectize('.selectize');

    $('a[href^="#"]:not(.link-tab)').each(function() {
        var href = $(this).attr('href');

        if (href.length > 1) {
            $(this).on('click', function (e) {
                modal_show(href);
                return false;
            });
        }
    });
});

$(document).mouseup(function (e){
    var modal = $('.modal.active .modal-content');

    if (modal.length && !modal.is(e.target) && modal.has(e.target).length === 0) {
        modal.parent().fadeOut();
        $('body').css('overflow', 'unset');
    }

    var filters = $('#filters.active');

    if (filters.length && !filters.is(e.target) && filters.has(e.target).length === 0) {
        $('.products').css('padding-top', '50px');
        filters.removeClass('active');
        $('.products').removeClass('preload preload2');
    }

    var menus = $('.menu_drop.active > div');

    if (menus.length && !menus.is(e.target) && menus.has(e.target).length === 0) {
        fadeToggle(menus.parent());
        $('body').css('overflow', 'unset');
    }

    var selectize = $('.selectize.active');

    if (selectize.length && !selectize.is(e.target) && selectize.has(e.target).length === 0) {
        if (selectize.parent().find('label.required.active').length) {
            selectize.parent().find('label.required.active').removeClass('active');
        }

        selectize.removeClass('active').find('.overflow-y').fadeOut();
    }

    var modal_cart = $('.modal_cart.active .cart_items');

    if (modal_cart.length && !modal_cart.is(e.target) && modal_cart.has(e.target).length === 0) {
        modal_cart.animate({ right: '-100%'}, 'slow');
        modal_cart.parent().fadeOut();
        $('body').css('overflow', 'unset');
    }

    var search_box = $('.search_box.active');

    if (search_box.length && !search_box.is(e.target) && search_box.has(e.target).length === 0 && !$('.search_show').is(e.target) && $('.search_show').has(e.target).length === 0) {
        search_box.css('display', 'none').removeClass('active');
        $('.search_show').removeClass('active');
    }
});

function fadeToggle(element, timeout) {
    if (typeof timeout === undefined) {
        timeout = 300;
    }

    if (typeof element !== undefined && element && $(element).length) {
        if (!$(element).hasClass('active')) {
            $(element).addClass('active').fadeIn(timeout);

            if (!$(element).hasClass('tabl_dis'))
            $('body').css('overflow', 'hidden');
        } else {
            $(element).fadeOut(timeout).removeClass('active');
            $('body').css('overflow', 'unset');
        }
    }
}

$(document).on('change', '.error-input input', function () {
    if ($(this).val().length > 0) {
        $(this).closest('.error-input').removeClass('error-input');
    }
});

$(document).on('click', '.menu_button', function () {
    fadeToggle('.menu_drop');
    return false;
});

$(document).on('click', 'header .search, .mob_search', function () {
    $('#modal_search input').focus();
    $('#modal_search').fadeIn().addClass('active');
    $('body').css('overflow', 'hidden');
    return false;
});

$(document).on('click', '.zapros span', function () {
    var text = $(this).attr('data-text');
    $('.zapros span').removeClass('active');

    if (!$(this).hasClass('active')) {
        $(this).addClass('active');
        $('.search [name="search"]').val(text).trigger('input');
    } else {
        $(this).removeClass('active');
        $('.search [name="search"]').val('').trigger('input');
    }

    return false;
});

$(document).on('click', '.categories_input a', function () {
    var text = $(this).attr('data-text');
    $('.zapros span').removeClass('active');
    $('.search [name="search"]').val(text).trigger('input');

    return false;
});

$(document).on('input focus', '#modal_search [name="search"]', function () {
    var self = $(this);

    if (self.val().length > 0) {
        $.ajax({
            url: $('base').attr('href') + '/post_search',
            type: 'POST',
            dataType: 'html',
            data: {
                search: encodeURIComponent(self.val())
            },
            success: function(html) {
                $('.search_right').fadeOut(0);
                $('.search_empty').html(html);
                $('.search_empty').addClass('active');
                wishlist.getList();
            }
        });
    } else {
        $('.search_right').fadeIn(0);
    }
});

$(document).on('change', '.custom_checkbox.error', function () {
    $(this).removeClass('error');
});
$(document).on('click', '.flex_tabs li', function() {
    var href = $(this).attr('data-href');
    $('.flex_tabs li').removeClass('active');
    $(this).addClass('active');

    if (href && typeof href != "undefined" && $(href).length) {
        $(this).parent().next().find('.tab-pane.active').fadeOut(0).removeClass('active');
        $(href).fadeIn().addClass('active');
    }
});
$(document).on('click', '.flex_tabs li a', function() {
    var href = $(this).closest('li').attr('data-href');
    $('.flex_tabs li').removeClass('active');
    $(this).closest('li').addClass('active');

    if (href && typeof href != "undefined" && $(href).length) {
        $(this).closest('li').parent().next().find('.tab-pane.active').fadeOut(0).removeClass('active');
        $(href).fadeIn().addClass('active');
    }

    return false;
});
$(document).on('click', 'ul.tabs a', function () {
    var href = $(this).attr('href');
    $(this).closest('.tabs').find('a').removeClass('active');
    $(this).addClass('active');
    $(this).find('input').prop('checked', true).trigger('change');

    if (href && typeof href != "undefined" && $(href).length) {
        if ($(this).parent().parent().parent().hasClass('over')) {
            $(this).parent().parent().parent().next().find('.tab-pane.active').fadeOut(0).removeClass('active');
        } else {
            $(this).parent().parent().next().find('.tab-pane.active').fadeOut(0).removeClass('active');
        }

        $(href).fadeIn().addClass('active');
    }

    return false;
});

$(document).on('submit', '.validate_js', function () {
    var self = $(this);
    var submit = self.find('[type="submit"]');
    if (submit.prop('disabled')) return false;
    submit.attr('disabled', true);

    if (!submit.find('label.error').length) {
        var formData = new FormData();
        var formImage = new FormData();

        self.find('[type="text"],[type="email"],[type="tel"],[type="number"],[type="hidden"],[type="password"],[type="checkbox"]:checked,[type="radio"]:checked,textarea,select').each(function(){
            if ($(this).val() && $(this).val() != 'null') formData.append($(this).attr('name'), $(this).val());
        });

        if (self.find('.imagef').length) {
            self.find('.imagef').each(function () {
                if ($(this).val() != '') {
                    formImage.append('file[]', $(this).prop('files')[0]);
                }
            });

            $.ajax({
                url: $('base').attr('href') + '/form_add_image',
                type: 'post',
                dataType: 'json',
                data: formImage,
                contentType: false,
                cache: false,
                processData: false,
                async: false,
                success: function (json) {
                    if (json.errors) {
                        for (var i in json.errors) {
                            self.find('#write_attach .item_file').eq(i).remove();
                        }

                        $('html, body').animate({scrollTop: self.find('#write_attach .item_file:first').offset().top}, 'slow');
                    } else {
                        for (var i in json) formData.append('file[]', json[i]);
                    }
                }
            });
        }

        $.ajax({
            url: self.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType:false,
            cache: false,
            processData: false,
            async: false,
            success: function(json) {
                submit.removeAttr('disabled');
                $('.alert-danger').remove();

                if (json.errors) {
                    var x = 0, name = '';
                    for (var i in json.errors) {
                        if (x == 0) var name = i;
                        self.find('[name="' + i + '"]').parent().addClass('error');
                        x++;
                    }

                    if (self.closest('.modal').length) {
                        $(self.closest('.modal')).animate({ scrollTop: self.find('[name="' + name + '"]').parent().offset().top }, 'slow');
                    } else {
                        $('html, body').animate({ scrollTop: self.find('[name="' + name + '"]').parent().offset().top-70 }, 'slow');
                    }
                } else if (json.error) {
                    submit.before('<div class="alert alert-danger">' + json.error + '</div>')
                } else if (json.redirect) {
                    location = json.redirect;
                } else {
                    if (typeof self.attr('data-step') !== "undefined" && self.attr('data-step')) {
                        if (typeof self.attr('data-text') !== "undefined" && self.attr('data-text') && json.sms == 1) {
                            $(self.attr('data-step')).find('[type="submit"]').before('<div class="alert alert-danger">' + self.attr('data-text') + '</div>');
                        }

                        $(self.closest('.modal')).fadeOut(0).removeClass('active');
                        $(self.attr('data-step')).fadeIn().addClass('active');

                        if ($('#timer_js').length) {
                            timerOr('#timer_js', 5)
                        }
                    } else {
                        $('#modal_success .modal-content').html('<a href="#" class="close" data-close-modal="#modal_success">\n' +
                            '            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">\n' +
                            '                <g clip-path="url(#clip0_506_47034)">\n' +
                            '                    <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>\n' +
                            '                </g>\n' +
                            '                <defs>\n' +
                            '                    <clipPath id="clip0_506_47034">\n' +
                            '                        <rect width="20" height="20" fill="white"/>\n' +
                            '                    </clipPath>\n' +
                            '                </defs>\n' +
                            '            </svg>\n' +
                            '        </a>' + (json.title ? '<div class="write_title">' + json.title + '</div>' : '') + json.success);

                        if (json.title) {
                            $('#modal_success .modal-content').addClass('text-center');
                        } else {
                            $('#modal_success .modal-content').removeClass('text-center');
                        }

                        $(self.closest('.modal')).fadeOut().removeClass('active');
                        $('#modal_success').fadeIn().addClass('active');
                        self.trigger('reset');
                        self.find('#write_attach').html('');

                        if ($('#timer_js').length) {
                            timerOr('#timer_js', 5)
                        }

                        setTimeout(function(){
                            $('#modal_success').fadeOut().removeClass('active');
                            $('body').css('overflow', 'unset');
                        }, 3500);
                    }
                }
            }
        });
    } else {
        $('html, body').animate({ scrollTop: self.find('.form_group.error:eq(0)')});
    }

    return false;
});

function timerOr(id, num) {
    var nNum = parseInt(num) - 1;

    if (nNum > 0) {
        setTimeout(function() {
            $(id).html(nNum + '...');
            timerOr(id, nNum);
        }, 1000);
    } else {
        $(id).closest('.modal').fadeOut().removeClass('active');
        $(id).closest('.modal2').fadeOut().removeClass('active');
        $('body').css('overflow', 'unset');
    }
}

$(document).on('click', '.plus_qw', function(){
    var input = $(this).parent().find('input');
    var qw = parseInt(input.val()) + 1;
    input.val(qw).trigger('input');
});

$(document).on('click', '.minus_qw', function(){
    var input = $(this).parent().find('input');
    var qw = parseInt(input.val()) - 1;
    if (qw <= 0) qw = 1;
    input.val(qw).trigger('input');
});

var cart = {
    'add': function(id, quantity, self) {
        $.ajax({
            url: $('base').attr('href') + '/cart_add',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: id,
                quantity: quantity,
                option: []
            },
            success: function(json) {
                $('#modal_cart .cart_items').html($(json['html']).find('.cart_items').html());
                $('#cart_total').text(json['total']);
                wishlist.getList();
            }
        });
    },
    'update': function(id, quantity) {
        $.ajax({
            url: $('base').attr('href') + '/cart_update',
            type: 'POST',
            dataType: 'json',
            data: {
                cart_id: id,
                quantity: quantity
            },
            success: function(json) {
                $('#modal_cart .cart_items').html($(json['html']).find('.cart_items').html());
                $('#cart_total').text(json['total']);
                wishlist.getList();
            }
        });
    },
    'remove': function(id, quantity, self) {
        $.ajax({
            url: $('base').attr('href') + '/cart_delete',
            type: 'POST',
            dataType: 'json',
            data: {
                cart_id: id
            },
            success: function(json) {
                $('#modal_cart .cart_items').html($(json['html']).find('.cart_items').html());
                $('#cart_total').text(json['total']);
                wishlist.getList();
            }
        });
    }
}

var wishlist = {
    'add': function(id) {
        $.ajax({
            url: $('base').attr('href') + '/wishlist_add',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: id
            },
            success: function(json) {
                $('.product_wishlist-' + id + ':not(.wish_remove)').addClass('active');
                $('.product_wishlist-' + id + ':not(.wish_remove)').find('svg').replaceWith('<svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9168 1.30774C13.1528 -0.435676 10.283 -0.435676 8.51946 1.30774L8.10984 1.71244L7.70045 1.30774C5.93688 -0.435912 3.0669 -0.435912 1.30332 1.30774C-0.424444 3.01575 -0.435664 5.72314 1.2773 7.60554C2.83965 9.32182 7.44742 13.0298 7.64292 13.1867C7.77565 13.2933 7.93534 13.3453 8.09408 13.3453C8.09933 13.3453 8.10458 13.3453 8.1096 13.345C8.27383 13.3526 8.43925 13.2969 8.57627 13.1867C8.77177 13.0298 13.38 9.32182 14.9428 7.60531C16.6556 5.72314 16.6444 3.01575 14.9168 1.30774Z" fill="#54B0AC"/></svg>');
                $('.product_wishlist-' + id + ':not(.wish_remove)').attr('onclick', 'wishlist.remove(' + id + ');return false;');
                $('#wishlist_total').text(json['count']);
            }
        });
    },
    'remove': function(id) {
        $.ajax({
            url: $('base').attr('href') + '/wishlist_delete',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: id
            },
            success: function(json) {
                $('.product_wishlist-' + id + ':not(.wish_remove)').removeClass('active');
                $('.product_wishlist-' + id + ':not(.wish_remove)').find('svg').replaceWith('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none"><path d="M15.1668 1.32286C13.4028 -0.440714 10.533 -0.440714 8.76946 1.32286L8.35983 1.73224L7.95045 1.32286C6.18688 -0.440953 3.3169 -0.440953 1.55332 1.32286C-0.174444 3.05063 -0.185664 5.78932 1.5273 7.69349C3.08965 9.42961 7.69742 13.1804 7.89292 13.3392C8.02564 13.4471 8.18534 13.4996 8.34408 13.4996C8.34933 13.4996 8.35458 13.4996 8.3596 13.4993C8.52383 13.507 8.68925 13.4506 8.82627 13.3392C9.02177 13.1804 13.63 9.42961 15.1928 7.69325C16.9056 5.78932 16.8944 3.05063 15.1668 1.32286ZM14.1282 6.73532C12.9101 8.08855 9.56173 10.8795 8.3596 11.8699C7.15746 10.8798 3.80983 8.08903 2.59194 6.73556C1.39697 5.40739 1.38575 3.51587 2.56592 2.3357C3.16866 1.7332 3.96021 1.43171 4.75177 1.43171C5.54332 1.43171 6.33488 1.73296 6.93761 2.3357L7.83802 3.2361C7.9452 3.34328 8.08031 3.40725 8.2221 3.42969C8.45222 3.47911 8.7019 3.41489 8.88093 3.23634L9.78182 2.3357C10.9875 1.13046 12.9488 1.1307 14.1537 2.3357C15.3339 3.51587 15.3227 5.40739 14.1282 6.73532Z" fill="#BED0D6"></path></svg>');
                $('.product_wishlist-' + id + ':not(.wish_remove)').attr('onclick', 'wishlist.add(' + id + ');return false;');
                $('#wishlist_total').text(json['count']);

                if ($('body.wishlist').length) {
                    $('.product_wishlist-' + id).closest('.col-3').remove();
                }
            }
        });
    },
    'getList': function() {
        $.ajax({
            url: $('base').attr('href') + '/getWishlist',
            type: 'POST',
            dataType: 'json',
            success: function(json) {
                if (json) {
                    for (var i in json) {
                        $('.product_wishlist-' + json[i]['product_id'] + ':not(.wish_remove)').addClass('active');
                        $('.product_wishlist-' + json[i]['product_id'] + ':not(.wish_remove)').find('svg').replaceWith('<svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9168 1.30774C13.1528 -0.435676 10.283 -0.435676 8.51946 1.30774L8.10984 1.71244L7.70045 1.30774C5.93688 -0.435912 3.0669 -0.435912 1.30332 1.30774C-0.424444 3.01575 -0.435664 5.72314 1.2773 7.60554C2.83965 9.32182 7.44742 13.0298 7.64292 13.1867C7.77565 13.2933 7.93534 13.3453 8.09408 13.3453C8.09933 13.3453 8.10458 13.3453 8.1096 13.345C8.27383 13.3526 8.43925 13.2969 8.57627 13.1867C8.77177 13.0298 13.38 9.32182 14.9428 7.60531C16.6556 5.72314 16.6444 3.01575 14.9168 1.30774Z" fill="#54B0AC"/></svg>');
                        $('.product_wishlist-' + json[i]['product_id'] + ':not(.wish_remove)').attr('onclick', 'wishlist.remove(' + json[i]['product_id'] + ');return false;');
                    }
                }
            }
        });
    }
}

/*$.getScript('/assets/site/js/mask.js', function (data, textStatus, jqxhr) {
    if (jqxhr.status === 200) {
        $('[type="tel"]').focus(function () {
            $(this).mask($(this).attr('data-mask'));
        });

        $('.tel').each(function () {
            $(this).mask($(this).attr('data-mask'));
        });
    }
});*/