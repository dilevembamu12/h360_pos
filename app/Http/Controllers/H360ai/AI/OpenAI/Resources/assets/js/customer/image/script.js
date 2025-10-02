'use strict';

let activeProvider = $('#provider option:selected').val();
let model = $("select[name='" + activeProvider + "[model]'] option:selected").val();
let dataAttrValues = {};
let attrValue;

storeAttrObject();
updateDataAttr();

function hideProviderOptions() 
{
    $('.ProviderOptions').each(function() {
        $(this).addClass('hidden')
    });
}

function updateDataAttr()
{
    for (let key in dataAttrValues) {
        if (dataAttrValues.hasOwnProperty(key)) {
            let value = dataAttrValues[key];
            if (model == value) {
                $('[data-attr="'+ value +'"]').removeClass('hidden')
            } else {
                $('[data-attr="'+ value +'"]').addClass('hidden')
            }
        }
    }
}

function storeAttrObject()
{
    $('[data-attr]').each(function() {
        attrValue = $(this).data('attr');
        dataAttrValues[$(this).attr('data-attr')] = attrValue;
    });
}


$('.AdavanceOption').on('click', function() {
    var className = $('#ProviderOptionDiv').attr('class');
    if (className == 'hidden') {
        hideProviderOptions()
        let activeProvider = $('#provider option:selected').val();

        $('.' + activeProvider + '_div').removeClass('hidden');
        $('#ProviderOptionDiv').removeClass('hidden');
    } else {
        $('#ProviderOptionDiv').addClass('hidden');
    }
});

function clear() {
    const imageDescriptionParent = $("#image-description").parent();
    
    if (imageDescriptionParent.is(":hidden")) {
        imageDescriptionParent.show();
        $("#image-description").attr('required', true);
    }
    
    // Always show the image description parent
    imageDescriptionParent.show();
    
    // Remove 'required' attribute from both file input fields
    $('input[name="stabilityai_file"], input[name="clipdrop_file"]').removeAttr('required');
}
$('#provider').on('change', function() {
    clear();
    hideProviderOptions();
    activeProvider = $(this).val();
    $('.' + activeProvider + '_div').removeClass('hidden');
    model = $("select[name='" + activeProvider + "[model]'] option:selected").val(); 

    storeAttrObject();
    updateDataAttr();
});

function toggleFileInput(service, fileInput, imageDescriptionInput, skipServices = []) {
    fileInput.removeAttr('required');
    fileInput.parent().show();
    imageDescriptionInput.parent().show();

    if (service === "text-to-image") {
        fileInput.parent().hide();
        imageDescriptionInput.attr('required', true);
    } else if (skipServices.includes(service)) {
        imageDescriptionInput.parent().hide();
        imageDescriptionInput.attr('required', false);
        fileInput.attr('required', true);
    } else if (service === "image-to-image" || service === "sketch-to-image") {
        imageDescriptionInput.attr('required', true);
        fileInput.attr('required', true);
    }
}

$(document).on('change', 'select[name="stabilityai[service]"]', function() {
    let value = $(this).val();
    let fileInput = $('input[name="stabilityai_file"]');
    toggleFileInput(value, fileInput, $("#image-description"));
});

$(document).on('change', 'select[name="clipdrop[service]"]', function() {
    let value = $(this).val();

    let fileInput = $('input[name="clipdrop_file"]');
    fileInput.val('');
    fileInput.trigger('change');

    const skipServices = ['remove-text', 'remove-background', 'reimagine'];
    toggleFileInput(value, fileInput, $("#image-description"), skipServices);
});

$(document).on('change', 'input[name="clipdrop_file"]', function() {
    let service = $('select[name="clipdrop[service]"]').val();
    const skipServices = ['remove-text', 'remove-background', 'reimagine'];
    let files = this.files;
    if (files.length > 0) {
        let fileNameWithoutExtension = files[0].name.split('.').slice(0, -1).join('.');

        if (skipServices.includes(service)) {
            $("#image-description").val(fileNameWithoutExtension);
        }
    }
});

$(document).on('change', '.model-class', function() {
    model = $(this).val();
    updateDataAttr();
});


