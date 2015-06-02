/**** VARIABLES ****/
var myScroll;

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
function isVisible(elt){
    var botView = myScroll + $(window).height();
    var topElt = elt.offset().top;
    var botElt = topElt + elt.height();
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

/**** Mélanger aléatoire un tableau ****/
function shuffle(array) {
  var random = array.map(Math.random);
  array.sort(function(a, b){
    return random[a] - random[b];
  });
}

/**** Validation email ****/
function isValidEmail(email) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(email);
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

/**** Slider avec fin et puces ****/
function setSliderHeight(sliders){

    function setHeight(context){
        var li = context.find('ul').eq(0).find('li'), liLength = li.length, i = 0, heightMaxLi = 0;

        for(i; i < liLength; i++){
            if (li.eq(i).height() > heightMaxLi){
                heightMaxLi = li.eq(i).height();
            }
        }

        context.find('ul').eq(0).css('height', heightMaxLi+"px");
    }

    var c = 0, carouselLength = sliders.length;
    for(c; c<carouselLength; c++){
        setHeight(sliders.eq(c));
    }
}

function setCarouselDots(carousel, slides, slideWidth, updateWidth){
    
    function letSlide(next){
        
        function goToSlide(numSlide){
            var y = 0, x = 0;

            for(y; y<numSlide; y++){
                slides.eq(y).stop().animate({'left': -(numSlide-y)*slideWidth}, 500, 'easeOutExpo');
            }
            slides.removeClass('on').eq(numSlide).addClass('on').stop().animate({'left': 0}, 500, 'easeOutExpo');
            for(numSlide; numSlide<nbSlides; numSlide++){
                slides.eq(numSlide).stop().animate({'left': x*slideWidth}, 500, 'easeOutExpo');
                x++;
            }

            carousel.parents('section').find('.dots').find('li').eq(carousel.find('.on').index()).find('button').addClass('actif').parents('li').siblings().find('button').removeClass('actif');
                
            if(strong.length){ 
                strong.removeClass('on').eq(carousel.find('.on').index()).addClass('on'); 
            }
        }

        var activeSlide = carousel.find('.on');

        if(next === true){
            if(activeSlide.next().length) next = activeSlide.next().index();
        }else if(next === false){
            if(activeSlide.prev().length) next = activeSlide.prev().index();
        }

        btnNext.removeClass('none');
        btnPrev.removeClass('none');
        if(next === 0) btnPrev.addClass('none');
        if(next+1 === nbSlides) btnNext.addClass('none');

        goToSlide(next);

    }

    var nbSlides = slides.length, i = 0, p = 0, posSlide = 0, strong = carousel.parents('section').find('.keyword').find('strong'), btnPrev, btnNext;

    if(nbSlides > 1){
        if(!updateWidth){
            carousel.prepend('<button id="prev" class="navSlider icon-left none"></button>').append('<button id="next" class="navSlider icon-right"></button>');
            carousel.parents('.wrapper-ecran').length ? carousel.parents('.wrapper-ecran').after('<ul class="dots"></ul>') : carousel.append('<ul class="dots"></ul>');

            for(i; i<nbSlides; i++){
                carousel.parents('section').find('.dots').append('<li><button>&bull;</button></li>');
            }

            btnPrev = carousel.find('#prev');
            btnNext = carousel.find('#next');

            btnNext.on('click', function(){ letSlide(true); });
            btnPrev.on('click', function(){ letSlide(false); });

            if(isMobile.any){
                carousel.on('swipeleft', function(){ letSlide(true); });
                carousel.on('swiperight', function(){ letSlide(false); });
            }

            carousel.parents('section').find('.dots').find('button').on('click', function(){ letSlide($(this).parents('li').index()); });
        }
        
        slides.css({'position': 'absolute', 'top': 0}).removeClass('on').eq(0).addClass('on');

        for(p; p<nbSlides; p++){
            slides.eq(p).css('left', posSlide);
            posSlide += slideWidth;
        }

        carousel.parents('section').find('.dots').find('button').removeClass('actif').parents('li').siblings().eq(0).find('button').addClass('actif');

        if(strong.length){  strong.eq(0).addClass('on'); }
    }
}

/**** INIT ****/
$(function(){

    // Carousel with dots //
    if($('.carouselDots').length){
        setSliderHeight($('.carouselDots'));
        var c = 0, carousels = $('.carouselDots'), nbCarousels = carousels.length;
        for(c; c<nbCarousels; c++){
            setCarouselDots(carousels.eq(c), carousels.eq(c).find('li'), carousels.eq(c).find('li').eq(0).width(), false);
        }
    }

    $(window).resize(function(){
        // Carousel with dots //
        if($('.carouselDots').length){
            setSliderHeight($('.carouselDots'));
            var c = 0, carousels = $('.carouselDots'), nbCarousels = carousels.length;
            for(c; c<nbCarousels; c++){
                setCarouselDots(carousels.eq(c), carousels.eq(c).find('ul').eq(0).find('li'), carousels.eq(c).find('li').eq(0).width(), true);
            }
        }
    }

    /*$(document).scroll(function() {
        myScroll = $(this).scrollTop();
    });*/

});