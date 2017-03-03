(function($){
	$(document).ready(function() {
		
		$('#date').focus(function(){
			var ship = $('#ship').val();
			
			$.ajax({
				url : '/index/calendar',
				data : {
					'ship' : ship
				},
				success : function(data) {
					var div = $('<div id="cal-outer" />');
					div.html(data);
					// create a dialog box
					div.dialog({
						modal : true,
						resizable : false,
						width : 400,
						// height : 400,
						open : setupCalendar,
					});
				}
			});
		});
		
		$('#ship').change(function(){
			$('#date').focus();
		});
		
		function setupCalendar (event, ui) {
			// add functionality to our box
			// console.log(event);s
			$('.has-cruise').click(function() {
				// get the date value
				var val = $(this).text();
				// get the month and year
				var monYear = $('#current-month-year').text().trim();
				monYear = monYear.split('/');
				// put the date in between the month and year
				monYear.splice(1,0,val);
				// glue it back together
				$('#date').val(monYear.join('/'));
				// close the box
				$('#cal-outer').dialog('close');
			});
			
			$('#prev-month').click(updateMonth);
		}
		
		function updateMonth () {
			var monYear = $('#current-month-year').text().trim();
			monYear = monYear.split('/');
			month = parseInt(monYear[0]);
			year = parseInt(monYear[1]);
			
			var ship = $('#ship').val();
			$.ajax({
				url : '/ship/calendar',
				data : {
					'ship' : ship,
					'month' : month += 1,
					'year' : year
				},
				success : function(data) {
					
					
					$( ".ui-dialog" ).dialog( "destroy" );
					
					$('#cal-outer').html(data);
					
					$('#cal-outer').dialog({
						modal : true,
						resizable : false,
						width : 400,
						// height : 400,
						open : setupCalendar,
					});
					
				}
			})
		}
	});
})(jQuery);