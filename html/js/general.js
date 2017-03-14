$(document).ready(function() {
	$('#delete, .delete').click(function(event) {
		if (!confirm('Are you sure you want to delete this, this action cannot be undone?')) {
			event.preventDefault();
		};
	});


	$('.list-filters').hide();

	$('.filter-handle').toggle(function() {
		$('.list-filters').slideDown('fast');
		$('.filter-handle').text('- Hide Filters');
	},
	function() {
		$('.list-filters').slideUp('fast');
		$('.filter-handle').text('+ Show Filters');
	});

	$('tbody.highlight tr').hoverIntent(function() {
		window.trColor = $(this).css('background');
		$(this).css({
			background : '#ff0'
		});
	},
	function() {
		$(this).css({
			background : window.trColor
		});
	});

	$('.highlight tr').click(function() {
		var href = $(this).find('a:first').attr('href');
		window.location = href;
	});

	// Show Messenger content as JS alert!
	var content = $('#messages').text();
	if (content != '') {
		$( '#messages' ).fadeOut( 18000, function() {
		});
			// alert(content);
	};

	// reservation search form
	$('#reservation-form #submit').remove();
	$('#reservation-form select').change(function() {
		var form = $('#reservation-form');
		form.trigger('submit');
	});


	function confirmChecked () {
		if (!$('#index #confirm').attr('checked')) {
			$('#index #checkout-form #submit').hide();
		} else {
			$('#index #checkout-form #submit').show();
		}
	}
	confirmChecked();

	$('#index #confirm').change(confirmChecked);

	if (null != $('#index #type')) {
		var val = $('#quantity').val();
		// console.log(val);
		if (val == 'none') {
			$('#quantity').addClass('empty')
		}
	};

	$('#checkout-form #submit').click(function(e) {
		// e.preventDefault();
		$(this).parent('div').hide();
		$('#checkout-form #please-wait').show();
	});

});
