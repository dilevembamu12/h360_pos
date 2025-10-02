'use strict';

$(document).on('submit', '.image-to-video', function (e) {
    e.preventDefault();
    $('.loader-video').removeClass('hidden');
    let dataArray = $(this).serializeArray();
    let formData = new FormData();
    let nameWithoutExtension = jsLang('default');
    
    dataArray.forEach(function(item) {
        formData.append(item.name, item.value);
    });

    var fileInput = $("#file_input")[0];
    if (fileInput && fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
        nameWithoutExtension = fileInput.files[0].name.substring(0, fileInput.files[0].name.lastIndexOf('.'));
    }
  
    
    $.ajax({
        url: SITE_URL + '/' + 'api/v2/image-to-video',
        type: "POST",
        beforeSend: function (xhr) {
            $(".loader").removeClass('hidden');
            $('#video-creation').attr('disabled', 'disabled');
            xhr.setRequestHeader('Authorization', 'Bearer ' + ACCESS_TOKEN);
        },
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            var videoPath = SITE_URL + "/public/uploads/aiVideos/" + response.data.meta.file_name;
            var video = `
                <div class="cursor-pointer mb-1" id="image_${response.data.id}">
                    <div class="relative">
                        <div class="video-container videos-container">
                            <video class="object-cover rounded-lg myVideo">
                                    <source src="${videoPath}" type="video/mp4">
                            </video>
                            <div class="custom-controls">
                                <button class="custom-play-pause playPauseBtn md:p-5 p-2">
                                    <!-- Play SVG Icon -->
                                    <svg class="playIcon neg-transition-scale w-[30px] h-[30px]" width="40" height="40"
                                        viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9.44906 7.93287C9.49374 7.55428 9.62621 7.19135 9.83592 6.873C10.0456 6.55464 10.3268 6.28965 10.657 6.09914C10.9872 5.90863 11.3573 5.79786 11.7379 5.77564C12.1184 5.75343 12.4989 5.8204 12.8491 5.9712C14.6191 6.72787 18.5857 8.5262 23.6191 11.4312C28.6541 14.3379 32.1957 16.8762 33.7341 18.0279C35.0474 19.0129 35.0507 20.9662 33.7357 21.9545C32.2124 23.0995 28.7141 25.6045 23.6191 28.5479C18.5191 31.4912 14.5991 33.2679 12.8457 34.0145C11.3357 34.6595 9.64573 33.6812 9.44906 32.0529C9.21906 30.1495 8.78906 25.8279 8.78906 19.9912C8.78906 14.1579 9.2174 9.83787 9.44906 7.93287Z"
                                            fill="white" />
                                    </svg>
                                    <!-- Pause SVG Icon -->
                                    <svg class="pauseIcon neg-transition-scale w-[30px] h-[30px]" width="40" height="40"
                                        viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        style="display: none;">
                                        <path
                                            d="M13.334 4.16699H10.0007C7.69946 4.16699 5.83398 6.03247 5.83398 8.33366V31.667C5.83398 33.9682 7.69946 35.8337 10.0007 35.8337H13.334C15.6352 35.8337 17.5007 33.9682 17.5007 31.667V8.33366C17.5007 6.03247 15.6352 4.16699 13.334 4.16699Z"
                                            fill="white" />
                                        <path
                                            d="M30 4.16699H26.6667C24.3655 4.16699 22.5 6.03247 22.5 8.33366V31.667C22.5 33.9682 24.3655 35.8337 26.6667 35.8337H30C32.3012 35.8337 34.1667 33.9682 34.1667 31.667V8.33366C34.1667 6.03247 32.3012 4.16699 30 4.16699Z"
                                            fill="white" />
                                    </svg>
                                </button>
                            </div>
                            <div class="progress-container" id="progressContainer">
                                <div class="progress-video-bar" id="progressBar"></div>
                            </div>
                        </div>
                        <div class="absolute top-0">
                            <a href="javascript: void(0)" id="${response.data.id}" type="video"
                                class="mt-3 ltr:ml-3 rounded-lg rtl:mr-3 relative w-[34px] h-[34px] flex items-center m-auto justify-center delete-image-bg border border-color-47 modal-toggle image-tooltip-delete gallery-dlt">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5 1.25C5 0.835786 5.33579 0.5 5.75 0.5H10.25C10.6642 0.5 11 0.835786 11 1.25C11 1.66421 10.6642 2 10.25 2H5.75C5.33579 2 5 1.66421 5 1.25ZM2.74418 2.75H1.25C0.835786 2.75 0.5 3.08579 0.5 3.5C0.5 3.91421 0.835786 4.25 1.25 4.25H2.04834L2.52961 11.4691C2.56737 12.0357 2.59862 12.5045 2.65465 12.8862C2.71299 13.2835 2.80554 13.6466 2.99832 13.985C3.29842 14.5118 3.75109 14.9353 4.29667 15.1997C4.64714 15.3695 5.0156 15.4377 5.41594 15.4695C5.80046 15.5 6.27037 15.5 6.8382 15.5H9.1618C9.72963 15.5 10.1995 15.5 10.5841 15.4695C10.9844 15.4377 11.3529 15.3695 11.7033 15.1997C12.2489 14.9353 12.7016 14.5118 13.0017 13.985C13.1945 13.6466 13.287 13.2835 13.3453 12.8862C13.4014 12.5045 13.4326 12.0356 13.4704 11.469L13.9517 4.25H14.75C15.1642 4.25 15.5 3.91421 15.5 3.5C15.5 3.08579 15.1642 2.75 14.75 2.75H13.2558C13.2514 2.74996 13.2471 2.74996 13.2427 2.75H2.75731C2.75294 2.74996 2.74857 2.74996 2.74418 2.75ZM12.4483 4.25H3.55166L4.0243 11.3396C4.06455 11.9433 4.09238 12.3525 4.13874 12.6683C4.18377 12.9749 4.23878 13.1321 4.30166 13.2425C4.45171 13.5059 4.67804 13.7176 4.95083 13.8498C5.06513 13.9052 5.22564 13.9497 5.53464 13.9742C5.85277 13.9995 6.26289 14 6.86799 14H9.13201C9.73711 14 10.1472 13.9995 10.4654 13.9742C10.7744 13.9497 10.9349 13.9052 11.0492 13.8498C11.322 13.7176 11.5483 13.5059 11.6983 13.2425C11.7612 13.1321 11.8162 12.9749 11.8613 12.6683C11.9076 12.3525 11.9354 11.9433 11.9757 11.3396L12.4483 4.25ZM6.5 6.125C6.91421 6.125 7.25 6.46079 7.25 6.875V10.625C7.25 11.0392 6.91421 11.375 6.5 11.375C6.08579 11.375 5.75 11.0392 5.75 10.625V6.875C5.75 6.46079 6.08579 6.125 6.5 6.125ZM9.5 6.125C9.91421 6.125 10.25 6.46079 10.25 6.875V10.625C10.25 11.0392 9.91421 11.375 9.5 11.375C9.08579 11.375 8.75 11.0392 8.75 10.625V6.875C8.75 6.46079 9.08579 6.125 9.5 6.125Z"
                                        fill="white" />
                                </svg>
                            </a>
                        </div>
                        <div class="absolute top-0 ltr:right-3 rtl:left-3">
                            <div class="flex justify-end items-center gap-2 mt-3 ltr:ml-3 rtl:mr-3">
                                <a class="file-need-download relative w-[34px] h-[34px] flex items-center m-auto justify-center rounded-lg border border-color-47 image-tooltip-download delete-image-bg"
                                    href="${videoPath}" download="${nameWithoutExtension}">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9 2.25C9.41421 2.25 9.75 2.58579 9.75 3V10.1893L12.2197 7.71967C12.5126 7.42678 12.9874 7.42678 13.2803 7.71967C13.5732 8.01256 13.5732 8.48744 13.2803 8.78033L9.53033 12.5303C9.23744 12.8232 8.76256 12.8232 8.46967 12.5303L4.71967 8.78033C4.42678 8.48744 4.42678 8.01256 4.71967 7.71967C5.01256 7.42678 5.48744 7.42678 5.78033 7.71967L8.25 10.1893V3C8.25 2.58579 8.58579 2.25 9 2.25ZM3 12C3.41421 12 3.75 12.3358 3.75 12.75V14.25C3.75 14.4489 3.82902 14.6397 3.96967 14.7803C4.11032 14.921 4.30109 15 4.5 15H13.5C13.6989 15 13.8897 14.921 14.0303 14.7803C14.171 14.6397 14.25 14.4489 14.25 14.25V12.75C14.25 12.3358 14.5858 12 15 12C15.4142 12 15.75 12.3358 15.75 12.75V14.25C15.75 14.8467 15.5129 15.419 15.091 15.841C14.669 16.2629 14.0967 16.5 13.5 16.5H4.5C3.90326 16.5 3.33097 16.2629 2.90901 15.841C2.48705 15.419 2.25 14.8467 2.25 14.25V12.75C2.25 12.3358 2.58579 12 3 12Z"
                                            fill="#F3F3F3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>`;
        
            $('#video-gallery').prepend(video);
            $(".loader-video").addClass('hidden');
            const newVideo = $('#video-gallery').children('.cursor-pointer').first().find('.myVideo')[0];
            initializeCustomVideoPlayer([newVideo]);
        },
        complete: () => {
            $(".loader-video").addClass('hidden');
            $('#video-creation').removeAttr('disabled');
        },
        error: function(response) {
            var jsonData = JSON.parse(response.responseText);
            var message = jsonData.error ? jsonData.error : jsonData.message;
            errorMessage(message, 'video-creation');
            $(".loader-video").addClass('hidden');
        }
    });
});

