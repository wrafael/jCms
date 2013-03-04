//  http://mbraak.github.com/jqTree/ 

$(function() {

	$('#objecttype #icon').mouseleave(function(e) {
		previewObjecttypeIcon();
	});
	$('#objecttype #icon').keyup(function(e) {
		previewObjecttypeIcon();
	});
	$('#objecttype #icon').keydown(function(e) {
		previewObjecttypeIcon();
	});
	previewObjecttypeIcon();

	$('table.datatable').dataTable({
		"sPaginationType" : "full_numbers",
		"bPaginate" : false
	});

	$('#usertable tr').click(function() {
		var id = $(this).attr('id').replace('user_', '');
		window.location = '/backend/index/useredit/id/' + id;
	});

	$('#roletable tr').click(function() {
		var id = $(this).attr('id').replace('role_', '');
		window.location = '/backend/index/roleedit/id/' + id;
	});
	
	$('input#delete_user').click(function(event){
		if(!confirm('Weet U zeker dat U deze gebruiker wilt verwijderen?')) {
			event.preventDefault();
		}
	});

	$('#tree').tree(
			{
				data : tree,
				autoOpen : 1,
				saveState : 'tree1',
				dragAndDrop : true,
				onCreateLi : function(node, li) {
					li.find('span').attr('id', 'node_' + node.id);

					li.find('div').addClass('jcms_treeitem');

					if (node.code) {
						li.find('span').attr(
								'title',
								node.objecttype + ' - ' + node.id + ' - '
										+ node.code);
					} else {
						li.find('span').attr('title',
								node.objecttype + ' - ' + node.id);
					}

					li.find('span').addClass(node.permissions);
					var icon = '<img class="icon" src="/img/backend/icons/'
							+ node.icon + '" alt="" />';
					if ($(li).hasClass('jqtree-folder')) {
						$(icon).insertAfter($(li).find('div a'));
					} else {
						$(li).find('div').prepend(icon);
					}
				},
				onCanMove : function(node) {
					if (node.permissions.indexOf('M') != -1) {
						return true;
					} else {
						return false;
					}
				},
				onCanMoveTo : function(moved_node, target_node, position) {
					return true; // 
				}
			});
	$('#tree').bind('tree.move', function(event) {
		// TODO give this message a nice dialog
		if (!confirm('Weet U zeker dat U deze pagina wilt verplaatsen?')) {
			event.preventDefault();
		}		
	});

	// binds the new context menu to the tree
	$("#tree").bind("contextmenu", function(event) {
		var $tree = $('#tree');
		var node_id = event.target.id.replace('node_', '');
		var node = $tree.tree('getNodeById', node_id);
		runTreeMenu(node);

		return false;
	});

	$('#tree').bind('tree.move', function(e) {
		changeContentPosition(e);
	});

	$('#treemenu li').live(
			'click',
			function(event) {
				runTreeMenuClick($(this).closest('ul').attr('ref'), $(this)
						.attr('class'), this);
			});

	$('div.jcms_treeitem img').live('click', function(e) {
		$('a', $(this).closest('div')).trigger('click');
	});

	$('div.objecttype_choice').live('click', function() {
		window.location = $(this).attr('ref');
	});

	$('a.change_objecttype_fields').click(function(e) {
		$.colorbox({
			href : $(this).attr('ref')
		});
		return false;
	});

	$('a.add_field_to_objecttype').live(
			'click',
			function() {
				var objecttype_id = $(this).attr('ref').replace('objecttype_',
						'');
				var db_name = $(this).attr('id').replace('add_', '');
				var label = $
						.trim($('input[name=label_' + db_name + ']').val());
				var alt_type = $('#alt_type_' + db_name).val();

				if (label == '') {
					Alert('U hebt geen label naam ingevuld.', 'Foutmelding');
					$('input[name=label_' + db_name + ']').closest('td').css(
							'border-color', 'red');
				} else {
					addObjecttypefieldFromObjecttype(objecttype_id, db_name,
							label, alt_type);
				}
			});
	$('a.remove_field_to_objecttype').live('click', function() {
		var objecttypefield_id = $(this).attr('id').replace('remove_', '');
		removeObjecttypefieldFromObjecttype(objecttypefield_id);
	});

	$('div.addcontent_objecttype_choice').click(function() {
		var id = $(this).attr('id');
		id = id.replace('objecttype_', '');
		$('#objecttype_id').val(id);
		$('#content').submit();
	});
	bindEditor();
	$("tr.datepicker > td > input").datetimepicker({
		dateFormat : "dd-mm-yy",
		timeFormat : "hh:mm"
	}); // datepicker({ dateFormat: "dd-mm-yy h:m:s" });
});

