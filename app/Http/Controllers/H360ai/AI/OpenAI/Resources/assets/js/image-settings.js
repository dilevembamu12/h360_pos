"use strict";

$(document).on("change", "#choose_engine", function (e) {
    const modelName = $(this).val();
    if (modelName == 'clipdrop') {
        $('#choose_service').parent().parent().removeClass('hidden');
    } else {
        $('#choose_service').parent().parent().addClass('hidden');
    }
    $.ajax({
        url: SITE_URL + "/user/formfiled-image",
        type: "get",
        dataType: "html",
        data: {
            model: modelName,
            _token: CSRF_TOKEN,
        },
        beforeSend: () => {
            $(".image-input-loader").removeClass("hidden");
            $(".image-appended-data").addClass("hidden");
        },
        success: function (response) {
            $(".image-appended-data").html(response);
            if (modelName === 'clipdrop') {
                $('#file_input').parent().toggle(!$('#choose_service').val().includes('text-to-image'));
            } 
        },
        complete: () => {
            $(".image-input-loader").addClass("hidden");
            $(".image-appended-data").removeClass("hidden");
        }
    });
});

$(document).ready(function(){
    $('#file_input').parent().toggle(!$('#choose_service').val().includes('text-to-image'));
})

$(document).on("change", "#choose_service", function() {
    $('#image-description').val('');
    const skipServices = ['remove-text', 'remove-background', 'reimagine'];
    const prompt = skipServices.includes($(this).val());
    if (prompt) {
        $('#image-description').val($(this).val());
    }

    $('#image-description').parent().toggle(!prompt);
    $('#image-description').prop('required', !prompt);
    
    const isSketchOrTextToImage = $('#choose_service').val().includes('text-to-image');
    $('#file_input').parent().toggle(!isSketchOrTextToImage);
    $('#file_input').prop('required', !isSketchOrTextToImage);
});