/**** VARIABLES ****/
var myScroll;


/**** Tester si un élément est visible dans la fenetre ****/
function isVisible(elt){
    var botView = myScroll + $(window).height();
    var topElt = elt.offset().top;
    var botElt = topElt + $(elt).height();
    return ((botElt <= botView) && (topElt >= myScroll));
}

/**** Récupérer un paramètre GET dans l'url ****/
jQuery.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null){
        return null;
    }else{
        return results[1] || 0;
    }
}

/**** Footer toujours en bas de page ****/
function stickyFooter(){
	var docHeight = $('html').height();
	var windowHeight = $(window).height();
	var footer = $('footer');

	if(footer.hasClass('bottom')){
		docHeight += footer.height();
		if (docHeight >= windowHeight) {
			footer.removeClass('bottom');
		}
	}
	if (docHeight < windowHeight) { 
	   footer.addClass('bottom');
	}
}


/**** INIT ****/
$(function(){

    $(document).scroll(function() {
        myScroll = $(this).scrollTop();
    });

});