function initiateCarousels(){
  $('.carousel-container').each(function(){
    hideScroll($(this));
    if (!!$(this).attr('autoscroll'))
      autoscrollSlides($(this));
  });

  $('.carousel-container').on('click', '.control-container', function(){
    if (!$(this).parents('.carousel-container').find('.fig-container').hasClass('sliding')){
      scrollSlide($(this).parents('.carousel-container'), $(this));
    }
  });
}

function autoscrollSlides(carousel){
  // Scrolls slides over whatever the autoscroll is
  var interval = parseInt(carousel.attr('autoscroll'));

  var carID = setInterval(function(){
    scrollSlide(carousel, carousel.find('.control-right'));
  }, interval);

  // Pauses and resumes autoscrolling if mouse hovers over the carousel
  if (!carousel.hasClass('no-pause')){
    carousel.on('mouseenter', function(){
      clearInterval(carID);
    });
    
    // Calls scrollSlide instead of control.click() because of mobile menu
    carousel.on('mouseleave', function(){
      carID = setInterval(function(){
        scrollSlide(carousel, carousel.find('.control-right'));
      }, interval);
    });
  }
}

function hideScroll(carousel){
  var currentSlide = carousel.find('.fig-container.target');

  // Hides controls if carousel is circular but has only one image
  if (carousel.hasClass('circular')){
    if (currentSlide.prev('.fig-container').length == 0 && currentSlide.next('.fig-container').length == 0)
      carousel.find('.control-container').hide();
  }

  // Hides the irrelevant controls if carousel isn't circular at first and last image
  // Shows them again when moving to images in between
  else{
    if (currentSlide.prev('.fig-container').length == 0)
      carousel.find('.control-left').hide();
    else
      carousel.find('.control-left').show();
  
    if (currentSlide.next('.fig-container').length == 0)
      carousel.find('.control-right').hide();
    else
      carousel.find('.control-right').show();
  }
}

function scrollSlide(carousel, control){
  var currentSlide = carousel.find('.fig-container.target');
  var dotRight = control.hasClass('dot') && control.attr('data-index') > currentSlide.attr('data-index');
  var dotLeft = control.hasClass('dot') && control.attr('data-index') < currentSlide.attr('data-index');
  var dataIndex = -1;
  var width = -1;

  // Set speed at which slides move when scrolling
  var slideSpeed = 700;
  if (!!carousel.attr('transition-speed'))
    slideSpeed = parseInt(carousel.attr('transition-speed'));

  // Set transition type
  var transition = "scroll";
  if (!!carousel.attr('transition-type'))
    transition = carousel.attr('transition-type');

  // Fix width of inner-container of carousel while scrolling; will re-adjust later
  if (carousel.hasClass('popup')){
    width = carousel.children('.inner-container').width();
    carousel.children('.inner-container').width(width);
  }

  // Scroll in whichever direction according to which control was clicked
  if (control.hasClass('control-left') || dotLeft){
    if (dotLeft)
      dataIndex = control.attr('data-index');
    switch(transition){
      default:
        scrollLeft(carousel, currentSlide, dataIndex, slideSpeed);
    }
  }
  else if (control.hasClass('control-right') || dotRight){
    if (dotRight)
      dataIndex = control.attr('data-index');
    switch(transition){
      default:
        scrollRight(carousel, currentSlide, dataIndex, slideSpeed);
    }
  }
  else if (control.hasClass('dot') && control.attr('data-index')==currentSlide.attr('data-index')){
    $('.control-container.dot').removeClass('active');
    control.addClass('active');
  }
}

