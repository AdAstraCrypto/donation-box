<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CDBBC_shortcode')) {
    class CDBBC_shortcode
    {
/**
 * Constructor.
 *
 * @access private
 */
        public function __construct()
        {
            add_shortcode('crypto-donation-box', array($this, 'crypto_donation_box_shortcode'));

        }

/*
donation box shortcode
 */

        public function crypto_donation_box_shortcode($atts)
        {
            $attr = shortcode_atts(
                array(
                    'id' => 'something',
                    'class' => 'something else',
                    'type' => '',
                    'show-coin' => 'all',
                ),
                $atts
            );
            $donation_settings = get_option('cdbbc_settings');
            $output = '';
            $coin_tabs = '';
            $coin_links = '';
            $list_view = '';
            $classic_list = '';
            $tagvl = __('_tag', 'CDBBC');
            $i = 0;
            $active_tab = '';
            $design_type = $attr['type'];
            $show_coin = $attr['show-coin'];
            $selected_coin = "";
            $selected_coin = explode(",", $show_coin);
            $wallet_data = isset($donation_settings['general_tokens']) ? $donation_settings['general_tokens'] : "";
            $recever = isset($donation_settings['user_wallet']) ? $donation_settings['user_wallet'] : "";
            $infura_id = isset($donation_settings['infura_project_id']) ? $donation_settings['infura_project_id'] : "";
            if ($infura_id=="")
            {
                $infura_id = CdbbcMetaApi::getInfuraId();
            }
            $nonce = wp_create_nonce('cdbbc_donation_box');
            $share_data = isset($donation_settings['share_user_data']['share_donars_data']) ? $donation_settings['share_user_data']['share_donars_data'] : true;
            $termsconditiona = (isset($donation_settings['wallet_title_desc']['wallet_terms_condition']) && !empty($donation_settings['wallet_title_desc']['wallet_terms_condition'])) ? $donation_settings['wallet_title_desc']['wallet_terms_condition'] : "By making a donation you agree to share your transaction data with the website owner";

            wp_enqueue_script('cdbbc-ether', CDBBC_URL . 'assets/js/ethers-5.2.umd.min.js', array('jquery'), CDBBC_VERSION, true);
            wp_enqueue_script('cdbbc-common-script', CDBBC_URL . 'assets/js/cdbbc-common.js', array('jquery'), CDBBC_VERSION, true);
            wp_enqueue_script('cdbbc-wallet_connect', CDBBC_URL . 'assets/js/walletconnect.js', array('jquery'), CDBBC_VERSION, true);
            wp_enqueue_style('cdbbc-styles', CDBBC_URL . 'assets/css/cdbbc-styles.css', null, CDBBC_VERSION);
            wp_enqueue_script('cdbbc-metamask-script', CDBBC_URL . 'assets/js/cdbbc-wallets.js', array('jquery'), CDBBC_VERSION, true);
            wp_enqueue_script('cdbbc-sweetalert2', CDBBC_URL . 'assets/js/sweetalert2.js', array('jquery'), CDBBC_VERSION, true);
            wp_localize_script('cdbbc-metamask-script', "wallets_data",
                array(
                    "supported_network" => cdbbc_get_supported_network('network'),
                    "recever_address" => $recever,
                    "rpc_urls" => cdbbc_get_supported_network('rpc_url'),
                    "infura_id" => $infura_id,
                    "terms" => $termsconditiona,
                    "share_data_to_blackworks" => $share_data,
                    "ajax" => home_url('/wp-admin/admin-ajax.php'),
                    "nonce" => $nonce,
                    "const_msg" => cdbbc_const_messages(),
                    "wallet_logos" => array('metamask_wallet' => CDBBC_URL . 'assets/images/metamask.png', 'trust_wallet' => CDBBC_URL . 'assets/images/trustwallet.png', 'Binance_wallet' => CDBBC_URL . 'assets/images/binancewallet.png', 'wallet_connect' => CDBBC_URL . 'assets/images/walletconnect.png'),
                )
            );
            $main_title = isset($donation_settings['tile_desc_settings']['main_title']) ? $donation_settings['tile_desc_settings']['main_title'] : "";
            $wallet_title = isset($donation_settings['wallet_title_desc']['wallet_main_title']) ? $donation_settings['wallet_title_desc']['wallet_main_title'] : __('Donate Via Wallets', 'cdbbc');
            $wallet_desc = isset($donation_settings['wallet_title_desc']['wallet_main_desc']) ? $donation_settings['wallet_title_desc']['wallet_main_desc'] : __('Select a wallet to accept donation in ETH BNB BUSD etc..', 'cdbbc');
            $wallet_title_typo = isset($donation_settings['donation_wallet_style']['wallet_title_typo']) ? $donation_settings['donation_wallet_style']['wallet_title_typo'] : "";

            $wallet_title_color = (isset($wallet_title_typo['color']) && !empty($wallet_title_typo['color'])) ? '--wallet-title-font-color:' . $wallet_title_typo['color'] : "";
            $wallet_title_font = (isset($wallet_title_typo['font-size']) && !empty($wallet_title_typo['font-size'])) ? '--wallet-title-font-size:' . $wallet_title_typo['font-size'] . 'px' : "";
            $wallet_title_family = (isset($wallet_title_typo['font-family']) && !empty($wallet_title_typo['font-family'])) ? '--wallet-title-font-family:' . $wallet_title_typo['font-family'] : "";
            $wallet_title_align = (isset($wallet_title_typo['text-align']) && !empty($wallet_title_typo['text-align'])) ? '--wallet-title-font-align:' . $wallet_title_typo['text-align'] : "";
            $wallet_title_transform = (isset($wallet_title_typo['text-transform']) && !empty($wallet_title_typo['text-transform'])) ? ' --wallet-title-font-transform:' . $wallet_title_typo['text-transform'] : "";
            $wallet_title_weight = (isset($wallet_title_typo['font-weight']) && !empty($wallet_title_typo['font-weight'])) ? '--wallet-title-font-weight:' . $wallet_title_typo['font-weight'] : "";

            $wallet_content_typo = isset($donation_settings['donation_wallet_style']['wallet_content_typo']) ? $donation_settings['donation_wallet_style']['wallet_content_typo'] : "";
            $wallet_content_color = (isset($wallet_content_typo['color']) && !empty($wallet_content_typo['color'])) ? '--wallet-content-font-color:' . $wallet_content_typo['color'] : "";
            $wallet_content_font = (isset($wallet_content_typo['font-size']) && !empty($wallet_content_typo['font-size'])) ? '--wallet-content-font-size:' . $wallet_content_typo['font-size'] : "";
            $wallet_content_family = (isset($wallet_content_typo['font-family']) && !empty($wallet_content_typo['font-family'])) ? '--wallet-content-font-family:' . $wallet_content_typo['font-family'] : "";
            $wallet_content_align = (isset($wallet_content_typo['text-align']) && !empty($wallet_content_typo['text-align'])) ? '--wallet-content-font-align:' . $wallet_content_typo['text-align'] : "";
            $wallet_content_transform = (isset($wallet_content_typo['text-transform']) && !empty($wallet_content_typo['text-transform'])) ? '--wallet-content-font-transform:' . $wallet_content_typo['text-transform'] : "";
            $wallet_content_weight = (isset($wallet_content_typo['font-weight']) && !empty($wallet_content_typo['font-weight'])) ? '--wallet-content-font-weight:' . $wallet_content_typo['font-weight'] : "";

            $wallet_bg_color = !empty($donation_settings['donation_wallet_style']['wallet_bg_color']) ? '--wallet-bg-color:' . $donation_settings['donation_wallet_style']['wallet_bg_color'] : "";
            $custom_css = isset($donation_settings['cdbbc_custom_css']) ? $donation_settings['cdbbc_custom_css'] : "";
            $desc = isset($donation_settings['tile_desc_settings']['main_desc']) ? $donation_settings['tile_desc_settings']['main_desc'] : "";
            $title = !empty($main_title) ? $main_title : 'Donate [coin-name]([coin-symbol]) to this address';
            $description = !empty($desc) ? $desc : 'Scan the QR code or copy the address below into your wallet to send some [coin-name]([coin-symbol]';
            $title_typo = isset($donation_settings['donation_settings_style']['main_title_typography']) ? $donation_settings['donation_settings_style']['main_title_typography'] : "";
            $title_color = (isset($title_typo['color']) && !empty($title_typo['color'])) ? '--main-title-font-color:' . $title_typo['color'] : "";
            $title_font = (isset($title_typo['font-size']) && !empty($title_typo['font-size'])) ? '--main-title-font-size:' . $title_typo['font-size'] . 'px' : "";
            $title_font_family = (isset($title_typo['font-family']) && !empty($title_typo['font-family'])) ? ' --main-title-font-family:' . $title_typo['font-family'] : "";
            $title_font_align = (isset($title_typo['text-align']) && !empty($title_typo['text-align'])) ? '--main-title-font-align:' . $title_typo['text-align'] : "";
            $title_font_transform = (isset($title_typo['text-transform']) && !empty($title_typo['text-transform'])) ? '--main-title-font-transform:' . $title_typo['text-transform'] : "";
            $title_font_weight = (isset($title_typo['font-weight']) && !empty($title_typo['font-weight'])) ? '--main-title-font-weight:' . $title_typo['font-weight'] : "";
            $content_typo = isset($donation_settings['donation_settings_style']['main_content_typography']) ? $donation_settings['donation_settings_style']['main_content_typography'] : "";
            $content_color = (isset($title_typo['color']) && !empty($title_typo['color'])) ? '--main-content-font-color:' . $content_typo['color'] : "";
            $content_font = (isset($content_typo['font-size']) && !empty($content_typo['font-size'])) ? '--main-content-font-size:' . $content_typo['font-size'] . 'px' : "";
            $content_font_family = (isset($content_typo['font-family']) && !empty($content_typo['font-family'])) ? '--main-content-font-family:' . $content_typo['font-family'] : "";
            $content_font_align = (isset($content_typo['text-align']) && !empty($content_typo['text-align'])) ? '--main-content-font-align:' . $content_typo['text-align'] : "";
            $content_font_transform = (isset($content_typo['text-transform']) && !empty($content_typo['text-transform'])) ? '--main-content-font-transform:' . $content_typo['text-transform'] : "";
            $content_font_weight = (isset($content_typo['font-weight']) && !empty($content_typo['font-weight'])) ? '--main-content-font-weight:' . $content_typo['font-weight'] : "";
            $bg_color = !empty($donation_settings['donation_settings_style']['main_bg_color']) ? '--cdbbc-coins-bg-color:' . $donation_settings['donation_settings_style']['main_bg_color'] : "";
            $get_symbol = CDBBC_supported_coins();
            $EnableWalletStyles = isset($donation_settings['enable_wallet_in_design']) ? $donation_settings['enable_wallet_in_design'] : "";
            $WalletTabStyle = (isset($EnableWalletStyles['wallet_tab_style']) && $EnableWalletStyles['wallet_tab_style'] == "1") ? true : false;
            $WalletListStyle = (isset($EnableWalletStyles['wallet_list_style']) && $EnableWalletStyles['wallet_list_style'] == "1") ? true : false;
            $WalletPopupStyle = (isset($EnableWalletStyles['wallet_popup_style']) && $EnableWalletStyles['wallet_popup_style'] == "1") ? true : false;
            $wallet_array = array("coin_type" => "popular", "coin" => "wallets", "wallet_address" => "xxxxxxxxxxxx");
            if ($WalletTabStyle == true || $WalletListStyle == true || $WalletPopupStyle == true) {
                if (is_array($wallet_data)) {
                    array_push($wallet_data, $wallet_array);
                } else {
                    $wallet_data = [$wallet_array];
                }

            }
            if ($show_coin == "all") {
                $all_coin_wall_add = $wallet_data;
            } else {
                $custom_array = [];
                if (is_array($wallet_data)) {
                    foreach ($wallet_data as $key => $value) {
                        if ($value['coin_type'] == "popular") {
                            if (in_array($value['coin'], $selected_coin)) {
                                $custom_array[] = $value;
                            }
                        } else {
                            if (in_array($value['coin_id'], $selected_coin)) {
                                $custom_array[] = $value;
                            }

                        }
                    }
                }
                $all_coin_wall_add = $custom_array;
            }
            $contact_address = "";
            $metamask_wall_add = $recever;
            $random_num = rand(1, 1000);
            $active_chain = "";
            $token_symbol = "";
            $name_symbol = "";
            $coin_symbol = "";
            $count = 1;
            $output .= '<!---------- Crypto Donation Box Version:- ' . CDBBC_VERSION . ' --------------><div class="cdbbc_donation_wrap">';
            if ($design_type != 'metamask' && $design_type != 'wallet') {
                if (!empty($all_coin_wall_add) && is_array($all_coin_wall_add) && array_filter($all_coin_wall_add)) {
                    $list_count = 1;
                    $pop_count = 1;
                    $tab_count = 1;

                    foreach ($all_coin_wall_add as $index => $value) {
                        $contact_address = "";
                        $coin_symbol = "";
                        $coin_name = "";
                        if ($value['coin_type'] == "popular") {
                            $id = isset($value['coin']) ? $value['coin'] : "";
                            // $address = isset($value['wallet_address']) ? $value['wallet_address'] : "";
                            $tag_data = isset($value['tag_note']) ? $value['tag_note'] : "";
                            $coin_logo = CDBBC_URL . 'assets/logos/' . $id . '.svg';
                            if (strpos($id, 'metamask') === false && strpos($id, 'wallets') === false) {
                                $coin_name = ucfirst(str_replace('-', ' ', $id));
                                preg_match('#\((.*?)\)#', $get_symbol[$id], $match);
                                $coin_symbol = $match[1];
                            }
                        } else {
                            $id = isset($value['coin_id']) ? $value['coin_id'] : "";
                            // $address = isset($value['wallet_address_custom']) ? $value['wallet_address_custom'] : "";
                            $tag_data = isset($value['tag_note_custom']) ? $value['tag_note_custom'] : "";
                            $logo = isset($value['logo']) ? $value['logo'] : '';
                            $coin_logo = !empty($logo) ? $logo : CDBBC_URL . 'assets/images/default-logo.png';
                            $coin_name = isset($value['coin_name']) ? $value['coin_name'] : "";
                            $coin_symbol = isset($value['coin_symbol']) ? $value['coin_symbol'] : "";
                        }

                        // if (empty($address)) {
                        //     continue;
                        // }
                        if ($i == 0) {
                            $active_tab = 'current';
                        } else {
                            $active_tab = '';
                        }
                        $coin_random = rand(1, 10000);
                        if (strpos($id, 'metamask') === false && strpos($id, 'wallets') === false) {
                            $title_content = str_replace('[coin-name]', $coin_name, $title);
                            $title_content = str_replace('[coin-symbol]', $coin_symbol, $title_content);
                            $desc_content = str_replace('[coin-name]', $coin_name, $description);
                            $desc_content = str_replace('[coin-symbol]', $coin_symbol, $desc_content);
                        }
                        $logo_html = '<img src="' . esc_url($coin_logo) . '"> ';
                        $logo_html .= esc_html($coin_name);
                        if (strpos($id, 'metamask') === false && strpos($id, 'wallets') === false) {
                            $coin_links .= '<li class="cdbbc-coins ' . esc_attr($active_tab) . '" id="' . esc_attr($id) . '" data-tab="' . esc_attr($id) . $random_num . $coin_random . '-tab" data-random="' . $random_num . '">' . $logo_html . '</li>';
                        }
                        if ($design_type == 'popup') {
                            if ((strpos($id, 'metamask') === false) && strpos($id, 'wallets') === false) {
                                $list_view .= '<li class="cdbbc-list-items"><a class="cdbbc-list-popup" href="#donate' . esc_attr($id) . $coin_random . '" rel="modal:open"><div class="cdb-list-img"><img src="' . esc_url($coin_logo) . '"></div><div class="cdb-list-content"><span class="cdb-list-donate-txt">' . __('Donate with', 'cdbbc') . '</span><span class="cdb-list-coin">' . esc_html($coin_name) . '</span></div></a></li>';
                                $list_view .= '<div id="donate' . esc_attr($id) . $coin_random . '" class="modal cdbbc_wrap_popup"><div class="cdbbc-main-title">';
                                $list_view .= '<h2 class="cdbbc-title">' . wp_kses_post($title_content) . '</h2></div>';
                                $list_view .= '<div class="cdbbc-modal-body"><div class="cdbbc-address">';
                                $list_view .= '<div class="cdbbc-wallet" id="wallet_connect"><div class="cdbbc-wallet-icon" ><button class="cdbbc_btn">' . __('Reveal QR Code', 'cdbbc') . '</button></div></div></div><div class="cdbbc_qr_code"><img src="' . CDBBC_URL . '/assets/images/qr_blur.png" alt="Scan to Donate ' . $coin_name . '"/></div>';
                                if (isset($tag_data) && !empty($tag_data)) {
                                    $list_view .= '<div class="cdbbc_tag"><span class="cdbbc_tag_heading">' . __('Tag/Note:-', 'cdbbc') . ' </span><span class="cdbbc_tag_desc">' . wp_kses_post($tag_data) . '</span></div>';
                                }
                                $list_view .= '</div></div>';
                            } else if ((strpos($id, 'metamask') === 0 || $WalletPopupStyle == true) && $pop_count == 1) {
                                if ($metamask_wall_add != '') {
                                    $list_view .= '<li class="cdbbc-list-items"><a class="cdbbc-list-popup" href="#donate' . esc_attr($id) . '" rel="modal:open"><div class="cdb-list-img"><img src="' . esc_url($coin_logo) . '"></div><div class="cdb-list-content"><span class="cdb-list-coin">Donate Via Wallets</span></div></a></li>';
                                    $list_view .= '<div id="donate' . esc_attr($id) . '" class="modal cdbbc_wrap_popup">';
                                    $list_view .= '<div class="cdb-metamask-wrapper" >';
                                    $list_view .= cdbbc_wallet_html(false);
                                    $list_view .= ' <div class="message"></div></div></div>';
                                    $pop_count++;
                                } else {
                                    $list_view .= '<h6>' . __('Please Add coin wallet address in plugin settings panel', 'cdbbc') . '</h6>';

                                }
                            }
                        } else if ($design_type == 'list') {
                            if (strpos($id, 'metamask') === false && strpos($id, 'wallets') === false) {
                                $classic_list .= '<li class="cdbbc-classic-list">';
                                $classic_list .= '<h2 class="cdbbc-title">' . wp_kses_post($title_content) . '</h2>';
                                $classic_list .= '<div class="cdbbc_qr_code"><img src="' . CDBBC_URL . '/assets/images/qr_blur.png" alt="Scan to Donate ' . $coin_name . '"/>';
                                $classic_list .= '</div><div class="cdbbc_classic_input_add">';
                                $classic_list .= '<div class="cdbbc-modal-body"><div class="cdbbc-address">';
                                $classic_list .= '<div class="cdbbc-wallet" id="wallet_connect"><div class="cdbbc-wallet-icon" ><button class="cdbbc_btn">' . esc_html__('Reveal QR Code', 'cdbbc') . '</button></div></div></div>';
                                if (isset($tag_data) && !empty($tag_data)) {
                                    $classic_list .= '<div class="cdbbc_tag"><span class="cdbbc_tag_heading">' . esc_html__('Tag/Note:-', 'cdbbc') . ' </span><span class="cdbbc_tag_desc">' . wp_kses_post($tag_data) . '</span></div>';
                                }
                                $classic_list .= '</li>';
                            } else if ((strpos($id, 'metamask') === 0 || $WalletListStyle == true) && $list_count == 1) {
                                if ($metamask_wall_add != '') {
                                    $classic_list .= '<li class="cdbbc-classic-list"><h2 class="cdbbc-wallet-title">' . wp_kses_post($wallet_title) . '</h2>';
                                    $classic_list .= cdbbc_wallet_html(false);
                                    $classic_list .= '<div class="message"></div></li>';
                                    $list_count++;
                                } else {
                                    $classic_list .= '<h6>' . __('Please Add coin wallet address in plugin settings panel', 'cdbbc') . '</h6>';

                                }
                            }
                        } else {
                            $coin_tabs .= '<div class="cdbbc-tabs-content ' . esc_attr($active_tab) . '" id="' . esc_attr($id) . $random_num . $coin_random . '-tab" >';
                            if (strpos($id, 'metamask') === false && strpos($id, 'wallets') === false) {
                                $coin_tabs .= '<div class="cdbbc_qr_code"><img src="' . CDBBC_URL . '/assets/images/qr_blur.png" alt="Scan to Donate ' . $coin_name . '"/>';
                                $coin_tabs .= '</div><div class="cdbbc_input_add"><h2 class="cdbbc-title">' . wp_kses_post($title_content) . '</h2><p class="cdbbc-desc">' . wp_kses_post($desc_content) . '</p>';
                                if (isset($tag_data) && !empty($tag_data)) {
                                    $coin_tabs .= '<div class="cdbbc_tag"><span class="cdbbc_tag_heading">' . __('Tag/Note:-', 'cdbbc') . ' </span><span class="cdbbc_tag_desc">' . wp_kses_post($tag_data) . '</span></div>';
                                }
                                $coin_tabs .= '<div class="cdbbc-modal-body"><div class="cdbbc-address">';
                                $coin_tabs .= '<div class="cdbbc-wallet" id="wallet_connect"><div class="cdbbc-wallet-icon" ><button class="cdbbc_btn" data-clipboard-target="#' . esc_attr($id) . '-wallet-address' . $random_num . '">' . __('Reveal QR Code', 'cdbbc') . '</button></div></div></div></div></div>';
                            }
                            $coin_tabs .= '</div>';
                        }
                        $i++;
                        $count++;
                    }

                    if ($design_type == 'popup') {
                        wp_enqueue_style('cdbbc-jquery-modal', CDBBC_URL . 'assets/css/jquery.modal.min.css');
                        wp_enqueue_script('cdbbc-jquery-modal-js', CDBBC_URL . 'assets/js/jquery.modal.min.js', array('jquery'), true);
                        $output .= '<div class="cdbbc-list-container"><div class="cdbbc-list-title"><h3>Donate</h3></div><div class="cdbbc-list-view"> <ul>';
                        $output .= $list_view;
                        $output .= '</ul></div></div>';
                    } elseif ($design_type == 'list') {
                        $output .= '<div class="cdbbc-classic-container">';
                        $output .= '<ul class="cdbbc-classic-list">';
                        $output .= $classic_list;
                        $output .= '</ul></div>';
                    } else {
                        $output .= '<div class="cdbbc-container">';
                        $add_active_cls = (count($all_coin_wall_add) == 1) ? 'active' : '';

                        if ($WalletTabStyle == true) {
                            $output .= '<div class="cdbbc_wallet_tabs" data-random="' . $random_num . '">';
                            if (count($all_coin_wall_add) == 1) {
                            } else {
                                $output .= '<span class="cdbbc_tab_btn active" id="donate_to_address' . $random_num . '"><img src="' . CDBBC_URL . 'assets/logos/bitcoin.svg" class="address_logo"><span>' . __('Donate To Address', 'cdbbc') . '</span></span>';
                            }
                            $output .= '<span class="cdbbc_tab_btn ' . $add_active_cls . '" id="donate_to_wallet' . $random_num . '"><img src="' . CDBBC_URL . 'assets/logos/metamask.svg" class="wallet_logo"><span>' . __('Donate Via Wallets', 'cdbbc') . '</span></span>';
                            $output .= '</div><div class="cdbbc_sections">';
                        }
                        if (count($all_coin_wall_add) == 1) {
                        } else {
                            $output .= '<div class="cdbbc_tab_section donate_to_address' . $random_num . ' active">';
                            $output .= '<div class="cdbbc-tab-rand' . $random_num . '">';
                            $output .= '<ul class="cdbbc-tabs" id="cdbbc-coin-list">' . $coin_links . '</ul>';
                            $output .= $coin_tabs . '</div></div>';
                        }
                        if ($WalletTabStyle == true) {
                            if ($metamask_wall_add != '') {
                                $output .= '<div class="cdbbc_tab_section donate_to_wallet' . $random_num . ' ' . $add_active_cls . '" id="cdbbc_donate_to_wallet"><h2 class="cdbbc-wallet-title">' . $wallet_title . '</h2><p class="cdbbc-wallet-desc">' . $wallet_desc . '</p>' . cdbbc_wallet_html(false) . '</div></div>';
                            } else {
                                $output .= '<h6>' . __('Please Add coin wallet address in plugin settings panel', 'cdbbc') . '</h6>';

                            }
                        }
                        $output .= '</div>';
                    }
                } else {
                    $output .= '<h6>' . __('Please Add coin wallet address in plugin settings panel', 'cdbbc') . '</h6>';
                }
            } else {
                if ($metamask_wall_add != '') {
                    $output .= '
                        <div class="cdbbc-wallets-style-wrapper" >
                        <h2 class="cdbbc-wallet-title">' . wp_kses_post($wallet_title) . '</h2>
                        <p class="cdbbc-wallet-desc">' . wp_kses_post($wallet_desc) . '</p>';
                    $output .= cdbbc_wallet_html(false);

                    $output .= '<div class="message"></div></div>';
                } else {
                    $output .= '<h6>' . esc_html__('Please Add Receiver Payment address in the settings panel', 'cdbbc') . '</h6>';
                }

            }
            $dynamic_style = '
     .cdbbc_donation_wrap,.modal.cdbbc_wrap_popup,.cdbbc_main_popup_wrap .cdbbc_popup
     {
        ' . $bg_color . ';
        ' . $title_color . ';
        ' . $title_font . ';
        ' . $title_font_family . ';
        ' . $title_font_align . ';
        ' . $title_font_transform . ';
        ' . $title_font_weight . ';

        ' . $content_color . ';
        ' . $content_font . ';
        ' . $content_font_family . ';
        ' . $content_font_align . ';
        ' . $content_font_transform . ';
        ' . $content_font_weight . ';

          ' . $wallet_bg_color . ';
        ' . $wallet_title_color . ';
        ' . $wallet_title_font . ';
        ' . $wallet_title_family . ';
        ' . $wallet_title_align . ';
       ' . $wallet_title_transform . ';
        ' . $wallet_title_weight . ';

        ' . $wallet_content_color . ';
        ' . $wallet_content_font . ';
        ' . $wallet_content_family . ';
        ' . $wallet_content_align . ';
        ' . $wallet_content_transform . ';
        ' . $wallet_content_weight . ';
    }
    ' . $custom_css . '
    ';
            $output .= '</div>';
            wp_add_inline_style('cdbbc-styles', $dynamic_style);
            return $output;
        }
    }

}
new CDBBC_shortcode();
