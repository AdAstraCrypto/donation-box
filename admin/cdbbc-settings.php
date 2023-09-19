<?php
if (!defined('ABSPATH')) {
    exit();
}

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
class CDBBC_option_settings
{
    function __construct()
    {
        if (is_admin()) {
            add_action('cmb2_admin_init', array($this,'cdbbc_option_settings'));
        }
    }

    function cdbbc_option_settings()
    {

        /**
         * Registers main options page menu item and form.
         */
        $prefix = "cdbbc_";
        $args = array(
        'id' => $prefix.'general_settings_tab',
        'title' => 'Crypto Donation Box Settings',
        'object_types' => array('options-page'),
        'menu_title' => "Crypto Donation",
        'icon_url'        => 'dashicons-share-alt',
        'option_key' => 'cdbbc-add-wallet',
        'position'        => 25,
        'tab_group' => 'Wallet_Settings',
        'tab_title' => 'Add wallet/coin',
        );

        // 'tab_group' property is supported in > 2.4.0.
        if (version_compare(CMB2_VERSION, '2.4.0')) {
            $args['display_cb'] = 'cdbbc_options_display_with_tabs';
        }

        $gernal_tab = new_cmb2_box($args);

        /**
         * Options fields ids only need
         * to be unique within this box.
         * Prefix is not needed.
         */
        // Repeatable group
        $group_repeat_test = $gernal_tab->add_field(array(
        'id' => $prefix . 'group_data',
        'type' => 'group',
        'options' => array(
            'group_title' => __('Wallet', 'your-text-domain') . ' {#}', // {#} gets replaced by row number
            'add_button' => __('Add New Wallet', 'your-text-domain'),
            'remove_button' => __('Remove Wallet', 'your-text-domain'),
            'sortable' => true, // beta
            'closed' => true,
        ),
        ));

        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => false,
        'id' => 'coin_type',
        'type' => 'radio_inline',
        'options' => array(
            'popular' => __('Popular wallet/coin', 'cmb2'),
            'custom' => __('Custom wallet/coin ', 'cmb2'),

        ),
        'default' => 'popular',
        ));
        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Select wallet, coin or token', 'cmc2'),
        'desc' => '',
        'id' => 'coin',
        'type' => 'select',
        'attributes' => array(
            'required' => true, // Will be required only if visible.
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'popular',
        ),
        'desc' => 'You can use below coin id to show selected coins in your shortcode',
        'options' => CDBBC_supported_coins(),

        ));

        // $gernal_tab->add_group_field($group_repeat_test, array(
        // 'name' => __('Enter address', 'cmc2'),
        // 'desc' => '',
        // 'id' => 'wallet_address',
        // 'attributes' => array(
        //     'required' => true, // Will be required only if visible.
        //     'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
        //     'data-conditional-value' => 'popular',
        // ),
        // 'type' => 'text',

        // ));

        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Tag/Note (if any)', 'cmc2'),
        'desc' => '',
        'id' => 'tag_note',
        'attributes' => array(
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'popular',
        ),
        'type' => 'text',
        'options' => CDBBC_supported_coins(),

        ));

        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Coin Name', 'cmc2'),
        'desc' => '',
        'id' => 'coin_name',
        'attributes' => array(
            'required' => true, // Will be required only if visible.
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'custom',
            'Coin_name_grp' => 'Coin_name_grp',

        ),
        'type' => 'text',

        ));
        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Coin Symbol', 'cmc2'),
        'desc' => '',
        'id' => 'coin_symbol',
        'attributes' => array(
            'required' => true, // Will be required only if visible.
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'custom',

        ),
        'type' => 'text',

        ));
        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Coin Id', 'cmc2'),
        'desc' => '',
        'id' => 'coin_id',
        'attributes' => array(
            'required' => true, // Will be required only if visible.
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'custom',

        ),
        'desc' => 'You can use this coin id to show selected coins in your shortcode',
        'type' => 'text',

        ));

        // $gernal_tab->add_group_field($group_repeat_test, array(
        // 'name' => __('Enter Wallet address', 'cmc2'),
        // 'desc' => '',
        // 'id' => 'wallet_address_custom',
        // 'attributes' => array(
        //     'required' => true, // Will be required only if visible.
        //     'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
        //     'data-conditional-value' => 'custom',

        // ),
        // 'type' => 'text',

        // ));

        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => __('Tag/Note:', 'cmc2'),
        'desc' => '',
        'id' => 'tag_note_custom',
        'attributes' => array(
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'custom',

        ),
        'type' => 'text',

        ));

        $gernal_tab->add_group_field($group_repeat_test, array(
        'name' => 'Logo',
        'desc' => 'Upload coin logo',
        'id' => 'logo',
        'attributes' => array(
            'required' => true, // Will be required only if visible.
            'data-conditional-id' => json_encode(array($group_repeat_test, 'coin_type')),
            'data-conditional-value' => 'custom',

        ),
        'type' => 'file',
        // Optional:
        'options' => array(
        'url' => false, // Hide the text input for the url
        ),
        'text' => array(
        'add_upload_file_text' => 'Upload logo', // Change upload button text. Default: "Add or Upload File"
        ),
        // query_args are passed to wp.media's library query.
        'query_args' => array(
        // 'type' => 'application/pdf', // Make library only display PDFs.
        // Or only allow gif, jpg, or png images
        'type' => array(
            'image/gif',
            'image/jpeg',
            'image/png',
        ),
        ),
        'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ));

    /**
     * Registers secondary options page, and set main item as parent.
     */
        $args = array(
        'id' => $prefix . 'extra_settings_tab',
        'title' => 'Crypto Donation Box Settings',
        'menu_title' => "↳ Settings", // Use menu title, & not title to hide main h2.
        'object_types' => array('options-page'),

        'option_key' => 'cdbbc-coin-settings',
        'parent_slug' => 'cdbbc-add-wallet',
        'tab_group' => 'Wallet_Settings',
        'tab_title' => 'Settings',
        );

// 'tab_group' property is supported in > 2.4.0.
        if (version_compare(CMB2_VERSION, '2.4.0')) {
            $args['display_cb'] = 'cdbbp_options_display_with_tabs';
        }

        $extra_tab = new_cmb2_box($args);

        $extra_tab->add_field(array(
        'name' => __('MetaMask wallet settings', 'cmc2'),
        'id' => 'metamask_settings_start',
        'type' => 'title',

        ));
        $extra_tab->add_field(array(
        'name' => __('Default Amount', 'cmc2'),
        'desc' => '',
        'id' => 'metamask_amount',
        'type' => 'text',
        'default' => '0.005',
        'desc' => 'Enter default amount for MetaMask',
        ));
        $extra_tab->add_field(array(
        'name' => __('MetaMask Title', 'cmc2'),
        'desc' => '',
        'id' => 'metamask_title',
        'type' => 'text',
        'desc' => 'Use [coin-name]([coin-network]) to dynamically change coin name like ETH(ERC20),BNB(BEP20) etc.',
        'default' => 'Donate [coin-name]([coin-network]) With MetaMask',
        ));

        $extra_tab->add_field(array(
        'name' => 'MetaMask Description',
        'id' => 'metamask_desc',
        'type' => 'textarea_small',
        'desc' => 'Use [coin-name]([coin-network]) to dynamically change coin name like ETH(ERC20),BNB(BEP20) etc.',
        'default' => 'Donate [coin-name]([coin-network]) With MetaMask',

        ));

        $extra_tab->add_field(array(
        'name' => __('Donation Box Content Settings', 'cmc2'),
        'id' => 'donation_settings_start',
        'type' => 'title',

        ));
        $extra_tab->add_field(array(
        'name' => __('Main Title', 'cmc2'),
        'desc' => '',
        'id' => 'main_title',
        'type' => 'text',
        'desc' => 'Use [coin-name]([coin-symbol]) to dynamically change coin name like Bitcoin(BTC),Ethereum(ETH) etc.',
        'default' => 'Donate [coin-name]([coin-symbol]) to this address',
        ));

        $extra_tab->add_field(array(
        'name' => 'Description',
        'id' => 'main_desc',
        'type' => 'textarea_small',
        'desc' => 'Use [coin-name]([coin-symbol]) to dynamically change coin name like Bitcoin(BTC),Ethereum(ETH) etc.',
        'default' => 'Scan the QR code or copy the address below into your wallet to send some [coin-name]([coin-symbol])',
        ));
        $extra_tab->add_field(array(
        'name' => __('Donation Box Style Settings', 'cmc2'),
        'id' => 'donation_settings_style',
        'type' => 'title',

        ));
        $extra_tab->add_field(array(
        'name' => 'Title Color',
        'id' => 'main_title_color',
        'type' => 'colorpicker',
        'default' => '#000',
        // 'options' => array(
        //     'alpha' => true, // Make this a rgba color picker.
        // ),
        ));
        $extra_tab->add_field(array(
        'name' => 'Content Color',
        'id' => 'main_content_color',
        'type' => 'colorpicker',
        'default' => '#000',
        // 'options' => array(
        //     'alpha' => true, // Make this a rgba color picker.
        // ),
        ));
        $extra_tab->add_field(array(
        'name' => 'Background  Color',
        'id' => 'main_bg_color',
        'type' => 'colorpicker',
        'default' => '#ffffff',
        // 'options' => array(
        //     'alpha' => true, // Make this a rgba color picker.
        // ),
        ));

        $extra_tab->add_field(array(
        'name' => __('Donation Box Shortcode Settings', 'cmc2'),
        'id' => 'tab_shortcode',
        'type' => 'title',
        'desc' => '<br><br><strong>METAMASK SHORTCODE </strong><code>[crypto-donation-box type="metamask" show-coin="all"]</code>
        <br><br><strong>TABULAR SHORTCODE </strong><code>[crypto-donation-box type="tabular" show-coin="all"]</code>
        <br><br><strong>POPUP SHORTCODE </strong><code>[crypto-donation-box type="popup" show-coin="all"]</code>
        <br><br><strong>LIST SHORTCODE </strong><code>[crypto-donation-box type="list" show-coin="all"]</code>
        <br><br><strong>SUPPORTED ATTRIBUTE </strong>You can show selected coin by definig attributes like: <strong>show-coin="bitcoin,ethereum" </strong>

        ',
        ));
    }
}

new CDBBC_option_settings();
