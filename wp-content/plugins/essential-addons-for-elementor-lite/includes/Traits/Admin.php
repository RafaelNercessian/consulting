<?php

namespace Essential_Addons_Elementor\Traits;

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

// Exit if accessed directly

use Essential_Addons_Elementor\Classes\Helper as HelperClass;
use Essential_Addons_Elementor\Classes\WPDeveloper_Notice;
use PriyoMukul\WPNotice\Notices;
use PriyoMukul\WPNotice\Utils\CacheBank;
use PriyoMukul\WPNotice\Utils\NoticeRemover;

trait Admin {

    private static $cache_bank = null;

    /**
     * Create an admin menu.
     *
     * @since 1.1.2
     */
    public function admin_menu() {

	    $menu_notice = '';
	    $menu_notice = ( $this->menu_notice_should_show() ) ?'<span class="eael-menu-notice">1</span>':'';
        add_menu_page(
            __( 'Essential Addons a', 'essential-addons-for-elementor-lite' ),
            sprintf(__( 'Essential Addons %s', 'essential-addons-for-elementor-lite' ), $menu_notice ),
            'manage_options',
            'eael-settings',
            [$this, 'admin_settings_page'],
            $this->safe_url( EAEL_PLUGIN_URL . 'assets/admin/images/ea-icon-white.svg' ),
            '58.6'
        );
    }

