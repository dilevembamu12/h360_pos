'use strict';

let topic = null;
let keywords = null;
let title = null;

let longArticleGeneratedTitles = null;
let longArticleGeneratedOutlines = null;
let longArticleGeneratedArticle = null;

let existOldLongArticle = false;
let longArticleCompletedStep = null;
let longArticleId = null
let selectedOutlines = [];

if (oldLongArticle !== null) {

    existOldLongArticle = true;
    longArticleId = oldLongArticle.id;
    longArticleCompletedStep = oldLongArticle.meta_data.completed_step;

    
    longArticleGeneratedTitles = oldGeneratedTitles ?? null;
    longArticleGeneratedOutlines = oldGeneratedOutlines != undefined ? JSON.parse(oldGeneratedOutlines) : null;
    longArticleGeneratedArticle = oldLongArticle.meta_data.article_value != undefined ? oldLongArticle.id : null;

    if (oldLongArticleOutline.length) {
        
        if (oldLongArticleOutline[0].meta_data.outline_keywords !== null) {
            title = oldLongArticleOutline[0].meta_data.outline_title;
            keywords = oldLongArticleOutline[0].meta_data.outline_keywords;
        } else {
            keywords = null;
        }
    } else {
        title = oldLongArticleTitle[0].meta_data.title_topic;
        keywords = oldLongArticleTitle[0].meta_data.title_keywords;
    }

    topic = oldLongArticleTitle[0].meta_data.title_topic ?? null;
    selectedOutlines = oldLongArticle.meta_data.article_outlines ? oldLongArticle.meta_data.article_outlines : [];
}

if (existOldLongArticle) {

    var longArticleFormData = {
        activeStep: longArticleCompletedStep,
        longArticleId: longArticleId,
        titles: {
            for: 'titles',
            data: {
                topic: topic,
                keywords: keywords,
                title: title,
                generatedTitles: longArticleGeneratedTitles ?? []
            }
        },
        outlines: {
            for: 'outlines',
            data: {
                title: title,
                keywords: keywords,
                generatedOutlines: longArticleGeneratedOutlines ?? []
            }
        },
        article: {
            for: 'article',
            data: {
                title: title,
                keywords: keywords,
                outlines: selectedOutlines ?? [],
                generatedArticleBlogId: longArticleGeneratedArticle ?? null 
            }
        }
    }; 
    
    setLocalLongArticleData();
}


var localLongArticleFormData = localStorage.getItem("longArticleFormData" + '_' + userId) ? JSON.parse(localStorage.getItem("longArticleFormData" + '_' + userId)) :
undefined;

longArticleFormData = localLongArticleFormData != undefined ? localLongArticleFormData : initLongArticleFormData;

loadInputFields(); // Load the Title, Outlins Forms with value
handleItemClick(); // Generated Output: Title, Outline Click event
displayTitleData(); // If Reload after generate title, this will again display the title
displayOulineData(); // If Reload after generate outline, this will again display the outline
displayArticleBlogData();
handleLongArticleId(); // After start generating an article, article id will be place in every form of title generate step

$(".SingleForm").removeClass("Active"); // Remove Active class from  all the form: title, outline
$(".SingleForm").eq(longArticleFormData.activeStep - 1).addClass("Active"); // Add active class depend on active step. Like activeStep: 1 => title, activeStep: 2 => Outline


$(".FormData").removeClass("Active"); // Remove Active class from  all the generated data: title, outline
$(".FormData").eq(longArticleFormData.activeStep - 1).addClass("Active"); // // Add active class depend on active step.

// Generated article id will be placed in all form input value
function handleLongArticleId() {
    $('.long_article_id').each(function() {
        $(this).val(longArticleFormData.longArticleId)
    });
}

// Updated longArticleFormData set to localStorage
function setLocalLongArticleData() {
    localStorage.setItem('longArticleFormData' + '_' + userId, JSON.stringify(longArticleFormData));
}

