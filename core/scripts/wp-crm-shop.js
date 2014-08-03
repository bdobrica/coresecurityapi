/**/
jQuery(document).ready(function(){
	var u = '/wp-content/plugins/wp-crm';
	jQuery('body').append('<div id="wp-crm-shop-shadow" style="z-index: 9998;"></div>');
	jQuery('body').append('<div id="wp-crm-shop-window" style="z-index: 9999;"></div>');
	var w = jQuery('#wp-crm-shop-window');
	var z = '<img alt="Se incarca ..." title="Se incarca ..."  src="'+u+'/icons/loading.gif" />';

	var WPCRMCookie = function (n) {
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++) {
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name) return unescape(y);
			}
		}

	var WPCRMShadow = function (o) {
		if (o) jQuery('#wp-crm-shop-shadow').css('opacity',0).height(jQuery(document).height()).width(jQuery(document).width()).animate({'opacity':0.7});
		else jQuery('#wp-crm-shop-shadow').animate({'opacity':0}, function(){jQuery('#wp-crm-shop-shadow').height(0).width(0);});
		}
	var WPCRMUIN = function (uin) {
		var ctr = [2,7,9,1,4,6,3,5,8,2,7,9];
		var c = 0, i, y, d;
		if (uin.length != 13) return 0;
		if (uin.match(/[^0-9]+/)) return 0;
		uin = uin.split('');
		if ((uin[0] != '1') && (uin[0] != '2') && (uin[0] != '5') && (uin[0] != '6')) return 0;
		y = (uin[0] == '5' || uin[0] == '6') ? (2000 + parseInt(uin[1])*10 + parseInt(uin[2])) : (1900 + parseInt(uin[1]*10) + parseInt(uin[2]));
//		d = new Date (y, parseInt(uin[3])*10 + parseInt(uin[4]), parseInt(uin[5])*10 + parseInt(uin[6]));
//		if (d.getFullYear() != y || d.getDate() != parseInt(uin[5])*10 + parseInt(uin[6]) || d.getMonth() != parseInt(uin[3])*10 + parseInt(uin[4])) return 0;
		for (i = 0; i<12; i++) c += parseInt(uin[i])*ctr[i];
		c = c%11; c = c == 10 ? 1 : c;
		if (c != parseInt(uin[12])) return 0;
		return 1;
		};
	var WPCRMEMail = function (email) {
		email = email.toLowerCase();
		if (email.indexOf('@')<0) return 0;
		if (!email.match(/[a-z.]{2,}@[a-z.]{3,}\.[a-z]{2,4}/)) return 0;
		return 1;
		}

	var WPCRMShop = function () {
		jQuery('.wp-crm-shop-cart-close').click(function(e){
			WPCRMShadow(0);
			w.hide('slow');
			w.empty();
			});
		jQuery('.wp-crm-shop-cart-button').click(function(e){
			jQuery(e.target).after(z);
			jQuery.post(u+'/ajax/shop/cart.php',{'a':jQuery(e.target).attr('rel'),'p':jQuery(e.target).closest('form').serialize()},function(d){
			jQuery(e.target).next().remove();
			w.empty().html(d);
			WPCRMShop();
			WPCRMCart();
			});});

		jQuery('.wp-crm-slide-control').click(function(e){
			jQuery('.wp-crm-slide').each(function(i,f){
				if (jQuery(f).attr('id') != jQuery(e.target).attr('rel')) jQuery(f).slideUp();
				});
			jQuery('#'+jQuery(e.target).attr('rel')).slideDown();
			});
		jQuery('.wp-crm-slide-colapsed').each(function(i,e){
			jQuery(e).slideUp();
			});
		jQuery('.wp-crm-shop-uin').keyup(function(e){
			if (WPCRMUIN(jQuery(e.target).val())) jQuery(e.target).removeClass('wp-crm-shop-field-er').addClass('wp-crm-shop-field-ok');
			else jQuery(e.target).removeClass('wp-crm-shop-field-ok').addClass('wp-crm-shop-field-er');
			});
		jQuery('.wp-crm-shop-email').keyup(function(e){
			if (WPCRMEMail(jQuery(e.target).val())) jQuery(e.target).removeClass('wp-crm-shop-field-er').addClass('wp-crm-shop-field-ok');
			else jQuery(e.target).removeClass('wp-crm-shop-field-ok').addClass('wp-crm-shop-field-er');
			});
		};

	var WPCRMCart = function () { jQuery.get(u+'/ajax/shop/cart-count.php', function(d){ if (parseInt(d)>0) jQuery('.wp-crm-popup-shop strong').text(d); }); };

	jQuery('#header').prepend('<div class="wp-crm-popup-shop"><img src="'+u+'/icons/shopcart.png" alt="Cursurile mele" title="Cursurile mele" /><strong></strong></div>');

	jQuery('.wp-crm-popup-shop').click(function(e){
		jQuery(e.target).before('<span>' + z + ' se incarca ... </span>');
		WPCRMShadow(1);
		jQuery.post(u+'/ajax/shop/cart.php',{'data':''},function(d){
			jQuery(e.target).prev().remove();
			w.html(d);
			w.show('slow');
			WPCRMShop();
			WPCRMCart();
			});
		});
	jQuery('.wp-crm-shop-product').mouseenter(function(e){
		jQuery(this).children('.wp-crm-shop-button').attr('src', jQuery(this).children('.wp-crm-shop-button').attr('src').replace('ns/l','ns/hl'));
		});
	jQuery('.wp-crm-shop-product').mouseleave(function(e){
		jQuery(this).children('.wp-crm-shop-button').attr('src', jQuery(this).children('.wp-crm-shop-button').attr('src').replace('ns/hl','ns/l'));
		});
	jQuery('.wp-crm-shop-buy-product, .wp-crm-shop-inline-product, .wp-crm-shop-custom-product').click(function(e){
		jQuery('.wp-crm-popup-shop img').before('<span>' + z + ' te inscriem la curs ... </span>');
		h = jQuery(this).prev().clone().css({'position':'absolute', 'z-index': 9999, 'border' : 0, 'overflow': 'hidden' });
		WPCRMShadow(1);
		jQuery(this).before(h);
		h.animate({'top': 5, 'right': 5, 'opacity' : 0, 'width' : 24 }, 2000, function(){h.remove();});
		jQuery('html, body').animate({'scrollTop' : 0});
		jQuery.post(u+'/ajax/shop/cart.php',{'a':'add','c':jQuery(this).children('a').attr('rel')},function(d){
			jQuery('.wp-crm-popup-shop img').prev().remove();
			w.html(d);
			w.show('slow');
			WPCRMShop();
			WPCRMCart();
			});
		});
	jQuery('.wp-crm-shop-product').click(function(e){
		jQuery('.wp-crm-popup-shop img').before('<span>' + z + ' te inscriem la curs ... </span>');
		h = jQuery(this).prev().clone().css({'position':'absolute', 'z-index': 9999, 'border' : 0, 'overflow': 'hidden' });
		WPCRMShadow(1);
		jQuery(this).before(h);
		h.animate({'top': 5, 'right': 5, 'opacity' : 0, 'width' : 24 }, 2000, function(){h.remove();});
		jQuery('html, body').animate({'scrollTop' : 0});
		jQuery.post(u+'/ajax/shop/cart.php',{'a':'add','c':jQuery(this).children('.wp-crm-shop-button').attr('rel')},function(d){
			jQuery('.wp-crm-popup-shop img').prev().remove();
			w.html(d);
			w.show('slow');
			WPCRMShop();
			WPCRMCart();
			});
		});

	jQuery('.wp-crm-gallery-thumb').click(function(e){
		var v = jQuery(e.target).parent().parent().parent().find('.wp-crm-gallery-view');
		var ts = jQuery(e.target).parent().find('.wp-crm-gallery-thumb').toArray();
		v.empty();
		jQuery('html, body').animate({'scrollTop': 280});
		jQuery('.wp-crm-gallery-prev').empty().append(jQuery(e.target).parent().prev().clone());
		jQuery('.wp-crm-gallery-next').empty().append(jQuery(e.target).parent().next().clone());
		jQuery.get(u+'/ajax/actions/image.php',{'i':jQuery(e.target).parent().attr('rel'),'e':1},function(d){
			jQuery(v).html(d);
			jQuery('#wp-crm-gallery-fb-like').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
			jQuery('#wp-crm-gallery-fb-share').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
			jQuery('.wp-crm-gallery-download').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?d=1&i='+jQuery(e.target).parent().attr('rel'));
			FB.XFBML.parse();
			});
		});

	jQuery('.wp-crm-gallery-view').each(function(n,v){
		jQuery('#extremebox').remove();
		var ts = jQuery(v).parent().find('.wp-crm-gallery-thumb').toArray();
		jQuery.get(u+'/ajax/actions/image.php',{'i':jQuery(ts[0]).attr('rel'), 'e':1},function(d){
			jQuery(v).html(d);
			jQuery('#wp-crm-gallery-fb-like').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
			jQuery('#wp-crm-gallery-fb-share').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
			jQuery('.wp-crm-gallery-download').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?d=1&i='+jQuery(e.target).parent().attr('rel'));
			FB.XFBML.parse();
			});
		jQuery('.wp-crm-gallery-prev').empty();
		jQuery('.wp-crm-gallery-next').empty().append(jQuery(ts[0]).next().clone());
		});

	jQuery('.wp-crm-gallery-prev, .wp-crm-gallery-next').click(function(e){
		var v = jQuery(e.target).parent().parent().parent().find('.wp-crm-gallery-view');
		var ts = jQuery(v).parent().find('.wp-crm-gallery-thumb').toArray();
		v.empty();
		jQuery('html, body').animate({'scrollTop': 280});
		for (c = 0; c<ts.length; c++) {
			if (jQuery(ts[c]).attr('rel') != jQuery(e.target).parent().attr('rel')) continue;
			
			jQuery('.wp-crm-gallery-prev').empty().append(jQuery(ts[c]).prev().clone());
			jQuery('.wp-crm-gallery-next').empty().append(jQuery(ts[c]).next().clone());
			jQuery.get(u+'/ajax/actions/image.php',{'i':jQuery(ts[c]).attr('rel'), 'e':1},function(d){
				jQuery(v).html(d);
				jQuery('#wp-crm-gallery-fb-like').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
				jQuery('#wp-crm-gallery-fb-share').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?i='+jQuery(e.target).parent().attr('rel'));
				jQuery('.wp-crm-gallery-download').attr('href', 'http://www.traininguri.ro'+u+'/ajax/actions/image.php?d=1&i='+jQuery(e.target).parent().attr('rel'));
				FB.XFBML.parse();
				});
			}
		});

	WPCRMCart();
	});
