'use strict';

$(function () {
    var pagination = ['v-pills-setup-tab',  'v-pills-bad-word-tab', 'v-pills-voiceover-tab', 'v-pills-access-tab'];

    if (typeof dynamic_page !== 'undefined') {
        pagination = ['v-pills-setup-tab'];
        for (const value of dynamic_page) {
            pagination.push(`v-pills-${value}-tab`)
        }
    }
    
    function tabTitle(id) {
        var title = $('#' + id).attr('data-id');
        $('#theme-title').html(title);
    }

    $(document).on("click", '.tab-name', function () {
        var id = $(this).attr('data-id');

        $('#theme-title').html(id);
    });

    $(".package-submit-button").on("click", function () {
        setTimeout(() => {
            for (const data of pagination) {
                if ($('#' + data.replace('-tab', '')).find(".error").length) {
                    var target = $('#' + data.replace('-tab', '')).attr("aria-labelledby");
                    $('#' + target).tab('show');
                    tabTitle(target);
                    break;
                }
            }
        }, 100);
    });

    $(document).on("click", '.tab-name', function () {
        setTimeout(() => {
            $('.nav-link.active').closest('ul').addClass('show').siblings('a').removeClass('collapses').attr('aria-expanded', true);
        }, 100);
        var id = $(this).attr('data-id');
        $('#theme-title').html(id);
        $('.tab-pane').removeClass('show active')
        $(`.tab-pane[aria-labelledby="${$(this).attr('id')}"`).addClass('show active')

        $('.tab-name').removeClass('active').attr('aria-selected', false);
        $(this).addClass('active').attr('aria-selected', true);
    });

    $(document).on('click', '.nav-list .nav-link', function(e) {
        var target = $(".tab-pane");
    
        $([document.documentElement, document.body]).animate(
            {
            scrollTop: $(target).offset().top - 350,
            },
            350
        );
    })
})