// Suggestion Block show/hide onload
if (longArticleFormData.activeStep == 1) {
    $('.OutlineSuggestionText').addClass('hidden');
}
if (longArticleFormData.activeStep == 2) {
    $('.TitleSuggestionText').addClass('hidden');
}

// Stepper active/deactive onload
if (longArticleFormData.titles.data.generatedTitles.length > 0) {
    $('.StepperParent .Stepper:eq(0) .empty-circle').addClass('hidden');
    $('.StepperParent .Stepper:eq(0) .full-circle').removeClass('hidden');
    // Suggestion Block show/hide if title already generated
    $('.TitleSuggestionText').addClass('hidden');
}

if (longArticleFormData.outlines.data.generatedOutlines.length > 0) {
    $('.StepperParent .Stepper:eq(1) .empty-circle').addClass('hidden');
    $('.StepperParent .Stepper:eq(1) .full-circle').removeClass('hidden');
    // Suggestion Block show/hide if outline already generated
    $('.OutlineSuggestionText').addClass('hidden');
}

if (longArticleFormData.article.data.generatedArticleBlogId != null) {
    $('.StepperParent .Stepper:eq(2) .empty-circle').addClass('hidden');
    $('.StepperParent .Stepper:eq(2) .full-circle').removeClass('hidden');
}
// Stepper active/deactive onload


// Generate Title, Article click event handled 
function handleItemClick() {
    $('.ItemClicked').each(function() {
        var $this = this;
        $($this).on('change', function() {
            let activeStep = longArticleFormData.activeStep;
            if (activeStep == 1) {
                // On first step: when clicked titles:title, outline:title setup
                longArticleFormData.titles.data.title = $($this).data('value');
                longArticleFormData.outlines.data.title = $($this).data('value');
            } else if (activeStep == 2) {
                longArticleFormData.article.data.outlines = $($this).data('value');
            }
            $('.ContinueButtonDiv').removeClass('hidden');
        })
    });
}

// After click each generated title or outline next step goes by this
$('.ContinueButton').on('click', function() {
    let activeStep = longArticleFormData.activeStep;
    longArticleFormData.activeStep = activeStep + 1;
 
    if (activeStep == 1) {
        longArticleFormData.outlines.data.keywords = $('#TitleKeywords').val();
        
        // Suggestion Block show/hide on click next
        if (longArticleFormData.outlines.data.generatedOutlines.length > 0) {
            $('.OutlineSuggestionText').addClass('hidden');
        } else {
            $('.OutlineSuggestionText').removeClass('hidden');
        }

    } else if (activeStep == 2) {
        longArticleFormData.article.data.title = $('#OutlineTitle').val();
        longArticleFormData.article.data.keywords = $('#OutlineKeywords').val();
        
        if (longArticleFormData.article.data.generatedArticleBlogId == '') {
            $('.ArticleSuggestion').removeClass('hidden');
            $('.ArticleSection').addClass('hidden');
        }
    }

    handleLongArticleId();
    loadInputFields();
    setLocalLongArticleData();

    $(".SingleForm").removeClass("Active");
    $(".SingleForm").eq(activeStep).addClass("Active");

    $(".FormData").removeClass("Active");
    $(".FormData").eq(activeStep).addClass("Active");
    //  active steps

    // hide data next button
    $('.ContinueButtonDiv').addClass('hidden');
});

// Previous Step
$('.ArticlePreviousButton').on('click', function() {
    let activeStep = longArticleFormData.activeStep - 1;
    longArticleFormData.activeStep = activeStep;
    
    // Suggestion Block show/hide on click back
    if (longArticleFormData.activeStep == 1) {
        if (longArticleFormData.titles.data.generatedTitles.length > 0) {
            $('.TitleSuggestionText').addClass('hidden');
        } else {
            $('.TitleSuggestionText').removeClass('hidden');
        }
        $('.OutlineSuggestionText').addClass('hidden');
    }
    

    $(".SingleForm").removeClass("Active");
    $(".SingleForm").eq(activeStep-1).addClass("Active");

    $(".FormData").removeClass("Active");
    $(".FormData").eq(activeStep-1).addClass("Active");
    //  active steps 

    $("input[type='radio']").prop('checked', false);
    setLocalLongArticleData();
});


