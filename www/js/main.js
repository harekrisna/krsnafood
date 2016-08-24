$(function(){
		var imgOrigHeight = $('header img').width();
		var imgOrigMargin = parseInt($('header img').css("margin-top"));
		var headerOrigHeight = $('header').height();
		var headerOrigShadow = "20";
		var leftSideOrigPadding = parseInt($('header ul#left_side').css("padding-top"));
		var rightSideOrigPadding = parseInt($('header ul#right_side').css("padding-top"));
		var headerOrigBackgroundOpacity = "1";
		var slim = false; 
		
		if(full_parallax == true)
			var container = 'main';
		else
			var container = window;

		$(container).on('scroll load',function () {
			var scrollPosY = $(container).scrollTop();
			
			if(scrollPosY < 60) {
				slim = false;
			}
			
			if(slim == false) {
				var header = $('header');
				var image = $('header img');
				var ul_left = $('header ul#left_side');
				var ul_right = $('header ul#right_side');
				if(scrollPosY < 60 && scrollPosY >= 0) {
					header.height(headerOrigHeight - (scrollPosY));
					header.css("box-shadow", "inset 0 -20px " + (headerOrigShadow - (scrollPosY/4)) + "px -20px rgba(0,0,0,0.5)");
					header.css("background-color", "rgba(255,255,255," + (headerOrigBackgroundOpacity - (scrollPosY/1000)) + ")");
					image.width(imgOrigHeight - (scrollPosY * 1.4));
					image.css("margin-top", imgOrigMargin - (scrollPosY/3.5)+"px");
					ul_left.css("padding-top", leftSideOrigPadding - (scrollPosY/1.3)+"px");
					ul_right.css("padding-top", rightSideOrigPadding - (scrollPosY/1.6)+"px");
				}
				else if(scrollPosY < 0) {
					header.height(headerOrigHeight);
					header.css("box-shadow", "inset 0 -20px " + headerOrigShadow + "px -20px rgba(0,0,0,0.5)");
					image.width(imgOrigHeight);
					image.css("margin-top", imgOrigMargin + "px");
					ul_left.css("padding-top", leftSideOrigPadding + "px");
					ul_right.css("padding-top", rightSideOrigPadding + "px");
				}
				else {
					header.height(51);
					header.css("box-shadow", "inset 0 -20px 5.25px -20px rgba(0,0,0,0.5)");
					image.width(117.4);
					header.css("background-color", "rgba(255,255,255,0.94)");
					image.css("margin-top", "-16.85px");
					ul_left.css("padding-top", "14.7px");	
					ul_right.css("padding-top", "22.7px");	
					slim = true;
				}
			}
		});	
	
	
	function smoothScroll(anchor) {
	    var target = anchor.hash;
	    var $target = $(target);

	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 1500, 'swing', function () {
	        window.location.hash = target;
	    });
	}
	
	//smooth anchor slider	
	$('a[href="#menu_anchor"]').on('click',function (e) {
	    e.preventDefault();
	    smoothScroll(this);
	});
	
	
	$('a.order').on('click',function (e) {
	    e.preventDefault();
	    $('section#menu').toggleClass('opa');
	    $('section#order').toggleClass('opa');
	    smoothScroll(this);
	});
		
	// menu/order form fadeOut/fadeIn
	$('div.order div').click(function() {
	    $('section#menu').toggleClass('opa');
	    $('section#order').toggleClass('opa');
	});
	
	$('div#return').click(function() {
	    $('section#menu').toggleClass('opa');
	    $('section#order').toggleClass('opa');
	});
	
	// display flash message smoothly
	$('.flash').hide().delay(500).fadeIn(1000).delay(3200).fadeOut(800);
    
    $('.flash').click(function() {
      $(this).fadeOut(800);
    });
});
