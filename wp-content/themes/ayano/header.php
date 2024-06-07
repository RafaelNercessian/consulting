<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Blazer
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="icon" href="<?= home_url() ?>/wp-content/uploads/2024/icon.png">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQGD8ND" height="0" width="0"
                  style="display:none;visibility:hidden"></iframe></noscript>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'blazer'); ?></a>
    <section class="blockfirstsectionheader__section--menu--overlay"></section>
    <section class="blockfirstsectionheader__section--menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="51" height="46" viewBox="0 0 51 46" fill="none">
            <line x1="7.23011" y1="2.76989" x2="48.2301" y2="43.7699" stroke="#313131" stroke-width="6.30769"/>
            <line y1="-3.15385" x2="57.9828" y2="-3.15385"
                  transform="matrix(-0.707107 0.707107 0.707107 0.707107 46 5)"
                  stroke="#313131" stroke-width="6.30769"/>
        </svg>
        <div class="blockfirstsectionheader__div--menu">
            <?php
            $menuItems = wp_get_nav_menu_items('menu-1');
            $current_page_id = get_queried_object_id();
            foreach ($menuItems as $menuItem):
                $menu_item_id = $menuItem->object_id;
                $is_current = ($current_page_id == $menu_item_id);
                $class = ($is_current) ? 'active' : '';
                ?>
                <a class="blockfirstsectionheader__a--menu <?= $class ?>" href="<?= $menuItem->url ?>"><?= $menuItem->title ?></a>
            <?php endforeach;
            ?>
        </div>
    </section>
    <header id="masthead" class="site-header">
        <section class="blockfirstsectionheader__section">
            <?php the_custom_logo() ?>
            <div class="blockfirstsectionheader__div">
                <a class="blockfirstsectionheader__a"
                   href="<?= home_url() . '/contact' ?>">お問い合わせ</a>
                <svg width="74" height="44" viewBox="0 0 74 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="hamburger menu">
                        <path d="M73.0975 0H0V5.57588H73.0975V0Z" fill="#FFFFFF"/>
                        <path d="M73.0975 18.7594H0V24.3353H73.0975V18.7594Z" fill="#FFFFFF"/>
                        <path d="M73.0975 37.5188H0V43.0947H73.0975V37.5188Z" fill="#FFFFFF"/>
                    </g>
                </svg>
            </div>
        </section>
    </header><!-- #masthead -->