// After every reload this will fill every input fields
function loadInputFields() {

    $('#TitleTopic').val(longArticleFormData.titles.data.topic);
    $('#TitleKeywords').val(longArticleFormData.titles.data.keywords);

    $('#OutlineTitle').val(longArticleFormData.outlines.data.title);
    $('#OutlineKeywords').val(longArticleFormData.outlines.data.keywords);

    $('#ArticleTitle').val(longArticleFormData.article.data.title);
    $('#ArticleKeywords').val(longArticleFormData.article.data.keywords);

    let OulineHtml = "";
    if (longArticleFormData.article.data.outlines.length == 0) {
        OulineHtml += `<div class="relative flex items-center mt-2 sort hidden border border-solid border-color-DF bg-color-F3 dark:bg-color-47 dark:!border-color-47 rounded-xl px-[9px]">
                <span class="handle cursor-move">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.66667 12.6667C6.66667 12.9304 6.58847 13.1882 6.44196 13.4074C6.29545 13.6267 6.08721 13.7976 5.84358 13.8985C5.59994 13.9994 5.33186 14.0258 5.07321 13.9744C4.81457 13.9229 4.57699 13.7959 4.39052 13.6095C4.20405 13.423 4.07707 13.1854 4.02562 12.9268C3.97417 12.6681 4.00058 12.4001 4.10149 12.1564C4.20241 11.9128 4.37331 11.7045 4.59257 11.558C4.81184 11.4115 5.06963 11.3333 5.33333 11.3333C5.68696 11.3333 6.02609 11.4738 6.27614 11.7239C6.52619 11.9739 6.66667 12.313 6.66667 12.6667ZM5.33333 6.66667C5.06963 6.66667 4.81184 6.74487 4.59257 6.89137C4.37331 7.03788 4.20241 7.24612 4.10149 7.48976C4.00058 7.73339 3.97417 8.00148 4.02562 8.26012C4.07707 8.51876 4.20405 8.75634 4.39052 8.94281C4.57699 9.12928 4.81457 9.25627 5.07321 9.30771C5.33186 9.35916 5.59994 9.33276 5.84358 9.23184C6.08721 9.13092 6.29545 8.96003 6.44196 8.74076C6.58847 8.52149 6.66667 8.26371 6.66667 8C6.66667 7.64638 6.52619 7.30724 6.27614 7.05719C6.02609 6.80714 5.68696 6.66667 5.33333 6.66667ZM5.33333 2C5.06963 2 4.81184 2.0782 4.59257 2.22471C4.37331 2.37122 4.20241 2.57945 4.10149 2.82309C4.00058 3.06672 3.97417 3.33481 4.02562 3.59345C4.07707 3.8521 4.20405 4.08967 4.39052 4.27614C4.57699 4.46261 4.81457 4.5896 5.07321 4.64105C5.33186 4.69249 5.59994 4.66609 5.84358 4.56517C6.08721 4.46426 6.29545 4.29336 6.44196 4.07409C6.58847 3.85483 6.66667 3.59704 6.66667 3.33333C6.66667 2.97971 6.52619 2.64057 6.27614 2.39052C6.02609 2.14048 5.68696 2 5.33333 2ZM10.6667 11.3333C10.403 11.3333 10.1452 11.4115 9.92591 11.558C9.70664 11.7045 9.53574 11.9128 9.43483 12.1564C9.33391 12.4001 9.30751 12.6681 9.35895 12.9268C9.4104 13.1854 9.53739 13.423 9.72386 13.6095C9.91033 13.7959 10.1479 13.9229 10.4065 13.9744C10.6652 14.0258 10.9333 13.9994 11.1769 13.8985C11.4205 13.7976 11.6288 13.6267 11.7753 13.4074C11.9218 13.1882 12 12.9304 12 12.6667C12 12.313 11.8595 11.9739 11.6095 11.7239C11.3594 11.4738 11.0203 11.3333 10.6667 11.3333ZM10.6667 6.66667C10.403 6.66667 10.1452 6.74487 9.92591 6.89137C9.70664 7.03788 9.53574 7.24612 9.43483 7.48976C9.33391 7.73339 9.30751 8.00148 9.35895 8.26012C9.4104 8.51876 9.53739 8.75634 9.72386 8.94281C9.91033 9.12928 10.1479 9.25627 10.4065 9.30771C10.6652 9.35916 10.9333 9.33276 11.1769 9.23184C11.4205 9.13092 11.6288 8.96003 11.7753 8.74076C11.9218 8.52149 12 8.26371 12 8C12 7.64638 11.8595 7.30724 11.6095 7.05719C11.3594 6.80714 11.0203 6.66667 10.6667 6.66667ZM10.6667 2C10.403 2 10.1452 2.0782 9.92591 2.22471C9.70664 2.37122 9.53574 2.57945 9.43483 2.82309C9.33391 3.06672 9.30751 3.33481 9.35895 3.59345C9.4104 3.8521 9.53739 4.08967 9.72386 4.27614C9.91033 4.46261 10.1479 4.5896 10.4065 4.64105C10.6652 4.69249 10.9333 4.66609 11.1769 4.56517C11.4205 4.46426 11.6288 4.29336 11.7753 4.07409C11.9218 3.85483 12 3.59704 12 3.33333C12 2.97971 11.8595 2.64057 11.6095 2.39052C11.3594 2.14048 11.0203 2 10.6667 2Z" fill="#898989"/>
                    </svg>
                </span>
                <p class="border-none w-full ml-1.5 px-0 bg-color-F3 dark:bg-color-47 rounded-xl"><span class="textarea-span text-13 font-Figtree font-normal text-color-14 dark:!text-white" role="textbox" contenteditable></span></p>
            </div>
            `;
    } else {
        let outlinesData = longArticleFormData.article.data.outlines;
        if (typeof (longArticleFormData.article.data.outlines) == 'string') {
            outlinesData = JSON.parse(longArticleFormData.article.data.outlines);
        }
        outlinesData.forEach((element, index) => {

            OulineHtml += `
                    <div class="relative single-outline flex items-center mt-2 sort border border-solid border-color-DF bg-color-F3 dark:bg-color-47 dark:!border-color-47 rounded-xl px-[9px]">
                    <span class="handle cursor-move">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.66667 12.6667C6.66667 12.9304 6.58847 13.1882 6.44196 13.4074C6.29545 13.6267 6.08721 13.7976 5.84358 13.8985C5.59994 13.9994 5.33186 14.0258 5.07321 13.9744C4.81457 13.9229 4.57699 13.7959 4.39052 13.6095C4.20405 13.423 4.07707 13.1854 4.02562 12.9268C3.97417 12.6681 4.00058 12.4001 4.10149 12.1564C4.20241 11.9128 4.37331 11.7045 4.59257 11.558C4.81184 11.4115 5.06963 11.3333 5.33333 11.3333C5.68696 11.3333 6.02609 11.4738 6.27614 11.7239C6.52619 11.9739 6.66667 12.313 6.66667 12.6667ZM5.33333 6.66667C5.06963 6.66667 4.81184 6.74487 4.59257 6.89137C4.37331 7.03788 4.20241 7.24612 4.10149 7.48976C4.00058 7.73339 3.97417 8.00148 4.02562 8.26012C4.07707 8.51876 4.20405 8.75634 4.39052 8.94281C4.57699 9.12928 4.81457 9.25627 5.07321 9.30771C5.33186 9.35916 5.59994 9.33276 5.84358 9.23184C6.08721 9.13092 6.29545 8.96003 6.44196 8.74076C6.58847 8.52149 6.66667 8.26371 6.66667 8C6.66667 7.64638 6.52619 7.30724 6.27614 7.05719C6.02609 6.80714 5.68696 6.66667 5.33333 6.66667ZM5.33333 2C5.06963 2 4.81184 2.0782 4.59257 2.22471C4.37331 2.37122 4.20241 2.57945 4.10149 2.82309C4.00058 3.06672 3.97417 3.33481 4.02562 3.59345C4.07707 3.8521 4.20405 4.08967 4.39052 4.27614C4.57699 4.46261 4.81457 4.5896 5.07321 4.64105C5.33186 4.69249 5.59994 4.66609 5.84358 4.56517C6.08721 4.46426 6.29545 4.29336 6.44196 4.07409C6.58847 3.85483 6.66667 3.59704 6.66667 3.33333C6.66667 2.97971 6.52619 2.64057 6.27614 2.39052C6.02609 2.14048 5.68696 2 5.33333 2ZM10.6667 11.3333C10.403 11.3333 10.1452 11.4115 9.92591 11.558C9.70664 11.7045 9.53574 11.9128 9.43483 12.1564C9.33391 12.4001 9.30751 12.6681 9.35895 12.9268C9.4104 13.1854 9.53739 13.423 9.72386 13.6095C9.91033 13.7959 10.1479 13.9229 10.4065 13.9744C10.6652 14.0258 10.9333 13.9994 11.1769 13.8985C11.4205 13.7976 11.6288 13.6267 11.7753 13.4074C11.9218 13.1882 12 12.9304 12 12.6667C12 12.313 11.8595 11.9739 11.6095 11.7239C11.3594 11.4738 11.0203 11.3333 10.6667 11.3333ZM10.6667 6.66667C10.403 6.66667 10.1452 6.74487 9.92591 6.89137C9.70664 7.03788 9.53574 7.24612 9.43483 7.48976C9.33391 7.73339 9.30751 8.00148 9.35895 8.26012C9.4104 8.51876 9.53739 8.75634 9.72386 8.94281C9.91033 9.12928 10.1479 9.25627 10.4065 9.30771C10.6652 9.35916 10.9333 9.33276 11.1769 9.23184C11.4205 9.13092 11.6288 8.96003 11.7753 8.74076C11.9218 8.52149 12 8.26371 12 8C12 7.64638 11.8595 7.30724 11.6095 7.05719C11.3594 6.80714 11.0203 6.66667 10.6667 6.66667ZM10.6667 2C10.403 2 10.1452 2.0782 9.92591 2.22471C9.70664 2.37122 9.53574 2.57945 9.43483 2.82309C9.33391 3.06672 9.30751 3.33481 9.35895 3.59345C9.4104 3.8521 9.53739 4.08967 9.72386 4.27614C9.91033 4.46261 10.1479 4.5896 10.4065 4.64105C10.6652 4.69249 10.9333 4.66609 11.1769 4.56517C11.4205 4.46426 11.6288 4.29336 11.7753 4.07409C11.9218 3.85483 12 3.59704 12 3.33333C12 2.97971 11.8595 2.64057 11.6095 2.39052C11.3594 2.14048 11.0203 2 10.6667 2Z" fill="#898989"/>
                        </svg>
                    </span>
                
                    <p class="border-none w-full ml-1.5 px-0 bg-color-F3 dark:bg-color-47 rounded-xl"><span class="textarea-span text-13 font-Figtree font-normal text-color-14 dark:!text-white" role="textbox" contenteditable> ${element} </span></p>
                    
                    ${ index != 0 ? `<button class="remove-button cursor-pointer ml-2">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.33881 2.33978C2.65773 2.02085 3.17482 2.02085 3.49375 2.33978L6.99961 5.84564L10.5055 2.33978C10.8244 2.02085 11.3415 2.02085 11.6604 2.33978C11.9793 2.65871 11.9793 3.17579 11.6604 3.49472L8.15455 7.00059L11.6604 10.5064C11.9793 10.8254 11.9793 11.3425 11.6604 11.6614C11.3415 11.9803 10.8244 11.9803 10.5055 11.6614L6.99961 8.15553L3.49375 11.6614C3.17482 11.9803 2.65773 11.9803 2.33881 11.6614C2.01988 11.3425 2.01988 10.8254 2.33881 10.5064L5.84467 7.00059L2.33881 3.49472C2.01988 3.17579 2.01988 2.65871 2.33881 2.33978Z" fill="#898989"/>
                        </svg>
                    </button>` : '' }
                </div>`;
        });

        $('#input-container').empty().html(OulineHtml);
    }

}

