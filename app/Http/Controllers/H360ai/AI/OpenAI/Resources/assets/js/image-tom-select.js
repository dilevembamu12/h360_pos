"use strict";

$('.selectg').each(function() {
    var tomSelect = new TomSelect(this, {
        onFocus: function() {
            firstValueOfDropedown = tomSelect.getValue(0);
        },
        onChange: function(value) {
            if (value.length > 0) {
            firstValueOfDropedown = value;
            }

            if (value === '') {
                tomSelect.setValue(firstValueOfDropedown);
            }
        }
    });
})