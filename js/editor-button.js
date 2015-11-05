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
			icon: true,
            icon: 'icon  gavickpro-own-icon', //dashicons-share-alt2 gavickpro-own-icon
            onclick: function() {
                var win = editor.windowManager.open( {
                    title: 'Represent Shortcode Gen.',
                    width: 475,
                    height: 300,
                    body: [{
                        type: 'textbox',
                        id: 'txt__rcc_question',
						name: 'txt__rcc_question',
                        label: 'Question',
						tooltip: 'Type your question',
						onKeyUp: function( ) {
							if(jQuery('#txt__rcc_question').val().length > 3)
							{
								var txt__rcc_question =jQuery('#txt__rcc_question').val();
								jQuery.post(
								ajaxurl, 
									{
										'action': 'rcc_searchapi',
										'txt__rcc_question': txt__rcc_question,
									}, 
									function(response){
										response = JSON.parse(response);
										if(response.success == true)
										{
											var questions = response.obj.question;
											var suggestion = '<ul>';
											jQuery(questions).each(function(i, e) {
												suggestion = suggestion + '<li id="'+e.permalink+'" onclick="callback__search_api_result_click(this.innerHTML, this.id)">' + e.question +'<li>';
                                            });
											suggestion = suggestion + '</ul>';
											jQuery('#rcc_search_result').html(suggestion);
										}
									}
								);
								
							}							
						}
                    },
					{
						type   : 'container',
						name   : 'container',
						label  : ' ',
						html   : '<div id="rcc_search_result"></div>',
						style: 'height: 0px; width:275px;',
                	},
                    {
                        type: 'textbox',
                        id: 'txt__rcc_description',
						name: 'txt__rcc_description',
                        label: 'Description',
                        size: 40,
                        multiline: true,
                        style: 'height: 50px',
						tooltip: 'Please keep the description',
                    },
					{
						type: 'listbox',
						id: 'sel_rcc_ds',
						name: 'sel_rcc_ds',
						label: 'Display Style',
						values: [
							{text: 'Link', value: 'link'},
							{text: 'Box', value: 'box'},
						],
						tooltip: 'Select the display style',
						onselect: function( ) {
							var ele_ds = win.find('#sel_rcc_whatnext');
							if ((ele_ds.value() == 'topic_question' || ele_ds.value() == 'random_question' || ele_ds.value() == 'one_of_my_question') && this.value() == 'box') {
								jQuery('#pmc_notification').html("NB: This will not work if the display style is 'box'");
							}
							if (this.value() == 'link')
							{
								jQuery('#pmc_notification').html("");
							}
							/*var ele_topic = win.find('#txt__rcc_topic');
							var ele_ds = win.find('#sel_rcc_whatnext');
							if (this.value() == 'box' && ele_ds.value() == 'topic_question') {
								ele_topic.disabled(false);
		                        tinyMCE.DOM.removeClass( 'txt__rcc_topic-l','mce-disabled');
							} else {
								ele_topic.disabled(true);
								ele_topic.value('');
								tinyMCE.DOM.addClass( 'txt__rcc_topic-l','mce-disabled');
							}*/
						},
					},
					{
                        type: 'textbox',
                        id: 'txt__rcc_link_text',
						name: 'txt__rcc_link_text',
                        label: 'Link Text',
						tooltip: 'What text should link',
                    },
					{
						type   : 'container',
						name   : 'container',
						label  : ' ',
						html   : '<small style="font-size:10px; font-style:italic;">This will default to be the same as the question unless you set it</small>',
						style: 'height: 15px',
                	},
					{
						type: 'listbox',
						id: 'sel_rcc_whatnext',
						name: 'sel_rcc_whatnext',
						label: 'What shows next?',
						values: [
							{text: 'Nothing', value: 'nothing'},
							/*{text: 'Results', value: 'results'},*/
							{text: 'Random question', value: 'random_question'},
							{text: 'One of my question', value: 'one_of_my_question'},
							{text: 'Topic question', value: 'topic_question'},
						],
						tooltip: 'Select what shows next?',
						onselect: function( ) {
							var ele_topic = win.find('#txt__rcc_topic');
							var ele_ds = win.find('#sel_rcc_ds');
							if ((this.value() == 'topic_question' || this.value() == 'random_question' || this.value() == 'one_of_my_question') && ele_ds.value() == 'box') {
								jQuery('#pmc_notification').html("NB: This will not work if the display style is 'box'");
							}
							if (ele_ds.value() == 'link')
							{
								jQuery('#pmc_notification').html("");	
							}
							/*var ele_topic = win.find('#txt__rcc_topic');
							var ele_ds = win.find('#sel_rcc_ds');
							if (this.value() == 'topic_question' && ele_ds.value() == 'box') {
								ele_topic.disabled(false);
		                        tinyMCE.DOM.removeClass( 'txt__rcc_topic-l','mce-disabled');
							} else {
								ele_topic.disabled(true);
								ele_topic.value('');
								tinyMCE.DOM.addClass( 'txt__rcc_topic-l','mce-disabled');
							}*/
						},
					},
					{
						type   : 'container',
						name   : 'container',
						label  : ' ',
						html   : '<small id="pmc_notification" style="font-size:10px; font-style:italic; color:red;"></small>',
						style: 'height: 15px',
                	},
					/*{
                        type: 'textbox',
                        id: 'txt__rcc_topic',
						name: 'txt__rcc_topic',
                        label: 'Which topic?',
						tooltip: 'Type a topic to choose question',
						disabled:true,
                    },
					{
						type   : 'container',
						name   : 'container',
						label  : ' ',
						html   : '<small style="font-size:10px; font-style:italic;">They\'ll be asked a series of questions from this topic.<br/>(Enable it by setting Display Style = Box and <br/> What shows next? = Topic question)</small>',
						style: 'height: 15px',
                	},*/
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
							var var_ques = e.data.txt__rcc_question;
							var var_des = e.data.txt__rcc_description;
							var var_ds = e.data.sel_rcc_ds;
							var var_link_text = e.data.txt__rcc_link_text;
							var var_whatnext = e.data.sel_rcc_whatnext;
							var var_topic = e.data.txt__rcc_topic;					
							jQuery.post(
								ajaxurl, 
								{
									'action': 'rcc_shortcode_gen',
									'txt__rcc_question': var_ques,
									'txt__rcc_description': var_des
								}, 
								function(response){
									if(response)
									{
										response = JSON.parse(response);
										if(response.success == true)
										{
											var question_id = response.obj.permalink;
										}
										else
										{
											var question_id = '';
										}
									}
									if(question_id == '')
									{
										var question_id = jQuery('#txt__rcc_question').attr('title');
									}
										var shortcode = '[represent_cc question="'+ var_ques + '"';
										shortcode = shortcode + ' type="'+ var_ds + '"';
										if(var_ds == 'box')
										{
											shortcode = shortcode + ' id="'+ question_id + '"';
										}
										if(var_link_text)
										{
											shortcode = shortcode + ' text="'+ var_link_text + '"';
										}
										if(var_whatnext)
										{
											shortcode = shortcode + ' next="'+ var_whatnext + '"';
										}
										if(var_topic)
										{
											shortcode = shortcode + ' topic="'+ var_topic + '"';
										}
										shortcode = shortcode + ']';
									
									editor.insertContent(shortcode);
									editor.windowManager.close();
								}
							);
							return false;
						}
                    },
					onclick: function() {
                    	document.getElementById("rcc_search_result").innerHTML = '';
                    }
                });
            }
        });
    });
})();