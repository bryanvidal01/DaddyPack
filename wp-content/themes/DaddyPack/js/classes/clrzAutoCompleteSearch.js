/*
<input name="rechercher" id="rechercher" data-ajaxurl="<?php echo get_permalink(WEBSERVICE_PAGEID); ?>" type="text" />
window.addEvent('domready',function(){
if($('rechercher'))
	new autoCompleteSearch({
		'id_input':'rechercher',
		
	});
});
*/


var autoCompleteSearch = new Class({
	initialize : function(options){
		this.options = options;
		var maclass = this, opt = this.options;
		if($(opt.id_input).get('data-ajaxurl') == null) return;
		this.ajaxurl = $(opt.id_input).get('data-ajaxurl');
		
		this.setElements();
		this.setEvents();
	},
	setElements : function(){
		var maclass = this, opt = this.options;
		
		this.block_autocomplete = new Element('div#ac_suggestion_'+opt.id_input);
		this.block_wrapper = new Element('div#ac_wrapper_'+opt.id_input);
		this.block_wrapper.setStyles({
			'position':'relative',
			'display':'inline-block'
		});
		this.block_autocomplete.setStyles({
			'position':'absolute',
			'top':'100%',
			'left':'0',
		});
		this.block_wrapper.wraps($(opt.id_input));
		this.block_wrapper.adopt(this.block_autocomplete);

	},
	setEvents : function(){
		var opt = this.options, maclass = this;
		$(opt.id_input).addEvent('keyup',function(){
			var req = new Request({
				method: 'get',
				url: maclass.ajaxurl,
				data: {
					'ajax': '1',
					'mode':'search',
					'search':$(opt.id_input).get('value')
				},
				onComplete: function(response) {
					maclass.block_autocomplete.set('html', response);
				}
			}).send();
		});
	}
});