function bindEditor() {
	$('textarea.tinymce')
			.each(
					function() {
						var id = $(this).attr('id');
						$('#' + id)
								.tinymce(
										{
											// Location of TinyMCE script
											script_url : '/tinymce/jscripts/tiny_mce/tiny_mce.js',
											// General options
											theme : "advanced",
											plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
											// Theme options
											theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
											theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
											theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
											theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
											theme_advanced_toolbar_location : "top",
											theme_advanced_toolbar_align : "left",
											theme_advanced_statusbar_location : "bottom",
											theme_advanced_resizing : true,
											external_image_list_url : "/backend/clean/tinymceimages",
											external_link_list_url : "/backend/clean/tinymcelinks",
											relative_urls : false
										});
					});
}

function removeObjecttypefieldFromObjecttype(objecttypefield_id) {
	$.ajax({
		type : 'POST',
		url : '/backend/ajax/removecontenttypefield',
		data : {
			objecttypefield_id : objecttypefield_id
		},
		success : function(data) {
			$.colorbox({
				href : $('#reload_url_objecttypefields').val()
			});
		},
		error : function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		}
	});
}

function addObjecttypefieldFromObjecttype(objecttype_id, db_name, label,
		alt_type) {
	label = $.trim(label);
	if (label != '') {
		$.ajax({
			type : 'POST',
			url : '/backend/ajax/addobjecttypefield',
			data : {
				objecttype_id : objecttype_id,
				db_name : db_name,
				label : label,
				alt_type : alt_type
			},
			success : function(data) {
				$.colorbox({
					href : $('#reload_url_objecttypefields').val()
				});
			},
			error : function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			}
		});
	}
}

function previewObjecttypeIcon() {
	if (!$("#objecttype #icon_preview").length > 0) {
		$('#objecttype #icon').after('<span id="icon_preview"></span>');
	}
	var icon = $('#objecttype #icon').val();
	$('#objecttype #icon_preview').html(
			'<img src="/img/backend/icons/' + icon + '" alt="&nbsp;" />');
}

function runTreeMenu(thisobject) {
	var id = thisobject.id;

	var html = '<ul id="treemenu" ref="' + id + '">';

	var has_something = false;
	if (thisobject.permissions.indexOf('W') != -1) {
		has_something = true;
		html = html + '<li class="edit">Bewerken</li>';
	}
	if (thisobject.permissions.indexOf('A') != -1) {
		has_something = true;
		html = html + '<li class="addchild">Nieuw</li>';
	}
	if (thisobject.permissions.indexOf('X') != -1) {
		if (thisobject.children.length == 0) {
			html = html + '<li class="remove">Verwijderen</li>';
		}
	}
	if (thisobject.permissions.indexOf('R') != -1) {
		if (!has_something)
			html = html + '<li>geen rechten</li>';
	}

	$('#node_' + id, '#tree').qtip({
		content : {
			text : html,
		},
		position : {
			at : 'right center',
			my : 'center left',
			viewport : $(window),
			effect : false
		},
		hide : 'unfocus',
		style : {
			classes : 'ui-tooltip-wiki ui-tooltip-light ui-tooltip-shadow'
		},
		show : {
			ready : true
		},
		events : {
			hide : function(event, api) {
				$('div.ui-tooltip').remove();
			}
		}
	});
}

// run treemenuclick, the object_id is the id of the object being clicked
function runTreeMenuClick(object_id, type) {
	switch (type) {
	case "edit":
		editContent(0, object_id);
		break;
	case "remove":
		RemoveItem('Wilt u dit item verwijderen?', object_id);
		break;
	case "addchild":
		// if we add something we only have a parent_id in the object_id
		editContent(object_id, 0);
		break;
	}
	$('div.ui-tooltip').hide('slow');
	$('div.ui-tooltip').remove();
}

// is called when a node is moved in the tree
function changeContentPosition(info) {
	$.ajax({
		type : 'POST',
		datatype : 'json',
		url : '/backend/ajax/changecontentposition',
		data : {
			position : info.move_info.position,
			moved_id : info.move_info.moved_node.id,
			previous_parent_id : info.move_info.previous_parent.id,
			target_id : info.move_info.target_node.id
		},
		success : function(data, text, xhqr) {
			var json = getHeaderJSON(xhqr);

			if (json.success == false) {
				url = '/backend/ajax/reloadtree';
				$.ajax({
					type : 'POST',
					datatype : 'json',
					url : url,
					success : function(data) {
						$('#tree').tree('loadData', data);
					},
				});
			}
		},
		error : function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		}
	});
}

/**
 * This function is called from the iframe
 * 
 * @param parent_id
 * @param mode
 * @param sort
 * @param icon
 * @param label
 * @param object_id
 * @param permissions
 */