$('#ImageForm').on('submit', function(e) {
    e.preventDefault();
    let gethtml = '';

    let data = new FormData();

    let formData = $(this).serializeArray();
    $.each(formData, function (key, input) {
        data.append(input.name, input.value);
    });

    let provider = $('#provider').val();
    let fileInput = $('input[name="'+provider+'_file"]');

    if (fileInput.length > 0) {
        let fileData = fileInput.prop('files')[0];
        if (fileData) {
            data.append('file', fileData);
        }
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        method: 'POST',
        url: url,
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#ImageForm .loader").removeClass('hidden');
            $('#image-creation').attr('disabled', 'disabled');
        },
        complete: function() {
            
        },
        success: function(response, textStatus, jqXHR) {

            if (textStatus == 'success' && jqXHR.status == 201) {
                $("#ImageForm .loader").addClass('hidden');

                $(".static-image-text").addClass('hidden');
                let credit = $('.image-credit-remaining');
                
                // Image creadit balance update
                if (!isNaN(credit.text()) && response.data.images != null && response.data.balance_reduce_type == 'subscription') {
                    credit.text(credit.text() - response.data.images.length);
                }

                gethtml +='<div class="flex flex-wrap justify-center items-center md:gap-6 gap-5 mt-10 image-content1 9xl:mx-32 3xl:mx-16 2xl:mx-5">'
                    $.each(response.data.images, function(key, image) {
                        gethtml +='<div class="relative md:w-[300px] md:h-[300px] w-[181px] h-[181px] download-image-container md:rounded-xl rounded-lg">'
                        gethtml += '<img class="m-auto md:w-[300px] md:h-[300px] w-[181px] h-[181px] cursor-pointer md:rounded-xl rounded-lg border border-color-DF dark:border-color-3A object-cover"src="'+ image.url +'" alt=""><div class="image-hover-overlay"></div>'
                        gethtml +='<div class=" flex gap-3 right-3 bottom-3 absolute">'
                        gethtml += '<div class="image-download-button"><a class="relative tooltips w-9 h-9 flex items-center m-auto justify-center" href="'+ image.slug_url +'">'
                        gethtml +=`<img class="w-[18px] h-[18px]" src="${SITE_URL}/Modules/OpenAI/Resources/assets/image/view-eye.svg" alt="">`
                        gethtml +='<span class="image-download-tooltip-text z-50 w-max text-white items-center font-medium text-12 rounded-lg px-2.5 py-[7px] absolute z-1 top-[138%] left-[50%] ml-[-22px]">View</span>'
                        gethtml += '</a>'
                        gethtml += '</div>'
                        gethtml += '<div class="image-download-button"><a class="file-need-download relative tooltips w-9 h-9 flex items-center m-auto justify-center" href="'+ image.url +'" download="'+ filterXSS(response.data.title) +'" Downlaod>'
                        gethtml +=`<img class="w-[18px] h-[18px]" src="${SITE_URL}/Modules/OpenAI/Resources/assets/image/file-download.svg" alt="">`
                        gethtml +='<span class="image-download-tooltip-text z-50 w-max text-white items-center font-medium text-12 rounded-lg px-2.5 py-[7px] absolute z-1 top-[138%] left-[50%] ml-[-38px]">Download</span>'
                        gethtml += '</a>'
                        gethtml += '</div>'
                        gethtml += '</div>'
                        gethtml += '</div>'

                    });
                    gethtml += '</div>';

                    $('#image-content').prepend(gethtml);
                    $(".loader").addClass('hidden');
                    $('#image-creation').removeAttr('disabled');
                
                toastMixin.fire({
                    title: jsLang('Image generated successfully.'),
                    icon: 'success'
                });
            } else {
                errorMessage(jsLang('Something went wrong'), 'image-creation');
            }
        },
        error: function(error) {
            let message = error.responseJSON.message ? error.responseJSON.message : error.responseJSON.error
            errorMessage(message, 'image-creation');
        },
    });
})

$(function() {
    $('select[name="stabilityai[service]"]').trigger('change');
    $('select[name="clipdrop[service]"]').trigger('change');
});
