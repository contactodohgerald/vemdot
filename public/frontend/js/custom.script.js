(function($) {
    "use strict";
    
	var html5elmeents = "nav|article|figure|figcaption|footer|header|section".split('|');
	var bigSliderImages = [];
	var shift = $(window).width() > 640 ? 400 : 150;
	var active = '';
	var flag = true;

	for(var i = 0; i < html5elmeents.length; i++){
		document.createElement(html5elmeents[i]);
	}


	/*-----------------------------------------------------------------------------------*/
	/*	Mobile Detect
	/*-----------------------------------------------------------------------------------*/

	var testMobile;
	var isMobile = {
	    Android: function() {
	        return navigator.userAgent.match(/Android/i);
	    },
	    BlackBerry: function() {
	        return navigator.userAgent.match(/BlackBerry/i);
	    },
	    iOS: function() {
	        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
	    },
	    Opera: function() {
	        return navigator.userAgent.match(/Opera Mini/i);
	    },
	    Windows: function() {
	        return navigator.userAgent.match(/IEMobile/i);
	    },
	    any: function() {
	        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
	    }
	};

	$(window).load(function() {		
		if(isMobile.any()){

			$('.animate, .fade, .rotate').removeClass('animate fade rotate');

		}else{
			$('.calc').each(function(){
				var val = $(this).text();
				$(this).attr('data-value', val);
				$(this).text(0);
			})
		}


		/*----------  PRELOADER  ----------*/
		setTimeout(function(){
			$('#preloader').animate({'opacity' : '0'},300,function(){
				$('#preloader').hide();
				if(!isMobile.any()){
					if(0 < $(window).scrollTop()){				
						scrolling();
					}
				}else{
					$('.animate').removeClass('animate');
					$('.menu-block, .reservation-block').css('opacity', 1);
				}	
			});
			$('.page-wrapper').animate({'opacity' : '1'},500);
		},800);
		/*----------  //PRELOADER  ----------*/


		/*----------  PARALLAX  ----------*/
		function parallaxInit() {
			testMobile = isMobile.any();
			if (testMobile == null)
			{
				$('.parallax').parallax("50%", 0.5);
			}
		}	
		parallaxInit();	
		/*----------  //PARALLAX  ----------*/

		/*----------  SMALL SLIDER  ----------*/
		$('.comment .flexslider, .events .flexslider').flexslider({slideshowSpeed: 6000});

		$('.flex-next').addClass('glyph fa-angle-right').text('');
		$('.flex-prev').addClass('glyph fa-angle-left').text('');
		/*----------  SMALL SLIDER  ----------*/

	 	/*----------  BIG SLIDER  ----------*/

		setTimeout(function(){
			$('.home .flexslider').height($(window).height()).flexslider({
				slideshowSpeed: 6000,
				after : function(slider){
					$('.home .flexslider .big, .home .flexslider .middle, .home .flexslider .dot, .home .flexslider p').css('opacity',0);
					var next = $('.flex-active-slide', slider).find('.slider-text-wrapper');
					var index = $('.flex-active-slide', slider).index();
					var nextImg = '';
					var prevImg = '';

					sliderAnimate(next);


					if(bigSliderImages.length - 1 == index){
						nextImg = bigSliderImages[0];
					}else{
						nextImg = bigSliderImages[index+1];
					}

					if(index == 0){
						prevImg = bigSliderImages[bigSliderImages.length - 1];
					}else{
						prevImg = bigSliderImages[index-1];
					}

					$('.home .flex-prev, .home .flex-next').css('opacity', 0);
					setTimeout(function(){
						$('.home .flex-prev').html('<img src="'+ prevImg +'" alt="">');
						$('.home .flex-next').html('<img src="'+ nextImg +'" alt="">');
					$('.home .flex-prev, .home .flex-next').css('opacity', 1);
					}, 300)
				}
			});
			
			sliderAnimate($('.flex-active-slide .slider-text-wrapper'));
			function sliderAnimate(next){
				if(next.hasClass('first')){
					var time = 0;

					$('.middle, .big, .dot, p', next).each(function(){
						var thiz = $(this);
						time += 200;
						setTimeout(function(){							
							thiz.addClass('fadeInDown animated').css('opacity','1');
							thiz.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
								thiz.removeClass('fadeInDown animated');
							});
						}, time);
					});
				}else if(next.hasClass('second')){						
					var time = 0;

					$('.middle, .big, .dot, p', next).each(function(){
						var thiz = $(this);
						time += 300;
						setTimeout(function(){							
							thiz.addClass('flipInX animated').css('opacity','1');
							thiz.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
								thiz.removeClass('flipInX animated');
							});
						}, time);
					});
				}else if(next.hasClass('third')){				
					var time = 0;

					$('.middle, .big, .dot, p', next).each(function(i){
						var thiz = $(this);
						time += 300;
						if(i == 0){
							setTimeout(function(){							
								thiz.addClass('rotateInDownLeft animated').css('opacity','1');
								thiz.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
									thiz.removeClass('rotateInDownLeft animated');
								});
							}, time);
						}else if(i == 1){
							setTimeout(function(){							
								thiz.addClass('fadeInLeft animated').css('opacity','1');
								thiz.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
									thiz.removeClass('fadeInLeft animated');
								});
							}, time);
						}else{
							setTimeout(function(){							
								thiz.addClass('pulse animated').css('opacity','1');
								thiz.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
									thiz.removeClass('pulse animated');
								});
							}, time);								
						}
					});
				}
			}

			$('.home li > img').each(function(){
				bigSliderImages.push($(this).attr('src'))
			})
			

			$('.home .flex-next').html('<img src="'+ bigSliderImages[1] +'" alt="">');
			$('.home .flex-prev').html('<img src="'+ bigSliderImages[bigSliderImages.length - 1] +'" alt="">');

			$('.home li > img').each(function(){
				$(this).css('background-image', 'url(' + $(this).attr('src') + ')')
					   .attr('src', '../images/1x1.png')
					   .height($(window).height());
			});
		},0)
		/*----------  //BIG SLIDER  ----------*/
		
		
		/*----------  NAVIGATION ON PAGE  ----------*/
		$('#mainNavi a, .slide-page').on('click', function(){
			var id = $(this).attr('href');
			if(id == '#home'){
				$('html,body')
					.stop()
					.scrollTo(0, 500);

				return false
			}
			$('html,body')
					.stop()
					.scrollTo($(id).offset().top-60, 500);
			if(!$(this).hasClass('button')){
				$('#mainNavi a').removeClass('active');
				$(this).addClass('active');
			}
			if($($(this).parent().hasClass('main-navi animate'))){
				$(this).parent().removeClass('animate');
			}
			return false
		});

		$('#moveTop').on('click', function(){
			$('html,body')
					.stop()
					.scrollTo(0, 1000);

			return false
		});

		/*----------  //NAVIGATION ON PAGE  ----------*/
		function scrolling(){
			var scroll = $(window).scrollTop() + $(window).height();

			/*----------  ANIMATION FOR HEADER  ----------*/
			if($(window).scrollTop() < $(window).height()){
				$('#mainNavi a').removeClass('active');
				$("#mainNavi a:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR HEADER  ----------*/

			/*----------  ANIMATION FOR RESTAURANT  ----------*/
			if($('#restaurant').length && parseInt($('#restaurant').offset().top)+shift < scroll){
				if($('#restaurant').hasClass('animate')){

					$('#restaurant').removeClass('animate');

					$('#restaurant .restaurant-block').eq(0).addClass('bounceInLeft animated');
					$('#restaurant .restaurant-block').eq(0).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$('#restaurant .restaurant-block').eq(0).removeClass('bounceInLeft animated');
					});

					$('#restaurant .restaurant-block').eq(1).addClass('bounceInRight animated');
					$('#restaurant .restaurant-block').eq(1).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$('#restaurant .restaurant-block').eq(1).removeClass('bounceInRight animated');
					});
				}
				
				$('#mainNavi a').removeClass('active');
				$("a[href='#restaurant']:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR RESTAURANT  ----------*/

			/*----------  ANIMATION FOR MENU  ----------*/
			if($('#menu').length && parseInt($('#menu').offset().top)+shift < scroll){
				if($('#menu').hasClass('animate')){

					$('#menu').removeClass('animate');

					var time = -200;
					$('#menu .menu-block').each(function(){
						time += 200;
						var thiz = this;
						setTimeout(function(){
							$(thiz).addClass('fadeInLeft animated').css('opacity',1);
							$(thiz).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
								$(thiz).removeClass('fadeInLeft animated');
							});						
						}, time)
					})
				}
				
				$('#mainNavi a').removeClass('active');
				$("a[href='#menu']:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR MENU  ----------*/

			/*----------  ANIMATION FOR ACHIEVMENT  ----------*/
			if($('.achievement').length && parseInt($('.achievement').offset().top)+shift < scroll){
				if($('.achievement').hasClass('animate')){
					$('.achievement').removeClass('animate');
					/*-----------------------------------------------------------------------------------*/
					/* use the jQuery countTo plagin for animate numbers
					/*-----------------------------------------------------------------------------------*/
					$('.calc').each(function(){
						$(this).countTo({
					        speed: 3000
					    });
				    });
				}
			}
			/*----------  //ANIMATION FOR ACHIEVMENT  ----------*/	

			/*----------  ANIMATION FOR EVENTS  ----------*/
			if($('#events').length && parseInt($('#events').offset().top)+shift < scroll){
				if($('#events').hasClass('animate')){

					$('#events').removeClass('animate');
					
					$('#events .container').addClass('fadeInUp animated');
					$('#events .container').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$('#events .container').removeClass('fadeInUp animated');
					});	
				}
				
				$('#mainNavi a').removeClass('active');
				$("a[href='#events']:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR EVENTS  ----------*/

			/*----------  ANIMATION FOR RESERVATION  ----------*/
			if($('#reservation').length && parseInt($('#reservation').offset().top)+shift < scroll){
				if($('#reservation').hasClass('animate')){

					$('#reservation').removeClass('animate');

					var time = -200;
					$('#reservation .reservation-block').each(function(){
						time += 200;
						var thiz = this;
						setTimeout(function(){
							$(thiz).addClass('fadeInLeft animated').css('opacity',1);
							$(thiz).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
								$(thiz).removeClass('fadeInLeft animated');
							});						
						}, time)
					})
				}
				
				$('#mainNavi a').removeClass('active');
				$("a[href='#reservation']:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR RESERVATION  ----------*/

			/*----------  ANIMATION FOR COMMENT  ----------*/
			if($('.comment').length && parseInt($('.comment').offset().top)+shift < scroll){
				if($('.comment').hasClass('animate')){

					$('.comment').removeClass('animate');
					
					$('.comment .container').addClass('fadeInUp animated');
					$('.comment .container').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$('.comment .container').removeClass('fadeInUp animated');
					});	
				}
			}
			/*----------  //ANIMATION FOR COMMENT  ----------*/

			/*----------  ANIMATION FOR CONTACTS  ----------*/
			if($('.contacts').length && parseInt($('.contacts').offset().top)+shift < scroll){
				if($('.contacts').hasClass('animate')){

					$('.contacts').removeClass('animate');
					
					$('.contacts .container').addClass('fadeInUp animated');
					$('.contacts .container').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$('.contacts .container').removeClass('fadeInUp animated');
					});	
				}
				
				$('#mainNavi a').removeClass('active');
				$("a[href='#contacts']:eq(0)").addClass('active');
			}
			/*----------  //ANIMATION FOR CONTACTS  ----------*/
		}

		$(window).on('scroll', function(){
			if(!isMobile.any()){
				scrolling();
			}
		});


		$('.menu-block').on('mouseenter', function(){
			$('.position', this).addClass('fadeInDown animated');
			$('.position', this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
				$('.position', this).removeClass('fadeInDown animated');
			});
			$('.food-name', this).addClass('fadeInDown animated');
			$('.food-name', this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
				$('.food-name', this).removeClass('fadeInDown animated');
			});
			$('.price', this).addClass('fadeInUp animated');
			$('.price', this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
				$('.price', this).removeClass('fadeInUp animated');
			});

		}).on('mouseleave', function(){
			$('.position', this).removeClass('fadeInDown animated');
			$('.food-name', this).removeClass('fadeInDown animated');
			$('.price', this).removeClass('fadeInUp animated');			
		});

		$('.menu-block').on('click', function(){
			active = $(this);

			var img = $('img', active);
			var position = $('.position', active);
			var name = $('.food-name', active);
			var text = $('p', active);

			if(active.index() == 0){				
				$('#popup .prev').addClass('unactive');
			}else if(active.index() == $('.menu-block').length-1 || active.index() == 7 || active.index() == 11){
				$('#popup .next').addClass('unactive');

			}

			$('#popup img').attr('src', img.attr('src').replace(/food/, 'food/big'));
			$('#popup .position').html(position.html());
			$('#popup .food-name').html(name.html());
			$('#popup p').html(text.html());

			$('#popup').show();

			$('#popup').css('opacity', 1);

			setTimeout(function(){
				$('#popup article').eq(0).addClass('fadeInLeft animated').css('opacity', 1);
				$('#popup article').eq(0).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
					$('#popup article').eq(0).removeClass('fadeInLeft animated');
				});	

				$('#popup article').eq(1).addClass('fadeInRight animated').css('opacity', 1);
				$('#popup article').eq(1).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
					$('#popup article').eq(1).removeClass('fadeInRight animated');
				});	
				
			}, 500);
			setTimeout(function(){				
				$('#popup .button').addClass('fadeInDown animated').css('opacity', 1);
				$('#popup .button').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
					$('#popup .button').removeClass('fadeInDown animated');
				});	
			}, 1000);
		});
		$('.close-button').on('click', function(){
			$('#popup article, #popup .button').css('opacity', 0);
			active = '';
			setTimeout(function(){
				$('#popup').hide()
				$('#popup .next, #popup .prev').removeClass('unactive');
			}, 500);
			return false;
		});
		$('#popup .next, #popup .prev').on('click', function(){
			var activeTemp = $(this).hasClass('prev') ? active.prev() : active.next();
			if(!activeTemp.length || !flag){
				if(!activeTemp.length){
					$(this).addClass('unactive');
				}
				return false;
			}
			$('#popup .next, #popup .prev').removeClass('unactive');
			flag = false;
			var img = $('img', activeTemp);
			var position = $('.position', activeTemp);
			var name = $('.food-name', activeTemp);
			var text = $('p', activeTemp);
			var price = $('.price', activeTemp);
			$('.details-wrapper > *, #popup img').css('opacity', 0);

			setTimeout(function(){
				$('#popup img').attr('src', img.attr('src').replace(/food/, 'food/big')).css('opacity', 1);
			}, 500);

			setTimeout(function(){
				$('#popup .position').html(position.html());
				$('#popup .food-name').html(name.html());
				$('#popup p').html(text.html());
				$('#popup .price').html(price.html());

				var time = 100;
				$('.details-wrapper > *').each(function(){
					time += 200;
					var thiz = this;
					setTimeout(function(){
						$(thiz).addClass('fadeInDown animated').css('opacity',1);
						$(thiz).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
							$(thiz).removeClass('fadeInDown animated');
						});						
					}, time)
				});
				setTimeout(function(){
					flag = true;
				}, 900);
			}, 300)
			

			active = activeTemp;

			return false
		});

		$('.more-food').on('click', function(){
			if($(this).hasClass('unactive')){
				return false;
			}
			$(this).addClass('animate');

			var amount = 4;
			var width = $(window).width();

			if(width <= 480){
				var amount = 4;
			}else if(width < 768){
				var amount = 2;
			}else if(width < 990){
				var amount = 3;
			}else{
				var amount = 4;
			}

			var time = -200;
			setTimeout(function(){
				$('.more-food').removeClass('animate');
				$('.menu-block.animate').each(function(i){
					if(i >= amount){
						return false;
					}
					time += 200;
					var thiz = this;
					$(this).show();
					if(i == 0){
						$('html,body')
							.stop()
							.scrollTo($(this).offset().top, 300);
					}
					setTimeout(function(){
						$(thiz).addClass('fadeInLeft animated').removeClass('animate').css('opacity',1);
						$(thiz).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
							$(thiz).removeClass('fadeInLeft animated');
						});						
						if(!$('.menu-block.animate').length){
							$('.more-food').addClass('unactive');
						}					
					}, time);
				});
			}, 1000);

			return false;
		})
		

		/*----------  PORTFOLIO GALLERY  ----------*/
		$('.reservation-block a').magnificPopup({
			type: 'image',
			gallery: {
				enabled: true
			},
			zoom: {
				enabled: true,
				duration: 300 // don't foget to change the duration also in CSS
			}
		});
		/*----------  //PORTFOLIO GALLERY  ----------*/

		$('.images-bg').each(function(){
			$(this).css({
				'background-image': 'url(' +$('>img', this).hide().attr('src') +')'
			});
		});



		/*----------  SUBMIT FUNCTION  ----------*/
	    $('#submit').on('click', function(){
	        var flag = true;

	        
	        if(!/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/.test($('#email').val())){
	            $('#email').val('').attr('placeholder','please enter correct e-mail').addClass('error');;
	            flag = false;
	        }
	        if(flag){
	            $(this).parents('form').submit(); 	
	            $(this).addClass('success').find('span:eq(1)').html('success');         	
	        }else{
	        	$(this).addClass('error').find('span:eq(1)').html('error'); 
	        }

	        setTimeout(function(){
	        	$('#submit').removeClass('error success').find('span:eq(1)').html('book a table'); 
	        }, 3000)
	        
	        return false;
	    });

		$("#ajax-contact-form").submit(function() {
			var str = $(this).serialize();		
			var href = location.href.replace(/parallax-page\/|video-page\/|default\/|index\.html/g,'');
			$.ajax({
				type: "POST",
				url: href + "contact_form/contact_process.php",
				data: str,
				success: function(msg) {
					// Message Sent - Show the 'Thank You' message and hide the form
					if(msg == 'OK') {
						$(this).addClass('success').find('span:eq(1)').html('success'); 
					} else {
						$(this).addClass('error').find('span:eq(1)').html('error'); 
					}
				}
			});
			return false;
		});
	    /*----------  //SUBMIT FUNCTION  ----------*/


        var width = $(window).width();
			
		if(width < 768){
			var amount = 3;
		}else if(width < 990){
			var amount = 5;
		}else{
			var amount = 7;
		}

		$('.menu-block').each(function(i){
			if(i > amount){
				$(this).addClass('animate');
			}
		})

		$('.mobile-menu').on('click', function(){
			$('#mainNavi').toggleClass('animate');
		});


		/*----------  FUNCTION FOR SWITCH THEME COLOR  ----------*/
		if($('.picker-btn').length){
			$('.picker-btn').on('click', function(){
				if(parseInt($('.color-picker').css('right')) == 0){
					$('.color-picker').stop().animate({'right': -160}, 500);
				}else{
					$('.color-picker').stop().animate({'right': 0}, 500);
				}
			});
			$('.color-picker .pwrapper div.color').on('click', function(){
				$('body').removeClass('lightgreen blue green lightred red yellow turquoise pink purple');
				$('body').addClass($(this).attr('data-color'));
			});
			$('.color-picker .pwrapper div.bg').on('click', function(){
				$('body').removeClass('white black');
				$('body').addClass($(this).attr('data-color'));
				if($(this).attr('data-color') == 'black'){
	                $('.clients img').each(function(){
	                    var src = $(this).attr('src');
	                    $(this).attr('src', src.replace(/clients\//,'clients/black-'))
	                })
	            }else{
	                $('.clients img').each(function(){
	                    var src = $(this).attr('src');
	                    $(this).attr('src', src.replace(/clients\/black-/,'clients/'))
	                })
	            }
			});
		}
		/*----------  //FUNCTION FOR SWITCH THEME COLOR  ----------*/


        /*----------  MAP  ----------*/
        if($('#map').length){ 
        	var myLatLng = new google.maps.LatLng(23.73176,90.40640);
            var mapOptions = {
                zoom: 8,
                center: myLatLng,
                scrollwheel: false,
                streetViewControl : true
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
           	
            var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				icon: 'images/map/location-icon.png',
				title: '',
			});

            $('#showMap').on('click', function(){
            	if($('#map').hasClass('active')){
            		$('#map, #showMap').removeClass('active');
            	}else{
	            	$('#map, #showMap').addClass('active');
	            	setTimeout(function(){
	            		$('html,body')
							.stop()
							.scrollTo($('#map').offset().top, 300);  
					}, 300)      		
            	}
            	return false;
            });

        }
        
        /*----------  //MAP  ----------*/
	});

})(jQuery); 