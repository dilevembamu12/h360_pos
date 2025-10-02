
"use strict";

$('.AdavanceOption').on('click', function() {
    if ($('#ProviderOptionDiv').attr('class') == 'hidden') {
        hideProviderOptions()
        $('.' + $('#provider option:selected').val() + '_div').removeClass('hidden');
        $('#ProviderOptionDiv').removeClass('hidden');
    } else {
        $('#ProviderOptionDiv').addClass('hidden');
    }
});

$('#provider').on('change', function() {
    hideProviderOptions();
    $('.' + $(this).val() + '_div').removeClass('hidden');
});

function hideProviderOptions() 
{
    $('.ProviderOptions').each(function() {
        $(this).addClass('hidden')
    });
}

$('#provider').on('change', function() {
    hideProviderOptions();
    $('.' + $(this).val() + '_div').removeClass('hidden');
});

$(document).on('submit', '#detector-form', function (e) {
    e.preventDefault();
    $(".loader").removeClass('hidden');
    let dataArray = $(this).serializeArray();
    
    var providerObject = dataArray.find(function(element) {
        return element.name === "provider";
    });

    var providerValue = providerObject ? providerObject.value : null;

    let data = new FormData();

    var description = $("#detector_description").val();
    if (description) {
        data.append('text', filterXSS(description.trim()));
    }

    data.append('provider', providerValue);
    
    var fileInput = $("#file_input")[0];
    if (fileInput && fileInput.files.length > 0) {
        data.append('file', fileInput.files[0]);
    }
    data.append('dataType', 'json');
    data.append('_token', CSRF_TOKEN);

    $.ajax({
        url: SITE_URL + PROMT_URL,
        type: "POST",
        beforeSend: function (xhr) {
            updateProgressBar(0);
            $(".percentage-wrapper .percentage").text(0);
            $('.data-append').text('');
            $(".loader").removeClass('hidden');
            $('#detector-creation').attr('disabled', 'disabled');
            xhr.setRequestHeader('Authorization', 'Bearer ' + ACCESS_TOKEN);
        },
        data: data,
        contentType: false,
        cache: false,
        processData:false,
        success: function(response) {
            if ( parseInt(response.data.percent) === 0) {
                $('.bar-inner').addClass('bar-green-inner').removeClass('bar-inner');
                updateProgressBar(100);
                $(".percentage-wrapper .percentage").text(100);
            } else {
                $(".percentage-wrapper .percentage").text(response.data.percent);
                updateProgressBar(response.data.percent);
                displayReadableData(response.data.report_data);
            }

            var credit = $('.total-page-left');

            if (credit.length > 0) {
                var creditValue = parseFloat(credit.text());
                if (!isNaN(creditValue) && response.data.pages != null) {
                    var pages = creditValue - response.data.pages;
                    if (pages < 0) {
                        pages = 0;
                    }

                    credit.text(pages);
                }
            }
        
            toastMixin.fire({
                title: jsLang('Ai Detector Report generated successfully.'),
                icon: 'success'
            });
        },
        complete: () => {
            $(".loader").addClass('hidden');
            $('#detector-creation').removeAttr('disabled');
        },
        error: function(response) {
            var jsonData = JSON.parse(response.responseText);
            var message = jsonData.error ? jsonData.error : jsonData.message;
            errorMessage(message, 'detector-creation');
         }
    });
});

function resetAll() {
    const fileInput = $('#file_input');
    const description = $('#detector_description');

    fileInput.val('');
    fileInput.prop('disabled', false);
    fileInput.attr('required', true);

    description.attr('required', true);
    description.prop('disabled', false);
}

$('#file_input').on('change', function() {
    const fileInput = $(this);
    const description = $('#detector_description');

    if (fileInput.val()) {
        description.prop('disabled', true);
        description.removeAttr('style');
        description.attr('required', false);
        description.parent().find('.error').remove();
        description.parent().removeClass('has-validation-error');
    } else {
        description.prop('disabled', false);
        description.attr('required', 'required');
    }
});

$('#detector_description').on('keyup', function() {
    const description = $(this);
    const fileInput = $('#file_input');
    
    if (description.val().trim() !== '') {
        fileInput.prop('disabled', true);
        fileInput.removeAttr('style');
        fileInput.attr('required', false);
    } else {
        fileInput.prop('disabled', false);
        fileInput.attr('required', true);
    }
});

function updateProgressBar(percentage) {
    $(".ai-detector-graph").each(function () {
        var $bar = $(this).find(".progress-bars");
        var $val = $(this).find(".percentage-value");
        var perc = percentage;
    
        $({
            p: 0
        }).animate({
            p: perc
        }, {
            duration: 1500,
            easing: "swing",
            step: function (p) {
                var progressValue = p | 0; // Cast to integer
                $bar.css({
                    transform: "rotate(" + (progressValue * 1.8) + "deg)", // 1.8 is the rotation factor
                });
                $val.text(progressValue + "%"); // Update only when necessary
            }
        });
    });
}

function displayReadableData(data) {
    const $reportDiv = $('.data-append');
    // Create a container for each report item using jQuery
    const $itemDiv = $('<div>').css('margin-bottom', '20px');

    // Add the details as HTML content
    $itemDiv.html(`
        <p><strong>${jsLang('Conclusion:')}</strong> ${data}</p>
        <hr>
    `);

    // Append the item to the report div
    $reportDiv.append($itemDiv);
}
  
$(document).ready(function () {
    updateProgressBar(0);
});
