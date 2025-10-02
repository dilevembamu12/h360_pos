"use strict";
const progressBar = document.getElementById('progress-input');
function copyToClipboard(e, element) {
    var text = $(element).text();
    navigator.clipboard.writeText(text);
    $(e).addClass('clicked');
    $(e).find('span.copy-data').addClass('hidden');

    setTimeout(function () {
        $(e).removeClass('clicked');
        $(e).find('span.copy-data').removeClass('hidden');
    }, 2000);
}

function downloadAll() {
    var imageElement = document.querySelectorAll('.variant-images img');

    imageElement.forEach(function (element) {
        const imageUrl = element.getAttribute("data-original");
        const downloadFileName = element.getAttribute("download");
        downloadImage(imageUrl, downloadFileName);
    });
}

function downloadImage(url, filename) {
    const anchor = document.createElement("a");
    anchor.href = url;
    anchor.download = filename;
    anchor.click();
}

$(".modal-not-open").on('click', function () {
    $(".image-information-modal").css("display", "none");
});

function clipboard(elem, event) {
    elem.prev('input[type="text"]').focus().select();
    document.execCommand(event);
    elem.prev('input[type="text"]').blur();
    elem.addClass('clicked');
    $('.copy-link').addClass('hidden');

    // Remove 'clicked' class and 'hidden' class after a timeout
    setTimeout(function () {
        elem.removeClass('clicked');
        $('.copy-link').removeClass('hidden');
    }, 3000);
}

// Single Image View 
$(document).on('click', ".image-modal-button", function () {
    $('.image-with-prompt').show();
    setAttribute($(this))
    fetchImageData($(this).data('slug'));
});

