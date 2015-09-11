(function($) {

	$.sdg = {};

	$.sdg.$postSlideToAnimation = false;
    $.sdg.$postSlideOutAnimation = false;
    $.sdg.$currentTarget = 0;

	$.sdg.initParallax = function() {

		$.sdg.$maxWidth = 1500;
		$.sdg.$isAnimating = false;
		$.sdg.setParallaxEnvironment();

		$.sdg.$firstSlide = $($('.parallax-heroes .slides').get(0));
		$.sdg.$lastSlide = $($('.parallax-heroes .slides').get(2));


		$('.parallax-heroes .bottom-out').css('height', $.sdg.$windowHeight-775+'px');

        if ($.sdg.$currentTarget == 0) $.sdg.$currentTarget = $($('.parallax-navigation a')[0]).attr('href');

		$(window).resize(function() {

			$.sdg.setParallaxEnvironment();
			$.sdg.resizeParallaxEnvironment();

			eventIntent(function() {
				// sdgParallaxUpdate();
			}, 400, 'parallaxAdjust');

		});

		$.sdg.slideToNextSlideOnWheel();

		$(window).bind('scroll', function() {
			$.sdg.observeScrollToHideOptions();
		});


		$('.parallax-navigation a').click(function() {

            if ($.sdg.$postSlideOutAnimation) {
                $.sdg.$postSlideOutAnimation();
            }

			var target = $(this).attr('href');
            $.sdg.$currentTarget = target;
			$.sdg.$isAnimating = true;
			$('html, body').animate({scrollTop: $(target).offset().top - 115}, 700, function() {
				$.sdg.$isAnimating = false;
			});

			$('.parallax-navigation a').removeClass('active');
			$(this).addClass('active');

            if ($.sdg.$postSlideToAnimation) {
                $.sdg.$postSlideToAnimation();
            }

			return false;
		});

	}

    $.sdg.setSlideResetAnimation = function(callback) {
        $.sdg.$postSlideOutAnimation = callback;
        callback();
    }

	$.sdg.setSlideAnimation = function(callback) {
		$.sdg.$postSlideToAnimation = callback; 
		callback();
	}

	$.sdg.setParallaxEnvironment = function() {

		$.sdg.$windowHeight = $(window).height();
		$.sdg.$windowWidth = $(window).width();

		if ($.sdg.$windowWidth > $.sdg.$maxWidth) $.sdg.$windowWidth = $.sdg.$maxWidth;

		$.sdg.$compensate = ($.sdg.$windowWidth-980)/2;
		if ($.sdg.$compensate < 0) $.sdg.$compensate = 0;

	}

	$.sdg.resizeParallaxEnvironment = function() {

		$('.parallax-heroes').width($.sdg.$windowWidth).css('margin-left', -$.sdg.$compensate+'px').css('margin-right', -$.sdg.$compensate+'px');
		$('.parallax-heroes .frames').not('.static').width($.sdg.$windowWidth);
		$('.parallax-heroes .slides').css('width', $.sdg.$windowWidth+'px');
		$('.parallax-heroes .bottom-out').css('height', $.sdg.$windowHeight-775+'px');

	}


	$.sdg.observeScrollToHideOptions = function() {

		var windowTop = $(window).scrollTop();
		var windowHeight = $(window).height();
		var windowBottom = windowTop + windowHeight;

		var parallaxTop = $('.parallax-heroes').scrollTop();
		var parallaxHeight = $('.parallax-heroes').height();

		if (windowBottom >= (parallaxTop+parallaxHeight)) {
			setTimeout(function() {
				$('.parallax-heroes .bottom-out').hide();
			}, 100);
		} else { 
			// down
			$('.parallax-heroes .bottom-out').show(); 
		}

		if (windowBottom >= ((parallaxTop+parallaxHeight) + 500)) {
			$('.parallax-navigation').fadeOut();
		} else { 
			$('.parallax-navigation').fadeIn();
		}

		$('.parallax-heroes .slides').each(function() {
			var slideTop = $(this).offset().top;
			var slideId = $(this).attr('id');
			var id = slideId.split('-');

			if (windowTop >= slideTop-10-115) {
				if (!$.sdg.$isAnimating) {
					$('.parallax-navigation a').removeClass('active');
					$('.parallax-navigation a.nav-'+id[1]).addClass('active');
				}
			}
		});

	}

	$.sdg.slideToNextSlideOnWheel = function() {

		var offSlides = false;
		var stickyDelta = 0.4;

		$('.parallax-heroes').bind('mousewheel', function(e, delta, deltaX, deltaY) {

			var current = $('.parallax-navigation a.active');
			var closestSlide = Math.floor(($(window).scrollTop()+115)/$.sdg.$firstSlide.height());
			if ((deltaY < stickyDelta && deltaY > -1 * stickyDelta) || $.sdg.$isAnimating) return false;

			if (deltaY > stickyDelta && closestSlide > 0) { // up 
				if (offSlides) {
					closestSlide = 3;
					offSlides = false;
				}
				$($('.parallax-navigation a')[closestSlide - 1]).trigger('click');
				return false;
			}

			console.log('closestSlide'+ closestSlide);
			if (deltaY < stickyDelta && closestSlide < 3) { // down
				if (closestSlide == 2) {
					closestSlide++;
					offSlides = true;
					$.sdg.$isAnimating = true;
					$('html, body').animate({scrollTop: $($.sdg.$lastSlide).offset().top + $.sdg.$lastSlide.outerHeight() - 115}, 700, function() {
						$.sdg.$isAnimating = false;
					});
				} else {
					console.log(closestSlide+1);
					$($('.parallax-navigation a')[closestSlide + 1]).trigger('click');
				}
				return false;
			}

			if ($.sdg.$isAnimating) {
                if ($.sdg.$postSlideToAnimation) {
                    $.sdg.$postSlideToAnimation();
                }
			}
		});

	}


	$.sdg.flicker = function(frame) {

		eventIntent(function() {
			frame.fadeOut(100).delay(500).fadeIn(150).fadeOut(150).delay(500).fadeIn(100).fadeOut(250).fadeIn(100);
		}, 800, 'flickering');

	}


})(jQuery);