// Generate Title
$('.TitleForm').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);

    $.ajax({
        method: 'POST',
        url: titleGenerateUrl,
        data: form.serialize(),
        beforeSend: function() {
            $(".TitleForm .loader").removeClass('hidden');
            $('#TitleGenerateButton').attr('disabled', 'disabled');
        },
        complete: function() {
            
        },
        success: function(data) {
            if (data.status == 'success') {
                $('.TitleData').empty();
                $('.TitleData').html(data.output);
                $('#TitleGenerateButton').removeAttr('disabled');
                $(".TitleForm .loader").addClass('hidden');
                $('.CreditBalance').empty();
                $(".CreditBalance").html(data.credit_balance);
                $('.ContinueButtonDiv').addClass('hidden');

                // Sidebar word count update
                var total_word_used = $('.total-word-used');
                var word_used_count = Number(total_word_used.text()) + data.word_used;
                total_word_used.text(word_used_count);

                longArticleFormData.longArticleId = data.long_article_id;
                longArticleFormData.titles.data.topic = $('#TitleTopic').val();
                longArticleFormData.titles.data.keywords = $('#TitleKeywords').val();
                longArticleFormData.titles.data.generatedTitles = data.titles;

                // Stepper active after title generation
                $('.StepperParent .Stepper:eq(0) .empty-circle').addClass('hidden');
                $('.StepperParent .Stepper:eq(0) .full-circle').removeClass('hidden');
                $('.TitleSuggestionText').addClass('hidden');


                handleLongArticleId();
                handleItemClick();
                setLocalLongArticleData();
                
                toastMixin.fire({
                    title: data.message,
                    icon: data.status
                });
            } else {
                errorMessage('Something went wrong', 'TitleGenerateButton');
            }
        },
        error: function(data) {
            var jsonData = JSON.parse(data.responseText);
            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message
            errorMessage(message, 'TitleGenerateButton');
         }
    });
})