function setAttribute(data)
{
    const dataNew = data.data();
    const filteredData = Object.keys(dataNew)
        .filter(key => key.startsWith('advanced_') && key !== 'file')
        .reduce((obj, key) => {
            const newKey = key.replace('advanced_', '');
            const value = dataNew[key];
            
            if (value !== null && value !== undefined && value !== '' && key !== 'file') {
                obj[newKey] = value;
            }
            
            return obj;
        }, {});
    $(".gallery-placeholder").show();
    const isVideo = data.data('type') === 'video';
    $('.additional-options').toggleClass('hidden', isVideo);

    $(".favorite-icon").remove();

    // Check if the element exists
    if ($(this).hasClass('.favorite-modal-image-' + data.data('id'))) {
    } else {
        $('.favorite-modal').addClass('favorite-modal-image-' + data.data('id'));
    }

    $(".image-information-modal").css("display", "flex");
    $('.image-modal-container').addClass('hidden');
    $(".main-image").attr("src", data.data('source'));
    if (data.data('type') == 'video') {
        $('.image-with-prompt').hide();
    }
    $('.image-promt').text(data.data('name'));
    $('.image-title').text(data.data('name'));
    $('.modal-image-delete').attr('id', data.data('id'));
    $('.modal-image-delete').attr('type', data.data('type'));
    $(".share-image").attr("src", data.data('source'));

    const generatedHTML = createDynamicHTML(filteredData);
    $('.advanceOptions').html(generatedHTML);

    $(".facebook-image-share").attr('href', 'https://www.facebook.com/sharer.php?u=' + encodeURIComponent(data.data('source')));
    $(".linkedin-image-share").attr('href', 'http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(data.data('source')));
    $(".instagram-image-share").attr('href', 'https://www.instagram.com/sharer.php?u=' + encodeURIComponent(data.data('source')));
    $(".whatsapp-image-share").attr('href', 'https://api.whatsapp.com/send?text=' + encodeURIComponent(data.data('source')));
    $(".pinterest-image-share").attr('href', 'http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(data.data('source')));

    $('.delete-image-bg').attr("href", data.data('source'));


    let siteUrl = SITE_URL + '/' + 'user/images/create'
    $('.variant-url').attr('href', siteUrl + '?id=' + data.data('id') + '&prompt=' + data.data('name'))

    $('.favorite-modal').attr('data-image-id', data.data('id'));
    $('.modal-image-delete').attr('image-id', data.data('id'));

    $(".image-share-text-box").val(SITE_URL + '/' + 'images/share/' + data.data('slug'));

    var isFavorite = $(".favorite-image-" + data.data('id')).attr('data-is-favorite');

    if (isFavorite === true || isFavorite == 'true') {
        var favorite = `<div class="favorite-icon relative md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg bg-white wishlist-border">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 4.5L6.5 3L3.5 4L2.5 6V8.5L9 15L9.5 14.5L12 11.5L14.5 9.5L15.5 7.5L14.5 4.5L11.5 3L9 4.5Z" fill="#E22861"/>
                <path d="M9.00077 3.39692C9.85769 2.62982 10.9759 2.22007 12.1256 2.25187C13.2752 2.28368 14.3691 2.75462 15.1823 3.56792C15.9948 4.38031 16.4658 5.47273 16.4987 6.62123C16.5316 7.76974 16.1239 8.88733 15.3593 9.74492L8.99927 16.1139L2.64077 9.74492C1.87521 8.88689 1.46717 7.76834 1.50042 6.61891C1.53367 5.46948 2.00569 4.37638 2.81956 3.56404C3.63344 2.7517 4.72742 2.28175 5.87691 2.25067C7.02641 2.21959 8.14419 2.62974 9.00077 3.39692ZM14.1203 4.62767C13.5785 4.08626 12.85 3.77273 12.0843 3.75139C11.3187 3.73005 10.5739 4.00253 10.0028 4.51292L9.00152 5.41142L7.99952 4.51367C7.43077 4.00509 6.68961 3.73232 5.92687 3.75086C5.16412 3.7694 4.43709 4.07787 3.89373 4.61349C3.35037 5.14911 3.03151 5.87163 3.00202 6.63404C2.97254 7.39644 3.23465 8.14143 3.73502 8.71742L9.00002 13.9907L14.265 8.71817C14.7633 8.14472 15.0254 7.4036 14.9986 6.6444C14.9717 5.8852 14.6578 5.16446 14.1203 4.62767Z" fill="#E22861"/>
            </svg> 
        </div>`
    } else {
        var favorite = `<div class="favorite-icon relative md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.00077 1.39692C8.85769 0.629819 9.97588 0.220071 11.1256 0.251875C12.2752 0.283678 13.3691 0.754618 14.1823 1.56792C14.9948 2.38031 15.4658 3.47273 15.4987 4.62123C15.5316 5.76974 15.1239 6.88733 14.3593 7.74492L7.99927 14.1139L1.64077 7.74492C0.875208 6.88689 0.467169 5.76834 0.500419 4.61891C0.533668 3.46948 1.00568 2.37638 1.81956 1.56404C2.63344 0.751699 3.72742 0.281748 4.87691 0.250669C6.02641 0.21959 7.14418 0.629741 8.00077 1.39692ZM13.1203 2.62767C12.5785 2.08626 11.85 1.77273 11.0843 1.75139C10.3187 1.73005 9.57389 2.00253 9.00277 2.51292L8.00152 3.41142L6.99952 2.51367C6.43077 2.00509 5.68961 1.73232 4.92687 1.75086C4.16412 1.7694 3.43709 2.07787 2.89373 2.61349C2.35037 3.14911 2.03151 3.87163 2.00202 4.63404C1.97254 5.39644 2.23465 6.14143 2.73502 6.71742L8.00002 11.9907L13.265 6.71817C13.7633 6.14472 14.0254 5.4036 13.9986 4.6444C13.9717 3.8852 13.6578 3.16446 13.1203 2.62767Z" fill="white"/>
            </svg>
        </div>`
    }

    $('.favorite-modal').html(favorite);
}

function createDynamicHTML(filteredData) {
    let htmlContent = `
        <div class="mt-5 grid grid-cols-2 gap-5">`;

    // Iterate over filteredData to dynamically populate fields
    for (const [key, value] of Object.entries(filteredData)) {
        htmlContent += `
                <div>
                    <p class="font-Figtree text-14 font-sm text-color-89 font-medium">${key.replace('_', ' ').replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())}</p>
                    <p class="font-Figtree leading-6 font-base text-color-14 dark:text-white font-semibold break-words line-clamp-double ${key}">${value}</p>
                </div>`;
    }
    htmlContent += `</div>`;
    return htmlContent;
}

$(document).on('click', ".related-image-modal", function () {
    swiper.destroy();
    swiper2.destroy();
    setAttribute($(this))
    fetchImageData($(this).data('slug'));
});

function fetchImageData(slug) {
    var variantImage = '';
    var relatedImage = '';
    $('.old-img').hide();
    
    $.ajax({
        url: SITE_URL + "/user/images/" + slug,
        type: "get",
        dataType: "json",
        beforeSend: function () {
            var skeletonHTML = `
                <div class="flex justify-center gap-3">
                    <div class="skeleton-loader !w-[80px] h-[68px] dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4 rounded-xl">
                        
                    </div>
                    <div class="skeleton-loader !w-[80px] h-[68px] dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4 rounded-xl">
                        
                    </div>
                    <div class="skeleton-loader !w-[80px] h-[68px] dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4 rounded-xl">
                        
                    </div>
                </div>`;

            var relatedSkeletonHTML = `
                <div class="flex gap-2 justify-center">
                    <div class="skeleton-loader xl:w-[252px] xl:h-[252px] lg:w-[206px] lg:h-[206px] md:w-[156px] md:h-[156px] xs:w-[174px] xs:h-[174px] w-[140px] h-[140px] rounded-lg object-cover dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4">
                        
                    </div>
                    <div class="skeleton-loader xl:w-[252px] xl:h-[252px] lg:w-[206px] lg:h-[206px] md:w-[156px] md:h-[156px] xs:w-[174px] xs:h-[174px] w-[140px] h-[140px] rounded-lg object-cover dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4">
                        
                    </div>
                    <div class="skeleton-loader xl:w-[252px] xl:h-[252px] lg:w-[206px] lg:h-[206px] md:w-[156px] md:h-[156px] xs:w-[174px] xs:h-[174px] w-[140px] h-[140px] rounded-lg object-cover dark:bg-color-89 mb-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4">
                        
                    </div>
                </div>`;


            $('.variant-image').append(skeletonHTML);
            $('.related-image').append(relatedSkeletonHTML);
        },
        success: function (data) {
           
            if (data.data.variants.length) {
                data.data.variants.forEach(function (item) {
                    variantImage += `<div class="swiper-slide cursor-grab !w-[80px] h-full rounded-lg old-img variant-images">
                    <img class="mx-auto w-[68px] h-[68px] rounded-[10px] object-cover" data-original="${item.originalImageUrl}" src="${item.url}" download="" />
                    </div>`
                });
                
                $('.variant-image').empty();
                $('.variant-image').append(variantImage);
                
                data.data.relatedImages.forEach(function (item) {
                    const optionsData = Object.entries(item.generation_options || {})
                        .map(([key, value]) => `data-advanced_${key}="${value}"`)
                        .join(" ");
                
                    relatedImage += `
                        <img 
                            src="${item.originalImageUrl}" 
                            data-slug="${item.slug}" 
                            data-name="${item.title}" 
                            data-promt="${item.title}" 
                            ${optionsData} 
                            data-id="${item.id}" 
                            data-advanced_created_on="${item.created_at}" 
                            data-source="${item.originalImageUrl}" 
                            id="${item.id}" 
                            class="old-img cursor-pointer related-image-modal xl:w-[252px] xl:h-[252px] lg:w-[206px] lg:h-[206px] md:w-[156px] md:h-[156px] xs:w-[174px] xs:h-[174px] w-[140px] h-[140px] rounded-lg object-cover">
                    `;
                });

                $('.related-image').empty();
                if ($.trim(relatedImage) === "") {
                    $('.related-image-body').hide();
                } else {
                    $('.related-image-body').show();
                    $('.related-image').append(relatedImage);
                }

                $('.main-image-variant').html(data.html);
                $(".gallery-placeholder").hide();
            }
            $(".loader-template").remove();
            
            video();

            $('.copy-btn').on('click', function () {
                clipboard($(this), 'copy')
            });

            gallerySlider();
            shareModal();

            $('.image-modal-container').removeClass('hidden');
            swiper2.on('slideChange', function () {
                var activeSlide = swiper2.activeIndex;
                $('.modal-image-delete').attr('id', $('.swiper-slide').eq(activeSlide).attr('id'));
            });

        },
    });
}

$(document).on('click', ".modal-close-btn", function () {
    $('.variant-image').val();
    $('.related-image').val();
    $('.main-image-variant').val();
    $(".image-information-modal").css("display", "none");
    swiper.destroy();
    swiper2.destroy();
    $('.favorite-modal').html(`  
    <div class="loader-template md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
            <mask id="path-1-inside-1_1032_3036" fill="white">
                <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
            </mask>
            <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)" />
            <defs> <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse"> <stop stop-color="#E60C84" /><stop offset="1" stop-color="#FFCF4B" /></linearGradient></defs>
        </svg>
    </div>
    `);
});

function shareModal() {
    $(".share-information").on('click', function () {
        $(".share-information-modal").css("display", "flex");
        $(".image-modal-container").show();
    });
    $(".share-modal-close-btn").on('click', function () {
        $(".share-information-modal").css("display", "none");
    });
}
let swiper;
let swiper2
function gallerySlider() {
    swiper = new Swiper(".gallery-slider", {
        slidesPerView: 4,
        roundLengths: true,
        loopAdditionalSlides: 30,
        observer: true,
        observeParents: true,
        observeChildren: true,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            428: {
                slidesPerView: 5,
            },
            640: {
                slidesPerView: 6,
            },
            800: {
                slidesPerView: 5,
            },
            1152: {
                slidesPerView: 6,
            },

        },
    });

    swiper2 = new Swiper(".gallery-slider2", {
        spaceBetween: 0,
        effect: 'fade',
        thumbs: {
            swiper: swiper,
        },
    });
}

const gallery = document.getElementById('gallery');

if (progressBar) {
    progressBar.addEventListener('input', updateColumnCount);
    function updateColumnCount() {
        const progressValue = parseInt(progressBar.value);
        const minColumns = 3;
        const maxColumns = 8;
        const numColumns = Math.round(minColumns + (progressValue / 100) * (maxColumns - minColumns));
        gallery.style.setProperty('--num-columns', numColumns);
    }
    const min = parseFloat(progressBar.min);
    const max = parseFloat(progressBar.max);
    let value = parseFloat(progressBar.value);

    function updateSliderBackground() {
        const percentage = ((value - min) / (max - min) * 100).toFixed(2);
        progressBar.style.background = `linear-gradient(to right, #898989 0%, #898989 ${percentage}%, #DFDFDF ${percentage}%, #DFDFDF 100%)`;
    }
    updateSliderBackground();
    progressBar.oninput = function () {
        value = parseFloat(this.value);
        updateSliderBackground();
    };
}

function imageToggle(element) {

    var imageId = element.getAttribute('data-image-id');
    var beforeIsFavorite = String(element.getAttribute('data-is-favorite'));
    var afterIsFavorite = (beforeIsFavorite == "true") ? false : true;

    toggleImage(imageId, afterIsFavorite, element);
}

function toggleImage(imageId, toggleState, element) {
    $.ajax({
        url: imageToggleFavoriteUrl,
        type: 'POST',
        dataType: "json",
        data: {
            _token: CSRF_TOKEN,
            image_id: imageId,
            toggle_state: String(toggleState)
        },
        beforeSend: () => {
            $(element).html(`  
            <div class="loader-template md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
                    <mask id="path-1-inside-1_1032_3036" fill="white">
                        <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                    </mask>
                    <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)" />
                    <defs> <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse"> <stop stop-color="#E60C84" /><stop offset="1" stop-color="#FFCF4B" /></linearGradient></defs>
                </svg>
            </div>
            `);
        },
        success: function (response) {
            if (toggleState) {
                $(element).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg bg-white wishlist-border">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5L6.5 3L3.5 4L2.5 6V8.5L9 15L9.5 14.5L12 11.5L14.5 9.5L15.5 7.5L14.5 4.5L11.5 3L9 4.5Z" fill="#E22861"/>
                        <path d="M9.00077 3.39692C9.85769 2.62982 10.9759 2.22007 12.1256 2.25187C13.2752 2.28368 14.3691 2.75462 15.1823 3.56792C15.9948 4.38031 16.4658 5.47273 16.4987 6.62123C16.5316 7.76974 16.1239 8.88733 15.3593 9.74492L8.99927 16.1139L2.64077 9.74492C1.87521 8.88689 1.46717 7.76834 1.50042 6.61891C1.53367 5.46948 2.00569 4.37638 2.81956 3.56404C3.63344 2.7517 4.72742 2.28175 5.87691 2.25067C7.02641 2.21959 8.14419 2.62974 9.00077 3.39692ZM14.1203 4.62767C13.5785 4.08626 12.85 3.77273 12.0843 3.75139C11.3187 3.73005 10.5739 4.00253 10.0028 4.51292L9.00152 5.41142L7.99952 4.51367C7.43077 4.00509 6.68961 3.73232 5.92687 3.75086C5.16412 3.7694 4.43709 4.07787 3.89373 4.61349C3.35037 5.14911 3.03151 5.87163 3.00202 6.63404C2.97254 7.39644 3.23465 8.14143 3.73502 8.71742L9.00002 13.9907L14.265 8.71817C14.7633 8.14472 15.0254 7.4036 14.9986 6.6444C14.9717 5.8852 14.6578 5.16446 14.1203 4.62767Z" fill="#E22861"/>
                    </svg> 
                </div>`)

            } else {
                $(element).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.00077 1.39692C8.85769 0.629819 9.97588 0.220071 11.1256 0.251875C12.2752 0.283678 13.3691 0.754618 14.1823 1.56792C14.9948 2.38031 15.4658 3.47273 15.4987 4.62123C15.5316 5.76974 15.1239 6.88733 14.3593 7.74492L7.99927 14.1139L1.64077 7.74492C0.875208 6.88689 0.467169 5.76834 0.500419 4.61891C0.533668 3.46948 1.00568 2.37638 1.81956 1.56404C2.63344 0.751699 3.72742 0.281748 4.87691 0.250669C6.02641 0.21959 7.14418 0.629741 8.00077 1.39692ZM13.1203 2.62767C12.5785 2.08626 11.85 1.77273 11.0843 1.75139C10.3187 1.73005 9.57389 2.00253 9.00277 2.51292L8.00152 3.41142L6.99952 2.51367C6.43077 2.00509 5.68961 1.73232 4.92687 1.75086C4.16412 1.7694 3.43709 2.07787 2.89373 2.61349C2.35037 3.14911 2.03151 3.87163 2.00202 4.63404C1.97254 5.39644 2.23465 6.14143 2.73502 6.71742L8.00002 11.9907L13.265 6.71817C13.7633 6.14472 14.0254 5.4036 13.9986 4.6444C13.9717 3.8852 13.6578 3.16446 13.1203 2.62767Z" fill="white"/>
                    </svg>
                </div>`)
            }

            $(".favorite-image-" + imageId).each((index, element) => {
                let value = (toggleState) ? 'true' : 'false';
                element.setAttribute("data-is-favorite", value);

                if (toggleState) {
                    $(element).closest('.gallery-item').removeClass('non-favorite').addClass('favorite');

                } else {
                    $(element).closest('.gallery-item').removeClass('favorite').addClass('non-favorite');

                    if ($('#favorite-image-filter').attr('data-text') == 'all') {
                        $(".non-favorite").hide();
                    }
                }
            });

            toastMixin.fire({
                title: response,
                icon: 'success'
            });
        },
        error: function (xhr, status, error) {
            toastMixin.fire({
                title: response,
                icon: 'error'
            });
        },
        complete: () => {
            $(".loader-template").remove();

        }
    });
}

