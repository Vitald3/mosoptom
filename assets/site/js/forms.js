$(document).on('click', '.forms .form_action a.sends', function () {
    var self = $(this);
    var form = self.closest('form');
    var error = false;

    form.find('.fake_input').removeClass('error-input');

    form.find('input[required], textarea[required], select[required]').each(function (){
        if ($(this).val() === '') {
            $(this).closest('.fake_input').addClass('error-input');
            error = true;
        }
    });

    if (!error) {
        form.find('.box-shadow').removeClass('error');
        self.removeClass('disabled').removeAttr('disabled');
    }

    if (typeof self.attr('disabled') !== "undefined" || error) return false;

    self.addClass('disabled').attr('disabled', true);

    $.ajax({
        url: form.attr('action') + '?title=' + $('title').text(),
        type: form.attr('method'),
        data: form.serialize(),
        dataType: 'json',
        success: function (json) {
            self.removeClass('disabled').removeAttr('disabled');

            if (json.success) {
                form.trigger('reset');

                if (typeof $('#modal_success') !== undefined && $('#modal_success').length) {
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
                    $('#modal_success').addClass('active').fadeIn();
                    $('body').css('overflow', 'hidden');
                }
            }
        }
    })

    return false;
});

$(document).ready(function() {
    if (typeof mask !== "function") {
        $.getScript('/assets/site/js/mask.js', function (data, textStatus, jqxhr) {
            if (jqxhr.status === 200) {
                $('[type="tel"]').focus(function () {
                    $(this).mask($('.forms .mask').attr('data-mask'));
                });

                $('.mask_phone').on('click', function () {
                    $(this).closest('.fake_input').find('[type="tel"]').mask($(this).attr('data-mask')).attr('placeholder', $(this).attr('data-mask').replace(/9/g, 'X'));
                    $(this).closest('.fake_input').find('.mask img').attr('src', $(this).find('img').attr('src'));
                    $(this).closest('.fake_input').find('.mask').trigger('click');
                    return false;
                });
            }
        });
    } else {
        $('[type="tel"]').focus(function () {
            $(this).mask($('.forms .mask').attr('data-mask'));
        });

        $('.mask_phone').on('click', function () {
            $(this).closest('.fake_input').find('[type="tel"]').mask($(this).attr('data-mask')).attr('placeholder', $(this).attr('data-mask').replace(/9/g, 'X'));
            $(this).closest('.fake_input').find('.mask img').attr('src', $(this).find('img').attr('src'));
            $(this).closest('.fake_input').find('.mask').trigger('click');
            return false;
        });
    }
});