// Generate Outline
$('.OutlineForm').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);

    $.ajax({
            method: 'POST',
            url: outlineGenerateUrl,
            data: form.serialize(),
            beforeSend: function() {
            
        },
        beforeSend: function() {
            $(".OutlineForm .loader").removeClass('hidden');
            $('#OutlineGenerateButton').attr('disabled', 'disabled');
            $('.ArticlePreviousButton').attr('disabled', 'disabled');
        },
        complete: function() {
            
            
        },
        success: function(data) {
            if (data.status == 'success') {
                $('.OutlineData').empty();
                $('.OutlineData').html(data['output']);
                $('#OutlineGenerateButton').removeAttr('disabled');
                $('.ArticlePreviousButton').removeAttr('disabled');
                $(".loader").addClass('hidden');
                $('.CreditBalance').empty();
                $(".CreditBalance").html(data.credit_balance);
                $('.ContinueButtonDiv').addClass('hidden');

                // Sidebar word count update
                var total_word_used = $('.total-word-used');
                var word_used_count = Number(total_word_used.text()) + data.word_used;
                total_word_used.text(word_used_count);

                longArticleFormData.outlines.data.title = $('#OutlineTitle').val();
                longArticleFormData.outlines.data.keywords = $('#OutlineKeywords').val();
                longArticleFormData.outlines.data.generatedOutlines = data.outlines;

                // Stepper active after outline generation
                $('.StepperParent .Stepper:eq(1) .empty-circle').addClass('hidden');
                $('.StepperParent .Stepper:eq(1) .full-circle').removeClass('hidden');
                $('.OutlineSuggestionText').addClass('hidden');


                handleItemClick();
                loadInputFields();
                setLocalLongArticleData();
                toastMixin.fire({
                    title: data.message,
                    icon: data.status
                });
            } else {
                $('.ArticlePreviousButton').removeAttr('disabled');
                errorMessage('Something went wrong', 'OutlineGenerateButton');
            }
        },
        error: function(data) {
            $('.ArticlePreviousButton').removeAttr('disabled');
            var jsonData = JSON.parse(data.responseText);
            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message
            errorMessage(message, 'OutlineGenerateButton');
        }
    });
})

