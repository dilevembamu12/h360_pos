'use strict';

$(document).ready(function() {
    $('.imageStyle').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text; // Return text if no ID (initial loading)
        }

        const imageUrl = $(option.element).data('image');
        const $option = $(
            `<span>
                <img src="${imageUrl}" style="width: 40px; height: 40px; margin-right: 5px;" />
                ${option.text}
            </span>`
        );
        return $option;
    }
});
