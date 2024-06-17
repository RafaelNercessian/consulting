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
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQGD8ND" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
</noscript>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <section class="blockfirstsectionheader__section">
            <section class="blockfirstsectionheader__section--one">
                <?php the_custom_logo() ?>
                <div class="blockfirstsectionheader__div--details">
                    <a class="blockfirstsectionheader__a--contact" href="<?= home_url() ?>/contact">Contact</a>
                    <a class="blockfirstsectionheader__a--info" href="#">Request an information pack</a>
                </div>
            </section>
            <nav class="blockfirstsectionheader__div">
                <ul>
                    <?php
                    $menuItems = wp_get_nav_menu_items('menu-1');
                    $current_page_id = get_queried_object_id();
                    foreach ($menuItems as $menuItem):
                        $menu_item_id = $menuItem->object_id;
                        $is_current = ($current_page_id == $menu_item_id);
                        $class = ($is_current) ? 'active' : '';
                        ?>
                        <li><a class="blockfirstsectionheader__a--menu <?= $class ?>"
                               href="<?= $menuItem->url ?>"><?= $menuItem->title ?></a></li>
                    <?php endforeach;
                    ?>
                </ul>
            </nav>
        </section>
    </header><!-- #masthead -->