    /**
     * Loading all essential scripts
     *
     * @since 1.1.2
     */
    public function admin_enqueue_scripts( $hook ) {
        wp_enqueue_style( 'essential_addons_elementor-notice-css', EAEL_PLUGIN_URL . 'assets/admin/css/notice.css', false, EAEL_PLUGIN_VERSION );

        if ( $hook == 'essential-addons_page_template-cloud' ) {
            wp_enqueue_style( 'essential_addons_elementor-template-cloud-css', EAEL_PLUGIN_URL . 'assets/admin/css/cloud.css', false, EAEL_PLUGIN_VERSION );
        }

        if ( isset( $hook ) && $hook == 'toplevel_page_eael-settings' ) {
            wp_enqueue_style( 'essential_addons_elementor-admin-css', EAEL_PLUGIN_URL . 'assets/admin/css/admin.css', false, EAEL_PLUGIN_VERSION );
            if ( $this->pro_enabled ) {
                wp_enqueue_style( 'eael_pro-admin-css', EAEL_PRO_PLUGIN_URL . 'assets/admin/css/admin.css', false, EAEL_PRO_PLUGIN_VERSION );
            }
            wp_enqueue_style( 'sweetalert2-css', EAEL_PLUGIN_URL . 'assets/admin/vendor/sweetalert2/css/sweetalert2.min.css', false, EAEL_PLUGIN_VERSION );
            wp_enqueue_script( 'sweetalert2-js', EAEL_PLUGIN_URL . 'assets/admin/vendor/sweetalert2/js/sweetalert2.min.js', array( 'jquery', 'sweetalert2-core-js' ), EAEL_PLUGIN_VERSION, true );
            wp_enqueue_script( 'sweetalert2-core-js', EAEL_PLUGIN_URL . 'assets/admin/vendor/sweetalert2/js/core.js', array( 'jquery' ), EAEL_PLUGIN_VERSION, true );

            wp_enqueue_script( 'essential_addons_elementor-admin-js', EAEL_PLUGIN_URL . 'assets/admin/js/admin.js', array( 'jquery' ), EAEL_PLUGIN_VERSION, true );

            //Internationalizing JS string translation
            $i18n = [
                'login_register' => [
                    //m=modal, rm=response modal, r=reCAPTCHA, g= google, f=facebook, e=error
                    'm_title'      => __( 'Login | Register Form Settings', 'essential-addons-for-elementor-lite' ),
                    'm_footer'     => $this->pro_enabled ? __( 'To configure the API Keys, check out this doc', 'essential-addons-for-elementor-lite' ) : __( 'To retrieve your API Keys, click here', 'essential-addons-for-elementor-lite' ),
                    'save'         => __( 'Save', 'essential-addons-for-elementor-lite' ),
                    'cancel'       => __( 'Cancel', 'essential-addons-for-elementor-lite' ),
                    'rm_title'     => __( 'Login | Register Form Settings Saved', 'essential-addons-for-elementor-lite' ),
                    'rm_footer'    => __( 'Reload the page to see updated data', 'essential-addons-for-elementor-lite' ),
                    'e_title'      => __( 'Oops...', 'essential-addons-for-elementor-lite' ),
                    'e_text'       => __( 'Something went wrong!', 'essential-addons-for-elementor-lite' ),
                    'r_title'      => __( 'reCAPTCHA v2', 'essential-addons-for-elementor-lite' ),
                    'r_sitekey'    => __( 'Site Key', 'essential-addons-for-elementor-lite' ),
                    'r_sitesecret' => __( 'Site Secret', 'essential-addons-for-elementor-lite' ),
                    'r_language'   => __( 'Language', 'essential-addons-for-elementor-lite' ),
                    'r_language_ph'=> __( 'reCAPTCHA Language Code', 'essential-addons-for-elementor-lite' ),
                    'g_title'      => __( 'Google Login', 'essential-addons-for-elementor-lite' ),
                    'g_cid'        => __( 'Google Client ID', 'essential-addons-for-elementor-lite' ),
                    'f_title'      => __( 'Facebook Login', 'essential-addons-for-elementor-lite' ),
                    'f_app_id'     => __( 'Facebook APP ID', 'essential-addons-for-elementor-lite' ),
                    'f_app_secret' => __( 'Facebook APP Secret', 'essential-addons-for-elementor-lite' ),
                ]
            ];

            wp_localize_script( 'essential_addons_elementor-admin-js', 'localize', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'essential-addons-elementor' ),
                'i18n'    => $i18n,
                'settings_save' => EAEL_PLUGIN_URL . 'assets/admin/images/settings-save.gif',
                'assets_regenerated' => EAEL_PLUGIN_URL . 'assets/admin/images/assets-regenerated.gif',
            ) );
        }

        $this->eael_admin_inline_css();
    }

    /**
     * Create settings page.
     *
     * @since 1.1.2
     */
    public function admin_settings_page() {
        ?>
        <form action="" method="POST" id="eael-settings" name="eael-settings">
            <div class="template__wrapper background__greyBg px30 py50">
                <div class="eael-container">
                    <div class="eael-main__tab mb45">
                        <ul class="ls-none tab__menu">
                            <li class="tab__list active"><a class="tab__item" href="#general"><i class="ea-admin-icon eael-icon-gear-alt"></i><?php echo __( 'General', 'essential-addons-for-elementor-lite' ); ?></a></li>
                            <li class="tab__list"><a class="tab__item" href="#elements"><i class="ea-admin-icon eael-icon-element"></i><?php echo __( 'Elements', 'essential-addons-for-elementor-lite' ); ?></a></li>
                            <li class="tab__list"><a class="tab__item" href="#extensions"><i class="ea-admin-icon eael-icon-extension"></i><?php echo __( 'Extensions', 'essential-addons-for-elementor-lite' ); ?></a></li>
                            <li class="tab__list"><a class="tab__item" href="#tools"><i class="ea-admin-icon eael-icon-tools"></i><?php echo __( 'Tools', 'essential-addons-for-elementor-lite' ); ?></a></li>
                            <li class="tab__list"><a class="tab__item" href="#integrations"><i class="ea-admin-icon eael-icon-plug"></i><?php echo __( 'Integrations', 'essential-addons-for-elementor-lite' ); ?></a></li>
                            <?php  if ( !$this->pro_enabled ) { ?>
                                <li class="tab__list"><a class="tab__item" href="#go-pro"><i class="ea-admin-icon eael-icon-lock-alt"></i><?php echo __( 'Go Premium', 'essential-addons-for-elementor-lite' ); ?></a></li>
                             <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="eael-admin-setting-tabs">
	                <?php
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/general.php';
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/elements.php';
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/extensions.php';
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/tools.php';
	                if ( !$this->pro_enabled ) {
		                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/go-pro.php';
	                }
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/integrations.php';
	                include_once EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/templates/admin/popup.php';
	                ?>
                </div>
            </div>
        </form>
        <?php
	    do_action( 'eael_admin_page_setting' );
    }

    /**
     * Saving data with ajax request
     * @param
     * @since 1.1.2
     */


    public function admin_notice() {
        require_once EAEL_PLUGIN_PATH . 'vendor/autoload.php';

        self::$cache_bank = CacheBank::get_instance();

        NoticeRemover::get_instance('1.0.0');
        NoticeRemover::get_instance('1.0.0', '\WPDeveloper\BetterDocs\Dependencies\PriyoMukul\WPNotice\Notices');

        $notices = new Notices( [
			'id'             => 'essential-addons-for-elementor',
			'storage_key'    => 'notices',
			'lifetime'       => 3,
			'stylesheet_url' => esc_url_raw( EAEL_PLUGIN_URL . 'assets/admin/css/notice.css' ),
			'styles' => esc_url_raw( EAEL_PLUGIN_URL . 'assets/admin/css/notice.css' ),
			'priority'       => 1
		] );

        $review_notice = __( 'We hope you\'re enjoying Essential Addons for Elementor! Could you please do us a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'essential-addons-for-elementor-lite' );
		$_review_notice = [
			'thumbnail' => plugins_url( 'assets/admin/images/icon-ea-logo.svg', EAEL_PLUGIN_BASENAME ),
			'html'      => '<p>' . $review_notice . '</p>',
			'links'     => [
				'later'            => array(
					'link'       => 'https://wpdeveloper.com/review-essential-addons-elementor',
					'target'     => '_blank',
					'label'      => __( 'Ok, you deserve it!', 'essential-addons-for-elementor-lite' ),
					'icon_class' => 'dashicons dashicons-external',
				),
				'allready'         => array(
					'label'      => __( 'I already did', 'essential-addons-for-elementor-lite' ),
					'icon_class' => 'dashicons dashicons-smiley',
					'attributes' => [
						'data-dismiss' => true
					],
				),
				'maybe_later'      => array(
					'label'      => __( 'Maybe Later', 'essential-addons-for-elementor-lite' ),
					'icon_class' => 'dashicons dashicons-calendar-alt',
					'attributes' => [
						'data-later' => true
					],
				),
				'support'          => array(
					'link'       => 'https://wpdeveloper.com/support',
					'label'      => __( 'I need help', 'essential-addons-for-elementor-lite' ),
					'icon_class' => 'dashicons dashicons-sos',
				),
				'never_show_again' => array(
					'label'      => __( 'Never show again', 'essential-addons-for-elementor-lite' ),
					'icon_class' => 'dashicons dashicons-dismiss',
					'attributes' => [
						'data-dismiss' => true
					],
				)
			]
		];

	    $notices->add(
			'review',
			$_review_notice,
			[
				'start'       => $notices->strtotime( '+7 day' ),
				'recurrence'  => 30,
				'refresh'     => EAEL_PLUGIN_VERSION,
				'dismissible' => true,
			]
		);

		$b_message            = '<p>Black Friday Sale: Unlock access to <strong>90+ advanced Elementor widgets</strong> with up to 40% discounts <span class="gift-icon">🎁</span></p><p><a class="button button-primary" href="https://wpdeveloper.com/upgrade/ea-bfcm" target="_blank">Upgrade to pro</a> <button data-dismiss="true" class="dismiss-btn button button-link">I don’t want to save money</button></p>';
		$_black_friday_notice = [
			'thumbnail' => plugins_url( 'assets/admin/images/full-logo.svg', EAEL_PLUGIN_BASENAME ),
			'html'      => $b_message,
		];

	    $notices->add(
			'black_friday_notice',
			$_black_friday_notice,
			[
				'start'       => $notices->time(),
				'recurrence'  => false,
				'dismissible' => true,
				'refresh'     => EAEL_PLUGIN_VERSION,
				"expire"      => strtotime( '11:59:59pm 2nd December, 2023' ),
				'display_if'  => ! $this->pro_enabled,
			]
		);

	    self::$cache_bank->create_account( $notices );
	    self::$cache_bank->calculate_deposits( $notices );
    }

	/**
	 * eael_admin_inline_css
     *
     * Admin Menu highlighted
     * @return false
	 * @since 5.1.0
	 */
	public function eael_admin_inline_css() {

	    $screen = get_current_screen();
		if ( ! empty( $screen->id ) && $screen->id == 'toplevel_page_eael-settings' ) {
			return false;
		}

		if ( $this->menu_notice_should_show() ) {
			$custom_css = "
                #toplevel_page_eael-settings a ,
                #toplevel_page_eael-settings a:hover {
                    color:#f0f0f1 !important;
                    background: #7D55FF !important;
                }
				#toplevel_page_eael-settings .eael-menu-notice {
                    display:block !important;
                }"
            ;
			wp_add_inline_style( 'admin-bar', $custom_css );
		}
	}

	/**
	 * menu_notice_should_show
     *
     * Check two flags status (eael_admin_menu_notice and eael_admin_promotion),
     * if both true this display menu notice. it's prevent to display menu notice multiple time
     *
	 * @return bool
     * @since 5.1.0
	 */
	public function menu_notice_should_show() {
		return ( get_option( 'eael_admin_menu_notice' ) < self::EAEL_PROMOTION_FLAG && get_option( 'eael_admin_promotion' ) < self::EAEL_ADMIN_MENU_FLAG );
	}

	public function essential_block_optin() {
		if ( is_plugin_active( 'essential-blocks/essential-blocks.php' ) || get_option( 'eael_eb_optin_hide' ) ) {
			return;
		}

		$screen           = get_current_screen();
		$is_exclude       = ! empty( $_GET['post_type'] ) && in_array( $_GET['post_type'], [ 'elementor_library', 'product' ] );
		$ajax_url         = admin_url( 'admin-ajax.php' );
		$nonce            = wp_create_nonce( 'essential-addons-elementor' );
		$eb_not_installed = HelperClass::get_local_plugin_data( 'essential-blocks/essential-blocks.php' ) === false;
		$action           = $eb_not_installed ? 'install' : 'activate';
		$button_title     = $eb_not_installed ? esc_html__( 'Install Essential Blocks', 'essential-addons-for-elementor-lite' ) : esc_html__( 'Activate', 'essential-addons-for-elementor-lite' );

		if ( $screen->parent_base !== 'edit' || $is_exclude ) {
			return;
		}
		?>
        <div class="wpnotice-wrapper notice  notice-info is-dismissible eael-eb-optin-notice">
            <div class="wpnotice-content-wrapper">
                <div class="eael-eb-optin">
                    <h3><?php esc_html_e( 'Using Gutenberg? Check out Essential Blocks!', 'essential-addons-for-elementor-lite' ); ?></h3>
                    <p><?php _e( 'Are you using the Gutenberg Editor for your website? Then try out Essential Blocks for Gutenberg, and explore 40+ unique blocks to make your web design experience in WordPress even more powerful. 🚀', 'essential-addons-for-elementor-lite' ); ?></p>
                    <p><?php _e( 'For more information, <a href="https://essential-blocks.com/demo/" target="_blank">check out the demo here</a>.', 'essential-addons-for-elementor-lite' ); ?></p>
                    <p>
                        <a href="#" class="button-primary wpdeveloper-eb-plugin-installer" data-action="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $button_title ); ?></a>
                    </p>
                </div>
            </div>
        </div>

        <script>
            // install/activate plugin
            (function ($) {
                $(document).on("click", ".wpdeveloper-eb-plugin-installer", function (ev) {
                    ev.preventDefault();

                    var button = $(this),
                        action = button.data("action");

                    if ($.active && typeof action != "undefined") {
                        button.text("Waiting...").attr("disabled", true);

                        setInterval(function () {
                            if (!$.active) {
                                button.attr("disabled", false).trigger("click");
                            }
                        }, 1000);
                    }

                    if (action === "install" && !$.active) {
                        button.text("Installing...").attr("disabled", true);

                        $.ajax({
                            url: "<?php echo esc_html( $ajax_url ); ?>",
                            type: "POST",
                            data: {
                                action: "wpdeveloper_install_plugin",
                                security: "<?php echo esc_html( $nonce ); ?>",
                                slug: "essential-blocks",
                            },
                            success: function (response) {
                                if (response.success) {
                                    button.text("Activated");
                                    button.data("action", null);

                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    button.text("Install");
                                }

                                button.attr("disabled", false);
                            },
                            error: function (err) {
                                console.log(err.responseJSON);
                            },
                        });
                    } else if (action === "activate" && !$.active) {
                        button.text("Activating...").attr("disabled", true);

                        $.ajax({
                            url: "<?php echo esc_html( $ajax_url ); ?>",
                            type: "POST",
                            data: {
                                action: "wpdeveloper_activate_plugin",
                                security: "<?php echo esc_html( $nonce ); ?>",
                                basename: "essential-blocks/essential-blocks.php",
                            },
                            success: function (response) {
                                if (response.success) {
                                    button.text("Activated");
                                    button.data("action", null);

                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    button.text("Activate");
                                }

                                button.attr("disabled", false);
                            },
                            error: function (err) {
                                console.log(err.responseJSON);
                            },
                        });
                    }
                }).on('click', '.eael-eb-optin-notice button.notice-dismiss', function (e) {
                    e.preventDefault();

                    var $notice_wrapper = $(this).closest('.eael-eb-optin-notice');

                    $.ajax({
                        url: "<?php echo esc_html( $ajax_url ); ?>",
                        type: "POST",
                        data: {
                            action: "eael_eb_optin_notice_dismiss",
                            security: "<?php echo esc_html( $nonce ); ?>",
                        },
                        success: function (response) {
                            if (response.success) {
                                $notice_wrapper.remove();
                            } else {
                                console.log(response.data);
                            }
                        },
                        error: function (err) {
                            console.log(err.responseText);
                        },
                    });
                });
            })(jQuery);
        </script>
		<?php
	}

	public function essential_block_special_optin() {
		if ( is_plugin_active( 'essential-blocks/essential-blocks.php' ) || get_option( 'eael_eb_optin_hide' ) ) {
			return;
		}

		$ajax_url         = admin_url( 'admin-ajax.php' );
		$nonce            = wp_create_nonce( 'essential-addons-elementor' );
		$eb_not_installed = HelperClass::get_local_plugin_data( 'essential-blocks/essential-blocks.php' ) === false;
		$action           = $eb_not_installed ? 'install' : 'activate';
		$button_title     = $eb_not_installed ? esc_html__( 'Install Essential Blocks', 'essential-addons-for-elementor-lite' ) : esc_html__( 'Activate', 'essential-addons-for-elementor-lite' );
		?>
        <style>
            /* Essential Blocks Special Optin*/
            .eael-eb-special-optin-notice {
                border-left-color: #6200ee;
                padding-top: 0;
                padding-bottom: 0;
                padding-left: 0;
            }

            .eael-eb-special-optin-notice h3,
            .eael-eb-special-optin-notice p,
            .eael-eb-special-optin-notice a {
                font-family: -apple-system,BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            }

            .eael-eb-special-optin-notice a {
                color: #2271b1;
            }

            .eael-eb-special-optin-notice .wpnotice-content-wrapper {
                display: flex;
            }

            .eael-eb-special-optin-notice .wpnotice-content-wrapper > div {
                padding-top: 15px;
            }

            .eael-eb-special-optin-notice .eael-eb-optin-logo {
                width: 50px;
                text-align: center;
                background: rgba(98, 0, 238, .1);
            }

            .eael-eb-special-optin-notice .eael-eb-optin-logo img {
                width: 25px;
            }

            .eael-eb-special-optin-notice .eael-eb-optin {
                padding-left: 10px;
            }

            .eael-eb-special-optin-notice .eael-eb-optin a.wpdeveloper-eb-plugin-installer {
                background: #5E2EFF;
            }
        </style>
        <div class="wpnotice-wrapper notice  notice-info is-dismissible eael-eb-special-optin-notice">
            <div class="wpnotice-content-wrapper">
                <div class="eael-eb-optin-logo">
                    <img src="<?php echo esc_url( EAEL_PLUGIN_URL . 'assets/admin/images/eb-new.svg' ); ?>" alt="">
                </div>
                <div class="eael-eb-optin">
                    <h3><?php esc_html_e( 'Using Gutenberg? Check out Essential Blocks!', 'essential-addons-for-elementor-lite' ); ?></h3>
                    <p><?php _e( 'Are you using the Gutenberg Editor for your website? Then try out Essential Blocks for Gutenberg, and explore 40+ unique blocks to make your web design experience in WordPress even more powerful. 🚀', 'essential-addons-for-elementor-lite' ); ?></p>
                    <p><?php _e( 'For more information, <a href="https://essential-blocks.com/demo/" target="_blank">check out the demo here</a>.', 'essential-addons-for-elementor-lite' ); ?></p>
                    <p>
                        <a href="#" class="button-primary wpdeveloper-eb-plugin-installer" data-action="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $button_title ); ?></a>
                    </p>
                </div>
            </div>
        </div>

        <script>
            // install/activate plugin
            (function ($) {
                $(document).on("click", ".wpdeveloper-eb-plugin-installer", function (ev) {
                    ev.preventDefault();

                    var button = $(this),
                        action = button.data("action");

                    if ($.active && typeof action != "undefined") {
                        button.text("Waiting...").attr("disabled", true);

                        setInterval(function () {
                            if (!$.active) {
                                button.attr("disabled", false).trigger("click");
                            }
                        }, 1000);
                    }

                    if (action === "install" && !$.active) {
                        button.text("Installing...").attr("disabled", true);

                        $.ajax({
                            url: "<?php echo esc_html( $ajax_url ); ?>",
                            type: "POST",
                            data: {
                                action: "wpdeveloper_install_plugin",
                                security: "<?php echo esc_html( $nonce ); ?>",
                                slug: "essential-blocks",
                            },
                            success: function (response) {
                                if (response.success) {
                                    button.text("Activated");
                                    button.data("action", null);

                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    button.text("Install");
                                }

                                button.attr("disabled", false);
                            },
                            error: function (err) {
                                console.log(err.responseJSON);
                            },
                        });
                    } else if (action === "activate" && !$.active) {
                        button.text("Activating...").attr("disabled", true);

                        $.ajax({
                            url: "<?php echo esc_html( $ajax_url ); ?>",
                            type: "POST",
                            data: {
                                action: "wpdeveloper_activate_plugin",
                                security: "<?php echo esc_html( $nonce ); ?>",
                                basename: "essential-blocks/essential-blocks.php",
                            },
                            success: function (response) {
                                if (response.success) {
                                    button.text("Activated");
                                    button.data("action", null);

                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    button.text("Activate");
                                }

                                button.attr("disabled", false);
                            },
                            error: function (err) {
                                console.log(err.responseJSON);
                            },
                        });
                    }
                }).on('click', '.eael-eb-special-optin-notice button.notice-dismiss', function (e) {
                    e.preventDefault();

                    var $notice_wrapper = $(this).closest('.eael-eb-optin-notice');

                    $.ajax({
                        url: "<?php echo esc_html( $ajax_url ); ?>",
                        type: "POST",
                        data: {
                            action: "eael_eb_optin_notice_dismiss",
                            security: "<?php echo esc_html( $nonce ); ?>",
                        },
                        success: function (response) {
                            if (response.success) {
                                $notice_wrapper.remove();
                            } else {
                                console.log(response.data);
                            }
                        },
                        error: function (err) {
                            console.log(err.responseText);
                        },
                    });
                });
            })(jQuery);
        </script>
		<?php
	}

	public function eael_eb_optin_notice_dismiss() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		update_option( 'eael_eb_optin_hide', true );
		wp_send_json_success();
	}

	public function eael_gb_eb_popup_dismiss() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		update_option( 'eael_gb_eb_popup_hide', true );
		wp_send_json_success();
	}

	public function eael_black_friday_optin_dismiss() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

