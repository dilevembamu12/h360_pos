"use strict";

var pageNumber = $('#folder-modal-view').data('next-page-url') ?  $('#folder-modal-view').data('next-page-url').split("?page=")[1] : 0;
var checked = true;
$('.move-modal-sidebar').on('scroll', function() {
    const contentHeight = $(this).prop('scrollHeight');
    const visibleHeight = $(this).prop('clientHeight');
    const scrollPosition = $(this).scrollTop();

    if ((scrollPosition + visibleHeight >= contentHeight) && pageNumber != 0 && pageNumber.length != 0 && checked) {
        checked = false;
        const parentDiv = $('#folder-modal-view');

        doAjaxprocess(
            SITE_URL + '/user/fetch/all-folder?page=' + pageNumber,
            {},
            'get',
            'json'
        ).done(function(response) {
            var tempElement = $('<div>').html(response.html);
            var filteredHtml = tempElement.find('#folder-modal-view').html();
            parentDiv.append(filteredHtml);
            parentDiv.removeAttr('data-next-page-url');
            pageNumber = response.nextPageUrl ? response.nextPageUrl.split("?page=")[1] : [];
            checked = true;
        });
    }
});