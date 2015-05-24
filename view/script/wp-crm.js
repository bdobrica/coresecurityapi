;Array.prototype.last = Array.prototype.last || function(){return this[this.length-1];};

//String.prototype.stripSlashes = function(){ return this.replace(/\\(.?)/g, function (s,n1){switch (n1) {case '\\':return '\\';case '0':return '\u0000';case '':return '';default:return n1;}});}

(function($,undefined){
	$.widget('ui.dragslider',$.ui.slider,{
		options: $.extend({},$.ui.slider.prototype.options,{rangeDrag:false}),
		_create: function(){ $.ui.slider.prototype._create.apply(this,arguments);this._rangeCapture = false;},
		_mouseCapture: function(event){ 
			var o = this.options;
			if (o.disabled) return false;
			if (event.target == this.range.get(0) && o.rangeDrag == true && o.range == true){
				this._rangeCapture = true;
				this._rangeStart = null;
			} else 
			this._rangeCapture = false;

			$.ui.slider.prototype._mouseCapture.apply(this,arguments);
			if (this._rangeCapture == true) {
				this.handles.removeClass("ui-state-active").blur();	
				}
			return true;
			},
		_mouseStop: function(event){
			this._rangeStart = null;
			return $.ui.slider.prototype._mouseStop.apply(this,arguments);
			},
		_slide: function(event, index, newVal){
			if(!this._rangeCapture)
				return $.ui.slider.prototype._slide.apply(this,arguments);
			if(this._rangeStart == null)
				this._rangeStart = newVal;

			var oldValLeft = this.options.values[0],
				oldValRight = this.options.values[1],
				slideDist = newVal - this._rangeStart,
				newValueLeft = oldValLeft + slideDist,
				newValueRight = oldValRight + slideDist,
				allowed;

			if (this.options.values && this.options.values.length){
				if (newValueRight > this._valueMax() && slideDist > 0){
					slideDist -= (newValueRight-this._valueMax());
					newValueLeft = oldValLeft + slideDist;
					newValueRight = oldValRight + slideDist;
					}
				if (newValueLeft < this._valueMin()){
					slideDist += (this._valueMin()-newValueLeft);
					newValueLeft = oldValLeft + slideDist;
					newValueRight = oldValRight + slideDist;
					}
				if ( slideDist != 0 ) {
					newValues = this.values();
					newValues[0] = newValueLeft;
					newValues[1] = newValueRight;
					// A slide can be canceled by returning false from the slide callback
					allowed = this._trigger ('slide', event, {
						handle: this.handles[ index ],
						value: slideDist,
						values: newValues
						});
					if (allowed !== false){
						this.values(0, newValueLeft, true);
						this.values(1, newValueRight, true);
						}
					this._rangeStart = newVal;
					}
				}
			}
		});
	})(jQuery);

function stripslashes(str) {
  //       discuss at: http://phpjs.org/functions/stripslashes/
  //      original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      improved by: Ates Goral (http://magnetiq.com)
  //      improved by: marrtins
  //      improved by: rezna
  //         fixed by: Mick@el
  //      bugfixed by: Onno Marsman
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //         input by: Rick Waldron
  //         input by: Brant Messenger (http://www.brantmessenger.com/)
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //        example 1: stripslashes('Kevin\'s code');
  //        returns 1: "Kevin's code"
  //        example 2: stripslashes('Kevin\\\'s code');
  //        returns 2: "Kevin\'s code"

}

google.load('visualization', '1', {packages:['orgchart']});
fd.jQuery();

jQuery.fn.tinyeditor = function () {
	var i = Math.floor(10000 * Math.random());
	if (!this.attr('id')) this.attr('id', 'wp-crm-rte-' + i);
	if (!this.attr('id')) return;
	jQuery('<div></div>').css('clear','both').insertAfter(this);

	this.rte = new TINY.editor.edit('e'+i,{
		id: this.attr('id'),
		width: this.width()+'%',
		height: this.height()+'%',
		cssclass: 'tinyeditor',
		controlclass: 'tinyeditor-control',
		rowclass: 'tinyeditor-header',
		dividerclass: 'tinyeditor-divider',
		controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
			'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
			'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
			'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print', 'fullscreen'],
		footer: true,
		fonts: ['Verdana','Arial','Georgia','Trebuchet MS'],
		xhtml: true,
		bodyid: 'editor',
		footerclass: 'tinyeditor-footer',
		toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
		resize: {cssclass: 'resize'}
		});
	return this;
	};

jQuery.fn.udecalc = function() {
	var jth = this;

	if (jth.hasClass('ude-calculator')) return;
	jth.addClass('ude-calculator');

	var pls = [{"name":"Gr\u00e2u comun","factor":"529.67"},{"name":"Gr\u00e2u dur","factor":"394.39"},{"name":"Secar\u0103","factor":"392.55"},{"name":"Orz","factor":"456.04"},{"name":"Ov\u0103z","factor":"302.81"},{"name":"Porumb boabe","factor":"640.66"},{"name":"Orez","factor":"822.98"},{"name":"Alte cereale","factor":"416.58"},{"name":"Maz\u0103re boabe, fasole boabe, lupin dulce","factor":"488.31"},{"name":"Linte, n\u0103ut \u0219i m\u0103z\u0103riche","factor":"387.25"},{"name":"Cartofi","factor":"3120.62"},{"name":"Sfecl\u0103 de zah\u0103r","factor":"1245.24"},{"name":"Culturi furajere \u2013 r\u0103d\u0103cinoase","factor":"1254.08"},{"name":"Tutun","factor":"1918.29"},{"name":"Hamei","factor":"913.37"},{"name":"Rapita","factor":"612.65"},{"name":"Floarea soarelui","factor":"501.37"},{"name":"Soia","factor":"574.46"},{"name":"In, altul dec\u00e2t inul pentru fibr\u0103","factor":"1522.65"},{"name":"Alte culturi oleaginoase","factor":"274.04"},{"name":"Alte culturi textile","factor":"47"},{"name":"Plante medicinale, condimente, plante aromatice \u0219i mirodenii, inclusiv ceaiul, cafeaua \u0219i cicoarea pentru cafea","factor":"812.88"},{"name":"Alte plante industriale","factor":"816.04"},{"name":"Legume proaspete, pepeni \u015fi c\u0103p\u015funi - \u00een c\u00e2mp","factor":"7113.49"},{"name":"Legume proaspete, pepeni galbeni \u0219i c\u0103p\u0219uni cultivate \u00een aer liber, \u00een gr\u0103din\u0103","factor":"7914.85"},{"name":"Legume proaspete, pepeni galbeni \u0219i c\u0103p\u0219uni cultivate \u00een spa\u021bii protejate","factor":"\u00a037209.23"},{"name":"Flori \u0219i plante ornamentale cultivate \u00een aer liber","factor":"25638.04"},{"name":"Flori \u0219i plante ornamentale cultivate \u00een spa\u021bii protejate","factor":"96808.28"},{"name":"Iarba temporara","factor":"256.84"},{"name":"Porumb furajer","factor":"980.6"},{"name":"Alte cereale pentru siloz","factor":"468.58"},{"name":"Alte plante furajere","factor":"632.35"},{"name":"Semin\u0163e \u015fi alte semin\u021be","factor":"3173.7"},{"name":"Alte plante","factor":"1018.19"},{"name":"Paji\u0219ti \u0219i p\u0103\u0219uni permanente","factor":"261.96"},{"name":"P\u0103\u0219uni s\u0103race: p\u0103\u0219unile s\u0103race, inclusiv l\u0103st\u0103ri\u0219ul, de obicei nefertilizate \u0219i ne\u00eentre\u021binute","factor":"94.74"},{"name":"Livezi de pomi fructiferi: fructe semin\u021boase: mere, pere etc., cu excep\u021bia stafidelor, sau fructe s\u00e2mburoase: prune, piersici, caise, cire\u0219e etc.","factor":"2703.58"},{"name":"Fructe mici \u0219i bace: coac\u0103ze albe \u0219i ro\u0219ii, zmeur\u0103, smochine","factor":"3430.92"},{"name":"Fructe cu coaj\u0103: nuci, alune, migdale, castane etc.","factor":"1556.94"},{"name":"Vii - vin de calitate","factor":"1737.12"},{"name":"Vii - alte vinuri","factor":"1604.54"},{"name":"Vii \u2013 struguri de masa","factor":"2028.99"},{"name":"Pepiniere","factor":"6653.13"},{"name":"Alte culturi\u00a0 permanente","factor":"541.52"},{"name":"Ciuperci\u00a0 - pe 100 mp (nr. recolte pe an=4)","factor":"3845.95"}];
	var lvs = [{"name":"Cabaline","factor":"1963.87"},{"name":"Bovine sub 1 an - total","factor":"243.86"},{"name":"Bovine sub 2 ani - masculi","factor":"398.96"},{"name":"Bovine sub 2 ani - femele","factor":"369.66"},{"name":"Bovine de 2 ani \u015fi peste - masculi","factor":"846.07"},{"name":"Bovine de 2 ani si peste\u00a0 - femele","factor":"874.52"},{"name":"Vaci pentru lapte","factor":"1033.43"},{"name":"Bovine de 2 ani \u015fi peste - alte vaci","factor":"561.8"},{"name":"Ovine - mioare montate","factor":"50.47"},{"name":"Ovine - alte oi","factor":"23.39"},{"name":"Caprine - capre montate","factor":"99.37"},{"name":"Caprine - alte capre","factor":"38.09"},{"name":"Porcine - tineret porcin sub 20 kg","factor":"30.71"},{"name":"Porcine - scroafe pentru reproduc\u0163ie peste 50 kg","factor":"304.03"},{"name":"Porcine - alte porcine","factor":"404.39"},{"name":"Pui pentru carne","factor":"4.24"},{"name":"G\u0103ini ou\u0103toare","factor":"22.7388"},{"name":"Alte pasari","factor":"12.0742"},{"name":"Iepuri (femele iepuri)","factor":"9.31"},{"name":"Familii de albine","factor":"52.26"}];

	var c = 0;
	var t = jQuery('<table />');
	var th = jQuery('<thead />').append(
		jQuery('<tr />').append(
			jQuery('<th />', {'html': 'Activitate Agricola', 'class':'wp-crm-ude-calculator-type'})).append(
			jQuery('<th />', {'html': 'Suprafata&nbsp;(ha) / Nr.&nbsp;capete', 'class': 'wp-crm-ude-calculator-value'})).append(
			jQuery('<th />', {'html': 'Valoare SO (&euro;)', 'class': 'wp-crm-ude-calculator-so'})).append(
			jQuery('<th />')));

	var sel = jQuery('<select />');
	var opt = jQuery('<optgroup />', {'label': 'Plante'});
	for (c = 0; c<pls.length; c++)
		opt.append(jQuery('<option />').val(pls[c].factor).html(pls[c].name));
	sel.append(opt);
	opt = jQuery('<optgroup />', {'label': 'Animale'});
	for (c = 0; c<lvs.length; c++)
		opt.append(jQuery('<option />').val(lvs[c].factor).html(lvs[c].name));
	sel.append(opt);

	var inp = jQuery('<input />', {'class': 'form-control input-sm', 'value': '0'});
	var vso = jQuery('<td />', {'html': '0.00'});
	var btn = jQuery('<button />', {'class': 'btn btn-sm btn-success'}).append(jQuery('<i />', {'class':'fa fa-plus'}));

	var tb = jQuery('<tbody />').append(
		jQuery('<tr />').append(
			jQuery('<td />').append(sel)).append(
			jQuery('<td />').append(inp)).append(
			vso).append(
			jQuery('<td />').append(btn)));

	var tot = jQuery('<td />', {'html': '0.00'});

	var tf = jQuery('<tfoot />').append(
		jQuery('<tr />').append(
			jQuery('<td />')).append(
			jQuery('<td />', {'html': 'Total:'})).append(
			tot).append(
			jQuery('<td />')));

	this.append(t.append(th).append(tb).append(tf));
	this.change(function(e){
		var udt = 0.0;
		var uds = jQuery('tbody td:nth-child(3)', this);
		for (c = 0; c < uds.length; c++)
			udt += parseFloat(uds[c].innerHTML);
		tot.html(udt.toFixed(2));
		});

	sel.chosen ({width: sel.width() + 'px'});

	sel.change(function(e){
		vso.html((sel.val() * inp.val()).toFixed(2));
		jth.trigger('change');
		});
	inp.change(function(e){
		inp.val(inp.val().replace(',','.'));
		vso.html((sel.val() * inp.val()).toFixed(2));
		jth.trigger('change');
		});
	btn.click(function(e){
		var row = jQuery(this).closest('tr');
		var crw = row.clone();
		crw.insertBefore(row);

		jQuery('td:first', crw).html(jQuery('select', row).find(':selected').text());
		jQuery('td:nth-child(2) input', crw).attr('rel', jQuery('select', row).val()).change(function(f){
			jQuery(this).closest('td').next().html((jQuery(this).val().replace(',', '.') * jQuery(this).attr('rel')).toFixed(2));
			jth.trigger('change');
			});
		jQuery('button i', crw).removeClass('fa-plus').addClass('fa-times');
		jQuery('button', crw).removeClass('btn-success').addClass('btn-danger').click(function(f){
			jQuery(this).closest('tr').remove();
			jth.trigger('change');
			});
		jQuery('td:nth-child(2) input', row).val('0').trigger('change');
		});
	};