// Article Form
$('.ArticleForm').on('submit', function(e) {
    e.preventDefault();


    let form = $(this).serializeArray();

    var outlines = $('#input-container p span').map(function() {
        if ($(this).text() != '') {
            return $(this).text();
        }
    }).get();

    form.push({name: 'outlines', value: JSON.stringify(outlines)});
    
    $.ajax({
            method: 'POST',
            url: initArticleGenerateUrl,
            data: form,
        beforeSend: function() {
            $(".ArticleForm .loader").removeClass('hidden');
            $('#ArticleGenerateButton').attr('disabled', 'disabled');
            $('.ArticlePreviousButton').attr('disabled', 'disabled');
            $('.BlogEditButton').addClass('hidden');
            $('.ArticleSuggestion').addClass('hidden');
            $('.ArticleSection').removeClass('hidden');
            
        },
        complete: function() {
            
            
        },
        success: function(data) {
            if (data.status == 200) {
                var timer = 0;
                setTimeout(() => {
                    timer = 1;
                }, 5000);
                $('.ArticleData').html(`
                    <h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">${$('#ArticleTitle').val()}</h1>
                `);
                $('.used-words-percentage').empty().append(data.usedPercentage);
                $('.article-data').empty().html(data['article']);
                $('.btn-create-content').prop('disabled', false);

                longArticleFormData.article.data.title = $('#ArticleTitle').val();
                longArticleFormData.article.data.keywords = $('#ArticleKeywords').val();
                longArticleFormData.article.data.generatedArticleBlogId = data.longArticleBlogId;
                longArticleFormData.article.data.outlines = JSON.stringify(outlines);
                setLocalLongArticleData();

                let url = articleGenerateUrl + "?long_article_id=" + data.longArticleBlogId;

                const source = new EventSource(url, {withCredentials: true});

                source.addEventListener("open", (e) => {
                    $('.ArticleSuggestion').addClass('hidden')
                    $('.ArticleData').removeClass('hidden');
                });

                source.addEventListener('update', function(event) {

                    if (event.data === "<END_STREAMING_SSE>") {
                        $('#ArticleGenerateButton').removeAttr('disabled');
                        $('.ArticlePreviousButton').removeAttr('disabled');
                        $(".ArticleForm .loader").addClass('hidden');
                        $(".BlogEditButton").attr("href", SITE_URL + "/user/articles/"+ data.longArticleBlogId + "/edit" );
                        $('.BlogEditButton').removeClass('hidden');
                        toastMixin.fire({
                            title: jsLang('Article generated successfully.'),
                            icon: 'success'
                        });
                        
                        // Stepper active after article generation
                        $('.StepperParent .Stepper:eq(2) .empty-circle').addClass('hidden');
                        $('.StepperParent .Stepper:eq(2) .full-circle').removeClass('hidden');

                        source.close();
                    }

                    let txt = event.data;
                    txt = txt.replace(/(?:\r\n|\r|\n)/g, '<br>');
                    let oldValue = '';
                    oldValue += $('.ArticleData').html();
                    let value = oldValue + txt;
                    value = value.replace(/\*\*(.*?)\*\*/g,
                                    '<br><br><h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">$1</h1>');
                    $('.ArticleData').html(value);
                    
                    if(timer == 1){
                        $('.article-scroll').scrollTop(10000);
                    }

                    $(".article-scroll").scroll(function(){
                        timer = 0;
                      });
                    

                    $('.article-scroll').scroll(function() {
                        var element = $(this);
                        
                        var sumScrollTopClientHeight = element.scrollTop() + element.innerHeight();
                    
                        var isAtBottom = Math.abs(sumScrollTopClientHeight - element[0].scrollHeight) < 1;
                    
                        if (isAtBottom) {
                            timer = 1;
                        } else {
                            timer = 0;
                        }
                    });
                    
                });

                source.addEventListener("message", (e) => {
                    if (e.data > 0) {
                        $('.total-word-left').text(e.data);
                    }
                });

                source.addEventListener("error", function() {
                    var message = jsLang("An error occurred while generating the content. Please try again.");
                    errorMessage(message, 'ArticleGenerateButton');
                });
                
                source.addEventListener("wordused", (e) => {
                    console.log(e.data);
                    if (e.data > 0) {
                        var total_word_used = $('.total-word-used');
                        var word_used_count = Number(total_word_used.text()) + Number(e.data);
                        total_word_used.text(word_used_count);

                    }
                });

            } else if (data.status = 404) {
                errorMessage(data.response, 'ArticleGenerateButton');
            } else {
                var message = jsLang('Something went wrong');
                errorMessage(message, 'ArticleGenerateButton');
            }
        },
        error: function(data) {
            var jsonData = JSON.parse(data.responseText);
            var message = jsonData.response.records.response ? jsonData.response.records.response : jsonData.response.status.message
            errorMessage(message, 'ArticleGenerateButton');
        }
    });
})


