<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ayano
 */

?>

<footer id="colophon" class="site-footer">
    <section class="blocksectionfooter__section--inner">
        <section class="blocksectionfooter__section--inner--one">
            <section class="blocksectionfooter__section">
                <?php the_custom_logo(); ?>
            </section>
            <section>
                <h3 class="blocksectionfooter__h3--content">Rest Home, Hospital, Dementia and Specialised Dementia
                    Care</h3>
                <p class="blocksectionfooter__p--content">With over 60 years of experience taking great care of older
                    people, CHT
                    Care Homes offer quality,
                    affordable aged care across rest home, hospital and dementia care.</p>
                <div class="blockfirstsectionheader__div--details--footer">
                    <a class="blockfirstsectionheader__a--contact blockfirstsectionheader__a--contact--footer" href="<?= home_url() ?>/contact">Contact</a>
                    <a class="blockfirstsectionheader__a--info" href="#">Free Assessment</a>
                </div>
            </section>
        </section>
    </section>
    <section class="blocksectionfooter__section--footer">
        <section class="blocksectionfooter__section--footer--inner">
            <p class="blocksectionfooter__p satoshi"><?= date('Y') ?> &copy; Ayano. All rights reserved.</p>
            <a class="blocksectionfooter__a satoshi" href="/privacy-policy">Privacy Policy</a>
            <a class="blocksectionfooter__a satoshi" href="/terms-conditions">Terms and conditions</a>
        </section>
    </section>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