jQuery.fn.datepicker.defaults.format = 'dd-mm-yyyy';
jQuery.fn.datepicker.defaults.weekStart = 1;
jQuery.fn.datepicker.defaults.language = 'ro';
jQuery.fn.datepicker.dates['ro'] = {
	days: ['Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata'],
	daysShort: ['Dum', 'Lun', 'Mar', 'Mie', 'Joi', 'Vin', 'Sam'],
	daysMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
	months: ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'],
	monthsShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Iun', 'Iul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	today: 'Astazi',
	clear: 'Sterge'
	};

jQuery.widget('wpcrm.seller', {
	options: {
		url: '',
		title: 'Seller',
		},

	win: null,
	htm: null,
	iid: null,

	_create: function () {
		var jthis = this;
		this.win = jQuery('<div class="ui-widget-seller ui-widget-content ui-corner-all"><h3 class="ui-widget-seller-header ui-widget-header ui-corner-all">' + this.options.title + '</h3></div>', {}).insertAfter(this.element).hide().append(h = this.htm = jQuery('<div class="ui-widget-seller-list"></div>'));
		this.iid = jQuery('<input type="hidden" name="'+this.element[0].name+'-id" value="0" />').insertBefore(this.element);
		this.win[0].style.marginLeft = this.element.position(this.element.parent()).left + 'px';
		this.win[0].style.marginTop = '-6px';

		jQuery.getJSON (this.options.url, function(d){
			jQuery.each(d, function(i,n){
				jthis.htm.append(jQuery('<a rel="'+n.id+'">'+n.name+'</a>').click(function(e){
					jthis.element.val(jQuery(e.target).html());
					jthis.iid.val(jQuery(e.target).attr('rel'));
					jthis.win.hide();
					}));
				});
			});

		this._on(this.element, {click: 'open', blur: 'close'});
		},

	_refresh: function () {
		this._trigger ('change');
		},

	open: function (e) {
		this.win.show();
		},

	close: function (e) {
		//this.win.hide();
		},

	_setOptions: function () {
		this._superApply (arguments);
		this._refresh ();
		},

	_setOptions: function (key, value) {
		this._super (key, value);
		}
	});

jQuery.widget('wpcrm.buyer', {
	options: {
		url: '',
		title: 'Buyer',
		},

	win: null,	/** Main Window */
	add: null,	/** Add Form */
	iid: null,	/** hidden <input /> that actually submits the value. the value should be WP_CRM_Person|WP_CRM_Company-#object_id */
	grp: null,
	cls: null,	/** Company List */
	pls: null,	/** Person List */
	typ: null,	/** Type of Buyer <input /> */

	_create: function () {
		var jthis = this;
		var a = this.element.attr('rel');
		var b = a.split('-');
		this.element.wrap(jQuery('<div />', {'class':'ui-widget-buyer-wrapper'}));
		var c = this.element.closest('.ui-widget-buyer-wrapper');
		this.win = jQuery('<div />', {'class':'ui-widget-buyer-win'}).append(jQuery('<div />', {'class':'ui-widget-buyer-form'}).append(
					jQuery('<div />', {'class': 'ui-widget-buyer-switch'}).append(
						jQuery('<label />', {'text':'Persoana Fizica / Juridica'})
					).append(
						jQuery('<label />', {'class': 'switch pull-right'}).append(
							this.typ = jQuery('<input />', {'class': 'switch-input', 'type': 'checkbox'}).change(function(e){
								if (e.target.checked) jthis.cls.show(),jthis.pls.hide(); else jthis.cls.hide(),jthis.pls.show();
								})
							).append(
							jQuery('<span />', {'class': 'switch-label', 'data-on': 'On', 'data-off': 'Off'})
							).append(
							jQuery('<span />', {'class': 'switch-handle'})
							)
					).append(
						jQuery('<div />', {'class': 'ui-widget-buyer-separator'})
					)
				).append(
					jQuery('<div />', {'class': 'ui-widget-buyer-switch'}).append(
						jQuery('<label />', {'text':'Inregistrare Noua'})
					).append(
						jQuery('<label />', {'class': 'switch pull-right'}).append(
							jQuery('<input />', {'class': 'switch-input', 'type': 'checkbox'}).change(function(e){
								e.target.checked ? jthis.add.show() : jthis.add.hide();
								})
							).append(
							jQuery('<span />', {'class': 'switch-label', 'data-on': 'On', 'data-off': 'Off'})
							).append(
							jQuery('<span />', {'class': 'switch-handle'})
							)
					).append(
						jQuery('<div />', {'class': 'ui-widget-buyer-separator'})
					)
				).append(
					this.add = jQuery('<form />', {'class':'ui-widget-buyer-add'}).append(
					jQuery('<ul />').append(
						'<li><label>Nume</label><input type="text" name="name" value="" class="form-control input-sm"/></li>' +
						'<li><label>CUI/CNP</label><input type="text" name="uin" value="" class="form-control input-sm"/></li>' +
						'<li><label>Reg. Com/</label><input type="text" name="rc" value="" class="form-control input-sm"/></li>' +
						'<li><label>Adresa</label><input type="text" name="address" value="" class="form-control input-sm"/></li>' +
						'<li><label>E-Mail</label><input type="text" name="email" value="" class="form-control input-sm"/></li>' +
						'<li><label>Telefon</label><input type="text" name="phone" value="" class="form-control input-sm"/></li>'
						).append(
						jQuery('<li />').append(
							jQuery('<button />', {'text': 'OK', 'class': 'btn btn-wide btn-primary pull-right'}).click(function(e){
								e.preventDefault();
								jQuery.post (jthis.options.url, jthis.add.serialize() + '&type=' + (jthis.typ[0].checked ? 'company' : 'person'), function(d){
									$wpcrmui._log(JSON.stringify(d));
									var l = jthis.typ[0].checked ? jthis.cls : jthis.pls;
									if (d.type && (d.type == 'object')) {
										l.prepend(
											jQuery('<li />', {'rel':d.class + '-' + d.id, 'text':d.data['name'], 'class':'ui-widget-buyer-list-show'}).click(function(f){
												jthis.element.val(jQuery(f.target).html());
												jthis.iid.val(jQuery(f.target).attr('rel'));
												jthis.win.hide();
												})
											);
										}
									}, 'json');
								jthis.add.hide();
								})
							).append(
							jQuery('<button />', {'text': 'Cancel', 'class': 'btn btn-wide btn-danger'}).click(function(e){
								e.preventDefault();
								jthis.add.hide();
								})
							)
						)
					).hide()
				)
			).append(
				this.cls = jQuery('<ul />', {'class': 'ui-widget-buyer-list ui-widget-buyer-list-companies'}).append(
				jQuery('<li />', {'text': 'loading ...'})
				)
			).append(
				this.pls = jQuery('<ul />', {'class': 'ui-widget-buyer-list ui-widget-buyer-list-persons'}).append(
				jQuery('<li />', {'text': 'loading ...'})
				)
			).hide();
		c.prepend(this.iid = jQuery('<input type="hidden" name="'+this.element[0].name+'-id" value="' + a + '" />'));
		c.append(this.win);
		jQuery('<div />', {'class': 'ui-widgtet-buyer-separator'}).insertAfter(c);

		this.typ[0].checked ? this.pls.hide() : this.cls.hide();

		jQuery.getJSON(this.options.url, {'type':'company'}, function(d){
			jthis.cls.empty();
			jQuery.each(d, function(i,n){
				jthis.cls.append(
					jQuery('<li />', {'rel':n.class + '-' + n.id, 'text':n.name, 'class':'ui-widget-buyer-list-show'}).click(function(e){
						jthis.element.val(jQuery(e.target).html());
						jthis.iid.val(jQuery(e.target).attr('rel'));
						jthis.win.hide();
						})
					);
				});
			});
		jQuery.getJSON(this.options.url, {'type':'person'}, function(d){
			jthis.pls.empty();
			jQuery.each(d, function(i,n){
				jthis.pls.append(
					jQuery('<li />', {'rel':n.class + '-' + n.id, 'text':n.name, 'class':'ui-widget-buyer-list-show'}).click(function(e){
						jthis.element.val(jQuery(e.target).html());
						jthis.iid.val(jQuery(e.target).attr('rel'));
						jthis.win.hide();
						})
					);
				});
			});


		/*
		this.win = jQuery('<div class="ui-widget-buyer ui-widget-content ui-corner-all"><h3 class="ui-widget-buyer-header ui-widget-header ui-corner-all">' + this.options.title + '</h3></div>', {}).insertAfter(this.element).hide().append(this.add = jQuery(
'<form action="" method="post" class="ui-widget-buyer-add"><ul><li><label>Persoana:</label><label for="person" class="radio"><input type="radio" name="type" value="person" id="person" data-toggle="radio" ' + (b[0] == 'person' ? 'checked' : '') + '/> Fizica</label><label class="radio" for="company"><input type="radio" name="type" value="company" id="company" data-toggle="radio" ' + (b[0] == 'company' ? 'checked' : '') + '/> Juridica</label></li><li><label>Nume</label><input class="form-control input-sm" type="text" name="name" value="" /></li><li><label>CUI/CNP</label><input class="form-control input-sm" type="text" name="uin" value="" /></li><li><label>Reg. Com.</label><input class="form-control input-sm" type="text" name="rc" value="" /></li><li><label>Adresa</label><input class="form-control input-sm" type="text" name="address" value="" /></li><li><label>County</label><input class="form-control input-sm" type="text" name="county" value="" /></li><li><label>E-mail</label><input class="form-control input-sm" type="text" name="email" value="" /></li><li><label>Telefon</label><input class="form-control input-sm" type="text" name="phone" value="" /></li><li><button class="btn btn-wide btn-primary" name="add">Adauga</button></li></ul></form>'
)).append(h = this.htm = jQuery('<div class="ui-widget-buyer-list"></div>')).append('<div style="clear: both;"></div>');

		if (b[0]) { var c = document.getElementById(b[0]); if (c) c.checked = true; }

		this.iid = jQuery('<input type="hidden" name="'+this.element[0].name+'-id" value="' + a + '" />').insertBefore(this.element);
		this.win[0].style.marginLeft = this.element.position(this.element.parent()).left + 'px';
		this.win[0].style.marginTop = '-6px';

		this.add.find('[data-toggle="radio"]').radio();

		this.add.find('input[name="type"]').change(function(e){
			jthis.htm.empty();
			jQuery.getJSON(jthis.options.url, {type:jthis.add.find('input[name="type"]:checked').val()}, function(d){
				jQuery.each(d, function(i,n){
					jthis.htm.append(jQuery('<a class="widget-list-show" rel="'+n.id+'">'+n.name+'</a>').click(function(e){
						jthis.element.val(jQuery(e.target).html());
						jthis.iid.val(jthis.add.find('input[name="type"]:checked').val() + '-' + jQuery(e.target).attr('rel'));
						jthis.win.hide();
						}));
					});
				});
			});


		jQuery.getJSON(this.options.url, {type:this.add.find('input[name="type"]:checked').val()}, function(d){
			jQuery.each(d, function(i,n){
				jthis.htm.append(jQuery('<a class="widget-list-show" rel="'+n.id+'">'+n.name+'</a>').click(function(e){
					jthis.element.val(jQuery(e.target).html());
					jthis.iid.val(jthis.add.find('input[name="type"]:checked').val() + '-' + jQuery(e.target).attr('rel'));
					jthis.win.hide();
					}));
				});
			});

		this.add.find('button').click(function(e){
			e.preventDefault();
			jQuery.post (jthis.options.url, jthis.add.serialize(), function(d){
				if (d.type && (d.type == 'object')) {
					jthis.htm.prepend(jQuery('<a class="widget-list-show" rel="' + d.id + '">' + jthis.add.find('input[name="name"]').val() + '</a>').click(function(f){
						jthis.element.val(jQuery(f.target).html());
						jthis.iid.val(jthis.add.find('input[name="type"]:checked').val() + '-' + jQuery(f.target).attr('rel'));
						jthis.win.hide();
						}));
					}
				}, 'json');
			}).before(jQuery('<button class="btn btn-wide btn-danger" name="close">Inchide</button>').click(function(e){
			e.preventDefault();
			jthis.close();
			}));

		*/
		this._on(this.element, {click: 'open', keyup: 'select'});
		},

	_refresh: function () {
		this._trigger ('change');
		},

	open: function (e) {
		this.win.show();
		},

	close: function (e) {
		this.win.hide();
		},

	select: function (e) {
		var s = jQuery(e.target).val().toLowerCase();
		var l = this.typ[0].checked ? this.cls : this.pls;
		if (s.length > this.grp)
			jQuery('.ui-widget-buyer-list-show', l).each(function(i,a){
				if (a.innerHTML.toLowerCase().indexOf(s) != 0)
					a.className = 'ui-widget-buyer-list-hide';
				});
		if (s.length < this.grp)
			jQuery('.ui-widget-buyer-list-hide', l).each(function(i,a){
				if (a.innerHTML.toLowerCase().indexOf(s) == 0)
					a.className = 'ui-widget-buyer-list-show';
				});
		this.grp = s.length;
		},

	_setOptions: function () {
		this._superApply (arguments);
		this._refresh ();
		},

	_setOptions: function (key, value) {
		this._super (key, value);
		}
	});

