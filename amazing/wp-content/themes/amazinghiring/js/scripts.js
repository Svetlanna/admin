$('.fa-bars').click(function() {
    $('header').toggleClass('open');
});
$('.wpcf7-list-item-label').on('click', function() {
    $(this).parent().find('input[type="checkbox"]').click();
});
$('.mobile-menu-btn, .bg-layout').on('click', function() {
    $('.mobile-menu-wrapper').toggleClass('open');
    $('.bg-layout').toggleClass('shown');
});
$('.menu-item-has-children').on('click', function() {
    $(this).toggleClass('inserted-open');
});

$(document).ready(function() {
    // $('.same-div').equalHeight();

    $('.popup-two').addClass("hide-block").viewportChecker({
        classToAdd: 'show-block animated slideInRight',
        offset: 100
    });
    Ya.share2('my-share', {
        content: {

            title: $('title').html(),
            description: $('meta[name=description]').attr("content"),
            image: $('.hide-img').html(),
        }
    });

    // ios parallax fix
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    jQuery(function($) {
        if (isMobile.any()) {
            document.documentElement.className = document.documentElement.className + " touch";

            $('.parallax-window').each(function(i, obj) {

                $(this).css("background-image", 'url(' + $(this).data('image-src') + ')');
                $(this).css("background-color", "#FFFFFF");
                $(this).css("background-size", "cover");
                $(this).css("background-position", "center center");


            });
        }

    });


});


$(window).load(function() {

    $(".popup-two .close").click(function() {
        $(".popup-two").slideUp("slow");
    });
    // if no cookie
    if ($.cookie('alert') != "true") {
        $(".popup-one").show();
        $(".popup-one .close").click(function() {
            $(".popup-one").slideUp("slow");
            // set the cookie for 24 hours
            var date = new Date();
            date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
            $.cookie('alert', "true", {
                expires: date
            });
        });
    }

    jQuery(function($) {
        // Ключ localStorage
        var LS_KEY = 'modal_shown';

        // Если модал еще не открыали
        if (!localStorage.getItem(LS_KEY)) {
            setTimeout(function() {
                // Открываем модал
                document.getElementById('overlay-p').style.display = 'block'

                // Сохраняем флаг в localStorage
                localStorage.setItem(LS_KEY, '1');
            }, 2000);
        }
    });

    if ($('html').hasClass('rusLang')) {
        $('.logo').attr('href', 'https://amazinghiring.ru');
        $('a[href="/faq.html"]').attr('href', 'http://amazinghiring.ru/faq.html')
    }
    if ($('html').hasClass('engLang')) {
        $('.logo').attr('href', 'https://amazinghiring.com');
        $('a[href="/faq.html"]').attr('href', 'http://amazinghiring.com/faq.html')
    }

});