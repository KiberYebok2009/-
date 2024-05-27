$(document).ready(function() {
    const $wrapper = $('.wrapper'); // Используйте класс .wrapper, если он есть в вашей разметке

    // Обработчик клика для переключения на раздел регистрации
    $('.register-link').on('click', function() {
        $wrapper.addClass('active');
    });

    // Обработчик клика для переключения обратно на раздел входа
    $('.login-link').on('click', function() {
        $wrapper.removeClass('active');
    });
});
