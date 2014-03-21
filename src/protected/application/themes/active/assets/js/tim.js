var hl;
(function($){
    $(document).ready(function(){
        // posição do header
        var lastScrollTop = 0;

        var $mainHeader = $('#main-header');
        var headerHeight = $mainHeader.outerHeight(true);

        var header_animation_status = 0;

        if($('#editable-entity').length){
            $('#main-section').css('margin-top', headerHeight + $('#editable-entity').outerHeight(true));
        }


        // ajusta o tamanho do mapa internamente no leaflet

        function adjustHeader(){
            // @TODO: pra otimizar a performance só calcular e executar a animação se esta ainda não terminou para o sentido do scroll,
            // exemplo: se está scrollando pra baixo e o header já está oculto não precisa calcular e mover ele
            var $mapa, $editableEntity, $busca, $mapa, buscaTop, headerDosResultadosHeight,
                scrollTop = $(this).scrollTop(),
                diffScrollTop = (lastScrollTop - scrollTop),
                newHeaderTop = parseInt($mainHeader.css('top')) + diffScrollTop;

            if($('#busca').length){
                $busca = $('#busca');
                $mapa = $('#mapa');
                if(!$busca.parent().is($mainHeader)){
                    $mainHeader.append($('<div class="clearfix busca-main-header" >').append($mainHeader.find('>*')));
                    $busca.appendTo($mainHeader);
                }

                headerDosResultadosHeight = $("#header-dos-resultados").outerHeight(true);

                headerHeight = $mainHeader.outerHeight() - headerDosResultadosHeight;

                buscaTop = $mainHeader.outerHeight(true);

                if(buscaTop < 0) buscaTop = 0;
//                $busca.css({position: 'fixed', top: buscaTop, zIndex: 123});


                //$('#lista').css('margin-top', headerHeight + headerDosResultadosHeight);

                if($mapa.is(':visible')){
                    $busca.addClass('sombra');

                    $mapa.css('top', newHeaderTop + headerHeight + headerDosResultadosHeight);

                    if($mapa.data('oldHeight') != $mapa.height() && window.leaflet)
                        window.leaflet.map.invalidateSize();

                    //if($('#infobox'))
                     //   $('#infobox').height($('#mapa').height()-52)

                    $mapa.data('oldHeight', $mapa.height());
                }else if(scrollTop == 0 && !$mapa.is(':visible'))
                    $busca.removeClass('sombra');
                else
                    $busca.addClass('sombra');

                $('body').css('min-height', $(window).height() + headerHeight - headerDosResultadosHeight - 24);

            }

            if(newHeaderTop <= -headerHeight){
                newHeaderTop = -headerHeight;

            }else if(newHeaderTop >= 0){
                newHeaderTop = 0;

            }else if(newHeaderTop < -$(window).scrollTop()){
                newHeaderTop = -$(window).scrollTop();
            }
            if(parseInt($mainHeader.css('top')) <= -headerHeight +3)
                $mainHeader.removeClass('sombra');
            else
                $mainHeader.addClass('sombra');

            $mainHeader.css('top', newHeaderTop);



            if($('#editable-entity').length){
                $editableEntity = $('#editable-entity');

                $editableEntity.css('top', newHeaderTop + headerHeight);

                //Sugestão de colocar o logo e outras coisas na edit bar:
                // if scrolltop > x
                if($editableEntity.position().top == 0) {
                    $mainHeader.removeClass('sombra');
                    $editableEntity.addClass('sombra');
                    $('#logo-spcultura-peq').stop().show('fast');
                }else{
                    $editableEntity.removeClass('sombra');
					$('#logo-spcultura-peq').stop().hide('fast');
                }

            }

            lastScrollTop = scrollTop;
        }

        // animações do scroll
        $(window).scroll(adjustHeader).resize(adjustHeader);
        adjustHeader();

		$('ul.abas').each(function(){
			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
			$active.parent().addClass('active');
			$content = $($active.attr('href'));

			// Hide the remaining content
			$links.not($active).each(function () {
				$($(this).attr('href')).hide();
			});

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.parent().removeClass('active');
				$content.hide();

				// Update the variables with the new link and content
				$active = $(this);
				$content = $($(this).attr('href'));

				// Make the tab active.
				$active.parent().addClass('active');
				$content.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});

		//abas da busca avançada
//		$('ul.abas-objetos .js-filtro').click(function(e){
//
//            $('ul.abas-objetos > li').removeClass('active');
//            //$('ul.abas-objetos .js-filtro').removeClass('arrow_carrot-up').addClass('arrow_carrot-down');
//            var $filterDiv = $($(this).attr('href'));
//                $('.filtro-objeto').not($filterDiv).hide();
//                $filterDiv.toggle();
//
//            if ($filterDiv.is(':visible')) {
//                $(this).parent().addClass('active');
//                //$(this).addClass('arrow_carrot-up').removeClass('arrow_carrot-down');
//
//            }
//            $(window).scroll();
//            e.preventDefault();
//
//		});


        //if($('header.header-do-objeto').length)
            // gambis para deixar a aba da pagina atual aberta por padrao
        //    $('li#aba-' + $('header.header-do-objeto').attr('id').replace('header-dos-', '') + ' .js-filtro').data('onload', true).click();

		//scroll das divs
		$('.tags div').slimScroll({
			position: 'right',
			distance: '0px',
			color: '#000',
			height: '144px'
		});
        $('.submenu-dropdown .lista-de-filtro').slimScroll({
			position: 'right',
			distance: '3px',
			color: '#000',
			height: '222px'
		});
		$('.notificacoes .submenu ul').slimScroll({
			position: 'right',
			distance: '0px',
			color: '#000',
			height: '316px'
		});
        $('.js-slimScroll').each(function(){
            $(this).slimScroll({
                position: 'right',
                distance: '0px',
                color: '#000',
                height: '192px'
            });

            $(this).css({height: 'initial', maxHeight: 192}).parents('.slimScrollDiv').css({ height: 'initial', maxHeight: 192});
        });

        var hideSharetimeout;

        //Compartilhar URLs
        $('#compartilhar a').hover(function() {
            $('form#compartilhar-url').show()
        }, function() {
            clearTimeout(hideSharetimeout);
            hideSharetimeout = setTimeout("$('form#compartilhar-url').hide();", 1000);
        });

        $('form#compartilhar-url').hover(function() {
            clearTimeout(hideSharetimeout);
        }, function() {
            hideSharetimeout = setTimeout("$('form#compartilhar-url').hide();", 1000);
        });


        //Scroll da Home ////////////////////////////////////////////////////

        $('#capa-intro div.ver-mais a').click(function (){
            $('nav#capa-nav a.down').click();
            return false;
        });

        if($('.js-page-menu-item').length){
            $items = $('.js-page-menu-item');

            // encontra o próximo item para o scroll para baixo
            var find_next_page_menu_item = function(){
                var $next_page_item = null;

                $items.each(function(){
                    if($(this).offset().top - $(window).scrollTop() > $('#main-header').outerHeight(true) + 50){
                        $next_page_item = $(this);
                        return false;
                    }
                });

                return $next_page_item;
            };

            // encontra o próximo item para o scroll para cima
            var find_prev_page_menu_item = function(){
                var $next_page_item = null;

                $items.each(function(){
                    if($(this).offset().top - $(window).scrollTop() < 0){
                        $next_page_item = $(this);
                    }else{
                        return false;
                    }
                });

                return $next_page_item;
            };

            // oculta a setinha de scroll se estiver no topo ou no final da página
            var show_hide_scrolls = function(skip_animation){
                var speed = skip_animation ? 0 : 200;
                find_prev_page_menu_item() ?
                    $('nav#capa-nav a.up').animate({opacity:1}, speed) :
                    $('nav#capa-nav a.up').animate({opacity:0}, speed);

                find_next_page_menu_item() ?
                    $('nav#capa-nav a.down').animate(speed) :
                    $('nav#capa-nav a.down').fadeOut(speed);
            };

            var scroll_timeout = null;
            $(window).scroll(function(){
                clearTimeout(scroll_timeout);
                scroll_timeout = setTimeout(show_hide_scrolls,100);
            });

            $('nav#capa-nav a.down').click(function(){
                var __this = this;
                var $next_page_item = find_next_page_menu_item();
                var scrollto;

                if($next_page_item){

                    scrollto = $next_page_item.offset().top;// - $('#main-header').outerHeight(true);


                    $('html, body').animate({scrollTop: Math.ceil(scrollto)}, 800, function(){
                        nav_mouseenter.apply(__this, [true]);
                    });
                }
                return false;
            });


            $('nav#capa-nav a.up').click(function(){
                var __this = this;
                var $next_page_item = find_prev_page_menu_item();
                var scrollto;

                if($next_page_item){
                    scrollto = $next_page_item.offset().top - $('#main-header').outerHeight(true);


                    $('html, body').animate({scrollTop: Math.ceil(scrollto)}, 800, function(){
                        nav_mouseenter.apply(__this, [true]);
                    });
                }else{
                    $('body').animate({scrollTop: 0}, 800, function(){
                        nav_mouseenter.apply(__this, [true]);
                    });
                }
                return false;
            });

            $('nav#capa-nav a').not('.up').not('.down').click(function() {
                var $target = $( $(this).attr('href') );
                scrollto = $target.offset().top - $('#main-header').outerHeight(true);
                if(scrollto > $('body').scrollTop())
                    scrollto += $('#main-header').outerHeight(true);
                $('html, body').animate({scrollTop: Math.ceil(scrollto)});
                return false;
            });

            // Copiado do Ski Brasil, essa parte aqui debaixo não sei se está sendo usada
            var nav_mouseenter = function(skip_show){

                if($("#page-menu").is(':visible'))
                    return;

                var $page_item = $(this).hasClass('up') ? find_prev_page_menu_item() : find_next_page_menu_item();
                if($page_item){
                    if(skip_show !== true)
                        $(this).find('.balao').show();

                    $(this).find('.balao').find('.balao-text').html($page_item.data('menu-label'));

                }else{
                    $(this).find('.balao').hide().find('.balao-text').html('');
                }

            };

            $('nav#capa-nav a.up, nav#capa-nav a.down').mouseenter(nav_mouseenter).mouseleave(function(){
                $(this).find('.balao').hide().find('.balao-text').html('');
            });
        }else{
            $('nav#capa-nav').hide();
        }


        ////////// Menu da Home //////////

        $('#capa-nav ul span.nav-title').each(function() {
            $(this).css('margin-left', ($(this).width() * -1) + 'px');
            //$(this).css('margin-left', '40px');
        })

        $('#capa-nav ul li a').hover(function() {
            $(this).siblings('span.nav-title').animate({marginLeft: '40px'}, 'fast');
        }, function() {
            var $slider = $(this).siblings('span.nav-title');
            $slider.animate({marginLeft: ($slider.width() * -1) + 'px'}, 'fast');
        });

	});

    $(document).ready(function(){
        hl.tip.init();
    });

    hl = {
        /**
         * modo de usar: <tag class=".hltip" title="Title: Content text" >
         */
        tip:{
            init: function(){
                $(document.body).on('mouseenter', ".hltip",function(e){
                    var tip = $(this).data('tip');
                    var $this = $(this);
                    var _left = $(this).offset().left + $this.outerWidth(true)/2 + $(document).scrollLeft();
                    var _top = $(this).offset().top ;
                    var _height = $(this).height();

                    //alterações para poder atualizar o hltip
                    if(!tip || $(this).hasClass('hltip-auto-update')){
                        var content = $(this).attr('title') ? $(this).attr('title') : $(this).attr('hltitle');

                        if(content.indexOf(':') > 0){
                            content = '<div class="hltip-title">'+(content.substr(0, content.indexOf(':')))+'</div>'+(content.substr(content.indexOf(':')+1));
                        }
                        tip = $('<div class="hltip-box"><div class="hltip-arrow-top"></div><div class="hltip-text">'+content+'</div><div class="hltip-arrow-bottom"></div></div><').hide();
                        tip.css({
                            position:'absolute',
                            zIndex: 9999
                        });
                        $(document.body).append(tip);
                        tip.css('width', tip.width());
                        $(this).data('tip',tip);
                        $(this).attr('hltitle', content);
                        $(this).attr('title','');
                        if($(this).data('hltip-classes'))
                            tip.attr('class', $(this).data('hltip-classes') + ' hltip-box');
                    }

                    _left -= tip.width()/2;

                    if(_left+tip.width() - $(document).scrollLeft() > $(window).width() - 11)
                        tip.css('left', $(window).width() - 11 - tip.width() + $(document).scrollLeft());
                    else if(_left - $(document).scrollLeft() < 6)
                        tip.css('left',$(document).scrollLeft()+6);
                    else
                        tip.css('left', _left);

                    var diff = $(this).offset().left + $this.outerWidth(true)/2 + $(document).scrollLeft() - parseInt(tip.css('left'));

                    if(diff < 1)
                        diff = 1;
                    else if (diff > parseInt(tip.outerWidth()) -11)
                        diff = parseInt(tip.outerWidth()) -11;

                    if($(window).height() + $(document).scrollTop() - 11 < _top + _height + tip.height()){
                        tip.find('.hltip-arrow-top').hide();
                        tip.find('.hltip-arrow-bottom').show();
                        tip.css('top', _top - tip.height() - 6);

                        tip.find('.hltip-arrow-bottom').css('margin-left',diff - tip.find('.hltip-arrow-bottom').outerWidth()/2);
                    }else{
                        tip.find('.hltip-arrow-top').show();
                        tip.find('.hltip-arrow-bottom').hide();
                        tip.find('.hltip-arrow-top').css('margin-left',diff - tip.find('.hltip-arrow-top').outerWidth()/2);
                        tip.css('top', _top + _height + 6);
                    }

                    if(!tip.is(':visible')){
                        tip.fadeIn('fast');
                    }
                });

                $(document.body).on('mouseleave', ".hltip", function(e){
                    $(this).data('tip').fadeOut('fast');

                });
            }

        }
    };
})(jQuery);
