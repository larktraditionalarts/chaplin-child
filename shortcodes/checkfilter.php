<?php
if ( ! function_exists( 'checkfilter_func' ) ) :

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

add_shortcode('checkfilter', 'checkfilter_func');

endif;
