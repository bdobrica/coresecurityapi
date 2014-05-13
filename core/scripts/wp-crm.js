jQuery(document).ready(function(){
	var WPCRMPCnt = 1;
	var WPCRMLoad = function (cx) {
		jQuery(cx).find('#uin-generate').click(function(e){
			var i = 0, l = 0;
			var c = [2,7,9,1,4,6,3,5,8,2,7,9];
			var u = '';
			u += jQuery('#uin-generator-gender').val();
			u += jQuery('#uin-generator-year').val();
			u += jQuery('#uin-generator-month').val();
			u += jQuery('#uin-generator-day').val();
			u += jQuery('#uin-generator-county').val();
			u += jQuery('#uin-generator-number').val();
			v = u.split('');
			for (i = 0; i<12; i++) l += parseInt(v[i])*c[i];
			l %= 11;
			if (l == 10) l = 1;
			u += l;
			jQuery('#uin-generator-uin').val(u);
			});
		jQuery(cx).find('.wp-crm-quick-product-new input').keyup(function(e){
			if (jQuery(e.target).attr('id') == 'wp-crm-quick-product-new-value')
				jQuery('#wp-crm-quick-product-new-vat-value').val(Math.round(eval(jQuery(e.target).val()) * (100 + eval(jQuery('#wp-crm-quick-product-new-vat').val()))) * 0.01);
			if (jQuery(e.target).attr('id') == 'wp-crm-quick-product-new-vat-value')
				jQuery('#wp-crm-quick-product-new-value').val(Math.round(eval(jQuery(e.target).val()) * 10000 / (100 + eval(jQuery('#wp-crm-quick-product-new-vat').val()))) * 0.01);
			if (jQuery(e.target).attr('id') == 'wp-crm-quick-product-new-vat')
				jQuery('#wp-crm-quick-product-new-vat-value').val(Math.round(eval(jQuery('#wp-crm-quick-product-new-value').val()) * (100 + eval(jQuery(e.target).val()))) * 0.01);
			});
		jQuery(cx).find('input').keydown(function(e){
			if (e.which == 13) e.preventDefault();
			});
		jQuery(cx).find('.wp-crm-quick-product-add').click(function(e){
			var t = jQuery(e.target).parent().parent().parent();
			var s = t.find('select option:selected');
			var x = 0, p = 0;

			t.children('tr').each(function(k,v){
				var i = jQuery(v).find('input');
				if ((jQuery(v).children('td').attr('colspan') == 2) && !x) {
					if (s.val() != -1)
						jQuery(v).before('<tr><th>'+s.text()+'</th><td><input type="text" name="wp_crm_quick_quantity_'+s.val()+'" value="1" style="width: 48px;" /></td></tr>');
					else {
						jQuery(v).before('<tr><th>'+jQuery('#wp-crm-quick-product-new').val()+'</th><td><input type="hidden" name="wp_crm_quick_new_product_name_'+WPCRMPCnt+'" value="'+jQuery('#wp-crm-quick-product-new').val()+'" /><input type="hidden" name="wp_crm_quick_new_product_value_'+WPCRMPCnt+'" value="'+jQuery('#wp-crm-quick-product-new-value').val()+'" /><input type="hidden" name="wp_crm_quick_new_product_vat_'+WPCRMPCnt+'" value="'+jQuery('#wp-crm-quick-product-new-vat').val()+'" /><input type="text" name="wp_crm_quick_new_product_quantity_'+WPCRMPCnt+'" value="1" style="width: 48px;" /></td></tr>');
						WPCRMPCnt++;
						}
					x = 1;
					}
				else
					if (i.attr('name').replace('wp_crm_quick_quantity_','') == s.val()) {
						i.val(parseInt(i.val())+1);
						x = 1;
						}
				});
			});

		jQuery(cx).find('.wp-crm-ajax-form').click(function(e){
			if (jQuery(e.target).attr('rel') == '1') return;
			jQuery(e.target).attr('rel',1);
			var f = jQuery(this).closest('form');
			if (jQuery(e.target).hasClass('wp-crm-ajax-append'))
				jQuery(this).closest('table').next().remove();
			else
				jQuery(this).next('span').remove();
			jQuery(this).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
			jQuery.post('/wp-content/plugins/wp-crm/ajax/actions/save.php', f.serialize(), function(d) {
				jQuery(e.target).attr('rel','');
				jQuery(e.target).next().remove();
				if (jQuery(e.target).hasClass('wp-crm-ajax-append'))
					jQuery(e.target).closest('table').after(jQuery(d));
				else {
					if (d.indexOf('OK') < 0)
						jQuery(e.target).after(jQuery('<span style="color: #c00">'+d+'</span>'));
					else
						jQuery(e.target).after(jQuery('<span style="color: #0c0">'+d+'</span>'));
					}
				});
			});

		jQuery(cx).find('.wp-crm-popup-control').each(function(k,i){
			jQuery(this).next().slideToggle(0);
			jQuery(this).click(function(e){
				jQuery(this).next().slideToggle('slow');
				});
			});
		};

	jQuery('.wp-crm-popup-control').each(function(k,i){
		jQuery(this).next().slideToggle(0);
		jQuery(this).click(function(e){
			jQuery(this).next().slideToggle('slow');
			});
		});
	jQuery('.wp-crm-popup-ajax').click(function(e){
		if (jQuery(this).next().html()) {
			jQuery(this).next().empty().hide();
			}
		else {
			var p = jQuery(this).next();
			p.show().html('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />');
			var q = p.offset().left + p.width() - jQuery(window).width();
			if (q > 0) p.offset({'left':p.offset().left - q - 10});
			jQuery.post('/wp-content/plugins/wp-crm/ajax/actions/popup.php', {'data' : jQuery(e.target).attr('rel')}, function(d){
				jQuery(e.target).next().empty().html(d);
				WPCRMLoad(jQuery(e.target).next());
				});
			}
		});
	jQuery('.wp-crm-table').each(function(k,i){
		if (!jQuery(this).hasClass('wp-crm-raised'))
			jQuery(this).find('tbody').each(function(l,j){jQuery(this).slideToggle(0);});
		jQuery(this).find('thead').click(function(e){
			jQuery(this).next().slideToggle('slow');
			});
		});
	jQuery('.wp-crm-product-complete').click(function(e){
		var p = jQuery(e.target).prev().val();
		jQuery(e.target).before(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/product.php?p=' + p, function(d){
			var i = jQuery(e.target).parent().next().next();
			jQuery(e.target).prev().remove();
			jQuery(e.target).prev().val(d.name + ' [[' + d.code.toUpperCase() + ']]');
			i.find('input').val(d.price);
			i.next().find('input').val(d.vat);
			});
		});
	jQuery('.wp-crm-product-delete').click(function(e){
		jQuery(e.target).parent().parent().remove();
		});
	jQuery('#wp-crm-add-product').click(function(e){
		var t = jQuery(e.target).parent().parent().next();
		var r = jQuery('<tr></tr>');
		var c = jQuery('<td></td>');
		var i = jQuery('<input type="text" name="" value="" />');
		var h = jQuery('<input type="hidden" name="" value="" />');
		var b = jQuery('<input type="button" name="" value="Sterge" />');
		var a = jQuery('<input type="button" name="" value="Completeaza" />');
		var o = jQuery('#wp-crm-invoice-products').attr('rel');
		o = parseInt(o ? o : 0);
		r.append(c.clone().append(i.clone().attr('name','product-'+o)).append(a.clone().addClass('wp-crm-product-complete').click(function(e){
			var p = jQuery(e.target).prev().val();
			jQuery(e.target).before(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
			jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/product.php?p=' + p, function(d){
				var i = jQuery(e.target).parent().next().next();
				jQuery(e.target).prev().remove();
				jQuery(e.target).prev().val(d.name + ' [[' + d.code.toUpperCase() + ']]');
				i.find('input').val(d.price);
				i.next().find('input').val(d.vat);
				});
			})));
		r.append(c.clone().append(i.clone().attr('name','quantity-'+o).addClass('wp-crm-input-narrow')));
		r.append(c.clone().append(i.clone().attr('name','value-'+o).addClass('wp-crm-input-wide')));
		r.append(c.clone().append(i.clone().attr('name','vat-'+o).addClass('wp-crm-input-narrow')));
		r.append(c.clone().append(i.clone().attr('name','vatval-'+o).addClass('wp-crm-input-narrow')));
		r.append(c.clone().append(i.clone().attr('name','total-'+o).addClass('wp-crm-input-narrow')));
		b.click(function(f){
			jQuery(f.target).parent().parent().remove();
			});
		r.append(c.clone().append(b));
		jQuery('#wp-crm-invoice-products').append(r);
		o+=1;
		jQuery('#wp-crm-invoice-products').attr('rel',o);
		});

	jQuery('#wp-crm-participants-update').click(function(e){
		var t = jQuery(e.target).parent().parent().next();
		var r = jQuery('<tr></tr>');
		var c = jQuery('<td></td>');
		var i = jQuery('<input type="text" name="" value="" />');
		var a = jQuery('<input type="button" name="" value="Completeaza" />');
		var l = null, o = 0;

		var p = '';

		jQuery('#wp-crm-invoice-participants tr').each(function(k,j){
			if (k>1) jQuery(this).remove();
			});

		jQuery('#wp-crm-invoice-products input[type="text"]').each(function(k,j){
			var m,g,h;
			if (k%6 == 0) {
				g = parseInt(this.name.replace('product-', ''));
				p = this.value;
				}
			if (k%6 == 1) {
				this.value = this.value ? this.value : 1;
				for (m = 0; m < this.value; m++) {
					l = r.clone();
					
					l.append(c.clone().append(p));
					l.append(c.clone().append(i.clone().attr('name','participant-uin-'+o)).append(a.clone().click(function(f){
						var n = jQuery(this);
						n.before(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
						
						jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/client.php?u=' + n.prev().prev().val(), function(d){
							var q = n.parent().next();
							q.children('input').val(d.name);
							q = q.next();
							q.children('input').val(d.delegateidser);
							q = q.next();
							q.children('input').val(d.delegateidnum);
							q = q.next();
							q.children('input').val(d.delegatephone);
							q = q.next();
							q.children('input').val(d.delegateemail);
							n.prev().remove();
							});
						})));

					l.append(c.clone().append(i.clone().attr('name','participant-name-'+o)));
					l.append(c.clone().append(i.clone().attr('name','participant-id-series-'+o)));
					l.append(c.clone().append(i.clone().attr('name','participant-id-number-'+o)));
					l.append(c.clone().append(i.clone().attr('name','participant-phone-'+o)));
					l.append(c.clone().append(i.clone().attr('name','participant-email-'+o)));

					jQuery('#wp-crm-invoice-participants').append(l);
					o++;
					}	
				}
			});
		});


	jQuery('#wp-crm-buyer-complete').click(function(e){
		var u = jQuery('#wp-crm-client-uin').val();
		jQuery('#wp-crm-client-uin').after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/client.php?u=' + u, function(d){
			jQuery('#wp-crm-client-name').val(d.name);
			jQuery('#wp-crm-client-reg').val(d.reg);
			jQuery('#wp-crm-client-address').val(d.address);
			jQuery('#wp-crm-client-county').val(d.county);
			jQuery('#wp-crm-client-account').val(d.account);
			jQuery('#wp-crm-client-bank').val(d.bank);
			jQuery('#wp-crm-client-person').val(d.delegate);
			jQuery('#wp-crm-client-person-uin').val(d.delegateuin);
			jQuery('#wp-crm-client-person-email').val(d.delegateemail);
			jQuery('#wp-crm-client-person-phone').val(d.delegatephone);
			jQuery('#wp-crm-client-uin').next().remove();
			});
		});

	jQuery('#wp-crm-invoice-update').click(function(e){
		var t = 0;
		var x = 0;
		var v = 0;
		var q = 0;
		var p = 0;
		var r = 0;
		var j = null;
		jQuery('#wp-crm-invoice-products input[type="text"]').each(function(k,i){
			if (k%6 == 1) {
				q = parseInt(this.value ? this.value : 1);
				this.value = q;
				}
			if (k%6 == 2) {
				p = parseFloat(this.value ? this.value : 0);
				j = this;
				}
			if (k%6 == 3) {
				x = parseFloat(this.value ? this.value : 0);
				this.value = x;
				}
			if (k%6 == 4) {
				if (p) this.value = Math.round(p*(100+x))*0.01;
				r = parseFloat(this.value ? this.value : 0);
				if (!p) {
					p = Math.round(r*10000/(100+x))*0.01;
					j.value = p;
					}
				}
			if (k%6 == 5) {
				this.value = Math.round(q*r*100)*0.01;
				t += q*p;
				v += q*p*x*0.01;
				}
			});
		jQuery('#wp-crm-invoice-value').empty().append(Math.round(100*t)*0.01);
		jQuery('#wp-crm-invoice-vat').empty().append(Math.round(100*t)*0.01);
		jQuery('#wp-crm-invoice-total').empty().append(Math.round((v+t)*100)*0.01);
		});
	jQuery('.wp-crm-persons-all').click(function(e){
		if (e.target.checked) {
			jQuery('.wp-crm-persons').attr('checked', 'checked');
			jQuery('.wp-crm-persons-all').attr('checked', 'checked');
			}
		else {
			jQuery('.wp-crm-persons').removeAttr('checked');
			jQuery('.wp-crm-persons-all').removeAttr('checked');
			}
		});
	jQuery('#wp-crm-voucher-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).parent().next().next().next().text() + ',';
			});
		jQuery('#wp-crm-voucher').val(m);
		});
	jQuery('#wp-crm-badge-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-badge').val(m);
		});
	jQuery('#wp-crm-diploma-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-diploma').val(m);
		});
	jQuery('#wp-crm-invoicesheet-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-invoicesheet').val(m);
		});
	jQuery('#wp-crm-invoices-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-invoices').val(m);
		});
	jQuery('#wp-crm-attendance-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-attendance').val(m);
		});
	jQuery('#wp-crm-opis-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-opis').val(m);
		});
	jQuery('#wp-crm-mailagent-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-mailagent').val(m);
		});
	jQuery('#wp-crm-cnfpa-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-cnfpa').val(m);
		});
	jQuery('#wp-crm-cnfpa-release-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-cnfpa-release').val(m);
		});
	jQuery('#wp-crm-cnfpa-participants-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-cnfpa-participants').val(m);
		});
	jQuery('#wp-crm-cnfpa-contract-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-cnfpa-contract').val(m);
		});
	jQuery('#wp-crm-cnfpa-supp-submit').click(function(e){
		var m = '';
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).attr('name').replace('wp_crm_participant_','') + ',';
			});
		jQuery('#wp-crm-cnfpa-supp').val(m);
		});
	jQuery('#wp-crm-contact-sendmail').click(function(e){
		var m = '';
		jQuery('#wp-crm-contact-container').css('display','block');
		jQuery('.wp-crm-persons:checked').each(function(k,i){
			m += jQuery(i).parent().next().next().next().next().next().next().next().html() + ',';
			});
		if (m.length>1) m = m.substr(0,m.length-1);
		jQuery('#wp-crm-contact-uins').val(m);
		});
	jQuery('#wp-crm-mail-template-load').click(function(e){
		var t = jQuery(e.target).closest('tr').find('select');
		jQuery(e.target).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/mailtemplate.php?t=' + t.val(), function(d){
			jQuery(e.target).next().remove();
			jQuery('#wp-crm-mail-template-subject').val(d.subject);
			tinyMCE.getInstanceById('wp-crm-mail-template').setContent(d.content);
			});
		});
	jQuery('#wp-crm-contact-load').click(function(e){
		var t = jQuery(e.target).closest('li').find('select');
		alert (t.text()+' = '+t.val());
		jQuery(e.target).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/mailtemplate.php?t=' + t.val(), function(d){
			jQuery(e.target).next().remove();
			jQuery('#wp-crm-contact-subject').val(d.subject);
			jQuery('#wp-crm-contact-message').html(d.content);
			});
		});
	jQuery('#wp-crm-location-load').click(function(e){
		var t = jQuery(e.target).closest('tr').find('select');
		jQuery(e.target).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.getJSON('/wp-content/plugins/wp-crm/ajax/search/location.php?l=' + t.val(), function(d){
			jQuery(e.target).next().remove();
			jQuery('#wp-crm-location-title').val(d.title);
			jQuery('#wp-crm-location-address').val(d.address);
			jQuery('#wp-crm-location-map').val(d.map);
			tinyMCE.getInstanceById('wp-crm-location-directions').setContent(d.directions);
			});
		});

	jQuery('.wp-crm-ajax-form').click(function(e){
		var f = jQuery(this).parent().parent().parent().parent().parent();
		jQuery(this).next('span').remove();
		jQuery(this).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.post('/wp-content/plugins/wp-crm/ajax/actions/save.php', f.serialize(), function(d) {
			jQuery(e.target).next().remove();
			if (d.indexOf('OK') < 0)
				jQuery(e.target).after(jQuery('<span style="color: #c00">'+d+'</span>'));
			else
				jQuery(e.target).after(jQuery('<span style="color: #0c0">'+d+'</span>'));
			WPCRMLoad(jQuery(e.target).next());
			});
		});
	jQuery('.wp-crm-ajax-bin').click(function(e){
		jQuery(this).after(jQuery('<img src="/wp-content/plugins/wp-crm/images/loading.gif" alt="" title="" width="16" height="16" />'));
		jQuery.post('/wp-content/plugins/wp-crm/ajax/actions/save.php', jQuery(e.target).attr('name') + '=' +(jQuery(e.target).is(':checked') ? 1 : 0), function(d){
			jQuery(e.target).next().remove();
			alert(d);
			});
		});
	jQuery('.wp-crm-bin-radio').click(function(e){
		if (!jQuery(this).is(':checked')) return;
		jQuery(this).closest('table').find(':checked').attr('checked', false);
		jQuery(this).attr('checked', true);
		});
	jQuery('.wp-crm-colorpicker a').click(function(e){
		jQuery(this).closest('table').find('a').css('background-image','');
		jQuery(this).css('background-image','url(/wp-content/plugins/wp-crm/icons/check.png)').closest('table').find('span').css('background-color', jQuery(this).attr('rel'));
		});
	});

