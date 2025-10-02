
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

$(document).on('submit', '#plagiarism-form', function (e) {
    $(".loader").removeClass('hidden');
    let dataArray = $(this).serializeArray();
    
    var providerObject = dataArray.find(function(element) {
        return element.name === "provider";
    });
    function getValueByName(name) {
        var item = dataArray.find(function(element) {
            return element.name === name;
        });
        return item ? item.value : null;
    }

    var providerValue = providerObject ? providerObject.value : null;
    e.preventDefault();
    $.ajax({
        url: SITE_URL + PROMT_URL,
        type: "POST",
        beforeSend: function (xhr) {
            updateProgressBar(0);
            $('.data-append').text('');
            $(".loader").removeClass('hidden');
            $('#plagiarism-creation').attr('disabled', 'disabled');
            xhr.setRequestHeader('Authorization', 'Bearer ' + ACCESS_TOKEN);
        },
        data: {
            text: filterXSS($("#plagiarism-description").val()),
            provider: providerValue,
            dataType: 'json',
            _token: CSRF_TOKEN
        },
        success: function(response) {

            if ( parseInt(response.data.percent) === 0) {
                $('.bar-inner').addClass('bar-green-inner').removeClass('bar-inner');
                updateProgressBar(100);
                $(".percentage-wrapper .percentage").text(response.data.percent);
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
                title: jsLang('Plagiarism generated successfully.'),
                icon: 'success'
            });
        },
        complete: () => {
            $(".loader").addClass('hidden');
            $('#plagiarism-creation').removeAttr('disabled');
        },
        error: function(response) {
            var jsonData = JSON.parse(response.responseText);
            var message = jsonData.error ? jsonData.error : jsonData.message;
            errorMessage(message, 'plagiarism-creation');
         }
    });
});

function updateProgressBar(percentage) {
    $(".ai-plagiarism-graph").each(function () {
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

function displayReadableData(jsonData) {
    const $reportDiv = $('.data-append');
    const data = JSON.parse(jsonData);

    data.forEach(item => {
      // Create a container for each report item using jQuery
      const $itemDiv = $('<div>').css('margin-bottom', '20px');
  
      // Add the details as HTML content
      $itemDiv.html(`
        <p><strong>${jsLang('Source:')}</strong> <a href="${item.source}" target="_blank">${item.link.name}</a></p>
        <p><strong>${jsLang('Content Type:')}</strong> ${item.content_type}</p>
        <p><strong>${jsLang('Plagiarism Detected:')}</strong> ${item.plagiarism_percent}%</p>
        <p><strong>${jsLang('Plagiarized Content Length:')}</strong> ${item.plagiarism_length} ${jsLang('characters')} </p>
        <hr>
      `);
  
      // Append the item to the report div
      $reportDiv.append($itemDiv);
    });
}
  
$(document).ready(function () {
    updateProgressBar(0);
});
