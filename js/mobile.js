$(document).ready(function() {
    // Кэширование селекторов для оптимизации производительности
    const $title = $('.title');
    const $leaf1 = $('.leaf1');
    const $leaf2 = $('.leaf2');
    const $bush2 = $('.bush2');
    const $mount1 = $('.mount1');
    const $mount2 = $('.mount2');
    const $mobile = $('.mobile'); // Селектор для мобильной панели

    // Обработчик прокрутки страницы
    $(window).on('scroll', function() {
        let value = $(this).scrollTop();

        $title.css('marginTop', value * 1.1 + 'px');

        $leaf1.css('marginLeft', -value + 'px');
        $leaf2.css('marginLeft', value + 'px');

        $bush2.css('marginBottom', -value + 'px');

        $mount1.css('marginBottom', -value * 1.1 + 'px');
        $mount2.css('marginBottom', -value * 1.2 + 'px');
    });

    // Функции для открытия и закрытия мобильной панели
    window.showMobile = function() {
        $mobile.css('display', 'flex');
    };

    window.hideMobile = function() {
        $mobile.css('display', 'none');
    };
    // Обработчик клика для ссылок на страницы
    $('#home, #korzina, #update_account, #authorize, #logout').on('click', function() {
        // Скрываем мобильную панель
        $mobile.css('display', 'none');
    });
})
