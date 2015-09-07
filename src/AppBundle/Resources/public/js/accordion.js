jQuery(function()
{
    jQuery.noConflict();
});

jQuery(document).ready(function(){

    $(".collapse").on('shown.bs.collapse',function(){
        $(this).parents('.pannelHead').removeClass('panel-md5-light');
        $(this).parents('.pannelHead').addClass('panel-md5');

        $(this).closest('.pannelHead').children('.panel-heading').children('a').children('h4').children('span').removeClass('glyphicon-chevron-down');
        $(this).closest('.pannelHead').children('.panel-heading').children('a').children('h4').children('span').addClass('glyphicon-chevron-up');
    });

    $(".collapse").on('hidden.bs.collapse',function(){
        $(this).parents('.pannelHead').removeClass('panel-md5');
        $(this).parents('.pannelHead').addClass('panel-md5-light');

        $(this).closest('.pannelHead').children('.panel-heading').children('a').children('h4').children('span').removeClass('glyphicon-chevron-up');
        $(this).closest('.pannelHead').children('.panel-heading').children('a').children('h4').children('span').addClass('glyphicon-chevron-down');
    });
});
