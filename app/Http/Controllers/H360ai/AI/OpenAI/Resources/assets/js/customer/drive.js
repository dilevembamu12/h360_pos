"use strict";

    // Grid and list view 
    let toggleView = document.querySelector('.toggleView');
    let container = document.querySelector('.container-box');
    let fileName =  Array.from(document.querySelectorAll('.file-name p'));
    let fileDivs =  Array.from(document.querySelectorAll('.file-name'));
    let fileSVG =  Array.from(document.querySelectorAll('.file-name svg'));
    let othersDivContainer = document.querySelector('.others-div');
    let DriveTdHead = document.querySelector('.drive-table-head');
    let divs = Array.from(document.querySelectorAll('.view-mode'));
    let hideData = Array.from(document.querySelectorAll('.check-data'));
    let actionDot = Array.from(document.querySelectorAll('.action-dot'));
    let contentDiv = Array.from(document.querySelectorAll('.content-parent'));
    let actionDropdown = Array.from(document.querySelectorAll('.action-dropdown'));
    let imageViews = Array.from(document.querySelectorAll('.image-views'));
    let otherSvg = Array.from(document.querySelectorAll('.other-svg'));
    let mediaStash = Array.from(document.querySelectorAll('.media-stash'));
    let bookmarkBox = Array.from(document.querySelectorAll('.bookmark-box'));
    let bookmarkFolder = Array.from(document.querySelectorAll('.bookmark-folder'));
    let bookmarkOption = Array.from(document.querySelectorAll('.bookmark-option'));
    let unmarked = Array.from(document.querySelectorAll('.unmarked'));
    let modified = Array.from(document.querySelectorAll('.modified'));
    let modifiedTime = Array.from(document.querySelectorAll('.modified-time'));

    let listUp = document.querySelector('.list-up');
    let gridDown = document.querySelector('.grid-down');

    let breadcrumbBox = document.querySelector('.breadcrumb-box');
    let parentTable = document.querySelector('.parent-table');
    
    
    toggleView.addEventListener('change', function() {
        listUp.classList.toggle('hidden');
        gridDown.classList.toggle('hidden');
        if (container) {
            let folderDivs = divs.filter(div => div.getAttribute('type') === 'folder');
            let otherDivs = divs.filter(div => div.getAttribute('type') !== 'folder');
    

            if (toggleView.checked) {
                // Grid view
                container.classList.remove('grid-cols-1', 'bg-white', 'dark:bg-color-3A');
                container.classList.add('grid-cols-1', 'xs:grid-cols-2', 'lg:grid-cols-3', '2xl:grid-cols-4', '8xl:grid-cols-6', 'gap-4', 'contain-head');

                fileName.forEach(div => {div.classList.remove('w-[145px]', 'min-[990px]:w-[300px]', '5xl:w-[492px]', 'line-clamp-single', 'text-14', 'font-medium', 'sm:line-clamp-1');});
                fileName.forEach(div => {div.classList.add('absolute', 'bottom-4', 'line-clamp-double', 'mr-4', 'text-13', 'font-normal');});

                fileDivs.forEach(div => {div.classList.remove('w-[175px]', 'min-[990px]:w-[348px]', 'xl:w-[400px]', '5xl:w-[492px]', 'min-[1730px]:w-[615px]', 'sm:items-center', 'xl:pr-[65px]');});
                fileDivs.forEach(div => {div.classList.add('flex-col');});

                actionDot.forEach(div => {div.classList.remove('border', 'p-[7px]', 'border-color-89', 'rounded-lg', 'dark:bg-color-47');});
                actionDot.forEach(div => {div.classList.add('absolute', 'top-0', '-left-2');});

                modifiedTime.forEach(div => {div.classList.remove('block', 'sm:hidden');});
                modifiedTime.forEach(div => {div.classList.add('hidden');});

                contentDiv.forEach(div => {div.classList.remove('sm:items-center');});
                contentDiv.forEach(div => {div.classList.add('justify-between');});

                fileSVG.forEach(div => {div.classList.remove('text-[#FF774B]');});
                fileSVG.forEach(div => {div.classList.add('w-7', 'h-7', 'text-[#FCCA19]');});

                actionDropdown.forEach(div => {div.classList.remove('ltr:right-9', 'rtl:left-9', '-top-2');});
                actionDropdown.forEach(div => {div.classList.add('ltr:right-0', 'rtl:left-0');});

                imageViews.forEach(div => {div.classList.remove('hidden');});
                otherSvg.forEach(div => {div.classList.add('hidden');});
                DriveTdHead.classList.add('drive-show');

                breadcrumbBox.classList.add('border-b', 'border-b-color-DF', 'dark:border-b-color-47');
                parentTable.classList.add('mt-5');
                modified.forEach(div => {div.classList.remove('sm:inline-flex');});

                hideData.forEach(div => div.classList.add('hidden'));
                mediaStash.forEach(div => div.classList.remove('xl:inline-flex'));
                bookmarkBox.forEach(div => div.classList.add('absolute', 'top-[63px]', 'left-[14px]'));
                bookmarkFolder.forEach(div => div.classList.add('absolute', 'top-[54px]', 'left-[19px]'));
                bookmarkOption.forEach(div => div.classList.remove('hidden'));
                unmarked.forEach(div => div.classList.add('hidden'));

                divs.forEach(div => div.classList.remove('list-view', 'sm:px-6', 'sm:py-6', 'py-[15px]', 'px-4', 'sm:pt-6', 'sm:pb-[23px]'));
                divs.forEach(div => div.classList.add('p-4', 'rounded-xl', 'h-[132px]', 'dark:border-b-color-3A', 'grid-view'));

                folderDivs.forEach(div => container.appendChild(div));
                otherDivs.forEach(div => othersDivContainer.appendChild(div));
                if (otherDivs.length > 0) {
                    othersDivContainer.insertAdjacentHTML('beforeend', '<p class="absolute -top-8 text-15 font-medium text-color-14 dark:text-white files-tittle">Files</p>');
                }

                othersDivContainer.classList.remove('hidden');
                othersDivContainer.classList.add('grid');
            } else {
                // List view
                container.classList.remove('grid-cols-6', 'gap-4', 'grid-cols-1', 'xs:grid-cols-2', 'lg:grid-cols-3', '2xl:grid-cols-4', '8xl:grid-cols-6',);
                container.classList.add('bg-white', 'grid-cols-1', 'dark:bg-color-3A');

                fileName.forEach(div => {div.classList.add('w-[145px]', 'min-[990px]:w-[300px]', '5xl:w-[492px]', 'line-clamp-single', 'text-14', 'font-medium', 'sm:line-clamp-1');});
                fileName.forEach(div => {div.classList.remove('absolute', 'bottom-4', 'line-clamp-double','mr-4', 'text-13', 'font-normal');});

                fileDivs.forEach(div => {div.classList.add('w-[175px]', 'min-[990px]:w-[348px]', 'xl:w-[400px]', '5xl:w-[492px]', 'min-[1730px]:w-[615px]', 'sm:items-center', 'xl:pr-[65px]');});
                fileDivs.forEach(div => {div.classList.remove('flex-col');});

                actionDot.forEach(div => {div.classList.add('border', 'p-[7px]', 'border-color-89', 'rounded-lg', 'dark:bg-color-47');});
                actionDot.forEach(div => {div.classList.remove('absolute', 'top-0', '-left-2');});

                modifiedTime.forEach(div => {div.classList.add('block', 'sm:hidden');});
                modifiedTime.forEach(div => {div.classList.remove('hidden');});

                breadcrumbBox.classList.remove('border-b', 'border-b-color-DF', 'dark:border-b-color-47');
                parentTable.classList.remove('mt-5');
                modified.forEach(div => {div.classList.add('hidden', 'sm:inline-flex');});

                contentDiv.forEach(div => {div.classList.add('sm:items-center');});
                contentDiv.forEach(div => {div.classList.remove('justify-between');});

                fileSVG.forEach(div => {div.classList.add('text-[#FF774B]');});
                fileSVG.forEach(div => {div.classList.remove('w-7', 'h-7', 'text-[#FCCA19]');});

                actionDropdown.forEach(div => {div.classList.add('ltr:right-9', 'rtl:left-9', '-top-2');});
                actionDropdown.forEach(div => {div.classList.remove('ltr:right-0', 'rtl:left-0');});

                imageViews.forEach(div => {div.classList.add('hidden');});
                otherSvg.forEach(div => {div.classList.remove('hidden');});
                DriveTdHead.classList.remove('drive-show');

                hideData.forEach(div => div.classList.remove('hidden'));
                mediaStash.forEach(div => div.classList.add('xl:inline-flex', 'hidden'));
                bookmarkBox.forEach(div => div.classList.remove('absolute', 'top-[63px]', 'left-[14px]'));
                bookmarkFolder.forEach(div => div.classList.remove('absolute', 'top-[54px]', 'left-[19px]'));
                bookmarkOption.forEach(div => div.classList.add('hidden'));

                unmarked.forEach(div => div.classList.remove('hidden'));

                divs.forEach(div => div.classList.add('list-view', 'sm:px-6', 'sm:py-6', 'py-[15px]', 'px-4', 'sm:pt-6', 'sm:pb-[23px]'));
                divs.forEach(div => div.classList.remove('p-4', 'h-[132px]', 'dark:border-b-color-3A', 'rounded-xl', 'grid-view'));

                divs.forEach(div => container.appendChild(div));
                othersDivContainer.innerHTML = '';
                othersDivContainer.classList.remove('grid');
                othersDivContainer.classList.add('hidden');
            }
        }
    });

    function gridAndListView() {
        toggleView = document.querySelector('.toggleView');
        container = document.querySelector('.container-box');
        fileName =  Array.from(document.querySelectorAll('.file-name p'));
        fileDivs =  Array.from(document.querySelectorAll('.file-name'));
        fileSVG =  Array.from(document.querySelectorAll('.file-name svg'));
        othersDivContainer = document.querySelector('.others-div');
        DriveTdHead = document.querySelector('.drive-table-head');
        divs = Array.from(document.querySelectorAll('.view-mode'));
        hideData = Array.from(document.querySelectorAll('.check-data'));
        actionDot = Array.from(document.querySelectorAll('.action-dot'));
        contentDiv = Array.from(document.querySelectorAll('.content-parent'));
        actionDropdown = Array.from(document.querySelectorAll('.action-dropdown'));
        imageViews = Array.from(document.querySelectorAll('.image-views'));
        otherSvg = Array.from(document.querySelectorAll('.other-svg'));
        mediaStash = Array.from(document.querySelectorAll('.media-stash'));
        bookmarkBox = Array.from(document.querySelectorAll('.bookmark-box'));
        bookmarkFolder = Array.from(document.querySelectorAll('.bookmark-folder'));
        bookmarkOption = Array.from(document.querySelectorAll('.bookmark-option'));
        unmarked = Array.from(document.querySelectorAll('.unmarked'));
        modified = Array.from(document.querySelectorAll('.modified'));
        modifiedTime = Array.from(document.querySelectorAll('.modified-time'));

        listUp = document.querySelector('.list-up');
        gridDown = document.querySelector('.grid-down');

        breadcrumbBox = document.querySelector('.breadcrumb-box');
        parentTable = document.querySelector('.parent-table');
    }

