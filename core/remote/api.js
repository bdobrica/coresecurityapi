jQuery(document).ready(function(){
	var disc = function () {
		var o = jQuery.parseJSON(jQuery('.wp-crm-form-coupon-data').val());
		jQuery('.wp-crm-form-basket select').each(function(i,v){
			var j = 0;
			var c = jQuery(v).attr('rel');
			var q = parseInt(v.options[v.selectedIndex].value);
			var d = 0;
			var t = parseFloat(jQuery('.wp-crm-form-basket-total').text().replace(/[^0-9.]+/,''));

			if (o[c].length)
				for (j = 0; j<o[c].length; j++)
					if ((o[c][j].max_quantity >= q) && (q >= o[c][j].min_quantity))
						d = o[c][j].type == 'fixed' ? parseFloat(o[c][j].value) : (0.01*parseFloat(o[c][j].value)*t);
			if (d)
				jQuery('.wp-crm-form-coupon-data').parent().after('<li class="wp-crm-form-coupon-discount"><label>Discount cupon:</label> <strong>-' + d.toFixed(2) + ' lei</strong><div style="clear: both;"></div></li>').next().next('.wp-crm-form-coupon-discount').remove();
			else
				jQuery('.wp-crm-form-coupon-discount').remove();
			jQuery('.wp-crm-form-basket-total').html('Total: ' + (t-d).toFixed(2) + ' lei');
			});
		};
	var init = function () {
		var a = jQuery('.wp-crm-form-wrapper').toArray();
		jQuery('.wp-crm-form-tabs li').each(function(n,l){
//			jQuery(a[n]).attr('rel', parseInt(jQuery(a[n]).height()));
//			alert(jQuery(a[n]).attr('rel'));
			if (jQuery(l).find('input:checked').length < 1) jQuery(a[n]).height(0);
			jQuery(l).attr('rel', n).click(function(e){
				var c = 0;
				jQuery(e.target).find('input').attr('checked', 'checked');
				for (;c<a.length;c++)
					if (parseInt(jQuery(e.target).attr('rel')) != c)
						jQuery(a[c]).animate({'height': 0});
					else
						jQuery(a[c]).animate({'height': jQuery(a[c]).attr('rel')});
				});
			jQuery(l).find('input').click(function(e){
				e.stopPropagation();
				var c = 0;
				for (;c<a.length;c++)
					if (parseInt(jQuery(e.target).parent().attr('rel')) != c)
						jQuery(a[c]).animate({'height': 0});
					else
						jQuery(a[c]).animate({'height': jQuery(a[c]).attr('rel')});
				});
			});
		jQuery('.wp-crm-form-basket select').change(function(e){
			var l = jQuery(e.target).parent().next().next().next();
			var q = parseFloat(jQuery(e.target.options[1]).attr('rel'));
			var r = parseInt(e.target.options[e.target.selectedIndex].value);
			var u = parseFloat(jQuery(e.target.options[e.target.selectedIndex]).attr('rel'));
			var c = l.text().replace(/^.+ /, '');
			l.html(r + ' x ' + parseFloat(u).toFixed(2) + ' ' + c + ' = ' + (r * u).toFixed(2) + ' ' + c);

			var t = 0;
			var v = '';
			jQuery('.wp-crm-form-basket select').each(function(n,l){
				var s = jQuery(l).parent().next().next().next().html().split(' = ');
				t += parseFloat(s[1]);
				v += jQuery(l).parent().next().next().next().attr('rel') + '-' + parseInt(l.options[l.selectedIndex].value) + '+';
				});
			jQuery('.wp-crm-form-basket').prev().val(v);
			jQuery('.wp-crm-form-basket-total').html(' Total: ' + t.toFixed(0) + ' ' + c);

			disc ();
			});
		jQuery('.wp-crm-form input[type="submit"]').click(function(e){
			e.preventDefault();
			jQuery('.wp-crm-form-shadow').css({'height': '100%', 'width': '100%'}).animate({'opacity': 1});
			var p = jQuery(this).closest('form').serialize() + '&' + jQuery(this).attr('name') + '=1';

			var u = document.createElement('a');

			jQuery.post(window.location.protocol + '//' + window.location.host + window.location.pathname.replace(/index.php/g,'') + 'index.php', p, function(d) {
				jQuery('.wp-crm-form-body').html(d);
				init ();

				jQuery('.wp-crm-form-shadow').animate({'opacity': 0}, 500, function(){
					jQuery(this).css({'height': 0, 'width': 0});
					});
				});
			});
		jQuery('.wp-crm-form-error').mouseenter(function(e){
			jQuery(this).find('.wp-crm-form-error-hint').show();
			}).mouseleave(function(e){
			jQuery(this).find('.wp-crm-form-error-hint').hide();
			});
		jQuery('.wp-crm-form-error-hint').hide();

		jQuery('.wp-crm-form-coupon-query').click(function(e){
			jQuery.post('/wp-content/plugins/wp-crm/remote/coupon.php', 'coupon=' + jQuery(this).prev().val(), function(d){
				if (!d) return;
				jQuery(e.target).prev().prev().val(d);
				jQuery(e.target).parent().append('<label>Cupon discount: <strong>' + jQuery(e.target).prev().val() + '</strong></label><div style="clear: both;"></div>');
				jQuery(e.target).parent().prev().hide();
				jQuery(e.target).prev().hide();
				jQuery(e.target).hide();

				disc ();
				});
			});

		jQuery('.tos-link').click(function(e){
			e.preventDefault();
			var tw = jQuery('.tos-link-window').css({'opacity': 0, 'height': '100%', 'width': '100%'}).animate({'opacity': 1});
			jQuery.get('/wp-content/plugins/wp-crm/remote/tos.php',function(d){
				if (!d) return;
				tw.html(d);
				tw.find('.tos-link-buttons button').click(function(f){
					jQuery(e.target).parent().parent().find('input').attr('checked', jQuery(f.target).hasClass('tos-link-yes') ? true : false);
					tw.animate({'opacity': 0}, 500, function(){
						jQuery(this).css({'height': 0, 'width': 0}).empty();
						});
					});
				});
			});
		};
	init ();
	});
