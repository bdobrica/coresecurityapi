jQuery(document).ready(function(){
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
			});
		jQuery('.wp-crm-form-basket select').change(function(e){
			var l = jQuery(e.target).parent().next().next().next();
			var p = l.html().split(' = ');
			var q = p[0].split(' x ');
			var r = parseInt(e.target.options[e.target.selectedIndex].value);
			var c = q[1].replace(parseFloat(q[1]) + ' ', '');
			l.html(r + ' x ' + parseFloat(q[1]) + ' ' + c + ' = ' + (r * parseFloat(q[1])) + ' ' + c);

			var t = 0;
			var v = '';
			jQuery('.wp-crm-form-basket select').each(function(n,l){
				var s = jQuery(l).parent().next().next().next().html().split(' = ');
				t += parseFloat(s[1]);
				v += jQuery(l).parent().next().next().next().attr('rel') + '-' + parseInt(l.options[l.selectedIndex].value) + '+';
				});
			jQuery('.wp-crm-form-basket').prev().val(v);
			jQuery('.wp-crm-form-basket-total').html(' Total: '+t+' '+c);
			});
		jQuery('.wp-crm-form input[type="submit"]').click(function(e){
			e.preventDefault();
			jQuery('.wp-crm-form-shadow').css({'height': '100%', 'width': '100%'}).animate({'opacity': 1});
			var p = jQuery(this).closest('form').serialize() + '&' + jQuery(this).attr('name') + '=1';
			jQuery.post(jQuery(this).closest('form').attr('action'), p, function(d) {
				jQuery('.wp-crm-form-body').html(d);
				init ();

				jQuery('.wp-crm-form-shadow').animate({'opacity': 0}, 500, function(){
					jQuery(this).css({'height': 0, 'width': 0});

					});
				});
			});
		};
	init ();
	});
