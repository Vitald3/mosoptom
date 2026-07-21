$(document).on('click', '.add_files_single', function (e) {
    e.stopPropagation();
    e.preventDefault();

    var self = $(this).closest('.event_file');

    if (typeof $(this).attr('data-input') !== "undefined") {
        var target_input = $($(this).attr('data-input'));
    } else {
        var target_input = $(self.attr('data-input'));
    }

    window.open(location.origin + '/admin/filemanager?type=image&type2=single', 'FileManager', 'width=900,height=600');

    window.SetUrl = function (items) {
        var file_path = items.map(function (item) {
            return item.url.replace(location.origin + '/', '');
        }).join(',');

        target_input.attr('value', file_path).trigger('change');

        if (self.length) {
            self.find('.events').remove();
        }

        self.find('img').attr('src', location.origin + '/' + file_path);
    };
});

$(function() {
    $('.tinymce').each(function() {
        $(this).tinymce({
            script_url: '/assets/admin/js/tinymce/tinymce.min.js',
            selector: '.tinymce',
            skin: 'bootstrap',
            language: $('html').attr('lang'),
            height: 300,
            file_picker_callback: function elFinderBrowser(callback, value, meta) {
                window.open(location.origin + '/admin/filemanager?type=image&type2=single', 'FileManager', 'width=900,height=600');

                window.SetUrl = function (items) {
                    var file_path = items.map(function (item) {
                        return item.url.replace(location.origin + '/', '');
                    }).join(',');

                    callback(location.origin + '/' + file_path);
                };
            },
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'save table contextmenu directionality emoticons template paste textcolor colorpicker'
            ],
            toolbar: 'bold italic sizeselect fontselect fontsizeselect | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | insertfile undo redo | forecolor backcolor emoticons | code',
            fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        });
    });
});

$(document).on('click', '.event_file', function (e) {
    $(this).find('.events').remove();
    $(this).append('<div class="events"><span class="add_files_single btn btn-primary">+</span><span class="del_files btn btn-danger">-</span></div>');
    return false;
});

$(document).on('click', '.del_files', function () {
    if (!$(this).closest('.event_file').hasClass('not_remove')) {
        $(this).closest('.event_file').remove();
    } else {
        $(this).closest('.event_file').find('img').attr('src', '/assets/admin/img/no_image.png');
        $($(this).closest('.event_file').attr('data-input')).val('');
        $(this).closest('.events').remove();
    }

    return false;
});

$(document).mouseup(function (e){
    var div = $('.event_file .events');
    if (!div.is(e.target) && div.has(e.target).length === 0) {
        div.remove();
    }
});

$(document).ready(function () {
    $('input[required], select[required], textarea[required]').each(function () {
        $(this).parent().find('label').addClass('required');
    });

    if (!$('input[name="slug"]').val()) $('input[name="meta[ru][name]"]').on('input', function() {
        var map = {
            'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
            'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
            'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
            'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu',
            'Я': 'Ya',
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
            'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
            'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
            'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu',
            'я': 'ya',
        };
        var text = $(this).val();
        for (var k in map) {
            text = text.replace(RegExp(k, 'g'), map[k]);
        }
        text = text.replace(/[^- _a-zA-Z0-9]/g, '');
        text = text.replace(/\s+/g, '-');
        text = text.replace(/-+/g, '-');
        $('input[name="slug"]').val(text.toLowerCase());
    });
});