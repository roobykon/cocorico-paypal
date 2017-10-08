$(function () {

    var arcanum = {
        nanobar: {
            options: {
                id: 'nanobar'
            },
            instance: new Nanobar(this.options),
            go: function (percent) {
                arcanum.nanobar.instance.go(percent);
            }
        },
        wrapper: $('.wrapper-arcanum-parameters'),
        findParameterName: function (row) {
            return row.find('.arcanum-parameter');
        },
        findParameterValue: function (row) {
            return row.find('.arcanum-value .arcanum-value-span');
        },
        findParameterValues: function () {
            return arcanum.wrapper.find('.arcanum-value .arcanum-value-span');
        },
        collectParameters: function () {
            var
                rows = arcanum.wrapper.find('.wrapper-arcanum-parameter'),
                parameters = {}, parameter, value;

            $.each(rows, function (i, row) {
                row = $(row);

                parameter = arcanum.findParameterName(row).text().trim();
                value = arcanum.findParameterValue(row).text().trim();

                // console.log(row.data('originValue'), value);
                if (row.data('originValue').toString() !== value) {
                    parameters[parameter] = value;
                }
            });

            return parameters;
        },
        toggleActions: function (row) {
            row.find('.arcanum-value-actions').find('.fa').toggleClass('hidden');
        },
        createInput: function (value, type) {
            type = type || 'text';

            return $('<input>', {val: value, type: type});
        },
        createSpan: function (text) {
            return $('<span>', {text: text});
        }
    };

    arcanum.wrapper
        .on('search', '.arcanum-parameters-filter input', function (e) {
            arcanum.wrapper.find('.wrapper-arcanum-parameter').removeClass('hidden');
        })
        .on('keyup', '.arcanum-parameters-filter input', function (e) {
            var
                search = $(this).val().trim(),
                rows = arcanum.wrapper.find('.wrapper-arcanum-parameter'),
                parameter;

            $.each(rows, function (i, row) {
                row = $(row);
                parameter = arcanum.findParameterName(row).text().trim();
                row.toggleClass('hidden', parameter.indexOf(search) === -1);
            })
        })
        .on('click', '.arcanum-parameters-actions .btn-save', function (e) {
            var
                url = $(this).data('url'),
                parameters = arcanum.collectParameters();

            if (!$.isEmptyObject(parameters)) {
                arcanum.nanobar.go(10);
                $.post(url, parameters, function (response) {
                    console.log(response);
                    if (response.hasOwnProperty('success') && response.success) {
                        arcanum.findParameterValues().removeClass('modified');
                    }
                }, 'json').always(function () {
                    arcanum.nanobar.go(100);
                });
            }
        })
        .on('click', '.arcanum-parameters-actions .btn-reload', function (e) {
            var
                btn = $(this),
                url = btn.data('url'),
                percent = 0,
                interval = setInterval(function () {
                    percent = percent + 3;
                    arcanum.nanobar.go(percent);
                }, 1000);

            btn.prop('disabled', true);

            $.post(url, {reload: true}, function (response) {
                if (response.hasOwnProperty('success') && response.success) {
                    console.log(response);
                }
            }, 'json').always(function () {
                clearInterval(interval);
                btn.prop('disabled', false);
                arcanum.nanobar.go(100);
            });
        })
        .on('click', '.arcanum-value-actions .fa-undo', function (e) {
            var
                row = $(this).closest('.row'),
                span = arcanum.findParameterValue(row);

            span.text(row.data('originValue'));
            span.removeClass('modified');
        })
        .on('click', '.arcanum-value-actions .fa-pencil', function (e) {
            var
                row = $(this).closest('.row'),
                span = arcanum.findParameterValue(row),
                input = arcanum.createInput(span.text().trim());

            row.data('initialValue', span.text().trim());
            input.addClass('form-control input-sm');
            span.replaceWith(input);
            arcanum.toggleActions(row);
            // input.select();
        })
        .on('click', '.arcanum-value-actions .fa-check', function (e) {
            var
                row = $(this).closest('.row'),
                input = row.find('input'),
                span = arcanum.createSpan(input.val());

            span.addClass('arcanum-value-span');
            if (row.data('originValue') !== input.val()) {
                span.addClass('modified');
            }
            input.replaceWith(span);

            arcanum.toggleActions(row);
        })
        .on('click', '.arcanum-value-actions .fa-times', function (e) {
            var
                row = $(this).closest('.row'),
                input = row.find('input'),
                span = arcanum.createSpan(row.data('initialValue'));

            span.addClass('arcanum-value-span');
            span.toggleClass('modified', row.data('originValue') !== row.data('initialValue'));
            input.replaceWith(span);

            arcanum.toggleActions(row);
        });

});