jQuery.widget('wpcrm.person', {
	options: {
		url: '',
		title: 'Person',
		},

	win: null,
	add: null,
	htm: null,
	iid: null,
	grp: null,

	_create: function () {
		var jthis = this;
		this.win = jQuery('<div class="ui-widget-person ui-widget-content ui-corner-all"><h3 class="ui-widget-person-header ui-widget-header ui-corner-all">' + this.options.title + '</h3></div>', {}).insertAfter(this.element).hide().append(this.add = jQuery('<form action="" method="post" class="ui-widget-person-add"><label>Nume</label><br/><input type="text" name="name" value="" /><label>CNP</label><br /><input type="text" name="uin" value="" /><br /><label>Adresa</label><br /><input type="text" name="address" value="" /><label>County</label><br /><input type="text" name="county" value="" /><label>E-mail</label><br /><input type="text" name="email" value="" /><label>Telefon</label><br /><input type="text" name="phone" value="" /><button name="add">Adauga</button></form>')).append(h = this.htm = jQuery('<div class="ui-widget-person-list"></div>')).append('<div style="clear: both;"></div>');
		this.iid = jQuery('<input type="hidden" name="'+this.element[0].name+'-id" value="'+this.element[0].getAttribute('rel')+'" />').insertBefore(this.element);
		this.win[0].style.marginLeft = this.element.position(this.element.parent()).left + 'px';
		this.win[0].style.marginTop = '-6px';

		jQuery.getJSON(this.options.url, {}, function(d){
			jQuery.each(d, function(i,n){
				jthis.htm.append(jQuery('<a class="widget-list-show" rel="'+n.id+'">'+n.name+'</a>').click(function(e){
					jthis.element.val(jQuery(e.target).html());
					jthis.iid.val(jthis.add.find('input[name="type"]:checked').val() + '-' + jQuery(e.target).attr('rel'));
					jthis.win.hide();
					}));
				});
			});

		this.add.find('button').click(function(e){
			e.preventDefault();
			jQuery.post (jthis.options.url, jthis.add.serialize(), function(d){
				if (d.type && (d.type == 'object')) {
					jthis.htm.prepend(jQuery('<a class="widget-list-show" rel="' + d.id + '">' + jthis.add.find('input[name="name"]').val() + '</a>').click(function(f){
						jthis.element.val(jQuery(f.target).html());
						jthis.iid.val(jthis.add.find('input[name="type"]:checked').val() + '-' + jQuery(f.target).attr('rel'));
						jthis.win.hide();
						}));
					}
				}, 'json');
			});

		this.add.append(jQuery('<button name="close">Inchide</button>').click(function(e){
			e.preventDefault();
			jthis.close();
			}));

		this._on(this.element, {click: 'open', keyup: 'select'});
		},

	_refresh: function () {
		this._trigger ('change');
		},

	open: function (e) {
		this.win.show();
		},

	close: function (e) {
		this.win.hide();
		},

	select: function (e) {
		var s = jQuery(e.target).val().toLowerCase();
		if (s.length > this.grp)
			this.htm.find('a.widget-list-show').each(function(i,a){
				if (a.innerHTML.toLowerCase().indexOf(s) != 0)
					a.className = 'widget-list-hide';
				});
		if (s.length < this.grp)
			this.htm.find('a.widget-list-hide').each(function(i,a){
				if (a.innerHTML.toLowerCase().indexOf(s) == 0)
					a.className = 'widget-list-show';
				});
		this.grp = s.length;
		},

	_setOptions: function () {
		this._superApply (arguments);
		this._refresh ();
		},

	_setOptions: function (key, value) {
		this._super (key, value);
		}
	});

jQuery.widget('wpcrm.product', {
	options: {
		url: '',
		title: 'Product',
		seller: ''
		},

	win: null,	/** The Window Opened */
	add: null,	/** Add New Product */
	asw: null,	/** The Add New Product Switch */
	bkt: null,	/** The Basket Container */
	pid: 0,		/** Current New Product ID */
	pls: null,	/** WP_CRM_Product list */

	_create: function () {
		var jthis = this;
		if (this.element.prev().hasClass('ui-widget-product-basket')) this.bkt = this.element.prev();

		this.element.wrap(jQuery('<div />', {'class':'ui-widget-product-wrapper'}));
		var c = this.element.closest('.ui-widget-product-wrapper');

		this.win = jQuery('<div />', {'class': 'ui-widget-product'}).append(
			jQuery('<div />', {'class': 'ui-widget-product-switch'}).append(
					jQuery('<label />', {'text':'Inregistrare Noua'})
				).append(
					jQuery('<label />', {'class': 'switch pull-right'}).append(
						this.asw = jQuery('<input />', {'class': 'switch-input', 'type': 'checkbox'}).change(function(e){
							e.target.checked ? jthis.add.show() : jthis.add.hide();
							})
						).append(
						jQuery('<span />', {'class': 'switch-label', 'data-on': 'On', 'data-off': 'Off'})
						).append(
						jQuery('<span />', {'class': 'switch-handle'})
						)
				).append(
					jQuery('<div />', {'class': 'ui-widget-separator'})
				)
			
			).append(
			this.add = jQuery('<form />').append(
				jQuery('<ul />').append(
					'<li><label>Cantitate</label><input type="text" name="quantity" value="" class="form-control input-sm"/></li>' +
					'<li><label>Denumire</label><input type="text" name="name" value="" class="form-control input-sm"/></li>' +
					'<li><label>Pret unitar</label><input type="text" name="price" value="" class="form-control input-sm"/></li>' +
					'<li><label>TVA (%)</label><input type="text" name="vat" value="" class="form-control input-sm"/></li>' + ''
					//'<li><label>Pret cu TVA</label><input type="text" name="vatprice" value="" class="form-control input-sm"/></li>'
					).append(
					jQuery('<li />').append(
						jQuery('<button />', {'text': 'OK', 'class': 'btn btn-wide btn-primary pull-right'}).click(function(e){
							e.preventDefault();
							var p = {
								'quantity': parseInt(jQuery('[name="quantity"]', jthis.add).val()),
								'name': String(jQuery('[name="name"]', jthis.add).val()),
								'price': parseFloat(jQuery('[name="price"]', jthis.add).val()),
								'vat': parseFloat(jQuery('[name="vat"]', jthis.add).val()),
								'vatprice': parseFloat(jQuery('[name="vatprice"]', jthis.add).val())
								};

							jthis.bkt.append(
								jQuery('<div />', {'class': 'row no-gutter'}).append(
										jQuery('<div />', {'class': 'col-lg-2'}).append(
											jQuery('<input />', {'type':'text', 'class':'form-control input-sm', 'name':'quantity_n_' + jthis.pid, 'value':p.quantity})
										)
									).append(
										jQuery('<div />', {'class': 'col-lg-5'}).append(
											jQuery('<input />', {'type':'text', 'class':'form-control input-sm pull-left', 'name':'name_n_' + jthis.pid, 'value':p.name})
										)
									).append(
										jQuery('<div />', {'class': 'col-lg-2'}).append(
											jQuery('<input />', {'type':'text', 'class':'form-control input-sm pull-left', 'name':'price_n_' + jthis.pid, 'value':p.price})
										)
									).append(
										jQuery('<div />', {'class': 'col-lg-2'}).append(
											jQuery('<input />', {'type':'text', 'class':'form-control input-sm pull-left', 'name':'vat_n_' + jthis.pid, 'value':p.vat})
										)
									).append(
										jQuery('<div />', {'class': 'col-lg-1'}).append(
											jQuery('<button />', {'type':'text', 'class':'btn btn-danger fa fa-times pull-right', 'rel':'n_' + jthis.pid, 'value':0}).click(function(f){
												f.preventDefault();
												jQuery(f.target).parent().parent().remove();
												})
										)
									)
								);

							jthis.pid ++;
							jthis.add.hide();
							jthis.asw.prop('checked', false);
							})
						).append(
						jQuery('<button />', {'text': 'Cancel', 'class': 'btn btn-wide btn-danger'}).click(function(e){
							e.preventDefault();
							jthis.add.hide();
							})
						)
					)
				).hide()
			).append(
			this.pls = jQuery('<ul />', {'class': 'ui-widget-product-list'}).append('<li>loading ...</li>')
			).append(
			jQuery('<div />', {'class': 'ui-widget-separator'})
			);

		jQuery.getJSON(this.options.url, {type:this.add.find('input[name="type"]:checked').val()}, function(d){
			jthis.pls.empty();
			jQuery.each(d, function(i,n){
				var j;
				jthis.htm.append(j = jQuery('<div>'));
				j.append(jQuery('<input class="form-control input-sm" type="text" value="0"> x <span>' + n.name + '</span>')).append(jQuery('<button class="btn btn-primary btn-sm fui-plus" rel="' +n.id+ '"></button>').click(function(e){
					e.preventDefault();
					jthis.insert (e);
					}));
				});
			});

		if (this.bkt === null) c.prepend(this.bkt = jQuery('<div />', {'class': 'ui-widget-product-basket'}).append(
			jQuery('<div />', {'class':'row no-gutter'}).append(
				jQuery('<div />', {'class':'col-lg-2', 'text':'Qty.'})
				).append(
				jQuery('<div />', {'class':'col-lg-5', 'text':'Produs/Serviciu'})
				).append(
				jQuery('<div />', {'class':'col-lg-2', 'text':'Pret'})
				).append(
				jQuery('<div />', {'class':'col-lg-2', 'text':'TVA'})
				)
			/*).append(		/** In order to compute the total of this invoice *
			jQuery('<div />', {'class':'row no-gutter'}).append(
				jQuery('<div />', {'class':'col-lg-7', 'text':'Total:'})
				).append(
				jQuery('<div />', {'class':'col-lg-2'}).append(
					jQuery('<input />', {'type':'text', 'class':'form-control input-sm pull-left', 'value':0})
					)
				)*/
			));
		c.append(this.win.hide());
		/*
		this.win = jQuery('<div class="ui-widget-product ui-widget-content ui-corner-all"><h3 class="ui-widget-product-header ui-widget-header ui-corner-all">' + this.options.title + '</h3></div>', {}).insertAfter(this.element).hide().append(this.add = jQuery('<form action="" method="post" class="ui-widget-product-add"><label>Cantitate</label><input class="form-control input-sm" type="text" name="quantity" value="0" /><br /><label>Denumire</label><input class="form-control input-sm" type="text" name="name" value="" /><br /><label>Pret unitar</label><input class="form-control input-sm" type="text" name="price" value="0.00" /><br /><label>TVA (%)</label><input class="form-control input-sm" type="text" name="vat" value="0.0" /><br /><label>Pret cu TVA</label><input class="form-control input-sm" type="text" name="pricevat" value="0" /><br /><button class="btn btn-wide btn-primary" name="add">Adauga</button></form>')).append(h = this.htm = jQuery('<div class="ui-widget-product-list"></div>')).append('<div style="clear: both;"></div>');
		var a = this.element.parent().find('.ui-widget-product-basket');
		if (a.length > 0) {
			jQuery('button',a).click(function(e){
				e.preventDefault();
				jQuery(this).parent().remove();
				});
			this.bkt = a;
			}
		else {
			this.bkt = jQuery('<div class="ui-widget-product-basket"></div>').insertBefore(this.element);
			jQuery('<div class="wp-crm-separator"></div>').insertBefore(this.element);
			}
//		this.win[0].style.marginLeft = this.element.position().left + 'px';
		this.win[0].style.marginTop = '-6px';

		jQuery.getJSON(this.options.url, {type:this.add.find('input[name="type"]:checked').val()}, function(d){
			jQuery.each(d, function(i,n){
				var j;
				jthis.htm.append(j = jQuery('<div>'));
				j.append(jQuery('<input class="form-control input-sm" type="text" value="0"> x <span>' + n.name + '</span>')).append(jQuery('<button class="btn btn-primary btn-sm fui-plus" rel="' +n.id+ '"></button>').click(function(e){
					e.preventDefault();
					jthis.insert (e);
					}));
				});
			});

		this.add.find('button').click(function(e){
			var j = jQuery('<div></div>');
			e.preventDefault ();
			e = jQuery (e.target);
			jthis.bkt.append(j);
			j.append(jQuery('<input type="text" class="form-control input-sm" name="quantity_n_' + jthis.pid + '" value="' + jthis.add.find('input[name="quantity"]').val() + '" /><input type="hidden" name="price_n_' + jthis.pid + '" value="' + jthis.add.find('input[name="price"]').val() + ';' + jthis.add.find('input[name="vat"]').val() + '"><input type="hidden" name="name_n_' + jthis.pid + '" value="' + jthis.add.find('input[name="name"]').val() + '"> x <span>' + jthis.add.find('input[name="name"]').val() + '</span>')).append(jQuery('<button class="btn btn-danger btn-sm fui-cross" rel="n_' + jthis.pid + '"></button>').click(function(e){
				e.preventDefault();
				jQuery(this).parent().remove();
				}));
			jthis.pid ++;
			}).before(jQuery('<button class="btn btn-wide btn-danger" name="close">Inchide</button>').click(function(e){
			e.preventDefault();
			jthis.close();
			}));
		*/
		this._on(this.element, {click: 'open'});
		},

	_refresh: function () {
		this._trigger ('change');
		},

	insert: function (d) {
		var j = jQuery('<div></div>');
		d = jQuery(d.target);
		this.bkt.append(j);
		j.append(jQuery('<input type="text" name="quantity_' + d.attr('rel') + '" value="' + d.prev().prev().val() + '" /> x <span>' + d.prev().html() + '</span>')).append(jQuery('<button rel="' + d.attr('rel') + '">-</button>').click(function(e){
			e.preventDefault();
			jQuery(this).parent().remove();
			}));
		d.prev().prev().val(0);
		},

	open: function (e) {
		e.preventDefault();
		this.win.show();
		},

	close: function (e) {
		this.win.hide();
		},

	_setOptions: function () {
		this._superApply (arguments);
		this._refresh ();
		},

	_setOptions: function (key, value) {
		this._super (key, value);
		}
	});