//		update_option( 'eael_black_friday_optin_hide', true );
		set_transient( 'eael_2M_optin_hide', true, 20 * DAY_IN_SECONDS );
		wp_send_json_success();
	}

	public function eael_black_friday_optin() {
		$time     = time();
		$ajax_url = admin_url( 'admin-ajax.php' );
		$nonce    = wp_create_nonce( 'essential-addons-elementor' );
		if ( $time > 1715126399 || get_transient( 'eael_2M_optin_hide' ) || defined( 'EAEL_PRO_PLUGIN_VERSION' ) ) {
			return;
		}
		?>
        <style>
            .eael-black-friday-notice,
            .eael-black-friday-notice * {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            }
            .eael-black-friday-notice {
                padding: 0;
                border-left-color: #6200ee;
            }
            .eael-black-friday-notice .wpnotice-content-wrapper {
                display: flex;
            }
            .eael-black-friday-notice .wpnotice-content-wrapper .eael-black-friday-optin-logo {
                width: 50px;
                padding: 26px 0 0;
                text-align: center;
                background: rgba(98, 0, 238, .1);
            }
            .eael-black-friday-notice .wpnotice-content-wrapper .eael-black-friday-optin {
                padding-left: 10px;
            }
            a.eael-2m-notice-hide {
                color: #2271b1;
                text-decoration: underline;
            }
            a.eael-2m-notice-hide:hover {
                color: #135e96;
                text-decoration: underline;
            }
        </style>
        <div class="wpnotice-wrapper notice notice-info is-dismissible eael-black-friday-notice">
            <div class="wpnotice-content-wrapper">
                <div class="eael-black-friday-optin-logo">
                    <img src="<?php echo esc_url( EAEL_PLUGIN_URL . 'assets/admin/images/icon-ea-logo.svg' ); ?>" width="25" alt="">
                </div>
                <div class="eael-black-friday-optin">
					<p><?php _e( 'Join us in celebrating <strong>2 MILLION+</strong> happy users and grab up to an exclusive 30% OFF on the most used Elementor addons!', 'essential-addons-for-elementor-lite' ); ?></p>
					<p><a href="https://essential-addons.com/upgrade-to-ea-pro" target="_blank"
						  class="button-primary"><?php _e( 'Upgrade To PRO Now', 'essential-addons-for-elementor-lite' ); ?></a>
						<a href="https://essential-addons.com/ea-lifetime-access" target="_blank"
						   class="button-secondary"><?php _e( 'Give Me LIFETIME access', 'essential-addons-for-elementor-lite' ); ?></a>
						<a href="#" target="_blank"
						   class="eael-2m-notice-hide"><?php _e( 'I don’t want to save money', 'essential-addons-for-elementor-lite' ); ?></a>
					</p>
                </div>
            </div>
        </div>

        <script>
            (function ($) {
                $(document).on('click', '.eael-black-friday-notice button.notice-dismiss, .eael-2m-notice-hide', function (e) {
                    e.preventDefault();

                    var $notice_wrapper = $(this).closest('.eael-black-friday-notice');

                    $.ajax({
                        url: "<?php echo esc_html( $ajax_url ); ?>",
                        type: "POST",
                        data: {
                            action: "eael_black_friday_optin_dismiss",
                            security: "<?php echo esc_html( $nonce ); ?>",
                        },
                        success: function (response) {
                            if (response.success) {
                                $notice_wrapper.remove();
                            } else {
                                console.log(response.data);
                            }
                        },
                        error: function (err) {
                            console.log(err.responseText);
                        },
                    });
                });
            })(jQuery);
        </script>
		<?php
	}
}
