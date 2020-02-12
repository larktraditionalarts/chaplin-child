<?php
if ( ! function_exists( 'wp_terms_checklist' ) ) {
    include ABSPATH . 'wp-admin/includes/template.php';
}

class LTA_Walker_Category_Checklist extends Walker {
    public $tree_type = 'category';
    public $db_fields = array(
        'parent' => 'parent',
        'id'     => 'term_id',
    ); //TODO: decouple this
 
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker:start_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "$indent<ul class='children'>\n";
    }
 
    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent  = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }
 
    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.5.1
     *
     * @param string $output   Used to append additional content (passed by reference).
     * @param object $category The current term object.
     * @param int    $depth    Depth of the term in reference to parents. Default 0.
     * @param array  $args     An array of arguments. @see wp_terms_checklist()
     * @param int    $id       ID of the current term.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }
 
        if ( $taxonomy == 'category' ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }
 
        $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
        $class                = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';
 
        $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];
            $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' .
				' /> ' .
                /** This filter is documented in wp-includes/category-template.php */
                esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</label>';
    }
 
    public function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }
}

add_shortcode('checkfilter', 'checkfilter_func');
function checkfilter_func( $a )
{
	$atts = shortcode_atts( array(
		'title' => 'Genre',
		'type' => 'genre',
	), $a );

	return do_shortcode(
		'[expand title="' . $atts['title'] . '"]' . 
			'<ul class="workshop-filter">' .
    		wp_terms_checklist(0, ['taxonomy' => $atts['type'], 'echo' => false, 'walker' => new LTA_Walker_Category_Checklist() ]) .
			'</ul>' .
		'[/expand]<br />'
	);
}

add_shortcode('print_evening_schedule', 'print_evening_schedule_func');
function print_evening_schedule_func( $a )
{
	$atts = shortcode_atts( array(
		'camp' => '1',
	), $a );

	// Get all events
	// filter by camp
	// sort by day, time start
	// Get the Pods object and run find()
	$params = array(
		'orderby' => 'event_day.meta_value ASC, event_start.meta_value DESC',
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
		$html .= '<td>' . $events->display( 'event_start' ) . '</td>';
		$html .= '<td>' . $location . '</td>';
		$html .= '<td>' . $events->display( 'title' ) . '</td>';
		
		$html .= "</tr>\n";
	}

	$html .= '</table>';

	// return the html
	return $html;
}

add_shortcode('print_workshop_schedule', 'print_workshop_schedule_func');
function print_workshop_schedule_func( $a )
{

    $clean_attribute = function ($str) {
        $str = str_replace("\n", '', $str);
        $str = str_replace("'", '&apos;', $str);

        return $str;
    };
	
	$format_period = function ($val, $period) {
		if (!$val) return $period + 1;

		$type = gettype ($val);
		
		if ($type === 'array') {
			return join(', ', $val);
		}
		
		return $val;
	};

    $camp_count = 3;
    $period_count = 7;
    $lunch_after = 2;

    $html = '<table class="workshops-schedule">' . "\n";

    for ($camp = 1; $camp <= $camp_count; $camp++) {

        $period_row = "<th>Camp $camp</th>";

        for($period = 1; $period <= $period_count; $period++) {
            $period_row .= "<th>Period $period</th>";

            if ($period == $lunch_after) {
                $period_row .= "<th>🍽</th>";
            };
        }

        $workshop_location = pods('workshop_location', [
            'orderby' => 't.name ASC',
            'where'   => "workshop_location_camp.name = \"Camp $camp\"",
            'limit' => -1,
        ]);

        $row_span = $period_count + 2;
        $html .= "<tr><th colspan=\"$row_span\">&nbsp;</th></tr>";
        $html .= '<tr>' . $period_row . '</tr>';

        while ( $workshop_location->fetch() ) {
            $location_id = $workshop_location->field('term_id');
            $location_name = preg_replace('/\(Camp .*\)/', '', $workshop_location->display('name'));

            $id = $workshop_location->id();
            $workshop = pods('workshop', [
                'orderby' => 'workshop_period.meta_value ASC',
                'where'   => "workshop_location.term_id = $location_id",
                'limit' => -1,
            ]);

            $html .= "<tr><td>$location_name</td>";

            $workshop->fetch();
            $workshop_period = $format_period($workshop->field('workshop_period.meta_value'), 0);
			
            for($period = 1; $period <= $period_count; $period++) {
                if ($workshop_period == $period) {
					$html .= "<td>";

					while ($workshop_period == $period) {
	                    $workshop_name = $workshop->display('name');
    	                $body = str_replace("\n", '', $workshop->display('content'));
        	            $instructor_name = $clean_attribute($workshop->field( 'workshop_instructor.post_title' ));
            	        $instructor_body = $clean_attribute($workshop->field( 'workshop_instructor.post_content' ));
                	    $instructor_pic = pods_image_url($workshop->field( 'workshop_instructor.pic' ), 'thumbnail');
        	            $instructor_youtube = $clean_attribute($workshop->field( 'workshop_instructor.youtube' ));
        	            $instructor_website = $clean_attribute($workshop->field( 'workshop_instructor.website' ));
						
						$html .= "<div class=\"workshop\" data-workshop-name='$workshop_name' data-workshop-body='$body' data-instructor-name='$instructor_name' data-instructor-body='$instructor_body' data-instructor-pic='$instructor_pic' data-instructor-website='$instructor_website' data-instructor-youtube='$instructor_youtube'>";

                    	$html .= "<div class=\"title\">$workshop_name</div>";
	                    $html .= "<div class=\"instructor\">$instructor_name</div>";
						$html .= "</div>";

						$workshop->fetch();
            			$workshop_period = $format_period($workshop->field('workshop_period.meta_value'), $workshop_period);
						if ($workshop_period == $period) $html .= "<hr />";
					}

                    $html .= "</td>";
                } else {
                    $html .= "<td>&nbsp;</td>";
                }

                if ($period == $lunch_after) $html .= "<td>🍽</td>";

            }

            $html .= "</tr>\n";
        }
    }

    $html .= '</table>';

    // return the html
    return $html;
}


