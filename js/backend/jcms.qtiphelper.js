// Our Alert method
function Alert(message, title)
{
	// Content will consist of the message and an ok button
	var message = $('<p />', { text: message }), ok = $('<button />', { text: 'Ok', 'class': 'full' });

	dialogue( message.add(ok), title );
}

// Our Prompt method
function Prompt(question, initial, callback)
{
	// Content will consist of a question elem and input, with ok/cancel buttons
	var message = $('<p />', { text: question }),
		input = $('<input />', { val: initial }),
		ok = $('<button />', { 
			text: 'Ok',
			click: function() { callback( input.val() ); }
		}),
		cancel = $('<button />', {
			text: 'Cancel',
			click: function() { callback(null); }
		});

	dialogue( message.add(input).add(ok).add(cancel), 'Attention!' );
}

// Our Confirm method
function Confirm(question, callback)
{
	// Content will consist of the question and ok/cancel buttons
	var message = $('<p />', { text: question }),
		ok = $('<button />', { 
			text: 'Ok',
			click: function() { callback(true); }
		}),
		cancel = $('<button />', { 
			text: 'Cancel',
			click: function() { callback(false); }
		});

	dialogue( message.add(ok).add(cancel), 'Weet u dit zeker?' );
}


//Our Confirm method
function RemoveItem(question, object_id)
{
	// Content will consist of the question and ok/cancel buttons
	var message = $('<p />', { text: question }),
		ok = $('<button />', { 
			text: 'Ok',
			class: 'confirm_box',
			click: function() { removeObject(object_id); }
		}),
		cancel = $('<button />', { 
			text: 'Cancel',
			class: 'confirm_box',
			click: function() { }
		});

	dialogue( message.add(ok).add(cancel), 'Weet u dit zeker?' );
}
/*
* Common dialogue() function that creates our dialogue qTip.
* We'll use this method to create both our prompt and confirm dialogues
* as they share very similar styles, but with varying content and titles.
*/
function dialogue(content, title) {
	/* 
	 * Since the dialogue isn't really a tooltip as such, we'll use a dummy
	 * out-of-DOM element as our target instead of an actual element like document.body
	 */
	$('<div />').qtip(
	{
		content: {
			text: content,
			title: title
		},
		position: {
			my: 'center', at: 'center', // Center it...
			target: $(window) // ... in the window
		},
		show: {
			ready: true, // Show it straight away
			modal: {
				on: true, // Make it modal (darken the rest of the page)...
				blur: false // ... but don't close the tooltip when clicked
			}
		},
		hide: false, // We'll hide it maunally so disable hide events
		style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue', // Add a few styles
		events: {
			// Hide the tooltip when any buttons in the dialogue are clicked
			render: function(event, api) {
				$('button', api.elements.content).click(api.hide);
			},
			// Destroy the tooltip once it's hidden as we no longer need it!
			hide: function(event, api) { api.destroy(); }
		}
	});
}