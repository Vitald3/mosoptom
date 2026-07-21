(function($) {
    var filter = {
        slider: function() {
            $('.filter_slider').each(function () {
                if (typeof window['slider-' + $(this).attr('id')] !== "undefined") {
                    window['slider-' + $(this).attr('id')].destroy();
                }

                var self = $(this);
                var min = parseInt(self.attr('data-min'));
                var max = parseInt(self.attr('data-max'));
                var start = self.attr('data-start');
                var end = self.attr('data-end');

                window['slider-' + self.attr('id')] = noUiSlider.create(this, {
                    start: [start, end],
                    connect: true,
                    tooltips: false,
                    step: 1,
                    format: {
                        from: function(value) {
                            return parseInt(value);
                        },
                        to: function(value) {
                            return parseInt(value);
                        }
                    },
                    range: {
                        'min': min,
                        'max': max
                    }
                });

                window['slider-' + self.attr('id')].on('change', function (values) {
                    filter.update();
                });

                window['slider-' + self.attr('id')].on('slide', function (values) {
                    self.closest('li').find('.filter_start').val(values[0]);
                    self.closest('li').find('.filter_end').val(values[1]);
                });
            });
        },
        init: function(event, options) {
            this.action = options.action;
            this.more = options.load_more;
            this.$element = event;
            this.$target = $('.target', event);
            this.$values = $('label', event);

            filter.slider();
        },
        update: function() {
            var element = this.$element;

            $.ajax({
                url: this.action + ($(window).width() < 641 ? '?only_url=1' : ''),
                data: $(element).serialize(),
                dataType: 'json',
                type: 'POST',
                beforeSend: function() {
                    $('.products').addClass('preload');
                },
                success: function(json) {
                    if ($(window).width() < 640) {
                        if (json.count_product) {
                            $('.filter_click a').attr('href', json.url).html(json.count_product_text).parent().fadeIn();
                        } else {
                            $('.filter_click a').attr('href', json.url).html(json.count_product_text).parent().fadeOut();
                        }
                    } else if (json.html && $(window).width() > 640) {
                        var html = json.html;

                        $(element).html($(html).find('.filters').html());

                        if (typeof json['token'] !== undefined) {
                            $('.filters input[name="_token"]').val(json['token']);
                        }

                        $('.products').html($(html).find('.products').html());
                        $('#content h1').text($(html).find('#content h1').text());
                        $('#content .total_cat').text($(html).find('#content .total_cat').text());
                        $('.sorts').html($(html).find('.sorts').html());
                        $('.bottom').html($(html).find('.bottom').html());
                        filter.slider();
                        wishlist.getList();
                        selectize('.selectize');
                        $('title').text($(html).filter('title').text());
                        window.history.pushState('', $(html).filter('title').text(), json.url);
                    }

                    $('.products').removeClass('preload preload2');
                }
            })
        }
    }

    $.fn.Filter = function(options) {
        return this.each(function() {
            var $this = $(this);
            $.extend(this, options);

            this.change = function(event) {
                event.preventDefault();
                filter.update();
                return false;
            }

            $this.on('change', '.target', $.proxy(this.change, this));
            filter.init(this, options);
        });
    };
})(window.jQuery);