function setFavorite(parent) {
    $('#favorite-image-filter').attr('data-text', 'all');
    $(".non-favorite").hide();
    $(parent).html(`<svg width="17" height="17" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.06383 17.3731C3.62909 17.5965 3.13682 17.206 3.22435 16.7071L4.15779 11.3864L0.195168 7.6102C-0.175161 7.25729 0.0165395 6.61204 0.512652 6.54156L6.02344 5.7587L8.48057 0.891343C8.70191 0.452886 9.3015 0.452886 9.52285 0.891343L11.98 5.7587L17.4908 6.54156C17.9869 6.61204 18.1786 7.25729 17.8083 7.6102L13.8456 11.3864L14.7791 16.7071C14.8666 17.206 14.3743 17.5965 13.9396 17.3731L9.00171 14.8351L4.06383 17.3731Z" fill="url(#paint0_linear_301_431)"/>
                    <defs>
                    <linearGradient id="paint0_linear_301_431" x1="11.7048" y1="15.3605" x2="6.10185" y2="1.87361" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#E60C84"/>
                    <stop offset="1" stop-color="#FFCF4B"/>
                    </linearGradient>
                    </defs>
                </svg>
                <p class="dark:text-white text-color-14 font-semibold mt-1">${jsLang('Favorites')}</p>
                `)
}