let currentlyPlayingVideo = null;
function initializeCustomVideoPlayer(videos) {
    videos.forEach(video => {
        const playPauseBtn = video.parentNode.querySelector('.playPauseBtn');
        const playIcon = video.parentNode.querySelector('.playIcon');
        const pauseIcon = video.parentNode.querySelector('.pauseIcon');
        const progressBar = video.parentNode.querySelector('.progress-video-bar');
        const gallery = document.getElementById('video-gallery');
        const progressContainer = video.parentNode.querySelector('.progress-container');
        const progressBars = document.getElementById('progressInput');
        
        function updateColumnCount() {
            const progressValue = parseInt(progressBars.value);
            const minColumns = 3;
            const maxColumns = 8;
            const numColumns = Math.round(minColumns + (progressValue / 100) * (maxColumns - minColumns));
            gallery.style.setProperty('--num-columns', numColumns);

            videos.forEach(video => {
                const btn = video.parentNode.querySelector('.playPauseBtn');
                if (numColumns > 4) {
                    btn.style.display = 'none';
                    video.style.cursor = 'pointer';
                } else {
                   
                    btn.style.display = 'block';
                    video.style.cursor = 'default';
                }
            });
        }

        if (progressBars) {
            progressBars.addEventListener('input', updateColumnCount);
            function updateSliderBackground() {
                const min = parseFloat(progressBars.min);
                const max = parseFloat(progressBars.max);
                const value = parseFloat(progressBars.value);
                const percentage = ((value - min) / (max - min) * 100).toFixed(2);
                progressBars.style.background = `linear-gradient(to right, #898989 0%, #898989 ${percentage}%, #DFDFDF ${percentage}%, #DFDFDF 100%)`;
            }

            updateSliderBackground();
            progressBars.oninput = function () {
                updateSliderBackground();
                updateColumnCount();
            };
        }
        playPauseBtn.addEventListener('click', () => {
            if (currentlyPlayingVideo && currentlyPlayingVideo !== video) {
                currentlyPlayingVideo.pause();
                currentlyPlayingVideo.parentNode.querySelector('.playIcon').style.display = 'block';
                currentlyPlayingVideo.parentNode.querySelector('.pauseIcon').style.display = 'none';
            }

            if (video.paused) {
                video.play();
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'block';
                currentlyPlayingVideo = video;
            } else {
                video.pause();
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
                currentlyPlayingVideo = null;
            }
        });
        video.addEventListener('timeupdate', () => {
            const progress = (video.currentTime / video.duration) * 100;
            progressBar.style.width = `${progress}%`;
        });
        progressContainer.addEventListener('click', (event) => {
            const rect = progressContainer.getBoundingClientRect();
            const offsetX = event.clientX - rect.left;
        
            if (rect.width > 0 && !isNaN(video.duration)) {
                const newTime = (offsetX / rect.width) * video.duration;
                video.currentTime = Math.min(Math.max(newTime, 0), video.duration);
            }
        });
        
        video.addEventListener('ended', () => {
            playIcon.style.display = 'block';
            pauseIcon.style.display = 'none';
            currentlyPlayingVideo = null;
        });
        video.addEventListener('click', function() {
            if (parseInt(gallery.style.getPropertyValue('--num-columns')) > 4) {
                if (video.paused) {
                    videos.forEach(v => {
                        v.pause();
                        v.parentNode.querySelector('.playIcon').style.display = 'block';
                        v.parentNode.querySelector('.pauseIcon').style.display = 'none';
                    });
                    video.play();
                    playIcon.style.display = 'none';
                    pauseIcon.style.display = 'block';
                } else {
                    video.pause();
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            }
        });

    });
}


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
    let activeProvider = $(this).val();
    $('.' + activeProvider + '_div').removeClass('hidden');
});


function hideProviderOptions() 
{
    $('.ProviderOptions').each(function() {
        $(this).addClass('hidden')
    });
}
