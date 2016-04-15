/**** VARIABLES ****/
var myScroll = 0, scrollDir = 0, lastScrollTop = 0;

/**** Remplacement document.scroll ****/
window.requestAnimFrame = (function(){
   return  window.requestAnimationFrame       ||
           window.webkitRequestAnimationFrame ||
           window.mozRequestAnimationFrame    ||
           window.oRequestAnimationFrame      ||
           window.msRequestAnimationFrame     ||
           function(callback){
             window.setTimeout(callback, 1000/60);
           };
})();

function scrollPage(){
    myScroll = $(document).scrollTop();
    requestAnimFrame(scrollPage);
}

/**** Tester si un élément est visible dans la fenetre ****/
function isVisible(el){
    var top = el.offsetTop,
        left = el.offsetLeft,
        width = el.offsetWidth,
        height = el.offsetHeight;

    while(el.offsetParent){
        el = el.offsetParent;
        top += el.offsetTop;
        left += el.offsetLeft;
    }

    return(
        top < (window.pageYOffset + window.innerHeight) && left < (window.pageXOffset + window.innerWidth) &&
        (top + height) > window.pageYOffset && (left + width) > window.pageXOffset
    );
}

/**** Récupérer un paramètre GET dans l'url ****/
$.urlParam = function(name){
    var params = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href),
        results = params == null ? null : params[1] || 0;
    return results;
}
// var paramContent = $.urlParam(paramName);

/**** Mélanger aléatoire un tableau ****/
function shuffle(array) {
  var elementsRemaining = array.length, temp, randomIndex;
  while (elementsRemaining > 1) {
    randomIndex = Math.floor(Math.random() * elementsRemaining--);
    if (randomIndex != elementsRemaining) {
      temp = array[elementsRemaining];
      array[elementsRemaining] = array[randomIndex];
      array[randomIndex] = temp;
    }
  }
  return array;
}

/**** Validation email ****/
function isValidEmail(email) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(email);
}

/**** Validation tel ****/
function isValidTel(n){
  var pattern = new RegExp(/^\+?[^.\-][0-9\.\- ]+$/);
  return pattern.test(n);
}

/**** Detect scroll direction (-1->to top, 1->to bottom) ****/
function detectScrollDir(){
    if(myScroll > lastScrollTop){
        scrollDir = -1;
    }else if(myScroll < lastScrollTop){
        scrollDir = 1;
    }else{
        scrollDir = 0;
    }
    lastScrollTop = myScroll;
}

/**** Parallax ****/
function calcParallaxVal(finalVal, speed){
    return (finalVal + (myScroll + windowHeight - docHeight) / speed) | 0;
}

/**** Footer toujours en bas de page ****/
function stickyFooter(){
	var docHeight = $('html').height(),
        windowHeight = $(window).height(),
	    footer = $('footer');

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

/**** Tooltip ****/
function setToolTip(context){
    var links = context.find('.lien'), linksLength = links.length, i = 0, titles = [], linkOffset;

    if(links.attr('title') !== ''){

        for(i; i<linksLength; i++){
            titles[i] = links.eq(i).attr('title');
            links.eq(i).removeAttr('title').append("<span class='tooltip' class='none'>"  + titles[i] + "</span>");
        }

        links.on('mouseenter', function(){
            $(this).find('.tooltip').delay(300).removeClass('none');
        }).on('mouseleave', function(){
            setTimeout(function(){ $(this).find('.tooltip').addClass('none'); }, 300);
        });

        links.on('mousemove', function(e){
            linkOffset = $(this).offset();
            $(this).find('.tooltip').css({'top': e.pageY - linkOffset.top + 20, 'left': e.pageX - linkOffset.left - 20});
        });

    }
}

/**** INIT ****/
$(function(){

    $(window).resize(function(){
    }

    /*$(document).scroll(function(){
        myScroll = $(this).scrollTop();
    });*/

});
