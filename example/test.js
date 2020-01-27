$(function () {

    $('#button').on('click', function (e) {
        var container = $('#container'),
            text = $('#text');
        if (container.hasClass('active')) {
            container.removeClass('active');
            text.html('<b>container hidden</b>');
        } else {
            container.addClass('actived');
            text.html('<b>container showed</b>');
        }
        e.preventDefault();
    });

    $('#load-btn').on('click', function (e) {

        $.ajax({
            type: 'GET',
            url: 'api/load-more',
            success: function (result) {
                console.log(result);
            },
            error: function (err) {
                console.log(err);
            }
        });

        e.preventDefault();
    });

});