// populateTitlesData
function displayTitleData() {
    let data = {
        generatedTitles: longArticleFormData.titles.data.generatedTitles
    };

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        method: 'POST',
        url: displayTitleDataUrl,
        data: data,
        beforeSend: function() {
            $('.TitleData').empty();
        },
        success: function(data) {
            $('.TitleData').html(data.html);
            handleItemClick();
        },
        error: function(data) {}
    });
}

function displayOulineData() {
    let data = {
        generatedOutlines: longArticleFormData.outlines.data.generatedOutlines
    };

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        method: 'POST',
        url: displayOutlineDataUrl,
        data: data,
        beforeSend: function() {
            $('.OutlineData').empty();
        },
        success: function(data) {
            $('.OutlineData').html(data.html);
            handleItemClick();
        },
        error: function(data) {}
    });
}


function displayArticleBlogData()
{
    let data = {
        generatedArticleBlogId: longArticleFormData.article.data.generatedArticleBlogId
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        method: 'POST',
        url: displayArticleBlogDataUrl,
        data: data,
        beforeSend: function() {
            $('.ArticleData').empty();
        },
        success: function(data) {
            $('.ArticleData').html(data.html);
            if (data.contents == null) {
                $('.ArticleSuggestion').removeClass('hidden');
                $('.ArticleSection').addClass('hidden');
            }
        },
        error: function(data) {}
    });
    
}


