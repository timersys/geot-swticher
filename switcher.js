jQuery(function ($) {
    $(document).ready(function () {
        if (geot && (/iP(od|hone)/i.test(window.navigator.userAgent) || /IEMobile/i.test(window.navigator.userAgent) || /Windows Phone/i.test(window.navigator.userAgent) || /BlackBerry/i.test(window.navigator.userAgent) || /BB10/i.test(window.navigator.userAgent) || /Android.*Mobile/i.test(window.navigator.userAgent))) {
            geot.dropdown_search = true;
        }
        const switcher_options = {
            onChange: function (city_name) {

                if (!city_name.length)
                    return;

                GeotCreateCookie('geot_switcher', city_name, 999);
                window.location.reload();

            }
        };

        if ($('.geot_switcher').length) {
            const $geot_switcher = $('.geot_switcher').selectize(switcher_options);
            if (GeotReadCookie('geot_switcher')) {
                const selectize = $geot_switcher[0].selectize;
                selectize.addItem(GeotReadCookie('geot_switcher'), true);
            }
        }
    });
    function GeotCreateCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function GeotReadCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});
