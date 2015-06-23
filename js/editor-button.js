/*(function() {
    tinymce.PluginManager.add('gavickpro_tc_button', function( editor, url ) {
        editor.addButton( 'gavickpro_tc_button', {
            text: 'My test button',
            icon: false,
            onclick: function() {
                editor.insertContent('Hello World!');
            }
        });
    });
})();*/
(function() {
    tinymce.PluginManager.add('gavickpro_tc_button', function( editor, url ) {
        editor.addButton( 'gavickpro_tc_button', {
            title: 'represent.cc Shortcode Gen.',
			text: 'r.cc',
            icon: false,
            //icon: 'icon dashicons-share-alt2', //dashicons-share-alt2 gavickpro-own-icon
            onclick: function() {
                editor.windowManager.open( {
                    title: 'represent.cc Shortcode Gen.',
                    width: 800,
                    height: 150,
                    body: [{
                        type: 'textbox',
                        id: 'txt__rcc_question',
						name: 'txt__rcc_question',
                        label: 'Question'
                    },
                    {
                        type: 'textbox',
                        id: 'txt__rcc_description',
						name: 'txt__rcc_description',
                        label: 'Description',
                        size: 40,
                        multiline: true,
                        style: 'height: 50px',
                    },
                    {
                        type: 'label',
                        id: 'lbl__rcc_status',
                        multiline: true,
                        style: 'height: 50px',
                        text: ""
                    }],
                    onsubmit: function( e ) {
						if(e.data.txt__rcc_question === '') {
							jQuery('#txt__rcc_question').css('border-color', 'red');
							return false;
						}
						else
						{
							var window_id = this._id;
							var var_ques = jQuery('#txt__rcc_question').val();
							var var_des = jQuery('#txt__rcc_description').val();
							jQuery.post(
								ajaxurl, 
								{
									'action': 'rcc_shortcode_gen',
									'txt__rcc_question': var_ques,
									'txt__rcc_description': var_des
								}, 
								function(response){
									editor.insertContent( '[represent_cc question="'+ var_ques + '"]');
									editor.windowManager.close();
								}
							);
							return false;
						}
                    }
                });
            }
        });
    });
})();