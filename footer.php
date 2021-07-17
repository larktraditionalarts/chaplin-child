<?php

$only_content_templates = array( 'template-only-content.php', 'template-full-width-only-content.php' );
$show_footer = apply_filters( 'chaplin_show_header_footer_on_only_content_templates', false );

// Don't output the markup of the footer on the only content templates, unless filtered to do so
if ( ! is_page_template( $only_content_templates ) || $show_footer ) : ?>

<footer class="" id="site-footer" role="contentinfo">
    <div class="footer-widgets-outer-wrapper border-color-border section-inner">
        <div class="footer-widgets-wrapper grid">

            <div class="footer-widgets grid-item">
                <img src="/wp-content/uploads/2019/10/LarkCamp_Logo_1000px-300x145.png" />
                <h3><?php echo get_the_title(7); ?></h3>
            </div>

            <div class="footer-widgets grid-item">
                <?php dynamic_sidebar( 'footer-one' ); ?>
            </div>

            <div class="footer-widgets grid-item">
                <?php dynamic_sidebar( 'footer-two' ); ?>
            </div>

        </div><!-- .footer-widgets-wrapper -->
    </div><!-- .footer-widgets-outer-wrapper -->

    <div class="footer-inner section-inner">

        <div class="footer-credits">

            <p class="footer-copyright">&copy; <?php echo esc_html( date_i18n( __( 'Y', 'chaplin' ) ) ); ?> <a target="_blank" href="https://larktraditionalarts.org">Lark Traditional Arts</a></p>

            <p class="footer-terms"><a href="/terms/">Terms of Registration</a></p>

            <p class="footer-coc"><a href="/code-of-conduct/">Code of Conduct</a></p>

            <ul class="footer-social social-menu">
                <?php
                    wp_nav_menu( array(
                        'container' 		=> '',
                        'depth'				=> 1,
                        'items_wrap' 		=> '%3$s',
                        'theme_location' 	=> 'social-menu',
                    ) );
                ?>
            </ul>

        </div><!-- .footer-credits -->
    </div><!-- .footer-inner-->

</footer><!-- #site-footer -->

<?php
endif;

wp_footer();
?>

    </body>
</html>
