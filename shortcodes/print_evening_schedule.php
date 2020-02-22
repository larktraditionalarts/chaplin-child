<?php
if ( ! function_exists( 'lta_print_evening_schedule_func' ) ) :

function lta_print_evening_schedule_func( $a )
{
	$atts = shortcode_atts( array(
		'camp' => '1',
	), $a );

	// Get all events
	// filter by camp
	// sort by day, time start
	// Get the Pods object and run find()
	$params = array(
		'orderby' => 'event_day.meta_value ASC, event_start.meta_value ASC',
		'where' => 'event_camp.name = "Camp ' . $atts['camp'] . '"',
		'limit' => -1,
    );

	$events = pods( 'events', $params );
	$html = '<table class="event-schedule">' . "\n";

	// loop through them creating a new row
	$current_day = 0;
	$day_lookup = [
		1 => 'Friday',
		2 => 'Saturday',
		3 => 'Sunday',
		4 => 'Monday',
		5 => 'Tuesday',
		6 => 'Wednesday',
		7 => 'Thursday',
		8 => 'Friday',
	];
	while ( $events->fetch() ) {
		$day_to_display = '&nbsp;';
		$row_props = '';
		$new_day = $events->display('event_day');
		
		if ($new_day !== $current_day) {
			$current_day = $new_day;
			$day_to_display = $day_lookup[$current_day];
			$row_props = ' class="start-of-day"';
		}
        
		$location = preg_replace('/\(Camp .*\)/', '', $events->display('event_location'));

		$html .= "<tr$row_props>";
		$html .= '<td>' . $day_to_display . '</td>';
		$html .= '<td>';
		$html .= '<div>' . $events->display( 'event_start' ) . '</div>';
		$html .= '<div>' . $location . '</div>';
		$html .= '</td>';
		$html .= '<td>' . $events->display( 'title' ) . '</td>';
		
		$html .= "</tr>\n";
	}

	$html .= '</table>';

	// return the html
	return $html;
}

add_shortcode('print_evening_schedule', 'lta_print_evening_schedule_func');

endif;
