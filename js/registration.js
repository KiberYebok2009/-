$(document).ready(function(){
    const $containerr = $('#containerr');
            // Обработчик клика для переключения на раздел регистрации
            $(document).on('click', '#registerBtn', function() {
                $containerr.addClass("active");
            });
            // Обработчик клика для переключения обратно на раздел входа
            $(document).on('click', '#loginBtn', function() {
                $containerr.removeClass("active");
            });
    
});