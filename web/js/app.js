document.addEventListener('DOMContentLoaded', function () {
    $('#submit-btn').on('click', function (e) {
        e.preventDefault();
        const form = $('#train-search-form');
        const submitBtn = $('#submit-btn');
        const errorBox = $('#error-message');
        const resultBox = $('#route-result-container');

        errorBox.addClass('d-none').text('');
        submitBtn.prop('disabled', true).text('Идет поиск маршрута ...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.success) {
                    resultBox.html(response.html);
                } else {
                    resultBox.html('');
                    errorBox.removeClass('d-none').html(response.error);
                }
            },
            error: function () {
                resultBox.html('');
                errorBox.removeClass('d-none').text('Произошла непредвиденная ошибка на сервере.');
            },
            complete: function () {
                submitBtn.prop('disabled', false).text('Показать маршрут');
            }
        });
        return false;
    });

    $('.select2').select2({
        tags: false,
        multiple: false,
        minimumInputLength: 3,
        minimumResultsForSearch: 10,
        language: 'ru',
        ajax: {
            url: '/train/search-station',
            dataType: "json",
            type: "get",
            data: function (params) {
                var queryParameters = {
                    'TrainRouteForm[q]': params.term
                }
                return queryParameters;
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
});