function scrollLeft(carousel, currentSlide, dataIndex, slideSpeed){
  var prevSlide;
  var prevWidth, prevRight, moveRight;
  var containWidth = carousel.children('.inner-container').outerWidth();

  // For circular carousels, scrolls left from the first slide to the last
  if (currentSlide.prev('.fig-container').length == 0){
    prevSlide = carousel.find('.fig-container.last');
    prevSlide.addClass('active');

    $('.fig-container').removeClass('target');
    prevSlide.addClass('target');

    adjustImgHeight(prevSlide);

    prevWidth = prevSlide.width();
    prevRight = containWidth+prevWidth;
    prevSlide.css('right', prevRight+'px');

    var index = prevSlide.attr('data-index');
    $('.control-container.dot').removeClass('active');
    $('.control-container.dot[data-index='+index+']').addClass('active');

    carousel.find('.fig-container.active').addClass('sliding');

    moveRight = Math.min(prevWidth, containWidth);

    prevSlide.animate({right: prevRight-moveRight+'px'}, slideSpeed);
    currentSlide.animate({right: -moveRight+'px'}, slideSpeed, function(){
      currentSlide.removeClass('active');
      carousel.find('.fig-container').css('right', 'auto');
      carousel.find('.fig-container').removeClass('sliding');
      if (carousel.hasClass('popup')){
        carousel.children('.inner-container').width('auto');
      }
      adjustImgHeight(prevSlide);
    });
  }
  // Otherwise, scroll left normally
  else {
    if (dataIndex > -1)
      prevSlide = carousel.find('.fig-container[data-index='+dataIndex+']');

    else
      prevSlide = currentSlide.prev('.fig-container');

    prevSlide.addClass('active');

    $('.fig-container').removeClass('target');
    prevSlide.addClass('target');

    adjustImgHeight(prevSlide);

    prevWidth = prevSlide.width();
    carousel.find('.fig-container.active').css('right', prevWidth+'px');

    var index = prevSlide.attr('data-index');
    $('.control-container.dot').removeClass('active');
    $('.control-container.dot[data-index='+index+']').addClass('active');

    carousel.find('.fig-container.active').addClass('sliding');

    moveRight = Math.min(prevWidth, containWidth);

    carousel.find('.fig-container.active').animate({right: prevWidth-moveRight+'px'}, slideSpeed, function(){
      currentSlide.removeClass('active');
      carousel.find('.fig-container').css('right', 'auto');
      carousel.find('.fig-container').removeClass('sliding');
      if (carousel.hasClass('popup')){
        carousel.children('.inner-container').width('auto');
      }
      hideScroll(carousel);
      adjustImgHeight(prevSlide);
    });
  }
}

function scrollRight(carousel, currentSlide, dataIndex, slideSpeed){
  var nextSlide;
  var currentWidth = currentSlide.width();
  var currentLeft;
  var nextWidth, nextLeft, moveLeft;
  var containWidth = carousel.children('.inner-container').outerWidth();

  // For circular carousels, scrolls right from the last slide to the first
  if (currentSlide.next('.fig-container').length == 0){
    nextSlide = carousel.find('.fig-container:first-child');
    nextSlide.addClass('active');

    $('.fig-container').removeClass('target');
    nextSlide.addClass('target');

    adjustImgHeight(nextSlide);

    nextWidth = nextSlide.width();
    nextLeft = containWidth;

    nextSlide.css('left', nextLeft+'px');
    currentSlide.css('left', -nextWidth+'px');

    var index = nextSlide.attr('data-index');
    $('.control-container.dot').removeClass('active');
    $('.control-container.dot[data-index='+index+']').addClass('active');

    carousel.find('.fig-container.active').addClass('sliding');

    moveLeft = Math.min(nextWidth, containWidth);

    nextSlide.animate({left: nextLeft-moveLeft+'px'}, slideSpeed);
    currentSlide.animate({left: -nextWidth-moveLeft+'px'}, slideSpeed, function(){
      currentSlide.removeClass('active');
      carousel.find('.fig-container').css('left', 'auto');
      carousel.find('.fig-container').removeClass('sliding');
      if (carousel.hasClass('popup')){
        carousel.children('.inner-container').width('auto');
      }
      adjustImgHeight(nextSlide);
    });
  }
  // Otherwise, scroll right normally
  else{
    if (dataIndex > -1)
      nextSlide = carousel.find('.fig-container[data-index='+dataIndex+']');
    else
      nextSlide = currentSlide.next('.fig-container');
    
    nextSlide.addClass('active');

    carousel.find('.fig-container').removeClass('target');
    nextSlide.addClass('target');

    adjustImgHeight(nextSlide);

    nextWidth = nextSlide.width();

    var index = nextSlide.attr('data-index');
    $('.control-container.dot').removeClass('active');
    $('.control-container.dot[data-index='+index+']').addClass('active');

    carousel.find('.fig-container.active').addClass('sliding');

    moveLeft = Math.min(nextWidth, containWidth);

    carousel.find('.fig-container.active').animate({left: -moveLeft+'px'}, slideSpeed, function(){
      currentSlide.removeClass('active');
      carousel.find('.fig-container').css('left', 'auto');
      carousel.find('.fig-container').removeClass('sliding');
      if (carousel.hasClass('popup')){
        carousel.children('.inner-container').width('auto');
      }
      hideScroll(carousel);
      adjustImgHeight(nextSlide);
    });
  }
}

// popup-carousel-specific functions 

