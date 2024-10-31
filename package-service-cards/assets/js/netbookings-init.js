/**
 * Created by Michael on 27/09/2017.
 */

(function($) {
    $('.nb-expand a.nb-show').click(function(e){
        e.preventDefault();
        $(this).parents('.nb-item-inner').children('.nb-content').children('.nb-description').children('.nb-long').fadeIn();
        $(this).parents('.nb-item-inner').children('.nb-content').children('.nb-description').children('.nb-ellipsis').hide();
        $(this).parents('.nb-item-inner').children('.nb-expand').children('.nb-show').hide();
        $(this).parents('.nb-item-inner').children('.nb-expand').children('.nb-hide').show();
    });

    $('.nb-expand a.nb-hide').click(function(e){
        e.preventDefault();
        $(this).parents('.nb-item-inner').children('.nb-content').children('.nb-description').children('.nb-long').hide();
        $(this).parents('.nb-item-inner').children('.nb-content').children('.nb-description').children('.nb-ellipsis').fadeIn();
        $(this).parents('.nb-item-inner').children('.nb-expand').children('.nb-hide').hide();
        $(this).parents('.nb-item-inner').children('.nb-expand').children('.nb-show').show();
    });

}(jQuery));