function setNotFavorite(parent) {
    $('#favorite-image-filter').attr('data-text', 'favorite');
    $(".non-favorite").show();
    $(parent).html(`
    <svg class="dark:text-white text-color-14" width="17" height="17" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2.50784 12.9944C2.43976 13.3824 2.82264 13.6862 3.16077 13.5124L7.00134 11.5384L10.8419 13.5124C11.18 13.6862 11.5629 13.3824 11.4948 12.9944L10.7688 8.8561L13.8509 5.91904C14.1389 5.64456 13.9898 5.14269 13.6039 5.08788L9.31778 4.47899L7.40667 0.693267C7.23452 0.352244 6.76817 0.352244 6.59601 0.693267L4.68491 4.47899L0.398743 5.08788C0.0128776 5.14269 -0.136223 5.64456 0.151811 5.91904L3.23385 8.8561L2.50784 12.9944ZM6.79937 10.5723L3.57434 12.2299L4.18181 8.76724C4.21044 8.60402 4.1566 8.43687 4.0394 8.32519L1.49592 5.90135L5.04169 5.39764C5.18827 5.37682 5.31625 5.2832 5.3854 5.14623L7.00134 1.94519L8.61729 5.14623C8.68643 5.2832 8.81442 5.37682 8.961 5.39764L12.5068 5.90135L9.96328 8.32519C9.84609 8.43687 9.79224 8.60402 9.82088 8.76724L10.4284 12.2299L7.20332 10.5723C7.07592 10.5068 6.92676 10.5068 6.79937 10.5723Z" fill="currentColor"/>
    </svg>
    <p class="dark:text-white text-color-14 font-semibold mt-1">${jsLang('Favorites')}</p>
    `);
}