function updateTreeNewContent(parent_id, mode, sort, icon, title, object_id,
		permissions) {
	var node = $('#tree').tree('getNodeById', parseInt(parent_id));
	if (mode == 'add') {
		var data = [ {
			sort : parseInt(sort),
			icon : icon,
			label : title,
			id : parseInt(object_id),
			parent : parseInt(parent_id),
			permissions : permissions
		} ];
		$('#tree').tree('loadData', data, node);
	} else {
		// edit mode, just change title
		$('#node_' + object_id).text(title);
	}
}

function getHeaderJSON(xhr) {
	var json;
	try {
		json = xhr.getResponseHeader('X-Json')
	} catch (e) {
	}

	if (json) {
		var data = eval('(' + json + ')'); // or JSON.parse or whatever you
		// like
		return data
	}
}

function editContent(parent_id, object_id) {
	var url = '/backend/index/contentedit%parent_id%%object_id%';
	var html = '<iframe id="content_iframe" width="750" height="%height%" src="%url%"></iframe>';

	// var width = $('#section').css('width').replace('px','') - 300;
	var height = $('#section').css('height').replace('px', '') - 65;

	html = html.replace('%height%', height);
	// html = html.replace('%width%',width);

	if (parent_id) {
		url = url.replace('%parent_id%', '/parent_id/' + parent_id);
	} else {
		url = url.replace('%parent_id%', '');
	}
	if (object_id) {
		url = url.replace('%object_id%', '/object_id/' + object_id);
	} else {
		url = url.replace('%object_id%', '');
	}

	window.location = url;

	// html = html.replace('%url%',url);
	// $('#section').html(html);
}

// Our Alert method
function Alert(message, title) {
	// Content will consist of the message and an ok button
	var message = $('<p />', {
		text : message
	}), ok = $('<button />', {
		text : 'Ok',
		'class' : 'full'
	});

	dialogue(message.add(ok), title);
}

// Our Prompt method
function Prompt(question, initial, callback) {
	// Content will consist of a question elem and input, with ok/cancel buttons
	var message = $('<p />', {
		text : question
	}), input = $('<input />', {
		val : initial
	}), ok = $('<button />', {
		text : 'Ok',
		click : function() {
			callback(input.val());
		}
	}), cancel = $('<button />', {
		text : 'Cancel',
		click : function() {
			callback(null);
		}
	});

	dialogue(message.add(input).add(ok).add(cancel), 'Attention!');
}

// Our Confirm method
function Confirm(question, callback) {
	// Content will consist of the question and ok/cancel buttons
	var message = $('<p />', {
		text : question
	}), ok = $('<button />', {
		text : 'Ok',
		click : function() {
			callback(true);
		}
	}), cancel = $('<button />', {
		text : 'Cancel',
		click : function() {
			callback(false);
		}
	});

	dialogue(message.add(ok).add(cancel), 'Weet u dit zeker?');
}

// Our Confirm method
function RemoveItem(question, object_id) {
	// Content will consist of the question and ok/cancel buttons
	var message = $('<p />', {
		text : question
	}), ok = $('<button />', {
		text : 'Ok',
		class : 'confirm_box',
		click : function() {
			window.location = '/backend/index/remove/object_id/' + object_id;
		}
	}), cancel = $('<button />', {
		text : 'Cancel',
		class : 'confirm_box',
		click : function() {
		}
	});

	dialogue(message.add(ok).add(cancel), 'Weet u dit zeker?');
}
/*
 * Common dialogue() function that creates our dialogue qTip. We'll use this
 * method to create both our prompt and confirm dialogues as they share very
 * similar styles, but with varying content and titles.
 */
function dialogue(content, title) {
	/*
	 * Since the dialogue isn't really a tooltip as such, we'll use a dummy
	 * out-of-DOM element as our target instead of an actual element like
	 * document.body
	 */
	$('<div />').qtip({
		content : {
			text : content,
			title : title
		},
		position : {
			my : 'center',
			at : 'center', // Center it...
			target : $(window)
		// ... in the window
		},
		show : {
			ready : true, // Show it straight away
			modal : {
				on : true, // Make it modal (darken the rest of the page)...
				blur : false
			// ... but don't close the tooltip when clicked
			}
		},
		hide : false, // We'll hide it maunally so disable hide events
		style : 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue', // Add
																			// a
																			// few
																			// styles
		events : {
			// Hide the tooltip when any buttons in the dialogue are clicked
			render : function(event, api) {
				$('button', api.elements.content).click(api.hide);
			},
			// Destroy the tooltip once it's hidden as we no longer need it!
			hide : function(event, api) {
				api.destroy();
			}
		}
	});
}