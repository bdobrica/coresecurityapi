jQuery.merge (elFinder.prototype._options.commands, [
	'ocr',
	'scan',
	'acls'
	]);
jQuery.merge (elFinder.prototype._options.contextmenu.files, [
	'ocr',
	'scan',
	'|',
	'acls'
	]);
jQuery.extend (elFinder.prototype.i18.en.messages, {
	'cmdocr'	:	'OCR',
	'cmdscan'	:	'Scan',
	'cmdacls'	:	'Permissions'
	});

elFinder.prototype.commands.ocr = function(){
	this.variants = [['docx','DOCx'], ['txt','Text']];
	this.getstate = function(){
		return 0;
		};
	this.exec = function(hashes){
		$wpcrmui.wo (null, $wpcrmui.action, null, null);
		};
	};
elFinder.prototype.commands.scan = function(){
	this.variants = [
		['optimize', 'Optimize'],
		['bnw', 'Black&amp;White'],
		['color', 'Color'],
		['100', '100 dpi'],
		['150', '150 dpi'],
		['200', '200 dpi'],
		['300', '300 dpi'],
		['600', '600 dpi']
		];
	this.exec = function(hashes){
		alert('Rescan');
		alert(hashes);
		};
	this.getstate = function(){
		return 0;
		};
	};
elFinder.prototype.commands.acls = function(){
	this.variants = [
		['default','Default'],
		['group','Groups'],
		['user','Users']
		];
	this.exec = function(hashes){
		alert('ACLs');
		alert(hashes);
		};
	this.getstate = function(){
		return 0;
		};
	};