$('#favorite-image-filter').on('click', function () {
    if ($("#favorite-image-filter").attr("data-text") == 'all') {
        setNotFavorite(this);
    } else {
        setFavorite(this);
    }
});

function modalImageToggle(element) {
    var imageId = element.getAttribute('data-image-id');
    var beforeIsFavorite = String(element.getAttribute('data-is-favorite'));
    var afterIsFavorite = (beforeIsFavorite == "true") ? false : true;
    toggleModalImage(imageId, afterIsFavorite, element);
}

function toggleModalImage(imageId, toggleState, element) {
    $.ajax({
        url: imageToggleFavoriteUrl,
        type: 'POST',
        dataType: "json",
        data: {
            _token: CSRF_TOKEN,
            image_id: imageId,
            toggle_state: String(toggleState)
        },
        beforeSend: () => {
            $(element).html(`
            <div class="loader-template md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
                    <mask id="path-1-inside-1_1032_3036" fill="white">
                        <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                    </mask>
                    <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)" />
                    <defs> <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse"> <stop stop-color="#E60C84" /><stop offset="1" stop-color="#FFCF4B" /></linearGradient></defs>
                </svg>
            </div>
            `);

        },
        success: function (response) {

            if (toggleState) {
                $(element).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg bg-white wishlist-border">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5L6.5 3L3.5 4L2.5 6V8.5L9 15L9.5 14.5L12 11.5L14.5 9.5L15.5 7.5L14.5 4.5L11.5 3L9 4.5Z" fill="#E22861"/>
                        <path d="M9.00077 3.39692C9.85769 2.62982 10.9759 2.22007 12.1256 2.25187C13.2752 2.28368 14.3691 2.75462 15.1823 3.56792C15.9948 4.38031 16.4658 5.47273 16.4987 6.62123C16.5316 7.76974 16.1239 8.88733 15.3593 9.74492L8.99927 16.1139L2.64077 9.74492C1.87521 8.88689 1.46717 7.76834 1.50042 6.61891C1.53367 5.46948 2.00569 4.37638 2.81956 3.56404C3.63344 2.7517 4.72742 2.28175 5.87691 2.25067C7.02641 2.21959 8.14419 2.62974 9.00077 3.39692ZM14.1203 4.62767C13.5785 4.08626 12.85 3.77273 12.0843 3.75139C11.3187 3.73005 10.5739 4.00253 10.0028 4.51292L9.00152 5.41142L7.99952 4.51367C7.43077 4.00509 6.68961 3.73232 5.92687 3.75086C5.16412 3.7694 4.43709 4.07787 3.89373 4.61349C3.35037 5.14911 3.03151 5.87163 3.00202 6.63404C2.97254 7.39644 3.23465 8.14143 3.73502 8.71742L9.00002 13.9907L14.265 8.71817C14.7633 8.14472 15.0254 7.4036 14.9986 6.6444C14.9717 5.8852 14.6578 5.16446 14.1203 4.62767Z" fill="#E22861"/>
                    </svg> 
                </div>`)
                $(".favorite-image-" + imageId).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg bg-white wishlist-border">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5L6.5 3L3.5 4L2.5 6V8.5L9 15L9.5 14.5L12 11.5L14.5 9.5L15.5 7.5L14.5 4.5L11.5 3L9 4.5Z" fill="#E22861"/>
                        <path d="M9.00077 3.39692C9.85769 2.62982 10.9759 2.22007 12.1256 2.25187C13.2752 2.28368 14.3691 2.75462 15.1823 3.56792C15.9948 4.38031 16.4658 5.47273 16.4987 6.62123C16.5316 7.76974 16.1239 8.88733 15.3593 9.74492L8.99927 16.1139L2.64077 9.74492C1.87521 8.88689 1.46717 7.76834 1.50042 6.61891C1.53367 5.46948 2.00569 4.37638 2.81956 3.56404C3.63344 2.7517 4.72742 2.28175 5.87691 2.25067C7.02641 2.21959 8.14419 2.62974 9.00077 3.39692ZM14.1203 4.62767C13.5785 4.08626 12.85 3.77273 12.0843 3.75139C11.3187 3.73005 10.5739 4.00253 10.0028 4.51292L9.00152 5.41142L7.99952 4.51367C7.43077 4.00509 6.68961 3.73232 5.92687 3.75086C5.16412 3.7694 4.43709 4.07787 3.89373 4.61349C3.35037 5.14911 3.03151 5.87163 3.00202 6.63404C2.97254 7.39644 3.23465 8.14143 3.73502 8.71742L9.00002 13.9907L14.265 8.71817C14.7633 8.14472 15.0254 7.4036 14.9986 6.6444C14.9717 5.8852 14.6578 5.16446 14.1203 4.62767Z" fill="#E22861"/>
                    </svg> 
                </div>`)

            } else {
                $(element).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.00077 1.39692C8.85769 0.629819 9.97588 0.220071 11.1256 0.251875C12.2752 0.283678 13.3691 0.754618 14.1823 1.56792C14.9948 2.38031 15.4658 3.47273 15.4987 4.62123C15.5316 5.76974 15.1239 6.88733 14.3593 7.74492L7.99927 14.1139L1.64077 7.74492C0.875208 6.88689 0.467169 5.76834 0.500419 4.61891C0.533668 3.46948 1.00568 2.37638 1.81956 1.56404C2.63344 0.751699 3.72742 0.281748 4.87691 0.250669C6.02641 0.21959 7.14418 0.629741 8.00077 1.39692ZM13.1203 2.62767C12.5785 2.08626 11.85 1.77273 11.0843 1.75139C10.3187 1.73005 9.57389 2.00253 9.00277 2.51292L8.00152 3.41142L6.99952 2.51367C6.43077 2.00509 5.68961 1.73232 4.92687 1.75086C4.16412 1.7694 3.43709 2.07787 2.89373 2.61349C2.35037 3.14911 2.03151 3.87163 2.00202 4.63404C1.97254 5.39644 2.23465 6.14143 2.73502 6.71742L8.00002 11.9907L13.265 6.71817C13.7633 6.14472 14.0254 5.4036 13.9986 4.6444C13.9717 3.8852 13.6578 3.16446 13.1203 2.62767Z" fill="white"/>
                    </svg>
                </div>`)
                $(".favorite-image-" + imageId).html(`
                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.00077 1.39692C8.85769 0.629819 9.97588 0.220071 11.1256 0.251875C12.2752 0.283678 13.3691 0.754618 14.1823 1.56792C14.9948 2.38031 15.4658 3.47273 15.4987 4.62123C15.5316 5.76974 15.1239 6.88733 14.3593 7.74492L7.99927 14.1139L1.64077 7.74492C0.875208 6.88689 0.467169 5.76834 0.500419 4.61891C0.533668 3.46948 1.00568 2.37638 1.81956 1.56404C2.63344 0.751699 3.72742 0.281748 4.87691 0.250669C6.02641 0.21959 7.14418 0.629741 8.00077 1.39692ZM13.1203 2.62767C12.5785 2.08626 11.85 1.77273 11.0843 1.75139C10.3187 1.73005 9.57389 2.00253 9.00277 2.51292L8.00152 3.41142L6.99952 2.51367C6.43077 2.00509 5.68961 1.73232 4.92687 1.75086C4.16412 1.7694 3.43709 2.07787 2.89373 2.61349C2.35037 3.14911 2.03151 3.87163 2.00202 4.63404C1.97254 5.39644 2.23465 6.14143 2.73502 6.71742L8.00002 11.9907L13.265 6.71817C13.7633 6.14472 14.0254 5.4036 13.9986 4.6444C13.9717 3.8852 13.6578 3.16446 13.1203 2.62767Z" fill="white"/>
                    </svg>
                </div>`)
            }

            let value = (toggleState) ? 'true' : 'false';
            $(".favorite-modal-image-" + imageId + ", " + ".favorite-image-" + imageId).attr("data-is-favorite", value);

            if (toggleState) {
                $("#image_" + imageId).removeClass('non-favorite').addClass('favorite');

            } else {
                $("#image_" + imageId).removeClass('favorite').addClass('non-favorite');

                if ($('#favorite-image-filter').attr('data-text') == 'all') {
                    $(".non-favorite").hide();
                }
            }

        },
    });
}

function filter(selectElement) {
    const selectedOption = selectElement.value;
    sortDivs(selectedOption);
}

function sortDivs(selectedOption) {
    const container = document.querySelector('.gallery');
    const allDiv = Array.from(container.querySelectorAll('.gallery-item'));

    allDiv.sort((a, b) => {
        const aId = parseInt(a.id.split('_')[1]);
        const bId = parseInt(b.id.split('_')[1]);

        if (selectedOption === 'newest') {
            return bId - aId;
        } else {
            return aId - bId;
        }
    });

    for (const div of allDiv) {
        container.appendChild(div);
    }
}

$(document).ready(function () {
    if (window.location.href.includes("?slug")) {

        var parts = window.location.href.split("?slug=");

        if (parts.length > 1) {
            var slug = decodeURIComponent(parts[1]);
            var element = $('.image-modal-button[data-slug="' + slug + '"]');

            if (element.length > 0) {
                element.trigger('click');
            }
        }
    }
});

var galleryPageNumber = $('#gallery').data('next-page-url') ? $('#gallery').data('next-page-url').split("?page=")[1] : 0;
var galleryChecked = true;

$('.gallery-scrollbar').on('scroll', function () {
    checkGalleryImageIfAtEnd(this)
});
var spinner = `
    <div class="flex flex-col gap-1 fetch-skeleton">
        <div class="skeleton-loader h-[220px] rounded-xl">
            
        </div>
        <div class="skeleton-loader h-[220px] rounded-xl">
            
        </div>
        <div class="skeleton-loader h-[220px] rounded-xl">
            
        </div>
    </div>
`;

function checkGalleryImageIfAtEnd(contentContainer) {

    var scrolltHeight = contentContainer.scrollHeight;
    var clientHeight = contentContainer.clientHeight;
    var scrollPosition = contentContainer.scrollTop;

    if ((scrollPosition + clientHeight >= scrolltHeight) && galleryPageNumber != 0 && galleryPageNumber.length != 0 && galleryChecked) {
        galleryChecked = false;
        const parentDiv = $('#gallery');
        parentDiv.append(spinner);
        doAjaxprocess(
            SITE_URL + '/' + 'user/gallery?page=' + galleryPageNumber,
            {},
            'get',
            'json'
        ).done(function (response) {
            var sidebarHTML = response.items.map(function (item) {
                return `
                <div class="gallery-item overflow-hidden md:rounded-lg rounded h-max cursor-pointer ${item.is_favorite ? 'favorite' : 'non-favorite'}" id="image_${item.id}">
                    <div class="img-content bg-white img-grow md:rounded-lg rounded relative download-gallery-image-container download-image-container">
                        <div class="tab-content-${item.id}">
                            <img class="img-responsive object-cover ${item.size}" src="${item.url}">
                            <div class="gallery-image-hover-overlay image-modal-button" id=${item.id} data-slug="${item.slug}" data-name="${item.title}" data-promt="${item.title}" data-id="${item.id}" data-style="${item.art_style}" data-resulation="${item.size}" data-created="${item.created_at}" data-lightstyle="${item.lighting_style}" data-source="${item.url}" data-is-favorite="${item.is_favorite}"></div>
                            <div class="absolute top-0">
                                <div class="image-download-button  mt-3 md:ml-3 ml-1 bg-color-14">
                                    <a href="javascript: void(0)" class="relative md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47 modal-toggle image-tooltip-delete gallery-dlt" id="${item.id}" data-type="${item.type}">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 1.25C5 0.835786 5.33579 0.5 5.75 0.5H10.25C10.6642 0.5 11 0.835786 11 1.25C11 1.66421 10.6642 2 10.25 2H5.75C5.33579 2 5 1.66421 5 1.25ZM2.74418 2.75H1.25C0.835786 2.75 0.5 3.08579 0.5 3.5C0.5 3.91421 0.835786 4.25 1.25 4.25H2.04834L2.52961 11.4691C2.56737 12.0357 2.59862 12.5045 2.65465 12.8862C2.71299 13.2835 2.80554 13.6466 2.99832 13.985C3.29842 14.5118 3.75109 14.9353 4.29667 15.1997C4.64714 15.3695 5.0156 15.4377 5.41594 15.4695C5.80046 15.5 6.27037 15.5 6.8382 15.5H9.1618C9.72963 15.5 10.1995 15.5 10.5841 15.4695C10.9844 15.4377 11.3529 15.3695 11.7033 15.1997C12.2489 14.9353 12.7016 14.5118 13.0017 13.985C13.1945 13.6466 13.287 13.2835 13.3453 12.8862C13.4014 12.5045 13.4326 12.0356 13.4704 11.469L13.9517 4.25H14.75C15.1642 4.25 15.5 3.91421 15.5 3.5C15.5 3.08579 15.1642 2.75 14.75 2.75H13.2558C13.2514 2.74996 13.2471 2.74996 13.2427 2.75H2.75731C2.75294 2.74996 2.74857 2.74996 2.74418 2.75ZM12.4483 4.25H3.55166L4.0243 11.3396C4.06455 11.9433 4.09238 12.3525 4.13874 12.6683C4.18377 12.9749 4.23878 13.1321 4.30166 13.2425C4.45171 13.5059 4.67804 13.7176 4.95083 13.8498C5.06513 13.9052 5.22564 13.9497 5.53464 13.9742C5.85277 13.9995 6.26289 14 6.86799 14H9.13201C9.73711 14 10.1472 13.9995 10.4654 13.9742C10.7744 13.9497 10.9349 13.9052 11.0492 13.8498C11.322 13.7176 11.5483 13.5059 11.6983 13.2425C11.7612 13.1321 11.8162 12.9749 11.8613 12.6683C11.9076 12.3525 11.9354 11.9433 11.9757 11.3396L12.4483 4.25ZM6.5 6.125C6.91421 6.125 7.25 6.46079 7.25 6.875V10.625C7.25 11.0392 6.91421 11.375 6.5 11.375C6.08579 11.375 5.75 11.0392 5.75 10.625V6.875C5.75 6.46079 6.08579 6.125 6.5 6.125ZM9.5 6.125C9.91421 6.125 10.25 6.46079 10.25 6.875V10.625C10.25 11.0392 9.91421 11.375 9.5 11.375C9.08579 11.375 8.75 11.0392 8.75 10.625V6.875C8.75 6.46079 9.08579 6.125 9.5 6.125Z" fill="white"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="absolute top-0 right-0">
                                <div class="flex justify-end items-center gap-2 mt-3 md:mr-3 mr-1">
                                    <div class="image-download-button delete-image-bg">
                                        <a class="file-need-download relative md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg border border-color-47 image-tooltip-download" href="${item.url}" download="${item.title}">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 2.25C9.41421 2.25 9.75 2.58579 9.75 3V10.1893L12.2197 7.71967C12.5126 7.42678 12.9874 7.42678 13.2803 7.71967C13.5732 8.01256 13.5732 8.48744 13.2803 8.78033L9.53033 12.5303C9.23744 12.8232 8.76256 12.8232 8.46967 12.5303L4.71967 8.78033C4.42678 8.48744 4.42678 8.01256 4.71967 7.71967C5.01256 7.42678 5.48744 7.42678 5.78033 7.71967L8.25 10.1893V3C8.25 2.58579 8.58579 2.25 9 2.25ZM3 12C3.41421 12 3.75 12.3358 3.75 12.75V14.25C3.75 14.4489 3.82902 14.6397 3.96967 14.7803C4.11032 14.921 4.30109 15 4.5 15H13.5C13.6989 15 13.8897 14.921 14.0303 14.7803C14.171 14.6397 14.25 14.4489 14.25 14.25V12.75C14.25 12.3358 14.5858 12 15 12C15.4142 12 15.75 12.3358 15.75 12.75V14.25C15.75 14.8467 15.5129 15.419 15.091 15.841C14.669 16.2629 14.0967 16.5 13.5 16.5H4.5C3.90326 16.5 3.33097 16.2629 2.90901 15.841C2.48705 15.419 2.25 14.8467 2.25 14.25V12.75C2.25 12.3358 2.58579 12 3 12Z" fill="#F3F3F3"/>
                                            </svg>   
                                        </a>
                                    </div>
                                    <div class="image-download-button modal-not-open delete-image-bg">
                                        <a href="javascript: void(0)" class="favorite-image-${item.id}" onclick="imageToggle(this)" data-image-id="${item.id}" data-is-favorite="${item.is_favorite}">
                                            ${item.is_favorite ? `
                                                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg bg-white wishlist-border">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9 4.5L6.5 3L3.5 4L2.5 6V8.5L9 15L9.5 14.5L12 11.5L14.5 9.5L15.5 7.5L14.5 4.5L11.5 3L9 4.5Z" fill="#E22861"/>
                                                        <path d="M9.00077 3.39692C9.85769 2.62982 10.9759 2.22007 12.1256 2.25187C13.2752 2.28368 14.3691 2.75462 15.1823 3.56792C15.9948 4.38031 16.4658 5.47273 16.4987 6.62123C16.5316 7.76974 16.1239 8.88733 15.3593 9.74492L8.99927 16.1139L2.64077 9.74492C1.87521 8.88689 1.46717 7.76834 1.50042 6.61891C1.53367 5.46948 2.00569 4.37638 2.81956 3.56404C3.63344 2.7517 4.72742 2.28175 5.87691 2.25067C7.02641 2.21959 8.14419 2.62974 9.00077 3.39692ZM14.1203 4.62767C13.5785 4.08626 12.85 3.77273 12.0843 3.75139C11.3187 3.73005 10.5739 4.00253 10.0028 4.51292L9.00152 5.41142L7.99952 4.51367C7.43077 4.00509 6.68961 3.73232 5.92687 3.75086C5.16412 3.7694 4.43709 4.07787 3.89373 4.61349C3.35037 5.14911 3.03151 5.87163 3.00202 6.63404C2.97254 7.39644 3.23465 8.14143 3.73502 8.71742L9.00002 13.9907L14.265 8.71817C14.7633 8.14472 15.0254 7.4036 14.9986 6.6444C14.9717 5.8852 14.6578 5.16446 14.1203 4.62767Z" fill="#E22861"/>
                                                    </svg> 
                                                </div>
                                            ` : `
                                                <div class="relative tooltips md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg border border-color-47">
                                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.00077 1.39692C8.85769 0.629819 9.97588 0.220071 11.1256 0.251875C12.2752 0.283678 13.3691 0.754618 14.1823 1.56792C14.9948 2.38031 15.4658 3.47273 15.4987 4.62123C15.5316 5.76974 15.1239 6.88733 14.3593 7.74492L7.99927 14.1139L1.64077 7.74492C0.875208 6.88689 0.467169 5.76834 0.500419 4.61891C0.533668 3.46948 1.00568 2.37638 1.81956 1.56404C2.63344 0.751699 3.72742 0.281748 4.87691 0.250669C6.02641 0.21959 7.14418 0.629741 8.00077 1.39692ZM13.1203 2.62767C12.5785 2.08626 11.85 1.77273 11.0843 1.75139C10.3187 1.73005 9.57389 2.00253 9.00277 2.51292L8.00152 3.41142L6.99952 2.51367C6.43077 2.00509 5.68961 1.73232 4.92687 1.75086C4.16412 1.7694 3.43709 2.07787 2.89373 2.61349C2.35037 3.14911 2.03151 3.87163 2.00202 4.63404C1.97254 5.39644 2.23465 6.14143 2.73502 6.71742L8.00002 11.9907L13.265 6.71817C13.7633 6.14472 14.0254 5.4036 13.9986 4.6444C13.9717 3.8852 13.6578 3.16446 13.1203 2.62767Z" fill="white"/>
                                                    </svg>
                                                </div>
                                            `}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="image-download-button absolute bottom-3 bg-transparent image-modal-button hidden md:block">
                                <p class="mx-2.5 line-clamp-double text-white text-base font-medium font-Figtree leading-6 wrap-anywhere">${item.title}
                                </p>
                            </div>
                        </div>
                        <div class="spinner favorite-template-loader"></div>
                    </div>
                </div>
                `;
            }).join('');
            $('.fetch-skeleton').hide();
            parentDiv.append(sidebarHTML);
            parentDiv.removeAttr('data-next-page-url');
            galleryPageNumber = response.nextPageUrl ? response.nextPageUrl.split("?page=")[1] : [];
            galleryChecked = true;
        });
    }
}

// Delete Image
$(document).on('click', '.gallery-dlt', function () {
    $(this).append(`
        <div class="loader-template hidden md:w-[34px] md:h-[34px] w-7 h-7 flex items-center m-auto justify-center rounded-lg delete-image-bg border border-color-47">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
                <mask id="path-1-inside-1_1032_3036" fill="white">
                    <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                </mask>
                <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)" />
                <defs> <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse"> <stop stop-color="#E60C84" /><stop offset="1" stop-color="#FFCF4B" /></linearGradient></defs>
            </svg>
        </div>
    `);
});


function video() {
    const $video = $('.myVideos');
    const $playPauseBtn = $('.playPauseBtn');
    const $playIcon = $('.playIcon');
    const $pauseIcon = $('.pauseIcon');
    const $progressContainer = $('.progress-container');
    const $progressBar = $('.progress-video-bar');
    $playIcon.show();
    $pauseIcon.hide();
    const togglePlayPause = () => {
        if ($video[0].paused || $video[0].ended) {
            $video[0].play();
            $playIcon.hide();
            $pauseIcon.show();
        } else {
            $video[0].pause();
            $playIcon.show();
            $pauseIcon.hide();
        }
    };

    $playPauseBtn.on('click', togglePlayPause);
    $video.on('click', togglePlayPause);
    $video.on('ended', () => {
        $playIcon.show();
        $pauseIcon.hide();
    });
    $video.on('timeupdate', () => {
        const progress = ($video[0].currentTime / $video[0].duration) * 100;
        $progressBar.css('width', `${progress}%`);
    });
    $progressContainer.on('click', (e) => {
        const offsetX = e.pageX - $progressContainer.offset().left;
        const newTime = (offsetX / $progressContainer.width()) * $video[0].duration;
        $video[0].currentTime = newTime;
    });
}