function showContainer(carousel, dataIndex){
  carousel.find('.fig-container').removeClass('active target');
  carousel.find('.fig-container[data-index='+dataIndex+']').addClass('active target');
  carousel.children('.inner-container').width('auto');

  var wide = $(window).width();
  var high = $(window).height();
  var ySpacing, sqSide, left, top;
  var navbar;

  if($('#page-header .menu-icon').css('display')!='none'){
    carousel.parent('.fixed-wrap').removeClass('fixed');
    carousel.addClass('absolute');
    navbar =  $('#page-header').outerHeight();
  }
  else{
    carousel.removeClass('absolute');
    carousel.parent('.fixed-wrap').addClass('fixed');
    navbar = 0;//$('#navbar ul li a#index').outerHeight();
  }

  var availHeight = high - navbar;

  carousel.addClass('active');
  
  if ($('#page-header .menu-icon').css('display')!='none'){
    ySpacing = 5;
    top = window.pageYOffset + navbar + ySpacing + 'px';

    if ($('.popup.carousel-container').css('color')!='rgb(1, 2, 3)')
      // portrait or non-mobile device
      sqSide = availHeight - 2*ySpacing + 'px';
    else
      // landscape mobile device
      sqSide = 'none';
  }
  
  else{
    sqSide = .95*availHeight + 'px';
    ySpacing = .025*availHeight;
    top = navbar + ySpacing + 'px';
  }

  carousel.css({'top': top, 'max-height': sqSide});

  /* Should make some code later to make things align bottom in mobile landscape? */

  resetImgHeight(carousel);
  adjustImgHeight(carousel.find('.fig-container.active.target'));
}

function hideContainer(){
  $('.carousel-container').removeClass('active');
}

function resetImgHeight(carousel){
  carousel.css('left', '');
  carousel.children('.inner-container').height('');
  carousel.find('img').css({'max-height': ''});
  carousel.find('figcaption').css({'max-width': '100%', 'bottom': '', 'position': ''})
}

// All the fun of making sure both caption and image fit inside a tiny box
function adjustImgHeight(figContainer){
  figContainer.parents('.inner-container').height('auto');
  if ($('#page-header .menu-icon').css('display')!='none' && figContainer.parents('.carousel-container').hasClass('popup')){
    figContainer.parents('.carousel-container').css('left', 'auto');
    var width = figContainer.parents('.carousel-container').width();
    var left = .5*($(window).width() - width);
    figContainer.parents('.carousel-container').css('left', left+'px');
  }
  // Do NOT adjust if there is no caption, or if the carousel isn't a popup, or if it's mobile landscape
  if (!!figContainer.find('figcaption').html() && figContainer.parents('.carousel-container').hasClass('popup') && $('.popup.carousel-container').css('color')!='rgb(1, 2, 3)'){
    function reAdjustImg(figContainer){
      var maxHeight = figContainer.parents('.carousel-container').css('max-height');
      var maxHeightString = maxHeight+' - 37px - 51px';

      var imgWidth = figContainer.find('img').outerWidth();

      figContainer.find('figcaption').css({'position': 'relative', 'max-width': imgWidth+'px'});

      var capHeight = figContainer.find('figcaption').outerHeight();

      figContainer.find('img').css('max-height', 'calc('+maxHeightString+' - '+capHeight+'px)');

      if (figContainer.find('figcaption').width() > (figContainer.find('img').width())){
        reAdjustImg(figContainer);
      }
      else{
        var finalHeight = figContainer.parents('.inner-container').height();
        figContainer.parents('.inner-container').height(finalHeight);

        if ($('#page-header .menu-icon').css('display')!='none'){
          var finalWidth = figContainer.parents('.carousel-container').width();
          var finalLeft = .5*($(window).width() - finalWidth);
          figContainer.parents('.carousel-container').css('left', finalLeft+'px');
        }

        figContainer.find('figcaption').css({'position': 'absolute', 'bottom': '-'+capHeight+'px'});
      }
    }
    reAdjustImg(figContainer);
  }
}

// popup carousel listeners
// Readjusting all the things so we get maximum image size and appropriate spacing
$(window).on('resize', function(){
  if ($('.popup.carousel-container').hasClass('active')){
    var index = $('.popup.carousel-container.active .fig-container.active.target').attr('data-index');
    var target = $('.popup.carousel-container.active');
    target.addClass('target');
    hideContainer();
    showContainer(target, index);
  }
});

// END popup carousel-specific functions

$(document).on('click', '.gallery figure img', function(){
  // You don't HAVE to close the popup carousel if you're on mobile if you see another image you like
  if (!$('.popup.carousel-container').hasClass('active') || $('#page-header .menu-icon').css('display')!='none'){
    hideContainer();
    var index = $(this).parents('.fig-container').attr('data-index');
    var collection = $(this).parents('.gallery').attr('collection');
    showContainer($('.popup.carousel-container[id='+collection+']'), index);
    $('.popup.carousel-container.active .dot-controls .dot').removeClass('active');
    $('.popup.carousel-container.active .dot-controls .dot[data-index='+index+']').addClass('active');
  }
});

$(document).on('click', '.popup.carousel-container .close-window-container', function(){
  hideContainer($(this).parent('.popup.carousel-container'));
});

// Scroll through popup carousels with arrow keys
$(document).on('keydown', function(e){
  if ($('.popup.carousel-container').hasClass('active')){
    if (e.which == 37){
      $('.popup.carousel-container.active .control-left').click();
    }
    else if(e.which == 39){
      $('.popup.carousel-container.active .control-right').click();
    }
  }
});