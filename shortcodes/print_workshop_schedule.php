<?php
if ( ! function_exists( 'lta_print_workshop_schedule_func' ) ) :

function lta_print_workshop_schedule_func( $a )
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

    $period_times = [
        1 => '9:30-10:30',
        2 => '10:45-11:45',
        3 => '12:30-1:30',
        4 => '1:45-2:45',
        5 => '3:00-4:00',
        6 => '4:15-5:15',
        7 => '5:30-6:30',
        8 => 'time',
    ];

    for ($camp = 1; $camp <= $camp_count; $camp++) {

        $period_row = "<th>Camp $camp</th>";

        for($period = 1; $period <= $period_count; $period++) {
            $period_time = $period_times[$period];
            $period_row .= "<th><div>Period $period</div><div>$period_time</div></th>";

            if ($period == $lunch_after) {
                // $period_row .= "<th class=\"lunch\"></th>";
            };
        }

        $workshop_location = pods('workshop_location', [
            'orderby' => 't.name ASC',
            'where'   => "workshop_location_camp.name = \"Camp $camp\"",
            'limit' => -1,
        ]);

        $row_remainder_span = $period_count + 1 - 4;
        $html .= "<thead><tr>";
        $html .= "<th colspan=\"2\">&nbsp;</th>";
        $html .= "<th colspan=\"2\" class=\"lunch\">No workshops (11:45 - 12:30)</th>";
        $html .= "<th colspan=\"$row_remainder_span\">&nbsp;</th>";
        $html .= "</tr></thead>";
        $html .= "<tbody>";
        $html .= '<tr>' . $period_row . '</tr>';

        while ( $workshop_location->fetch() ) {
            $location_id = $workshop_location->field('term_id');
            $location_name = preg_replace('/\(Camp .*\)/', '', $workshop_location->display('name'));

            $id = $workshop_location->id();
            $workshop = pods('workshop', [
                'orderby' => 'workshop_period ASC',
                'where'   => "workshop_location.term_id = $location_id",
                'limit' => -1,
            ]);

            $html .= "<tr><td>$location_name</td>";

            $workshop->fetch();
            $workshop_period = $format_period($workshop->field('workshop_period'), 0);

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
                        $instructor_media1 = pods_image_url($workshop->field( 'workshop_instructor.media_1' ), null);
                        $instructor_media2 = pods_image_url($workshop->field( 'workshop_instructor.media_2' ), null);


                        $html .= "<div class=\"workshop\" data-workshop-name='$workshop_name' data-workshop-body='$body' data-instructor-name='$instructor_name' data-instructor-body='$instructor_body' data-instructor-pic='$instructor_pic' data-instructor-website='$instructor_website' data-instructor-youtube='$instructor_youtube' data-instructor-media1='$instructor_media1' data-instructor-media2='$instructor_media2'>";

                        $html .= "<div class=\"title\">$workshop_name</div>";
                        $html .= "<div class=\"instructor\">$instructor_name</div>";
                        $html .= "</div>";

                        $workshop->fetch();
                        $workshop_period = $format_period($workshop->field('workshop_period'), $workshop_period);
                        if ($workshop_period == $period) $html .= "<hr />";
                    }

                    $html .= "</td>";
                } else {
                    $html .= "<td>&nbsp;</td>";
                }

                // if ($period == $lunch_after) $html .= "<td class=\"lunch\"></td>";

            }

            $html .= "</tr>\n";
        }
    }
    $html .= "</tbody>";
    $html .= '</table>';

    // return the html
    return $html;
}


add_shortcode('print_workshop_schedule', 'lta_print_workshop_schedule_func');

endif;
