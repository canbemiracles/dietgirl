jQuery.noConflict();

jQuery(function($) {
	$('.hastip').tooltipsy( {ffset: [10, 0]} );
	
	$("input[name='weight']").mask("99?9");
	$("input[name='birthdate']").mask("99/99/9999");
	$("input[name='height-feet']").mask("9");
	$("input[name='height-inches']").mask("9?9");
	$("input[name='goal_weight']").mask("99?9");
	
	if( $('#wpdmi-profile-form input[name="gender"]:checked', '#wpdmi-profile-form').val() === '0' ) {
		$('input[name="lactation"]').attr( 'checked', false);
	}
	
	if( $('select[name="weight_goals"]').val() != 1 ) {
		$('#wpdmi-set-goals').show();
	} else {
		$('#wpdmi-set-goals').hide();
	}
	
	$('input[name="lactation"]').click( function() {
		if( $('input[name="gender"]:checked', '#wpdmi-profile-form').val() === '0' ) {
			alert('If your gender is male you cannot be lactating.');
			$('input[name="lactation"]').attr( 'checked', false);
		}
	});
	
	$('input[name="gender"]').click( function() {
		if( $(this).val() === '0' ) {
			$('input[name="lactation"]').attr( 'checked', false);
		}
	});
	
	$('[name=bmr_calc_method]').click( function() {
		$('.hidden_option').hide();
		$('#bmr_option_' + $(this).val()).show();
	});
	
	// Update fields based on the general unit
	$('[name=general-units]').click( function() {
		
		if( $(this).val() === '0' ) {
			
			// US
			//$( '#wpdmi-height-row' ).show();
			//$( '#wpdmi-goal-rate-row' ).show();
			$('#wpdmi-weight-row .input-format small').text( 'lbs' )
			$('#wpdmi-weight-goal-row .input-format small').text( 'lbs' )
			$('#wpdmi-goal-rate-row .input-format small').text( 'lbs/week' )
			
		} else if( $(this).val() === '1' ) {
			
			//International
			$('#wpdmi-weight-row .input-format small').text( 'kg' )
			$('#wpdmi-weight-goal-row .input-format small').text( 'kg' )
			$('#wpdmi-goal-rate-row .input-format small').text( 'kg/week' )
			
		}
	});
	
	// Update fields based on the date format
	$('[name=date-format]').click( function() {
		
		if( $(this).val() === '0' ) {
			
			// US
			//$( '#wpdmi-birthdate-row' ).show();
			$('#wpdmi-birthdate-row .input-format small').text( 'mm/dd/yyyy' )
			
			
		} else if( $(this).val() === '1' ) {
			
			//International
			$('#wpdmi-birthdate-row .input-format small').text( 'dd/mm/yyyy' )
		}
	});
	
	
});