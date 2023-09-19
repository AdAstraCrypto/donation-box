<?php defined('ABSPATH') || exit;
/*
|--------------------------------------------------------------------------
|  Register admin setting panel fields
|--------------------------------------------------------------------------
*/

if (class_exists('CSF')):
    class CDBBC_CSF extends CSF
    {
    }
    $prefix = 'cdbbc_settings';
    $veryfy_activation = "";
    $resgister_site = "";
    if (isset($_SERVER['HTTPS']) && 'on' == strtolower($_SERVER['HTTPS']) && get_option('cdbbc_email_verification')) {
        if (is_admin()) {
            $veryfy_activation = CdbbcMetaApi::getActivationStatus(CDBBC_PLUGIN_NAME);
        }

    }
    if ($veryfy_activation == "registered") {
        $resgister_site = array(
            'type' => 'submessage',
            'style' => 'success',
            'dependency' => array('share_donars_data', '==', true),
            'content' => __('The plugin has been activated successfully!', 'cdbbc'),
        );
    } else {
        $resgister_site = array(
            'id' => 'admin_email',
            'title' => __('Enter Email', 'cdbbc'),
            'type' => 'text',
            'class' => 'cdbbc_admin_email',
            'default' => get_option('admin_email'),
            'help' => esc_html__('Make sure to use Permalinks >> Post name (/%postname%/) before activating this plugin. ', 'cdbbc'),
            'desc' => 'By registering your site with BlackWorks.io(Plugin Author), you are agreeing to these <a href="https://www.blackworks.io/donationboxterms" target="_blank">terms</a> and <a href="https://www.blackworks.io/donationboxprivacy" target="_blank">privacy policy.</a><br>Save settings once if email address updated.<br><span style="color:red"> Make sure to use <b>Permalinks >> Post name (/%postname%/)</b> before activating this plugin. </span>',
            'after' => '<span class="button button-primary" id="cdbbc_register_site">Register Site</span><p class="cdbbc_response_msg"></p>',
            'dependency' => array('share_donars_data', '==', true),
        );
    }
    CDBBC_CSF::createOptions(
        $prefix,
        array(
            'framework_title' => esc_html__('Crypto Donation Box', 'cdbbc'),
            'menu_title' => "Crypto Donation",
            'menu_slug' => 'cdbbc-crypto-donations',
            'menu_capability' => 'manage_options',
            'menu_type' => 'menu',
            'menu_position' => 25,
            'menu_hidden' => false,
            'nav' => 'block',
            'menu_icon' => 'dashicons-share-alt',
            'show_bar_menu' => false,
            'show_sub_menu' => true,
            'show_reset_section' => false,
            'show_reset_all' => false,
            'footer_text' => '',
            'theme' => 'dark',

        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'title' => 'Quick Setup',
            'icon' => 'fas fa-running',
            'fields' => array(
                array(
                    'id' => 'user_wallet',
                    'title' => __('Common Donation ETH Address For Wallets <span style="color:red">(Required)</span>', 'cdbbc'),
                    'type' => 'text',
                    'placeholder' => '0x1dCXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                    'validate' => 'csf_validate_required_wallet',
                    'help' => esc_html__('Default wallet ETH address to receive payments', 'cdbbc'),
                    'desc' => esc_html__('Default wallet ETH address to receive payments ', 'cdbbc'),
                ),
                array(
                    'id' => 'infura_project_id',
                    'title' => __('Infura Project Id', 'cdbbc'),
                    'type' => 'text',
                    'help' => esc_html__('Please enter infura project id for Walletconnect to work ', 'cdbbc'),
                    'desc' => __('Get your infura project API-KEY by signing up  <a href="https://infura.io/register" target="_blank"> here</a>. Choose <b>Web3 API</b> as <b>network</b> and give a nice <b>name</b> of your choice. Copy the API-KEY from the next window.', 'cdbbc'),
                ),
                array(
                    'id' => 'tutorial_videos',
                    'title' => esc_html__('Tutorial Videos', 'cdbbc'),
                    'type' => 'fieldset',
                    'fields' => array(
                        array(
                            'type' => 'content',
                            'content' => '<a href="https://vimeo.com/825029621" target="_blank">Tutorial for basic setup</a>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => '<a href="https://www.veed.io/view/1a3c733e-ee1d-4ea3-a875-28235f0bead9?panel=share" target="_blank">Tutorial for advanced settings</a>',
                        ),

                    ),
                ),
                array(
                    'id' => 'rules_desc',
                    'title' => esc_html__('Important Guidelines', 'cdbbc'),
                    'type' => 'fieldset',
                    'fields' => array(
                        array(
                            'type' => 'content',
                            'content' => 'Please ensure that the wallet address provided is accurate and not a coin address. <strong>Failure to provide a valid wallet address will result in the inability to accept donations.</strong>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => 'Get your infura project API-KEY by signing up  <strong><a href="https://infura.io/register" target="_blank"> here</a></strong>. Choose <b>Web3 API</b> as <b>network</b> and give a nice <b>name</b> of your choice. Copy the API-KEY from the next window.',
                        ),
                        array(
                            'type' => 'content',
                            'content' => 'On the <a href="admin.php?page=cdbbc-crypto-donations#tab=networks-for-wallets" target="_blank">Networks For Wallets</a> settings page, enable only the desired networks and coins for visibility to users. <strong>Ensure that any custom coins added have accurate information.</strong>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => 'To add custom coins, navigate to the <a href="admin.php?page=cdbbc-crypto-donations#tab=networks-for-wallets" target="_blank">Networks For Wallets</a> settings page and adjust settings as needed.',
                        ),
                        array(
                            'type' => 'content',
                            'content' => 'To display specific coins in the UI, use the "show-coin" attribute and list the desired coins <strong>(e.g. show-coin="bitcoin,ethereum").</strong>',
                        ),

                    ),
                ),
            )
        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'title' => 'Donation Via Wallets',
            'icon' => 'fas fa-wallet',
            'fields' => array(
                array(
                    'id' => 'wallet_notice',
                    'title' => __('Wallet Notice', 'cdbbc'),
                    'type' => 'content',
                    'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                    'dependency' => array('user_wallet', '==', '', 'all'),
                ),
                array(
                    'id' => 'share_user_data',
                    'title' => esc_html__('Activate Fraud Detection', 'cdbbc'),
                    'type' => 'fieldset',
                    'class' => 'hidden',
                    'fields' => array(
                        array(
                            'id' => 'share_donars_data',
                            'title' => esc_html__('Register Your Site', 'cdbbc'),
                            'type' => 'switcher',
                            'help' => esc_html__('Activate Fraud Detection', 'cdbbc'),
                            'class' => 'hidden',
                            'default' => true,
                        ),
                        $resgister_site,

                    ),

                ),

                array(
                    'id' => 'supported_wallets',
                    'title' => esc_html__('Accept Donation Via Wallets', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'metamask_wallet',
                            'title' => esc_html__('MetaMask Wallet', 'cdbbc'),
                            'type' => 'checkbox',
                            'help' => esc_html__('this wallet for payment', 'cdbbc'),
                            'default' => true,
                        ),
                        array(
                            'id' => 'binance_wallet',
                            'title' => esc_html__('Binance Wallet', 'cdbbc'),
                            'type' => 'checkbox',
                            'default' => true,
                        ),
                        array(
                            'id' => 'trust_wallet',
                            'title' => esc_html__('Trust Wallet', 'cdbbc'),
                            'type' => 'checkbox',
                            'default' => true,
                        ),
                        array(
                            'id' => 'wallet_connect',
                            'title' => esc_html__('Wallet Connect', 'cdbbc'),
                            'type' => 'checkbox',
                            'default' => true,
                        ),
                        // array(
                        //     'id' => 'qr',
                        //     'title' => esc_html__('Enable Wallet Connect', 'cdbbc'),
                        //     'type' => 'switcher',
                        //     'default' => true,
                        // ),

                    ),

                ),

                array(
                    'id' => 'wallet_title_desc',
                    'title' => esc_html__('Wallet Settings', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'wallet_main_title',
                            'title' => __('Wallet Title', 'cdbbc'),
                            'type' => 'text',
                            'placeholder' => 'Title',
                            'default' => 'Donate Via Wallets',
                        ),
                        array(
                            'id' => 'wallet_main_desc',
                            'type' => 'textarea',
                            'title' => 'Description',
                            'desc' => esc_html__('Wallet description', 'cdbbc'),
                            'default' => 'Select a wallet to accept donation in ETH BNB BUSD etc..',
                        ),
                        array(
                            'id' => 'wallet_terms_condition',
                            'type' => 'textarea',
                            'title' => 'Terms&Conditions',
                            'desc' => esc_html__('These terms will be visible to a donor if he is making a donation via a wallet.', 'cdbbc'),
                            'default' => 'By making a donation you are agreeing to share your transaction data with website owner',
                        ),

                    ),

                ),

            ),

        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'id' => 'add_coins_tokens',
            'title' => esc_html__('Add Coins/Tokens', 'cdbbc'),
            'icon' => 'fa fa-btc',
            'fields' => array(
                array(
                    'id' => 'general_tokens',
                    'type' => 'group',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'accordion_title_by' => array('coin'),
                    'title' => 'Add Coins/Tokens ETH Address To Accept Donations',
                    'fields' => array(

                        array(
                            'id' => 'coin_type',
                            'type' => 'radio',
                            'title' => false,
                            'inline' => true,
                            'class' => 'cdbbc_coin_type',
                            'options' => array(
                                'popular' => 'Popular wallet/coin',
                                'custom' => 'Custom wallet/coin',
                            ),
                            'default' => 'popular',

                        ),
                        array(
                            'id' => 'coin',
                            'type' => 'select',
                            'title' => 'Select coin or token',
                            'dependency' => array('coin_type', '==', 'popular'),
                            'placeholder' => 'Select coin or token',
                            'class' => 'cdbbc_popular_coin',
                            'after' => ' <span class="cdbbc_selected_coin">up to</span>',
                            'options' => CDBBC_supported_coins(),
                            'desc' => esc_html__('You can use this coin id to show selected coins in your shortcode', 'cdbbc'),
                            'default' => 'bitcoin',
                        ),
                        // array(
                        //     'id' => 'wallet_address',
                        //     'title' => __('Enter address', 'cdbbc'),
                        //     'type' => 'text',
                        //     'placeholder' => '0x1dCXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                        //     'validate' => 'csf_validate_required',
                        //     'dependency' => array('coin_type', '==', 'popular'),
                        //     'help' => esc_html__('Default wallet address to receive payments ', 'cdbbc'),
                        //     'desc' => esc_html__('Default wallet address to receive payments ', 'cdbbc'),
                        // ),
                        array(
                            'id' => 'tag_note',
                            'title' => __('Tag/Note (if any)', 'cdbbc'),
                            'type' => 'text',
                            'dependency' => array('coin_type', '==', 'popular'),
                            'help' => esc_html__('Enter Tag/Note if have any', 'cdbbc'),
                            'desc' => esc_html__('Enter Tag/Note if have any', 'cdbbc'),
                        ),

                        array(
                            'id' => 'coin_name',
                            'title' => __('Coin Name', 'cdbbc'),
                            'type' => 'text',
                            'class' => 'cdbbc_custom_coin',
                            'dependency' => array('coin_type', '==', 'custom'),
                        ),
                        array(
                            'id' => 'coin_symbol',
                            'title' => __('Coin Symbol', 'cdbbc'),
                            'type' => 'text',
                            'dependency' => array('coin_type', '==', 'custom'),
                        ),
                        array(
                            'id' => 'coin_id',
                            'title' => __('Coin Id', 'cdbbc'),
                            'type' => 'text',
                            'dependency' => array('coin_type', '==', 'custom'),
                            'help' => esc_html__('You can use this coin id to show selected coins in your shortcode', 'cdbbc'),
                            'desc' => esc_html__('You can use this coin id to show selected coins in your shortcode', 'cdbbc'),
                        ),
                        // array(
                        //     'id' => 'wallet_address_custom',
                        //     'title' => __('Enter Wallet address', 'cdbbc'),
                        //     'type' => 'text',
                        //     'dependency' => array('coin_type', '==', 'custom'),
                        // ),
                        array(
                            'id' => 'logo',
                            'type' => 'upload',
                            'title' => 'Logo',
                            'library' => 'image',
                            'placeholder' => 'http://',
                            'button_title' => 'Add Image',
                            'dependency' => array('coin_type', '==', 'custom'),
                            'remove_title' => 'Remove Image',
                        ),
                        array(
                            'id' => 'tag_note_custom',
                            'title' => __('Tag/Note (if any)', 'cdbbc'),
                            'type' => 'text',
                            'dependency' => array('coin_type', '==', 'custom'),
                            'help' => esc_html__('Enter Tag/Note if have any', 'cdbbc'),
                            'desc' => esc_html__('Enter Tag/Note if have any', 'cdbbc'),
                        ),
                    ),
                ),
                array(
                    'id' => 'enable_wallet_in_design',
                    'title' => esc_html__('Enable Donation Via Wallets In', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'wallet_tab_style',
                            'title' => esc_html__('Tabular Style', 'cdbbc'),
                            'type' => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id' => 'wallet_list_style',
                            'title' => esc_html__('List Style', 'cdbbc'),
                            'type' => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id' => 'wallet_popup_style',
                            'title' => esc_html__('Popup Style', 'cdbbc'),
                            'type' => 'switcher',
                            'default' => true,
                        ),

                    ),

                ),
                array(
                    'id' => 'wallet_notice',
                    'title' => __('Wallet Notice', 'cdbbc'),
                    'type' => 'content',
                    'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                    'dependency' => array('user_wallet', '==', '', 'all'),
                ),
                array(
                    'id' => 'tile_desc_settings',
                    'title' => esc_html__('DONATION BOX CONTENT SETTINGS', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'main_title',
                            'title' => __('Main Title', 'cdbbc'),
                            'type' => 'text',
                            'placeholder' => 'Title',
                            'help' => esc_html__('Use [coin-name]([coin-symbol]) to dynamically change coin name like Bitcoin(BTC),Ethereum(ETH) etc.', 'cdbbc'),
                            'desc' => esc_html__('Use [coin-name]([coin-symbol]) to dynamically change coin name like Bitcoin(BTC),Ethereum(ETH) etc. ', 'cdbbc'),
                            'default' => 'Donate [coin-name]([coin-symbol]) to this address',
                        ),
                        array(
                            'id' => 'main_desc',
                            'type' => 'textarea',
                            'title' => 'Description',
                            'desc' => esc_html__('Use [coin-name]([coin-symbol]) to dynamically change coin name like Bitcoin(BTC),Ethereum(ETH) etc. ', 'cdbbc'),
                            'default' => 'Scan the QR code or copy the address below into your wallet to send some [coin-name]([coin-symbol])',
                        ),

                    ),

                ),

            ),
        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'title' => 'Networks For Wallets',
            'icon' => 'fas fa-network-wired',
            'fields' => array(
                array(
                    'id' => 'wallet_notice',
                    'title' => __('Wallet Notice', 'cdbbc'),
                    'type' => 'content',
                    'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                    'dependency' => array('user_wallet', '==', '', 'all'),
                ),
                array(
                    'id' => 'custom_networks',
                    'title' => esc_html__('Accepted Networks', 'cdbbc'),
                    'type' => 'group',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'accordion_title_by' => array('chainName', 'enable'),
                    'accordion_title_by_prefix' => ' | ',
                    'button_title' => esc_html__('Add new', 'cdbbc'),
                    'default' => cdbbc_default_networks(),

                    'fields' => array(
                        array(
                            'id' => 'enable',
                            'title' => esc_html__('Enable Network', 'cdbbc'),
                            'type' => 'switcher',
                            'desc' => 'Get your network details <a href="https://chainlist.org/" target="_blank">Click Here</a>',
                            'help' => esc_html__('Enable this network for payment', 'cdbbc'),
                            'default' => true,
                        ),

                        array(
                            'id' => 'recever_wallet',
                            'title' => __('Payment ETH Address', 'cdbbc'),
                            'type' => 'text',
                            'placeholder' => '0x1dCXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                            'dependency' => array('enable', '==', true),
                            'desc' => '<strong>Leave this field empty if you want to use default payment ETH address.</strong>',
                            'help' => esc_html__('Leave this field empty if you want to use default payment ETH address.', 'cdbbc'),
                        ),

                        array(
                            'title' => esc_html__('Network Name', 'cdbbc'),
                            'id' => 'chainName',
                            'dependency' => array('enable', '==', true),
                            'type' => 'text',
                        ),
                        array(
                            'title' => esc_html__('Network RPC URL', 'cdbbc'),
                            'id' => 'rpcUrls',
                            'dependency' => array('enable', '==', true),
                            'type' => 'text',
                        ),
                        array(
                            'title' => esc_html__('Network Chain ID', 'cdbbc'),
                            'id' => 'chainId',
                            'dependency' => array('enable', '==', true),
                            'class' => 'cdbbc_chain_id',
                            'type' => 'text',
                        ),
                        array(
                            'title' => esc_html__('Block Explorer URL', 'cdbbc'),
                            'id' => 'blockExplorerUrls',
                            'dependency' => array('enable', '==', true),
                            'type' => 'text',
                        ),
                        array(
                            'id' => 'wallet_notice',
                            'title' => __('Wallet Notice', 'cdbbc'),
                            'type' => 'content',
                            'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                            'dependency' => array('user_wallet', '==', '', 'all'),
                        ),
                        array(
                            'id' => 'nativeCurrency',
                            'dependency' => array('enable', '==', true),
                            'type' => 'fieldset',
                            'dependency' => array('user_wallet', '!=', '', 'all'),
                            'title' => esc_html__('Network Main Currency', 'cdbbc'),
                            'fields' => array(
                                array(
                                    'id' => 'enable',
                                    'title' => esc_html__('Enable Currency', 'cdbbc'),
                                    'type' => 'switcher',
                                    'help' => esc_html__('Enable this currency', 'cdbbc'),
                                    'default' => true,
                                ),
                                array(
                                    'id' => 'name',
                                    'type' => 'text',
                                    'dependency' => array('enable', '==', true),
                                    'title' => esc_html__('Name', 'cdbbc'),
                                ),
                                array(
                                    'id' => 'symbol',
                                    'type' => 'text',
                                    'dependency' => array('enable', '==', true),
                                    'title' => esc_html__('Symbol', 'cdbbc'),
                                ),
                                array(
                                    'id' => 'decimals',
                                    'type' => 'number',
                                    'dependency' => array('enable', '==', true),
                                    'title' => esc_html__('Decimals', 'cdbbc'),
                                ),
                                array(
                                    'title' => esc_html__('Image', 'cdbbc'),
                                    'id' => 'image',
                                    'dependency' => array('enable', '==', true),
                                    'type' => 'upload',
                                ),
                                array(
                                    'id' => 'token_price',
                                    'type' => 'number',
                                    'dependency' => array('enable', '==', true),
                                    'title' => 'Default Price',
                                    'desc' => esc_html__('Default donation amount', 'cdbbc'),
                                ),

                            ),
                        ),
                        array(
                            'id' => 'wallet_notice',
                            'title' => __('Wallet Notice', 'cdbbc'),
                            'type' => 'content',
                            'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                            'dependency' => array('user_wallet', '==', '', 'all'),
                        ),
                        array(
                            'id' => 'currencies',
                            'dependency' => array('enable', '==', true),
                            'type' => 'group',
                            'dependency' => array('user_wallet', '!=', '', 'all'),
                            'accordion_title_by' => array('symbol', 'enable'),
                            'accordion_title_by_prefix' => ' | ',
                            'title' => esc_html__('Tokens', 'cdbbc'),
                            'button_title' => esc_html__('Add new', 'cdbbc'),
                            'fields' => array(
                                array(
                                    'id' => 'enable',
                                    'title' => esc_html__('Enable Currency', 'cdbbc'),
                                    'type' => 'switcher',
                                    'help' => esc_html__('Enable this Token to show in Network', 'cdbbc'),
                                    'default' => true,
                                ),
                                array(
                                    'title' => esc_html__('Symbol', 'cdbbc'),
                                    'id' => 'symbol',
                                    'dependency' => array('enable', '==', true),
                                    'type' => 'text',
                                ),
                                array(
                                    'title' => esc_html__('Contract Address', 'cdbbc'),
                                    'id' => 'contract_address',
                                    'placeholder' => '0x1dCXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                                    'dependency' => array('enable', '==', true),
                                    'type' => 'text',
                                ),
                                array(
                                    'title' => esc_html__('Image', 'cdbbc'),
                                    'id' => 'image',
                                    'dependency' => array('enable', '==', true),
                                    'type' => 'upload',
                                ),
                                array(
                                    'id' => 'token_price',
                                    'type' => 'number',
                                    'dependency' => array('enable', '==', true),
                                    'title' => 'Default Price',
                                    'desc' => esc_html__('Default donation amount', 'cdbbc'),
                                ),

                            ),
                        ),
                    ),
                ),
            ),

        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'title' => 'Style Settings',
            'icon' => 'fas fa-palette',
            'fields' => array(
                array(
                    'id' => 'donation_settings_style',
                    'title' => esc_html__('Coins/Tokens Style', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'main_title_typography',
                            'title' => 'Title',
                            'type' => 'typography',
                            'line_height' => false,
                            'subset' => false,
                            'letter_spacing' => false,

                        ),
                        array(
                            'id' => 'main_content_typography',
                            'title' => 'Content',
                            'type' => 'typography',
                            'line_height' => false,
                            'subset' => false,
                            'letter_spacing' => false,

                        ),
                        array(
                            'id' => 'main_bg_color',
                            'type' => 'color',
                            'default' => '#fff',
                            'title' => 'Background Color',
                        ),

                    ),

                ),
                array(
                    'id' => 'wallet_notice',
                    'title' => __('Wallet Notice', 'cdbbc'),
                    'type' => 'content',
                    'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                    'dependency' => array('user_wallet', '==', '', 'all'),
                ),
                array(
                    'id' => 'donation_wallet_style',
                    'title' => esc_html__('Wallet Style', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'id' => 'wallet_title_typo',
                            'title' => 'Title',
                            'type' => 'typography',
                            'line_height' => false,
                            'subset' => false,
                            'letter_spacing' => false,

                        ),
                        array(
                            'id' => 'wallet_content_typo',
                            'title' => 'Content',
                            'type' => 'typography',
                            'line_height' => false,
                            'subset' => false,
                            'letter_spacing' => false,

                        ),
                        array(
                            'id' => 'wallet_bg_color',
                            'type' => 'color',
                            'default' => '#fff',
                            'title' => 'Background Color',
                        ),

                    ),

                ),

                array(
                    'id' => 'cdbbc_custom_css',
                    'type' => 'code_editor',
                    'title' => 'Custom CSS',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'settings' => array(
                        'theme' => 'default',
                        'mode' => 'css',
                    ),
                    'default' => '',
                ),
            ),

        )
    );

    CDBBC_CSF::createSection(
        $prefix,
        array(
            'title' => 'Shortcodes',

            'icon' => 'fas fa-code',
            'fields' => array(
                array(
                    'id' => 'wallet_notice',
                    'title' => __('Wallet Notice', 'cdbbc'),
                    'type' => 'content',
                    'content' => __('Please enter your Common Donation ETH Address For Wallets to access the rest of the settings. Click <a href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '" target="_blank">here</a> to get redirected to the relevant page', 'cdbbc'),
                    'dependency' => array('user_wallet', '==', '', 'all'),
                ),
                array(
                    'id' => 'tab_shortcode',
                    'title' => esc_html__('Donation Box Shortcode Settings', 'cdbbc'),
                    'type' => 'fieldset',
                    'dependency' => array('user_wallet', '!=', '', 'all'),
                    'fields' => array(
                        array(
                            'type' => 'content',
                            'content' => '<strong>Wallet Shortcode </strong><code>[crypto-donation-box type="wallet"]</code>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => '<strong>Tabular Shortcode </strong><code>[crypto-donation-box type="tabular" show-coin="all"]</code>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => '<strong>Popup Shortcode </strong><code>[crypto-donation-box type="popup" show-coin="all"]</code>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => '<strong>List Shortcode </strong><code>[crypto-donation-box type="list" show-coin="all"]</code>',
                        ),
                        array(
                            'type' => 'content',
                            'content' => '<strong>Supported Attributes </strong>You can show selected coin by definig attributes like: <strong>show-coin="bitcoin,ethereum" </strong>',
                        ),

                    ),

                ),

            ),

        )
    );

endif;