function resetLongArticleFormData() {
    forgetSessionData().then((status) => {
        if (status == 'OK') {
            longArticleFormData = initLongArticleFormData;
            setLocalLongArticleData();
            window.location.href = longArticleCreateUrl;
        }
    });   
}

function forgetSessionData()
{
    return  new Promise(function(myResolve, myReject) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            method: 'POST',
            url: forgetSessionDataUrl,
            data: {},
            beforeSend: function() {},
            success: function(data) {
                myResolve("OK");
            },
            error: function(data) {}
        });
    });
}

// Make input fields sortable
$("#input-container").sortable({
    handle: ".handle"
});

// Add Field button functionality
$("#add-button").on('click', function(e) {
    e.preventDefault();
    var newInputField = $(".sort:hidden").first().clone();
    newInputField.find("input").val("");
    newInputField.removeClass("hidden"); // Remove the "hidden" class from the cloned block
    $("#input-container").append(newInputField);
});

// Remove button functionality
$("#input-container").on("click", ".remove-button", function() {
    $(this).closest(".sort").remove();
});

$('.avatar-img').on('dragstart', (event) => {
    event.preventDefault();
});


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


$('#provider').on('change', function() {
    hideProviderOptions();
    let activeProvider = $(this).val();
    $('.' + activeProvider + '_div').removeClass('hidden');
});


function hideProviderOptions() 
{
    $('.ProviderOptions').each(function() {
        $(this).addClass('hidden')
    });
}