var $wpcrmui = new function () {
	this.win = [];
	this.ttl = null;
	this.txt = null;
	this.rte = [];
	this.rws = null;
	this.dir = null;
	this.srt = null;
	this.grp = 0;
	this.flg = 0;
	this.ldg = null;	// loading window
	this.par = null;	// particle system
	this.nod = null;	// current node
	this.tod = null;	// towards this node
	this.obj = [];		// last object queue
	this.url = [];		// last urls queue
	this.upd = [];		// update queue - holds objects which are updated (tables, selects ...)
	this.bosh = null;	// bosh for xmpp pre-binding
	this.action = '/wp-content/themes/wp-crm/ajax/index.php';
	this.upload = '/wp-content/themes/wp-crm/ajax/upload.php';

	this._get = function (v) {
		var l = window.location.search.slice(1).split('&');
		for (var n = 0; n<l.length; n++) {
			var e = l[n].split('=');
			if (e[0] == v) return e[1];
			}
		return null;
		};

	this._log = function (m, a) {
		var l = jQuery('.wp-crm-ui-logger textarea');
		if (l.length < 1) return;
		l.val ((a ? (l.val() + m) : m) + "\n");
		return;
		};

	this._cookie = function (c, v) {
		if (v) {
			var d = new Date();
			d.setTime(d.getTime() + 1209600000);
			document.cookie = c + '=' + escape (v) + '; expires=' + d.toGMTString() + '; path=/';
			return;
			}
		var n = c + '=';
		var s = document.cookie.split(';');
		for (var i = 0; i<s.length; i++) {
			var t = s[i];
			while (t.charAt(0) == ' ') t = t.substring(1, t.length);
			if (t.indexOf(n) == 0) return unescape(t.substring(n.length, t.length));
			}
		return null;
		};

	this.tswap = function (m, n) {
		if (m == n) return 0;
		/*
		var rm = jQuery(this.rws[m]);
		var rn = jQuery(this.rws[n]);
		var a = rm.clone(true);
		var b = rn.clone(true);
		rn.after(a); rm.after(b);
		rn.remove(); rm.remove();
		this.rws[m] = b;
		this.rws[n] = a;
		*/
		var t = 0;
		if (m > n) { t = m; m = n; n = t; }

		var rm = jQuery(this.rws[m]);
		var rn = jQuery(this.rws[n]);

		if (m+1 == n) rm.before(rn);
		else {
			jQuery(this.rws[m+1]).before(rn);
			jQuery(this.rws[n-1]).after(rm);
			}

		t = this.rws[m];
		this.rws[m] = this.rws[n];
		this.rws[n] = t;
		return 1;
		};

	this.tsort = function (t,p) {
		var i = 0, j = 0;

		if ((this.rws === null) || (!jQuery(this.rws[0]).closest('table').is(t)))
			this.rws = jQuery('tbody tr', t);

		var ta = jQuery(this.rws[0]).find('td');
		ta = jQuery(ta[p]);
		var type = 0;
		if (!type && (Date.parse(ta.text()) > 0)) type = 1; // date
		if (!type && ((parseFloat(ta.text())))) type = 2; // number

		for (i = 0; i<this.rws.length-1; i++)
			for (j = 0; j<this.rws.length-1-i; j++) {
				var a = jQuery(this.rws[j]).find('td');
				var b = jQuery(this.rws[j+1]).find('td');
				switch (type) {
					case 1:
						if (this.dir) {
							if (Date.parse(jQuery(a[p]).text()) > Date.parse(jQuery(b[p]).text()))
								this.tswap (j, j+1);
							}
						else {
							if (Date.parse(jQuery(a[p]).text()) < Date.parse(jQuery(b[p]).text()))
								this.tswap (j, j+1);
							}
						break;
					case 2:
						if (this.dir) {
							if (parseFloat(jQuery(a[p]).text()) > parseFloat(jQuery(b[p]).text()))
								this.tswap (j, j+1);
							}
						else {
							if (parseFloat(jQuery(a[p]).text()) < parseFloat(jQuery(b[p]).text()))
								this.tswap (j, j+1);
							}
						break
					default:
						if (this.dir) {
							if (jQuery(a[p]).text().toLowerCase() > jQuery(b[p]).text().toLowerCase())
								this.tswap (j, j+1);
							}
						else {
							if (jQuery(a[p]).text().toLowerCase() < jQuery(b[p]).text().toLowerCase())
								this.tswap (j, j+1);
							}
					}
				}
		};

	this.rowcol = function (s, r, c) {
		r = parseInt(r);
		c = parseInt(c);
		var a = s.lastIndexOf('_');
		var b = s.lastIndexOf('_', a - 1);

		var cc = s.substr (a + 1, s.length - a);
		var rr = s.substr (b + 1, a - b - 1);

		var p = s.substr (0, b+1);

		if (rr == 'row') {
			cc = parseInt (cc) + r;
			}
		else
		if (rr == 'col') {
			cc = parseInt (cc) + c;
			}
		else {
			cc = parseInt (cc) + c;
			rr = parseInt (rr) + r;
			}
		return p + rr + '_' + cc;
		}

	this.scan = function (w) {
		jQuery('.wp-crm-form-file', w).each(function(i,u){
			u = jQuery(u);
			var h = jQuery('[type="hidden"]', u);
			var f = jQuery('<form/>').addClass('wp-crm-form-hidden');
			var s = jQuery('<input/>', {type: 'file', name: h[0].name});
			var k = $wpcrmui._get('k');
			(f.append(s)).insertAfter(u);
			s.change(function(e){
				jQuery('.wp-crm-form-file-name', u).html(s.val());
				});
			jQuery('.wp-crm-form-file-select', u).click(function(e){
				e.preventDefault();
				s.click();
				});
			jQuery('.wp-crm-form-file-upload', u).click(function(e){
				e.preventDefault();
				var fd = new FormData(f[0]);
				var p = jQuery(this).data('path');

				if (k) fd.append ('k', k);
				if (p) fd.append ('p', p);

				jQuery.ajax({
					url: $wpcrmui.upload,
					type: 'POST',
					xhr: function(){
						var mx = jQuery.ajaxSettings.xhr();
						if (mx.upload) mx.upload.addEventListener('progress', function (f) {
							if (f.lengthComputable) {
								var p = Math.ceil (100 * f.loaded / f.total);
								jQuery ('.wp-crm-form-file-bar', u).width(p + '%');
								}
							}, false);
						return mx;
						},
					success: function(r){
						$wpcrmui._log (r);
						var d = jQuery.parseJSON(r);
						if (d.error) {
							}
						else {
							var n = jQuery('.wp-crm-form-file-name', u);
							var l = (d[0].type == 'jpg' || d[0].type == 'png') ?
								'<a href="' + d[0].url + '" target="_blank"><img src="' + d[0].url + '" width=32 height=32 /></a>'
								:
								'<a href="' + d[0].url + '" target="_blank">' + n.html() + ' <i class="fa fa-external-link"></i></a>';
							n.html(l);
							h.val(r);
							jQuery ('.wp-crm-form-file-bar', u).width('100%');
							}
						},
					data: fd,
					cache: false,
					contentType: false,
					processData: false
					});
				});
			});

		jQuery('.wp-crm-form-select', w).chosen ({width: '100%'});
		jQuery('.wp-crm-form-textarea-rte', w).each(function(n,r){
			$wpcrmui.rte.push(jQuery(r).tinyeditor().rte);
			});
		jQuery('.wp-crm-form-cond', w).each(function(n,i){
			var c = jQuery(i).attr('rel').split('=');
			var d = c[1].split(',');
			if (d.indexOf(jQuery('[name="' + c[0] + '"]', jQuery(i).parent()).val()) < 0) jQuery(i).hide();
			jQuery('[name="' + c[0] + '"]', jQuery(i).parent()).on('change', function(e){
				if (d.indexOf(jQuery(e.target).val()) >= 0) jQuery(i).show(); else jQuery(i).hide();
				});
			});

		/* flat-ui element */
		jQuery('.select-sm', w).selectpicker({size: false, style: 'btn-sm btn-wide btn-primary', menuStyle: 'dropdown-inverse' });
		jQuery('[data-toggle="radio"]', w).radio();
		jQuery('[data-toggle="checkbox"]', w).checkbox();

		/* ui elements */
		//jQuery('.nav-tabs a:first', w).tab('show');
		//jQuery('.nav-tabs a', w).click(function(e){e.preventDefault();jQuery(this).tab('show');});
		jQuery('.wp-crm-form-seller', w).seller({title:'Emitent:',url:'/wp-content/themes/wp-crm/ajax/widget/seller.php'});
		jQuery('.wp-crm-form-buyer', w).buyer({title:'Cumparator',url:'/wp-content/themes/wp-crm/ajax/widget/buyer.php'});
		jQuery('.wp-crm-form-person', w).person({title:'Persoana',url:'/wp-content/themes/wp-crm/ajax/widget/person.php'});
		jQuery('.wp-crm-form-product', w).product({title:'Produse',url:'/wp-content/themes/wp-crm/ajax/widget/product.php'});

		jQuery('.wp-crm-form-tab-add', w).click(function(e){
			var p = jQuery(e.target).parent();
			var r = jQuery(e.target).parent().parent().prev().find('.active');
			var n = jQuery('.tab-pane', jQuery(e.target).parent().parent()).length;
			var q = p.clone().removeClass('active').insertBefore(p);
			q[0].id = 'newtab' + n;
			r.clone().removeClass('active').html('<a href="#newtab' + n + '">' + jQuery('input[type="text"]', p).first().val() + '</a>').insertBefore(r).find('a').on('click', function(f){f.preventDefault();jQuery(this).tab('show')});
			jQuery('input[type="text"]', p).val('');
			jQuery('input[type="text"]', q).each(function(m,i){
				i.name += '-n' + n;
				});
			jQuery('input[type="button"]', q).val('-').removeClass('btn-primary').removeClass('wp-crm-form-tab-add').addClass('btn-danger').click(function(f){
				jQuery('.active', jQuery(f.target).parent().parent().prev()).remove();
				jQuery('li a', jQuery(f.target).parent().parent().prev()).first().tab('show');
				jQuery(f.target).parent().remove();
				});
			});

		jQuery('.wp-crm-form-date', w).datepicker();
		jQuery('.wp-crm-form-time', w).timepicker();
		jQuery('.wp-crm-form-color', w).iris();
		jQuery('.wp-crm-form-matrix-add-row', w).click(function(e){
			e.preventDefault();
			var r = jQuery(e.target).parent().parent().parent().prev().clone().insertBefore(jQuery(e.target).parent().parent().parent());
			r.find('button').parent().remove();
			r.find('input').each(function(i,j){
				j.name = $wpcrmui.rowcol (j.name, 1);
				});
			var b = jQuery('<button class="btn btn-sm btn-danger fa fa-times"></button>').click(function(ee){ ee.preventDefault(); jQuery(ee.target).parent().parent().parent().remove(); });
			r.children('fieldset').append(jQuery('<div>').append(b));
			r.find('.wp-crm-form-date').attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker();
			});
		jQuery('.wp-crm-form-matrix-add-col', w).click(function(e){
			e.preventDefault();
			var k = 0;
			jQuery(e.target).parent().parent().find('div').each(function(i,u){
				if (jQuery(u)[0] === jQuery(e.target).parent()[0]) k = i - 1;
				});
			jQuery(e.target).parent().parent().parent().parent().find('fieldset').each(function(i,u){
				if (jQuery(u).parent().hasClass('wp-crm-form-matrix-row-delete')) {
					var b = jQuery('<button class="btn btn-sm btn-danger fa fa-times"></button>').click(function(ee){
						ee.preventDefault ();

						var kk = 0;
						jQuery(ee.target).parent().parent().find('div').each(function(ii,uu){
							if (jQuery(uu)[0] === jQuery(ee.target).parent()[0]) kk = ii;
							});

						jQuery(ee.target).parent().parent().parent().parent().find('fieldset').each(function(ii,uu){
							jQuery('div', jQuery(uu)).each(function(jj,ll){
								if (jj == kk) jQuery(ll).remove();
								});
							});
						
						});
					jQuery(u).append(jQuery('<div>').append(b));
					return;
					}
				jQuery('div', jQuery(u)).each(function(j,l){
					if (j == k) {
						jQuery(l).clone().insertAfter(jQuery(l)).find('input').each(function(m,n){
							n.name = $wpcrmui.rowcol (n.name, 0, 1);
							});
						}
					});
				});
			});
		jQuery('.wp-crm-form-spread-add-row', w).click(function(e){
			e.preventDefault();
			var t = jQuery(e.target).closest('table');
			var r = jQuery('tr', t).last();
			var c = r.clone ();
			jQuery('input', c).each(function(n,i){
				i.value = '';
				var ni = i.name.indexOf('cell_') + 5;
				var no = i.name.indexOf('_', ni);
				var id = 0;
				if (i.name.substr(ni, no - ni).indexOf('n') > -1) id = parseInt(i.name.substr(ni + 1, no - ni -1)) + 1;
				i.name = i.name.substr(0, ni) + 'n' + id + i.name.substr(no);
				});
			jQuery('.wp-crm-form-spread-del-row', c).click(function(f){
				f.preventDefault();
				if (jQuery('tr', jQuery(f.target).closest('table')).length > 2)
					jQuery(f.target).closest('tr').remove();
				});
			t.append(c);
			});
		jQuery('.wp-crm-form-spread-del-row', w).click(function(e){
			e.preventDefault();
			if (jQuery('tr', jQuery(e.target).closest('table')).length > 2)
				jQuery(e.target).closest('tr').remove();
			});


		jQuery('.wp-crm-form-nested .dd', w).nestable().on('change', function(e){
			jQuery('.wp-crm-form-nested-input', jQuery(this).parent()).val(JSON.stringify(jQuery(e.target).nestable('serialize')));
			});
		jQuery('.wp-crm-form-nested-item-add', w).click(function(e){
			e.preventDefault();

			$wpcrmui.wo (e, $wpcrmui.action, 'add', function(){
				var l = jQuery('.wp-crm-form-nested-list', jQuery(e.target).closest('.wp-crm-form-nested'));

				l.append (
					jQuery('<li />', {'class': 'dd-item', 'data-id':'WP_CRM_Task-1'}).append (
						jQuery('<div />', {'class': 'dd-handle'}).append (
							jQuery('<span />', {'text':'Task #1'})
							).append(
							jQuery('<div />', {'class':'dd-actions pull-right'}).append (
								jQuery('<button />', {'class':'btn btn-sm btn-primary fa fa-edit wp-crm-form-nested-item-edit'}).on('mousedown',function(f){
									f.preventDefault();
									f.stopPropagation();
									})
								).append(
								jQuery('<button />', {'class':'btn btn-sm btn-danger fa fa-times wp-crm-form-nested-item-delete'}).on('mousedown',function(f){
									f.preventDefault();
									f.stopPropagation();

									var u = jQuery(this).closest('ol');
									jQuery(this).closest('li').remove();
									if ((!u.hasClass('wp-crm-form-nested-list')) && jQuery('li', u).length == 0) u.remove();
									})
								)
							)
						)
					);
				});

			});
		jQuery('.wp-crm-form-nested-item-edit', w).on('mousedown', function(e){
			e.preventDefault();
			e.stopPropagation();
			$wpcrmui.wo (e, $wpcrmui.action, 'edit', function(){
				});
			});
		jQuery('.wp-crm-form-nested-item-delete', w).on('mousedown', function(e){
			e.preventDefault();
			e.stopPropagation();

			var u = jQuery(this).closest('ol');
			jQuery(this).closest('li').remove();
			if ((!u.hasClass('wp-crm-form-nested-list')) && jQuery('li', u).length == 0) u.remove();
			});

		
		jQuery('.wp-crm-form-filedrop', w).each(function(i,d){
			d = jQuery(d);
			//var u = '/wp-content/themes/wp-crm/ajax/upload.php';
			var o = {iframe: {url: $wpcrmui.upload}, multiple: true};

			d.filedrop(o).on('fdsend', function(e,fs){
				fs.each(function(f){
					$wpcrmui._log (JSON.stringify(f));
					f.event('sendXHR', function(){
						jQuery ('.wp-crm-form-file-bar', d).width('0');
						});
					f.event('progress', function(current, total){
						var p = current / total * 100;
						jQuery ('.wp-crm-form-file-bar', d).width(p + '%');
						});
					f.event('done', function(xhr){
						});
					f.sendTo($wpcrmui.upload);
					});
				})
			.on('filedone', function(e,f,xhr){
				$wpcrmui._log($wpcrmui.upload + "\n" + 'done:' + xhr.responseText);
				var x = JSON.parse(xhr.responseText)[0];
				var i = jQuery('<li />').html(f.name).prepend(jQuery('<input />').attr({'type': 'checkbox', 'name': jQuery('.wp-crm-form-filedrop-default', d).attr('name') + '[]','value': x.class + '-' + x.id}).prop('checked', true)).append(jQuery('<i />').addClass('fa fa-times').click(function(g){
					jQuery(g.target).closest('li').remove();
					}));
				jQuery('.wp-crm-form-filedrop-list', d).append(i);
				})
			.on('fileerror', function(e,f,xhre,xhr){
				$wpcrmui._log('error:' + xhr.responseText);
				})
			.on('fdiframedone', function(e,xhr){
				$wpcrmui._log('idone:' + xhr.responseText);
				});

			jQuery('.wp-crm-form-filedrop-select', d).click(function(e){e.preventDefault();jQuery('.fd-file', d).click();});
			});

		jQuery('.wp-crm-form-sliderrange').dragslider({
			'range':true,
			'rangeDrag':true,
			'min':0,
			'max':100,
			'change': function (e,ui){
				jQuery(this).slider('values', 1, ui.value + 10);
				}
			});

		jQuery('.wp-crm-form-slider').dragslider({
			'min':1,
			'max':100,
			'change': function (e,ui){
				}
			});
		
		//jQuery('.wp-crm-ude-calculator').udecalc();

		jQuery('.wp-crm-view-actions', w).click(function(e){
			//var u = '/wp-content/themes/wp-crm/ajax/index.php';
			var c = jQuery(e.target).attr('class').split(/\s+/);
			var m = null;
			var g = null;
			var a = null;

			if (jQuery(e.target).hasClass('upd-select')) $wpcrmui.upd.push(jQuery('select', jQuery(e.target).parent()));
			if (jQuery(e.target).hasClass('upd-table')) $wpcrmui.upd.push(jQuery(e.target).closest('.wp-crm-view-table'));
			if (jQuery(e.target).hasClass('upd-tabs')) $wpcrmui.upd.push(jQuery(e.target).closest('.nav-tabs'));

			for (var n = 0; n<c.length; n++) {
				if (c[n].indexOf('wp-crm-view-actions') == 0) continue;
				if (c[n].indexOf('wp-crm-view-group-') == 0) {
					g = c[n].replace('wp-crm-view-group-','');
					m = null;
					continue;
					}
				if (c[n].indexOf('wp-crm-view-') == 0) {
					m = c[n].replace('wp-crm-view-','');
					g = null;
					continue;
					}
				}

			if (m == 'link') {
				if (jQuery(e.target).prop('checked')) {
					var b = jQuery('[data-unique="' + jQuery(e.target).attr('data-unique') + '"]:checked', w);
					if (b.length)
						for (var n = 0; n<b.length; n++)
							jQuery(b[n]).prop('checked', false);
					$wpcrmui.lo (e, $wpcrmui.action, m, function (d) {
						});
					jQuery(e.target).prop('checked', true);
					}
				else {
					jQuery(e.target).prop('checked', false);
					$wpcrmui.lo (e, $wpcrmui.action, 'un' + m, function (d) {
						});
					}
				return 1;
				}
			if (m == 'delete') {
				a = function () {
					jQuery(e.target).closest('tr').animate({'opacity': 0}, 400, 'swing', function(){
						jQuery(this).remove();
						});
					return 1;
					};
				}
			if (m == 'selall') {
				jQuery('tr td', jQuery(e.target).closest('.wp-crm-view-data')).addClass('wp-crm-selected');
				return 1;
				}
			if (m == 'seldel') {
				jQuery('tr td', jQuery(e.target).closest('.wp-crm-view-data')).removeClass('wp-crm-selected');
				return 1;
				}
			if (m == 'loadtemplate') {
				e.preventDefault();
				var f = jQuery(e.target).closest('form');
				var c = jQuery('select.wp-crm-view-template', f);
				$wpcrmui._log ('load');
				jQuery(e.target).attr('rel', c.val()); 
				$wpcrmui.xo (e, $wpcrmui.action, 'template', {'action':'load'}, function (d){
					var f = jQuery(e.target).closest('form');
					var a = jQuery('input[name="subject"]', f).val(d.subject);
					var b = jQuery('textarea[name="message"]', f).val(d.content);
					jQuery.each($wpcrmui.rte,function(n,r){r.load();});
					});
				return 1;
				}
			if (m == 'savetemplate') {
				e.preventDefault();
				var f = jQuery(e.target).closest('form');
				var a = jQuery('input[name="subject"]', f).val();
				var c = jQuery('select.wp-crm-view-template', f);
				jQuery.each($wpcrmui.rte,function(n,r){r.post();});
				var b = jQuery('textarea[name="message"]', f).val();

				jQuery(e.target).attr('rel', c.val ());

				$wpcrmui.xo (e, $wpcrmui.action, 'template', {'action':'save', 'subject':a, 'content':b}, function (d){
					var f = jQuery(e.target).closest('form');
					var c = jQuery('select.wp-crm-view-template', f);
					if (c.val() == 'WP_CRM_Template-0')
						c.append(jQuery('<option />').val(d.object).html(d.subject)).val(d.object).trigger('chosen:updated')
					else {
						jQuery('option:selected',c).text(d.subject);
						c.trigger('chosen:updated');
						}
					});

				return 1;
				}
			if (m == 'deletetemplate') {
				e.preventDefault();
				var f = jQuery(e.target).closest('form');
				var c = jQuery('select.wp-crm-view-template', f);
				$wpcrmui._log('delete');
				jQuery(e.target).attr('rel', c.val()); 
				var y = window.confirm ('Esti sigur ca vrei sa stergi "'+jQuery('option:selected',c).text()+'"?') ? 'yes' : 'no';
				$wpcrmui.xo (e, $wpcrmui.action, 'template', {'action':'delete','confirm':y}, function (d){
					var f = jQuery(e.target).closest('form');
					var c = jQuery('select.wp-crm-view-template', f);
					if (c.val() != 'WP_CRM_Template-0') {
						jQuery('option:selected',c).remove();
						c.trigger('chosen:updated');
						}
					});
				return 1;
				}
			if (m == 'intelligence') {
				$wpcrmui.ldg.css({'top': jQuery(window).scrollTop() + 50}).show();
				jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');

				var _ttl = jQuery('.message_title', jQuery(this).closest('.message')).html();
				jQuery.ajax({
					url: '/wp-content/themes/wp-crm/tools/news.php',
					type: 'GET',
					xhr: function(){
						var mx = jQuery.ajaxSettings.xhr();
						if (mx.upload) mx.upload.addEventListener('progress', function (f) {
							if (f.lengthComputable) {
								var p = Math.ceil (100 * f.loaded / f.total);
								jQuery ('.progress-bar', $wpcrmui.ldg).width(p + '%');
								}
							}, false);
						return mx;
						},
					success: function(d){
						jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');
						$wpcrmui.ldg.hide();

						$wpcrmui._log(d);

						if (d.indexOf('<!-- MODAL_TITLE:') > -1) {
							var a = d.indexOf('<!-- MODAL_TITLE:');
							var b = d.indexOf(" -->", a);
							$wpcrmui.ttl = d.substr(a + 17, b - a - 17);
							}

						$wpcrmui.window(1, null, null, null, 1);
						$wpcrmui.win.last().txt.html(d);
						return;
						},
					data: 'news=' + jQuery(this).attr('rel'),
					cache: false,
					contentType: false,
					processData: false
					});
				return 1;
				}
			if (m == 'scanner') {
				$wpcrmui.ldg.css({'top': jQuery(window).scrollTop() + 50}).show();
				jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');

				var _ttl = jQuery('.message_title', jQuery(this).closest('.message')).html();
				jQuery.ajax({
					url: '/wp-content/themes/wp-crm/tools/scanner-proxy.php',
					type: 'GET',
					xhr: function(){
						var mx = jQuery.ajaxSettings.xhr();
						if (mx.upload) mx.upload.addEventListener('progress', function (f) {
							if (f.lengthComputable) {
								var p = Math.ceil (100 * f.loaded / f.total);
								jQuery ('.progress-bar', $wpcrmui.ldg).width(p + '%');
								}
							}, false);
						return mx;
						},
					success: function(d){
						jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');
						$wpcrmui.ldg.hide();

						$wpcrmui._log(d);
						$wpcrmui.ttl = 'SCANNER';
						var o = JSON.parse(d);
						if (o.indexOf('ERROR') > -1) {
							d = 'Eroare';
							}
						else
							d = 'Succes! Fisierul a fost salvat in REMOTE/repo/st2/Scanner.';

/*						if (d.indexOf('<!-- MODAL_TITLE:') > -1) {
							var a = d.indexOf('<!-- MODAL_TITLE:');
							var b = d.indexOf(" -->", a);
							$wpcrmui.ttl = d.substr(a + 17, b - a - 17);
							}*/

						$wpcrmui.window(1);
						$wpcrmui.win.last().txt.html(d);
						return;
						},
					cache: false,
					contentType: false,
					processData: false
					});
				return 1;
				}
			if (g == 'delete') {
				a = function () {
					jQuery('tr td.wp-crm-selected:first-child').closest('tr').animate({'opacity': 0}, 400, 'swing', function(){
						jQuery(this).remove();
						});
					return 1;
					};
				}

			if (m !== null)
				$wpcrmui.wo (e, $wpcrmui.action, m, a);
			if (g !== null)
				$wpcrmui.go (e, $wpcrmui.action, g, a);
			});


		jQuery('.wp-crm-view-nodeadd', w).click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/add.php';
			$wpcrmui.wo (e, $wpcrmui.action);
			});
		jQuery('.wp-crm-view-nodeedit', w).click(function(e){
			jQuery('.wp-crm-view-tree-menu').hide();
			if ($wpcrmui.nod != null) jQuery(e.target).attr('rel', $wpcrmui.nod.data['oid']);
			var u = '/wp-content/themes/wp-crm/ajax/edit.php';
			$wpcrmui.wo (e, $wpcrmui.action);
			});
		jQuery('.wp-crm-view-nodedelete', w).click(function(e){
			e.preventDefault ();
			jQuery('.wp-crm-view-tree-menu').hide();
			});
		jQuery('.wp-crm-view-nodelink', w).click(function(e){
			e.preventDefault ();
			jQuery('.wp-crm-view-tree-menu').hide();
			var l = $wpcrmui.par.getEdgesFrom($wpcrmui.nod);
			if (l.length > 0) return;
			$wpcrmui.nod.linkto = true;
			$wpcrmui.tod = $wpcrmui.nod;
			});
		jQuery('.wp-crm-view-nodeunlink', w).click(function(e){
			e.preventDefault ();
			jQuery('.wp-crm-view-tree-menu').hide();
			if ($wpcrmui.nod != null) jQuery(e.target).attr('rel', $wpcrmui.nod.data['oid']);

			var u = '/wp-content/themes/wp-crm/ajax/unlink.php';

			$wpcrmui.lo (e, u, function (d){
				var l = $wpcrmui.par.getEdgesFrom($wpcrmui.nod);
				if (l.length == 0) return;
				jQuery.each(l, function(i,v){
					$wpcrmui.par.pruneEdge(v);
					});
				});
			});

		jQuery('.wp-crm-form-inventory-add', w).click(function(e){
			e.preventDefault ();
			/*
			var c = jQuery(e.target).closest('.wp-crm-form-inventory');
			var s = jQuery('select', jQuery(e.target).closest('.row'));
			var n = s[0].name;
			var x = jQuery('input[type="hidden"]', c).length;

			var r = jQuery('<div class="row"></div>');
			c.append(r.append('<div class="col-md-2"><input type="text" name="' + n + '_q' + x + '" value="' + jQuery('input', jQuery(e.target).closest('.row')).val() + '" class="form-control" /></div><div class="col-md-1">x</div><div class="col-md-8"><input type="hidden" value="' + s.val() + '" name="' + n + '_' + x + '" /><span>' + jQuery('select>option:selected', jQuery(e.target).closest('.row')).text() + '</span></div><div class="col-md-1"><button class="form-control wp-crm-form-inventory-delete"><i class="fa fa-minus"></i></button></div>'));

			jQuery('.wp-crm-form-inventory-delete', r).click(function(f){
				jQuery(f.target).closest('.row').remove();
				});
			*/
			});

		jQuery('.wp-crm-form-inventory-delele', w).click(function(e){
			jQuery(e.target).closest('.row').remove();
			});

		jQuery('.wp-crm-view-file-manager', w).each(function(n,f){
			jQuery(f).elfinder({url: jQuery(f).attr('rel')}).elfinder('instance');
			//jQuery(f).elfinder({url:'/wp-content/themes/wp-crm/tools/finder-proxy.php'}).elfinder('instance');
			});
		
		/* flowplayer */
		jQuery('.wp-crm-view-course-player').flowplayer().bind('cuepoint', function(e,a,c){
			var cs = jQuery(e.target).data('cueslides').split(',');
			var is = jQuery('.wp-crm-view-course-slideshow li', jQuery(e.target).closest('.row'));
			for (var i = 0; i<is.length; i++) {
				if (cs[c.index] == jQuery(is[i]).data('slide'))
					jQuery(is[i]).click();
				}
			});
		jQuery('.wp-crm-view-live-player').flowplayer({
			clip: {
				url: 'test',
				live: true,
				provider: 'rtmp'
				},
			plugins: {
				rtmp: {
					url: 'flowplayer.rtmp-3.2.13.swf',
					netConnectionUrl: 'rtmp://gw.einvest.ro/live'
					}
				}
			});
		/* slideshow */
		jQuery('.wp-crm-view-course-slideshow').each(function(n,s){
			jQuery(s).height(jQuery('.wp-crm-view-course-video', jQuery(s).closest('.row')).height());
			var is = jQuery('.wp-crm-view-course-slideshow-item', jQuery(s));
			var nu = jQuery('<ul />');
			for (var i = 0; i < is.length; i++) {
				nu.append(jQuery('<li />', {'class': i == 0 ? 'selected' : '', 'data-slide': jQuery(is[i]).data('slide')}).html(i+1).click(function(e){
					jQuery('li', jQuery(e.target).closest('ul')).removeClass('selected');
					var is = jQuery('.wp-crm-view-course-slideshow-item', jQuery(e.target).addClass('selected').closest('.wp-crm-view-course-slideshow'));
					for (var i = 0; i < is.length; i++)
						if ((i+1) == e.target.innerHTML) jQuery(is[i]).show(); else jQuery(is[i]).hide();
					}));
				if (i>0) jQuery(is[i]).hide();
				}
			jQuery(s).append(nu);
			jQuery(s).append(jQuery('<span />', {'class': 'prev fa fa-angle-left'}).click(function(e){
				jQuery('li.selected', jQuery(e.target).closest('.wp-crm-view-course-slideshow')).prev().click();
				}));
			jQuery(s).append(jQuery('<span />', {'class': 'next fa fa-angle-right'}).click(function(e){
				jQuery('li.selected', jQuery(e.target).closest('.wp-crm-view-course-slideshow')).next().click();
				}));
			jQuery(s).append(jQuery('<span />', {'class': 'view fa fa-arrows-alt'}).click(function(e){
				jQuery('body').append(jQuery('<div />', {'class': 'wp-crm-view-course-slide-fullscreen'}).append(
					jQuery('<div />', {'class': 'wp-crm-view-course-slide-shadow'})
					).append(
					jQuery('.wp-crm-view-course-slideshow-item:visible img', jQuery(e.target).closest('.wp-crm-view-course-slideshow')).clone()
					).append(
					jQuery('<span />', {'class': 'exit fa fa-times'}).click(function(f){
						jQuery(f.target).closest('.wp-crm-view-course-slide-fullscreen').remove();
						})
					));
				}));
			});
		/* quiz */
		jQuery('.wp-crm-view-course-quiz-view').each(function(n,q){
			
			});
		/* calendar */
		jQuery('.calendar-small').each(function(n,c){
			if (jQuery(c).hasClass('fc')) return;
			jQuery.ajax({
				url: '/wp-content/themes/wp-crm/ajax/widget/journal.php',
				type: 'GET',
				xhr: function(){
					var mx = jQuery.ajaxSettings.xhr();
					return mx;
					},
				success: function(d){
					$wpcrmui._log (d);
					jQuery(c).fullCalendar({
						//'lang': 'ro',
						'header': { 'right': 'title', 'left' : 'prev,next,today' },
						'defaultView': 'month',
						'editable': true,
						'events': JSON.parse(d),
						'dayClick': function(date, allDay, e, view){
							$wpcrmui._log ('x');
							},
						'eventClick': function(entry, e, view) {
							jQuery(e.target).attr('rel',entry.id);
							$wpcrmui.wo (e, $wpcrmui.action, 'edit'); //!!!!! ADMIN ONLY !!!!!!
							},
						'eventMouseover': function(entry, e, view) {
							},
						'eventMouseout': function(entry, e, view) {
							}
						});
					return;
					},
				cache: false,
				contentType: false,
				processData: false
				});
			});
		jQuery('*[data-selected="radio"]', w).change(function(e){
			if (this.checked) jQuery(jQuery(this).data('target')).collapse('show'); else jQuery(jQuery(this).data('target')).collapse('hide');
			});
		jQuery('*[data-selected="radio"]:checked', w).each(function(n,i){
			jQuery(jQuery(i).data('target')).collapse('show');
			});
		jQuery('*[data-autofill="ajax"]', w).change(function(e){
			var f = jQuery(jQuery(this).data('target'));
			jQuery.ajax({
				url: '/wp-content/themes/wp-crm/ajax/widget/info.php',
				data: 'object=' + jQuery(this).data('requestobject') + this.options[this.selectedIndex].value,
				type: 'GET',
				success: function(d) {
					var i = JSON.parse(d);
					if (i.error) return;
					if (!i.data) return;
					alert(f[0]);
					for (var k in i.data)
						if (i.data.hasOwnProperty(k)) {
							alert (k + '=' + i.data[k]);
							jQuery('*[data-autofill="' + k + '"]', f).val(i.data[k]);
							}
					},
				cache: false,
				contentType: false,
				processData: false
				});
			});
		
		if (w) {
			jQuery('input[type="text"]', w).keydown(function(f){
				if (f.keyCode == 13) {
					return false;
					}
				});
			jQuery('input[type="submit"]', w).click(function(f){
				//var u = '/wp-content/themes/wp-crm/ajax/index.php';
				$wpcrmui.ldg.css({'top': jQuery(window).scrollTop() + 50}).show();
				jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');

				f.preventDefault();
				jQuery.each($wpcrmui.rte,function(n,r){r.post();});

				jQuery.ajax({
					url: $wpcrmui.action,
					type: 'POST',
					data: 'ajax=' + $wpcrmui.win.last().ajx + '&' + jQuery(this).closest('form').serialize() + '&object=' + $wpcrmui.obj.pop() + '&' + jQuery(this).attr('name')+'=1',
					xhr: function(){
						var mx = jQuery.ajaxSettings.xhr();
						if (mx.upload) mx.upload.addEventListener('progress', function (f) {
							if (f.lengthComputable) {
								var p = Math.ceil (100 * f.loaded / f.total);
								jQuery ('.progress-bar', $wpcrmui.ldg).width(p + '%');
								}
							}, false);
						return mx;
						},
					success: function(d){
						jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');
						$wpcrmui.ldg.hide();

						$wpcrmui._log(d);

						if (d.indexOf('OK') == 0) {
							window.location.reload ();
							if (d.indexOf('REDRAW:') > -1) {
								var a = d.indexOf('REDRAW:');
								var b = d.indexOf("\n", a);
								var c = jQuery(d.substr(a + 7, b - a - 7));
								c.html (d.substr(b+1));
								$wpcrmui.scan (c);
								}
							if (d.indexOf('UPDATE:') > -1) {
								var a = d.indexOf('UPDATE:');
								var c = JSON.parse(d.substr(a + 7));
								if ($wpcrmui.obj.length > 0) {
									jQuery('td>span', jQuery('.wp-crm-view-table input[value="'+$wpcrmui.obj[$wpcrmui.obj.length-1]+'"]').closest('tr')).each(function(i,s){
										s.innerHTML = c[s.className.substr(20)];
										});
									}
								$wpcrmui.window(0);
								}
							if (d.indexOf('ADD:') > -1) {
								var u = $wpcrmui.upd.pop ();

								var a = d.indexOf('ADD:');
								var c = JSON.parse(d.substr(a+4));

								if (u.prop('tagName').toLowerCase() == 'select')
									u.append(jQuery('<option />').val(c.id).html(c.name?c.name:c.title)).val(c.id).trigger('chosen:updated');
								/*
								var t = jQuery('.wp-crm-view-table').last();
								var r = jQuery('tr', t).last().clone();
								var rc = jQuery('.wp-crm-view-row-count', r);
								rc.html(parseInt(rc.html()) + 1);

								for (var k in c) {
									if (c.hasOwnProperty(k)) {
										jQuery('.wp-crm-view-keyname-' + k, r).html(c[k]);
										}
									}
								jQuery('.wp-crm-view-object-id', r).val(c['class'] + '-' + c['id']);
							
								jQuery('tbody', t).append(r);
								*/
								$wpcrmui.window(0);
								}
							if (d.indexOf('VAR:') > -1) {
								var a = d.indexOf('VAR:');
								var c = JSON.parse (d.substr(a+4));
								for (var i = 0; i < c.length; i++) {
									jQuery('.wp-crm-view-var-' + c[i].key).html(c[i].val);
									/** some special {key,value} pairs */
									if ((c[i].key == 'invitations') && (c[i].val < 1)) jQuery('.upd-invitation').prop('disabled', true);
									}
								$wpcrmui.window(0);
								}
							if (d.indexOf('TAB:') > -1) {
								var a = d.indexOf('VAR:');
								var c = JSON.parse (d.substr(a+4));
								}
							return;
							}

						$wpcrmui.window(0);
						return;
						}
					});
				});
			jQuery('.wp-crm-form-button-close', w).click(function(f){
				f.preventDefault();
				$wpcrmui.window(0);
				});
			}
		} // end scan

	this.trr = function (c) {
		var c = jQuery(c).get(0);
		var ctx = c.getContext('2d');
		var pS;

		var rnd = {
			init: function (s) {
				pS = s;
				pS.screenSize(jQuery(c).width(), jQuery(c).height());
				rnd.initMouse();
				},
			redraw: function () {
				ctx.fillStyle = 'white';
				ctx.fillRect (0, 0, jQuery(c).width(), jQuery(c).height());
				pS.eachEdge(function (e, p1, p2) {
					var w = 20;
					ctx.strokeStyle = 'rgba(0,0,0,.333)';
					ctx.lineWidth = 1;
					ctx.beginPath();
					ctx.moveTo(p1.x, p1.y);
					ctx.lineTo(p2.x, p2.y);
					var r = (p2.x - p1.x != 0) ? Math.atan((p2.y - p1.y)/(p2.x - p1.x)) : (p2.y > p1.y ? Math.PI/2 : -Math.PI/2);
					if (p2.x < p1.x) r = Math.PI + r;
					ctx.lineTo(p2.x - w * Math.cos(r + Math.PI/6), p2.y - w * Math.sin(r + Math.PI/6));
					ctx.moveTo(p2.x, p2.y);
					ctx.lineTo(p2.x - w * Math.cos(r - Math.PI/6), p2.y - w * Math.sin(r - Math.PI/6));
					ctx.stroke();
					});
				pS.eachNode(function (n, p) {
					var w = 50;
					var h = 15;
					ctx.strokeStyle = n.linkto ? 'green' : 'red';
					ctx.strokeRect (p.x - w/2, p.y - h/2, w, h);
					ctx.fillStyle = 'black';
					ctx.font = '12px Calibri';
					ctx.textAlign = 'center';
					ctx.fillText (n.data['name'], p.x, p.y + 2, w);
					});
				},
			initMouse: function() {
				var d = null;
				var hnd = {
					clicked: function (e) {
						var p = jQuery(c).offset();
						_mouseP = arbor.Point(e.pageX - p.left, e.pageY - p.top);
						d = pS.nearest(_mouseP);
						if (d && d.node !== null) {
							d.node.fixed = true;
							$wpcrmui.nod = d.node;
							if ($wpcrmui.tod && $wpcrmui.tod.linkto) {
								var u = '/wp-content/themes/wp-crm/ajax/link.php';
								if ($wpcrmui.nod != null) jQuery(e.target).attr('rel', $wpcrmui.tod.data['oid']);
								$wpcrmui.obj[$wpcrmui.obj.length] = d.node.data['oid'];
								$wpcrmui.lo (e, u, function (d) {
									$wpcrmui.par.addEdge ($wpcrmui.tod, $wpcrmui.nod);
									$wpcrmui.tod.linkto = false;
									$wpcrmui.tod = null;
									},
									function (d) {
									$wpcrmui.tod.linkto = false;
									$wpcrmui.tod = null;
									});
								$wpcrmui.obj.pop ();
								}
							d.node.toggle = !d.node.toggle;
							}
						jQuery(c).bind('mousemove', hnd.dragged);
						jQuery(c).bind('mouseup', hnd.dropped);
						return false;
						},
					dragged: function (e) {
						var p = jQuery(c).offset();
						var s = arbor.Point(e.pageX - p.left, e.pageY - p.top);
						if (d && d.node !== null) {
							p = pS.fromScreen(s);
							d.node.p = p;
							}
						return false;
						},
					dropped: function (e) {
						if (d === null || d.node === undefined) return;
						if (d.node !== null) d.node.fixed = false;
						d.node.tempMass = 1000;
						d = null;
						jQuery(c).unbind('mousemove', hnd.dragged);
						jQuery(c).unbind('mouseup', hnd.dropped);
						_mouseP = null;
						},
					rclicked: function (e) {
						e.preventDefault();
						var p = jQuery(c).offset();
						var s = arbor.Point(e.pageX - p.left, e.pageY - p.top);
						var n = pS.nearest(s);
						if (n && n.node !== null) {
							$wpcrmui.nod = n.node;
							}
						jQuery('.wp-crm-view-tree-menu').show().offset({'left': e.pageX - 2, 'top': e.pageY - 2});
						},
					dclicked: function (e) {
						var p = jQuery(c).offset();
						var s = arbor.Point(e.pageX - p.left, e.pageY - p.top);
						$wpcrmui.nod = pS.nearest(s);
						if (n && n.node !== null) {
							$wpcrmui.nod = n.node;
							}
						}
					};
				jQuery(c).mousedown(hnd.clicked);
				jQuery(c).contextmenu(hnd.rclicked);
				jQuery(c).dblclick(hnd.dclicked);
				jQuery('.wp-crm-view-tree-menu').mouseleave(function(e){
					jQuery('.wp-crm-view-tree-menu').hide();
					});
				}
			};
		return rnd;
		};

	this.leaf = function (r, l) {
		if (!l.leaves) return;
		if (l.leaves.legth == 0) return;
		for (var m=0; m<l.leaves.length; m++) {
			r.push([{v:l.leaves[m].data.oid, f:l.leaves[m].data.name}, l.data.oid, l.leaves[m].data.name]);
			$wpcrmui.leaf (r, l.leaves[m]);
			}
		};

	this.tree = function (c, t) {
		var r = [];
		if (t && t.length > 0)
		for (var n=0; n<t.length; n++) {
			r.push([{v:t[n].data.oid, f:t[n].data.name}, '', t[n].data.name]);
			$wpcrmui.leaf (r, t[n]);
			}
		var d = new google.visualization.DataTable();
		d.addColumn('string', 'ID');
		d.addColumn('string', 'Parent');
		d.addColumn('string', 'Value');
		d.addRows(r);
		var c = new google.visualization.OrgChart(jQuery(c)[0]);
		c.draw(d, {allowHtml:true});
		google.visualization.events.addListener(c, 'select', function(e){
			$wpcrmui._log (JSON.stringify(e));
			});
		};
	/*
	this.leaf = function (l) {
		if (!l.leaves) return;
		if (l.leaves.length == 0) return;
		jQuery.each(l.leaves, function(i,v) {
			$wpcrmui.par.addNode(v.id, v.data);
			$wpcrmui.par.addEdge(v.id, l.id);
			$wpcrmui.leaf (v);
			});
		}
	this.tree = function (c, t) {
		if (this.par === null) {
			this.par = arbor.ParticleSystem (600, 2000, 0.5);
			this.par.parameters({gravity: true});
			this.par.renderer = $wpcrmui.trr (c);
			if (t && t.length > 0)
			jQuery.each(t, function(i,v){
				$wpcrmui.par.addNode(v.id, v.data);
				$wpcrmui.leaf (v);
				});
			}
		}
	*/
	
	this.wo = function (e, u, x, v) { // window open, ajax (remember!)
		e.preventDefault();
		e.stopPropagation();

		this.ldg.css({'top': jQuery(window).scrollTop() + 50}).show();
		jQuery ('.progress-bar', this.ldg).width('100%');

		var o = jQuery(e.target).attr('rel');
		
		var ctx = jQuery('input[name="context"]', jQuery(e.target).closest('.buttons'));
		ctx = ctx.length > 0 ? ctx.val () : '';

		if (!(jQuery(e.target).hasClass('wp-crm-view-add') || jQuery(e.target).hasClass('wp-crm-view-invitation')) && o.indexOf('-0') > 0) {
			var g = new Array ();
			jQuery('.wp-crm-selected .wp-crm-view-object-id').each(function(n,i){g.push(n ? jQuery(i).val().replace(/.+-/,'') : jQuery(i).val());});
			o = g.join(',');
			}

		jQuery.ajax({
			url: u,
			type: 'GET',
			xhr: function(){
				var mx = jQuery.ajaxSettings.xhr();
				if (mx.upload) mx.upload.addEventListener('progress', function (f) {
					if (f.lengthComputable) {
						var p = Math.ceil (100 * f.loaded / f.total);
						jQuery ('.progress-bar', $wpcrmui.ldg).width(p + '%');
						}
					}, false);
				return mx;
				},
			success: function(d){
				$wpcrmui.url[$wpcrmui.obj.length] = u;
				$wpcrmui.obj[$wpcrmui.obj.length] = jQuery(e.target).attr('rel');
				jQuery ('.progress-bar', $wpcrmui.ldg).width('100%');
				$wpcrmui.ldg.hide();

				$wpcrmui._log(d);

				if (d.indexOf('<!-- MODAL_TITLE:') > -1) {
					var a = d.indexOf('<!-- MODAL_TITLE:');
					var b = d.indexOf(" -->", a);
					$wpcrmui.ttl = d.substr(a + 17, b - a - 17);
					}

				if (d.indexOf('OK') == 0) {
					if (d.indexOf('REDRAW:') > -1) {
						var a = d.indexOf('REDRAW:');
						var b = d.indexOf("\n", a);
						var c = jQuery(d.substr(a + 7, b - a - 7));
						c.html (d.substr(b+1));
						$wpcrmui.scan (c);
						}
					if (d.indexOf('UPDATE:') > -1) {
						var a = d.indexOf('UPDATE:');
						var b = d.indexOf("\n", a);
						var c = jQuery(d.substr(a + 7, b - a - 7));
						$wpcrmui._log (c);
						}
					return;
					}

				$wpcrmui.window(1, x, jQuery(e.target).attr('rel'));

				$wpcrmui.win.last().txt.html(d);
				$wpcrmui.scan ($wpcrmui.win.last().txt);
				return;
				},
			data: 'k='+$wpcrmui._get('k')+'&ajax='+x+'&object='+o+'&context='+ctx, // this.obj.join(',')
			cache: false,
			contentType: false,
			processData: false
			});
		};

	this.lo = function (e, u, x, s, f) { // load data
		jQuery.ajax({
			url: u,
			type: 'GET',
			success: function(d) {
				$wpcrmui._log(d);
				if (d.indexOf('OK')==0) {
					if(typeof(s)=='function')s(d.substr(2));
					}
				else {
					if(typeof(f)=='function')f(d.substr(2));
					}
				},
			data: 'ajax='+x+'&object='+jQuery(e.target).attr('rel')+'&context='+this.obj.join(','),
			cache: false,
			contentType: false,
			processData: false
			});
		};

	this.xo = function (e, u, x, p, s, f) { // load data
		jQuery.extend (p, {
			'ajax': x,
			'object':jQuery(e.target).attr('rel'),
			'context':this.obj.join(','),
			});
		jQuery.ajax({
			url: u,
			type: 'POST',
			success: function(d) {
				$wpcrmui._log(d);
				d = JSON.parse(d);
				if (d.error) {
					if(typeof(f)=='function')f(d);
					}
				else {
					if(typeof(s)=='function')s(d);
					}
				},
			data: p,
			cache: false,
			processData: true
			});
		};

	this.ready = function () {
		this.dir = 1;

		jQuery('.wp-crm-view-table tbody td').click(function(e){
			jQuery(e.target).parent().find('td').each(function(n,t){
				if (jQuery(t).hasClass('wp-crm-selected')) {
					jQuery(t).removeClass('wp-crm-selected');
					$wpcrmui.grp--;
					}
				else {
					jQuery(t).addClass('wp-crm-selected');
					$wpcrmui.grp++;
					}
				});
			});

		jQuery('.wp-crm-view-table th .column-sort').click(function(e){
			var c = jQuery('th', jQuery(this).closest('tr'));
			jQuery('i', c).removeClass('fa-sort-alpha-desc').removeClass('fa-sort-alpha-asc').addClass('fa-sort');
			var t = jQuery(this).closest('th').text();
			var p = 0;
			while ((p < c.length) && (jQuery(c[p]).text() != t)) p ++;

			if ($wpcrmui.srt == p)
				$wpcrmui.dir = $wpcrmui.dir ? 0 : 1;
			else
				$wpcrmui.srt = p, $wpcrmui.dir = 1;

			$wpcrmui.tsort (jQuery(this).closest('table'), p);

			if ($wpcrmui.dir)
				jQuery(this).removeClass('fa-sort').removeClass('fa-sort-alpha-desc').addClass('fa-sort-alpha-asc');
			else
				jQuery(this).removeClass('fa-sort').removeClass('fa-sort-alpha-asc').addClass('fa-sort-alpha-desc');
			});

		jQuery('.wp-crm-dblclickable').dblclick(function(e){
			var v = jQuery(e.target).text();
			var i = jQuery('<input />', {'type': 'text', 'value': v, 'class': 'wp-crm-inline-input'});
			jQuery(e.target).empty().append(i.focus());
			i.blur(function(f){
				var w = jQuery(f.target).val();
				var o = jQuery('td:first-child input', jQuery(f.target).closest('tr')).val();
				var c = jQuery(f.target).parent().parent()[0].className.split(/\s+/);
				var k = '';
				for (var n = 0; n<c.length; n++) if (c[n].indexOf('wp-crm-view-keyname-') === 0) k = c[n].substr(20);

				jQuery(f.target).parent().html(w);
				if (v != w) { // double click onchange
					var u = '/wp-content/themes/wp-crm/ajax/index.php';

					f.preventDefault();
					jQuery.each($wpcrmui.rte,function(n,r){r.post();});

					jQuery.ajax({
						url: u,
						type: 'POST',
						data: 'ajax=set&object=' + o + '&key=' + k +'&value=' + escape(w),
						success: function (d){
							/**
							 *Check if the returned d is OK or ERROR. Maybe ERROR should be followed by a json explenation of the error.
							 */
							}
						});
					}
				});
			});

		jQuery('.wp-crm-view-excerpt-field button').click(function(e){
			var a = jQuery(e.target).closest('.wp-crm-view-list-excerpts');
			a.animate({width: 300});
			});

		var go = function (e, u, v) {
			e.preventDefault();
			e.stopPropagation();
			$wpcrmui.txt.empty();
			var o = new Array ();
			jQuery('.wp-crm-selected .wp-crm-view-object-id').each(function(n,i){o.push(n ? jQuery(i).val().replace(/.+-/,'') : jQuery(i).val());});
			o = o.join(',');
			jQuery.get (u, 'object='+o, function(d){
				$wpcrmui.txt.html(d);
				$wpcrmui.window(1, function(){
					$wpcrmui.txt.find('input[type="submit"]').click(function(f){
						f.preventDefault();
						jQuery.each($wpcrmui.rte,function(n,r){r.post();});
						var p = jQuery(this).closest('form').serialize() + '&object=' + o + '&' + jQuery(this).attr('name')+'=1';
						jQuery.post (u, p, function(d) {
							if (typeof v == 'function') {
								if (v()) $wpcrmui.window(0);
								}
							else
								$wpcrmui.window(0);
							});
						});
					$wpcrmui.txt.find('.wp-crm-form-button-close').click(function(f){
						f.preventDefault();
						$wpcrmui.window(0);
						});
					});
				});
			};

		jQuery('.wp-crm-view-group-selall').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			$wpcrmui.txt.empty();
			jQuery('.wp-crm-view-table tbody td').each(function(n,t){
				if (!jQuery(t).hasClass('wp-crm-selected')) jQuery(t).addClass('wp-crm-selected');
				});
			$wpcrmui.grp = jQuery('.wp-crm-view-table tbody td').toArray().length;
			});
		jQuery('.wp-crm-view-group-selnone').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			$wpcrmui.txt.empty();
			jQuery('.wp-crm-view-table tbody td').each(function(n,t){
				if (jQuery(t).hasClass('wp-crm-selected')) jQuery(t).removeClass('wp-crm-selected');
				});
			$wpcrmui.grp = 0;
			});

		jQuery('.app-slide-wrapper').each(function(n,w){
			w = jQuery(w);
			var s = jQuery(w.children('.app-slide-container:first-child'));
			var h = parseInt(w[0].clientHeight);
			var l = Math.floor(parseInt(s[0].clientHeight)/h);
			var d = 0;

			w.bind('mousewheel', function(e){
				if (window.console && window.console.log) window.cosole.log(e.wheelDelta);
				});

			w.find('.app-slide-up').click(function(e){
				if (d) return 0;
				d = 1;
				var m = parseInt(s[0].style.top || 0);
				m += h;
				m = m>0 ? 0 : m;
				//s[0].style.top = m + 'px';
				s.animate({'top': m + 'px'}, 200, function(){d=0;});
				$wpcrmui.flg = 0;
				});
			w.find('.app-slide-down').click(function(e){
				if (d) return 0;
				d = 1;
				var m = parseInt(s[0].style.top || 0);
				m -= h;
				m = m<(h * (1 - l)) ? h * (1 - l) : m;
				//s[0].style.top = m + 'px';
				s.animate({'top': m + 'px'}, 200, function(){d=0;});
				$wpcrmui.flg = 0;
				});
			});

		jQuery('body').append('<div class="wp-crm-view-loading"><span class="wp-crm-view-loading-title">Loading ...</span><div class="progress"><div class="progress-bar progress-bar-success"></div></div></div>');

		/* actions */
		this.scan ();
		jQuery('.wp-crm-view-window-close').click(function(e){
			e.preventDefault();
			$wpcrmui.window(0);
			});

		this.ldg = jQuery('.wp-crm-view-loading');
		this.ldg.hide ();

		/* menu */

		jQuery("ul.main-menu").find("a").each(function(){
			if(jQuery(jQuery(this))[0].href==String(window.location).replace(/\/+$/gm, '')){
				jQuery(this).parent().addClass("active");
				jQuery(this).parents("ul").add(this).each(function(){
					jQuery(this).show();
					jQuery(this).prev("a").find(".chevron").removeClass("closed").addClass("opened")
					});
				}
			});

		/* bosh */
		this.bosh = this._cookie ('WP_CRM_BOSH_COOKIE');
		if (this.bosh) this.bosh = JSON.parse (this.bosh);

		if (typeof this.bosh.sid != 'undefined' && typeof Candy != 'undefined') {
			if (!jQuery('#candy').length) {
				jQuery('body').append(jQuery('<div />', {'id': 'candy', 'class': 'floating-chat'}).hide());
				jQuery('.candy-toggle').click(function(e){
					jQuery('#candy').toggle ();
					});
				}
			Candy.init('/http-bind/', {
				core: { debug: false, autojoin: ['lobby@rms.api.acreditate.ro'], disableWindowUnload: true },
				view: { assets: '/wp-content/themes/wp-crm/assets/candy/res/' }});
			Candy.Core.attach (this.bosh.jid, this.bosh.sid, parseInt(this.bosh.rid));
			Candy.View.Pane.Chat.allTabsClosed = function () { return false; };
			window.onbeforeunload = function (){
				jQuery('#chat-tabs li').each(function(n,r){
					var jid = jQuery(r).attr('data-roomjid');
					if (Candy.View.Pane.Chat.rooms[jid].type === 'chat') Candy.View.Pane.Room.close(jid); else Candy.Core.Action.Jabber.Room.Leave(jid);
					});
				$wpcrmui.bosh.rid = parseInt(Candy.Core.getConnection()._proto.rid)+1;
				$wpcrmui._cookie('WP_CRM_BOSH_COOKIE', JSON.stringify($wpcrmui.bosh));
				};
			}

		/* logging */
		jQuery('body').append(
			jQuery('<div />', {'class':'wp-crm-ui-logger'}).data('open', 0).append(
				jQuery('<label />', {'html':'Logger:'})).append(
				jQuery('<span />', {'class':'wp-crm-ui-logger-toggle fa fa-cog'}).click(function(e){
					var w = jQuery(this).parent();
					var s = w.data('open');
					if (s) { w.animate({'width': 16, 'height': 16});  jQuery(this).removeClass('fa-times').addClass('fa-cog'); }
					else { w.animate({'width': 300, 'height': 250}); jQuery(this).removeClass('fa-cog').addClass('fa-times'); }
					w.data('open', s ? 0 : 1);
					})).append(
				jQuery('<textarea />', {'rows': 10})));
		};

	this.progress = function (e){
		if (e.lengthComputable){
			// e.loaded / e.total
			}
		};

	this.window = function (open, ajax, obj, f, wide) {
		if (open) {
			var mwin = {
				'mod': jQuery('<div />', {'class': 'modal fade' + (wide ? ' wide' : '')}),
				'win': jQuery('<div />', {'class': 'modal-dialog'}),
				'cnt': jQuery('<div />', {'class': 'modal-content'}),
				'hdr': jQuery('<div />', {'class':'modal-header'}),
				'cls': jQuery('<button />', {'data-dismiss':'modal','aria-hidden':'true','class':'close','html':'&times;'}),
				'ttl': jQuery('<h4 />', {'class':'modal-title', 'html':$wpcrmui.ttl}),
				'txt': jQuery('<div />', {'class':'modal-body'}),
				'ftr': jQuery('<div />', {'class':'modal-footer'}),
				'ajx': ajax,
				'obj': obj
				};
			jQuery('body').append(mwin.mod.append(mwin.win.append(mwin.cnt.append(mwin.hdr.append(mwin.cls).append(mwin.ttl)).append(mwin.txt).append(mwin.ftr))));

			mwin.mod.modal('show');
			this.win.push(mwin);

			if (typeof(f) == 'function') f();
			}
		else {
			var mwin = this.win.pop();
			mwin.mod.modal('hide');
			}
		};
	};

jQuery(document).ready(function(){
	$wpcrmui.ready ();
	});
