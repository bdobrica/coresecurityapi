jQuery.fn.tinyeditor = function () {
	var i = Math.floor(10000 * Math.random());
	if (!this.attr('id')) this.attr('id', 'wp-crm-rte-' + i);
	if (!this.attr('id')) return;
	jQuery('<div></div>').css('clear','both').insertAfter(this);

	this.rte = new TINY.editor.edit('e'+i,{
		id: this.attr('id'),
		width: this.width(),
		height: this.height(),
		cssclass: 'tinyeditor',
		controlclass: 'tinyeditor-control',
		rowclass: 'tinyeditor-header',
		dividerclass: 'tinyeditor-divider',
		controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
			'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
			'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
			'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'],
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

	win: null,
	add: null,
	htm: null,
	iid: null,
	grp: null,

	_create: function () {
		var jthis = this;
		var a = this.element.attr('rel');
		var b = a.split('-');
		this.win = jQuery('<div class="ui-widget-buyer ui-widget-content ui-corner-all"><h3 class="ui-widget-buyer-header ui-widget-header ui-corner-all">' + this.options.title + '</h3></div>', {}).insertAfter(this.element).hide().append(this.add = jQuery('<form action="" method="post" class="ui-widget-buyer-add"><label>Persoana:</label><label for="person" class="radio"><input type="radio" name="type" value="person" id="person" data-toggle="radio" ' + (b[0] == 'person' ? 'checked' : '') + '/> Fizica</label><label class="radio" for="company"><input type="radio" name="type" value="company" id="company" data-toggle="radio" ' + (b[0] == 'company' ? 'checked' : '') + '/> Juridica</label><br /><label>Nume</label><br/><input class="form-control input-sm" type="text" name="name" value="" /><label>CUI/CNP</label><br /><input class="form-control input-sm" type="text" name="uin" value="" /><label>Reg. Com.</label><br /><input class="form-control input-sm" type="text" name="rc" value="" /><label>Adresa</label><br /><input class="form-control input-sm" type="text" name="address" value="" /><label>County</label><br /><input class="form-control input-sm" type="text" name="county" value="" /><label>E-mail</label><br /><input class="form-control input-sm" type="text" name="email" value="" /><label>Telefon</label><br /><input class="form-control input-sm" type="text" name="phone" value="" /><button class="btn btn-wide btn-primary" name="add">Adauga</button></form>')).append(h = this.htm = jQuery('<div class="ui-widget-buyer-list"></div>')).append('<div style="clear: both;"></div>');

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
		},

	win: null,
	add: null,
	bkt: null,
	htm: null,
	pid: 0,

	_create: function () {
		var jthis = this;
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
	this.sha = null;
	this.win = null;
	this.ttl = null;
	this.txt = null;
	this.rte = null;
	this.rws = null;
	this.dir = null;
	this.srt = null;
	this.grp = 0;
	this.flg = 0;

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

	this.rowcol = function (s, r = 0, c = 0) {
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

	this.ready = function () {
		this.rws = jQuery('.wp-crm-view-table tbody tr');
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

		jQuery('.wp-crm-view-table th img').click(function(e){
			var c = jQuery(e.target).parent().parent().find('th');
			var i = 0;
			var p = 0;
			for (i = 0; i<c.length; i++)
				if (jQuery(c[i]).text() == jQuery(e.target).parent().text()) p = i;
			if ($wpcrmui.srt == p) $wpcrmui.dir = $wpcrmui.dir ? 0 : 1;
			else { $wpcrmui.srt = p; $wpcrmui.dir = 1; }
			$wpcrmui.tsort (jQuery(e.target).closest('table'), p);
			if ($wpcrmui.dir) e.target.src = e.target.src.replace('down','up'); else e.target.src = e.target.src.replace('up','down');
			});
//.mouseleave(function(e){
//			jQuery(e.target).parent().find('td').css({'background-color': '#fff'});
//			});

		jQuery('.wp-crm-view-excerpt-field button').click(function(e){
			var a = jQuery(e.target).closest('.wp-crm-view-list-excerpts');
			a.animate({width: 300});
			});

		var wo = function (e, u, v) {
			e.preventDefault();
			e.stopPropagation();
			$wpcrmui.txt.empty();
			jQuery.get (u, 'object='+jQuery(e.target).attr('rel'), function(d){
				$wpcrmui.txt.html(d);
				$wpcrmui.window(1, function(){
					$wpcrmui.txt.find('input[type="text"]').keydown(function(f){
						if (f.keyCode == 13) {
							return false;
							}
						});
					$wpcrmui.txt.find('input[type="submit"]').click(function(f){
						f.preventDefault();
						jQuery.each($wpcrmui.rte,function(n,r){r.post();});
						var p = jQuery(this).closest('form').serialize() + '&object=' + jQuery(e.target).attr('rel') + '&' + jQuery(this).attr('name')+'=1';
						jQuery.post (u, p, function(d) {
							alert(d);
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
	/*
	 * Actions:
	 */
		jQuery('.wp-crm-view-add').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/add.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-edit').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/edit.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-view').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/view.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-group-view').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/view.php';
			go (e, u);
			});
		jQuery('.wp-crm-view-contact').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/contact.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-memo').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/memo.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-pay').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/pay.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-people').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/people.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-price').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/price.php';
			wo (e, u);
			});
		jQuery('.wp-crm-view-delete').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/delete.php';
			var v = function () {
				jQuery(e.target).closest('tr').animate({'opacity': 0}, 400, 'swing', function(){
					jQuery(this).remove();
					});
				return 1;
				};
			wo (e, u, v);
			});
		jQuery('.wp-crm-view-group-delete').click(function(e){
			var u = '/wp-content/themes/wp-crm/ajax/delete.php';
			var v = function () {
				jQuery('tr td.wp-crm-selected:first-child').closest('tr').animate({'opacity': 0}, 400, 'swing', function(){
					jQuery(this).remove();
					});
				return 1;
				};
			go (e, u, v);
			});

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
				alert(e.wheelDelta);
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

		jQuery('body').append('<div class="wp-crm-view-shadow"></div><div class="alert wp-crm-view-window"><div class="wp-crm-view-window-header"><button class="close fui-cross wp-crm-view-window-close"></button></div><div class="wp-crm-view-window-content"></div></div>');
		jQuery('.wp-crm-view-window-close').click(function(e){
			e.preventDefault();
			$wpcrmui.window(0);
			});

		this.sha = jQuery('.wp-crm-view-shadow');
		this.win = jQuery('.wp-crm-view-window');
		this.ttl = jQuery('.wp-crm-view-window-header');
		this.txt = jQuery('.wp-crm-view-window-content');
		};

	this.progress = function (e){
		if (e.lengthComputable){
			// e.loaded / e.total
			}
		};

	this.window = function (o,f) {
		if (o) {
//			jQuery('html, body').animate({'scrollTop': 0});
			this.sha.css({'opacity': 0, 'display': 'block', 'height': jQuery(document).height()}).animate({'opacity': .8}, function(){
				$wpcrmui.win.css({'top': jQuery('html, body').scrollTop() + 50, 'display': 'block'}).animate({'height': 300}, 400, 'swing', function(){
					$wpcrmui.win.css({'height':'auto'});

					/* flat-ui element */
					$wpcrmui.txt.find('.nav-tabs a').on('click', function(e){e.preventDefault();jQuery(this).tab('show')});
					$wpcrmui.txt.find(".select-sm").selectpicker({size: false, style: 'btn-sm btn-wide btn-primary', menuStyle: 'dropdown-inverse' });
					$wpcrmui.txt.find('[data-toggle="radio"]').radio();
					$wpcrmui.txt.find('[data-toggle="checkbox"]').checkbox();
					/* ui elements */
					$wpcrmui.txt.find('.wp-crm-form-seller').seller({title:'Emitent:',url:'/wp-content/themes/wp-crm/ajax/widget/seller.php'});
					$wpcrmui.txt.find('.wp-crm-form-buyer').buyer({title:'Cumparator',url:'/wp-content/themes/wp-crm/ajax/widget/buyer.php'});
					$wpcrmui.txt.find('.wp-crm-form-person').person({title:'Persoana',url:'/wp-content/themes/wp-crm/ajax/widget/person.php'});
					$wpcrmui.txt.find('.wp-crm-form-product').product({title:'Produse',url:'/wp-content/themes/wp-crm/ajax/widget/product.php'});

					$wpcrmui.txt.find('.wp-crm-form-date').datepicker({dateFormat: 'dd-mm-yy', dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'], firstDay: 1, monthNames:['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie']});
					$wpcrmui.txt.find('.wp-crm-form-matrix-add-row').click(function(e){
						e.preventDefault();
						var r = jQuery(e.target).parent().parent().parent().prev().clone().insertBefore(jQuery(e.target).parent().parent().parent());
						r.find('button').parent().remove();
						r.find('input').each(function(i,j){
							j.name = $wpcrmui.rowcol (j.name, 1);
							});
						var b = jQuery('<button class="btn btn-sm btn-danger fui-cross"></button>').click(function(ee){ ee.preventDefault(); jQuery(ee.target).parent().parent().parent().remove(); });
						r.children('ul').append(jQuery('<li>').append(b));
						r.find('.wp-crm-form-date').attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({dateFormat: 'dd-mm-yy', dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'], firstDay: 1, monthNames:['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie']});
						});
					$wpcrmui.txt.find('.wp-crm-form-matrix-add-col').click(function(e){
						e.preventDefault();
						var k = 0;
						jQuery(e.target).parent().parent().find('li').each(function(i,u){
							if (jQuery(u)[0] === jQuery(e.target).parent()[0]) k = i - 1;
							});
						jQuery(e.target).parent().parent().parent().parent().find('ul').each(function(i,u){
							if (jQuery(u).parent().hasClass('wp-crm-form-matrix-row-delete')) {
								var b = jQuery('<button class="btn btn-sm btn-danger fui-cross"></button>').click(function(ee){
									ee.preventDefault ();

									var kk = 0;
									jQuery(ee.target).parent().parent().find('li').each(function(ii,uu){
										if (jQuery(uu)[0] === jQuery(ee.target).parent()[0]) kk = ii;
										});

									jQuery(ee.target).parent().parent().parent().parent().find('ul').each(function(ii,uu){
										jQuery('li', jQuery(uu)).each(function(jj,ll){
											if (jj == kk) jQuery(ll).remove();
											});
										});
									
									});
								jQuery(u).append(jQuery('<li>').append(b));
								return;
								}
							jQuery('li', jQuery(u)).each(function(j,l){
								if (j == k) {
									jQuery(l).clone().insertAfter(jQuery(l)).find('input').each(function(m,n){
										n.name = $wpcrmui.rowcol (n.name, 0, 1);
										});
									}
								});
							});
						});

					
					$wpcrmui.txt.find('.wp-crm-form-filedrop').each(function(i,u){
						u = jQuery(u);
						u.filedrop({
							paramname: u.find('input').last().attr('name'),
							maxfiles: 1,
							maxfilesize: 10,
							url: '/wp-content/themes/wp-crm/ajax/upload.php',
							uploadFinished: function(i,file,response){
								u.find('input').value = response.url;
								u.find('.wp-crm-form-filedrop-progressbar').css('width', '100%');
								},
							error: function(err,file){
								switch(err){
									case 'BrowserNotSupported':
										u.find('.wp-crm-form-filedrop-message').html('Browserul nu suporta HTML5');
										break;
									case 'TooManyFiles':
										break;
									case 'FileTooLarge':
										break;
									}
								},
							beforeEach: function(file){
								u.find('.wp-crm-form-filedrop-progressbar').css('width', '0');
								},
							uploadStarted: function(i,file,len){
								var r = new FileReader();
								r.onload = function(g){
									u.find('img').attr('src', g.target.result);
									};
								r.readAsDataURL(file);
								},
							progressUpdated: function(i,file,progress){
								u.find('.wp-crm-form-filedrop-progressbar').css('width', progress + '%');
								}
							});
						});

					$wpcrmui.txt.find('.wp-crm-form-file').each(function(i,u){
						u = jQuery(u);
						var h = jQuery('[type="hidden"]', u);
						var f = jQuery('<form/>').addClass('wp-crm-form-hidden');
						var s = jQuery('<input/>', {type: 'file', name: h[0].name});
						$wpcrmui.txt.append (f.append(s));
						jQuery('.wp-crm-form-file-select', u).click(function(e){
							e.preventDefault();
							s.click();
							});
						jQuery('.wp-crm-form-file-upload', u).click(function(e){
							e.preventDefault();
							jQuery.ajax({
								url: '/wp-content/themes/wp-crm/ajax/upload.php',
								type: 'POST',
								xhr: function(){
									var mx = jQuery.ajaxSettings.xhr();
									if (mx.upload) mx.upload.addEventListener('progress', $wpcrmui.progress, false);
									return mx;
									},
								success: function(r){
									var d = jQuery.parseJSON(r);
									if (d.error) alert ('error!');
									else {
										jQuery('.wp-crm-form-file-view', $wpcrmui.txt).empty().append(jQuery('<img />', {src: d[0].url}));
										jQuery('.wp-crm-form-file input[type="hidden"]', $wpcrmui.txt).val(d[0].url);
										}
									},
								data: new FormData (f[0]),
								cache: false,
								contentType: false,
								processData: false
								});
							});
						});

					$wpcrmui.rte = [];
					$wpcrmui.txt.find('.wp-crm-form-textarea').each(function(n,r){
						$wpcrmui.rte.push(jQuery(r).tinyeditor().rte);
						});
					$wpcrmui.txt.find('.tab-pane').each(function(n,p){if (n>0) jQuery(p).removeClass('active');});

					if (typeof(f) == 'function') f();
					});
				});
			}
		else {
			this.win.animate({'height': 0}, 400, 'swing', function(){
				$wpcrmui.win.css({'display': 'none'});
				$wpcrmui.sha.animate({'opacity': 0}, 400, 'swing', function(){
					$wpcrmui.sha.css({'display': 'none'});
					if (typeof(f) == 'function') f();
					});
				});
			}
		};
	};

jQuery(document).ready(function(){
	$wpcrmui.ready ();
	});
