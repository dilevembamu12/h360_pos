"use strict";


function formValidation() {
    let status = true;
    let ids = ['#name', '#description', '#language', '#status'];

    for (const key in ids) {
        if ($(ids[key]).val().length == '' && $(ids[key]).siblings('.error').length == 0) {
            $(ids[key]).parent().append(`<label class="error">${jsLang('This field is required.')}</label>`);
            status = false;
        } else if ($(ids[key]).val().length == '') {
            status = false;
        }
    }

    if (status == false) {
        return false;
    }

    $('#btnUpdate').text(jsLang('Updating')).append(`<div class="spinner-border spinner-border-sm ml-2" role="status"></div>`).addClass('disabled-btn');

    return true;
}
