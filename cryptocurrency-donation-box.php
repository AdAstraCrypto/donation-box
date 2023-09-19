<?php
/**
 * Plugin Name:Cryptocurrency Donation Box - Bitcoin & Crypto Donations
 * Description:Create cryptocurrency donation box and accept crypto coin payments, show coin address and QR code - best bitcoin & crypto donations plugin.
 * Plugin URI:https://www.adastracrypto.com
 * Author: AdAstraCrypto
 * Author URI:https://www.adastracrypto.com
 * Version: 2.2.8
 * License: GPL2
 * Text Domain:CDBBC
 * Domain Path: /languages
 *
 * @package Cryptocurrency_Donation_Box
 */

define('CDBBC_DIR', __DIR__ . '/');
define('CDBBC_URI', plugins_url('/', __FILE__));
if (!defined('ABSPATH')) {
    exit;
}

if (defined('CDBBC_VERSION')) {
    return;
}

define('CDBBC_VERSION', '2.2.8');
define('CDBBC_FILE', __FILE__);
define('CDBBC_PATH', plugin_dir_path(CDBBC_FILE));
define('CDBBC_URL', plugin_dir_url(CDBBC_FILE));
define('CDBBC_PLUGIN_NAME', 'donationbox');

register_activation_hook(CDBBC_FILE, array('Cryptocurrency_Donation_Box', 'activate'));
register_deactivation_hook(CDBBC_FILE, array('Cryptocurrency_Donation_Box', 'deactivate'));

/**
 * Class Cryptocurrency_Donation_Box
 */
final class Cryptocurrency_Donation_Box
{

    /**
     * Plugin instance.
     *
     * @var Cryptocurrency_Donation_Box
     * @access private
     */
    private static $instance = null;

    /**
     * Get plugin instance.
     *
     * @return Cryptocurrency_Donation_Box
     * @static
     */
    public static function get_instance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @access private
     */
    private function __construct()
    {
        $this->cdbbc_includes();
        add_action('admin_init', array($this, 'CDBBC_do_activation_redirect'));
        add_action('plugins_loaded', array($this, 'cdbbc_load_lang'));
        add_action('init', array($this, 'cdbbc_plugin_version_verify'));
        if (is_admin()) {
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'cdbbc_setting_panel_action_link'));
            // notice for review
            require_once CDBBC_PATH . '/admin/class.review-notice.php';
            add_action('admin_enqueue_scripts', array($this, 'cdbbc_admin_scripts'));
            add_action('admin_notices', array($this, 'cdbbc_admin_notice_warn'));
            add_action('admin_menu', array($this, 'cdbbc_add_submenu_page'), 31);

        }
        add_action('wp_ajax_nopriv_cdbbc_payment_verify', array('CDBBC_CONFIRM_TRANSACTION', 'cdbbc_payment_verify'));
        add_action('wp_ajax_cdbbc_payment_verify', array('CDBBC_CONFIRM_TRANSACTION', 'cdbbc_payment_verify'));
        add_action('wp_ajax_cdbbc_activate_site', array('CDBBC_CONFIRM_TRANSACTION', 'cdbbc_activate_site'));

        add_action('rest_api_init', array($this, 'cdbbc_auth_on_restapi_init'));

    }
    
    public function cdbbc_auth_on_restapi_init($server)
    {
        CdbbcMetaApi::registerRoutes(CDBBC_PLUGIN_NAME.'/v1');
    }
    
    public function cdbbc_add_submenu_page()
    {

        add_submenu_page('cdbbc-crypto-donations', 'Donation Box Transaction', 'Transaction', 'manage_options', 'cdbbc-transaction', array('CDBBC_TRANSACTION_TABLE', 'cdbbc_transaction_table'), 31);

    }
    public function add_admin_menu($context)
    {
        $activated = get_option('cddbc_activated');

        if (!$activated) {
            $status ="";
            
            $status = CddbcMetaApi::getActivationStatus(CDBBC_PLUGIN_NAME);
            
            if ('registered' === $status) {
                update_option('cddbc_activated', 1);
            }
        }

        if (!get_option('cddbc_activated')) {
            $this->hook_name = cdbbc_add_submenu_page();
        }
    }

    
    /*
    |--------------------------------------------------------------------------
    |  admin noticce for add infura project key
    |--------------------------------------------------------------------------
     */

    public function cdbbc_admin_notice_warn()
    {

        $donation_settings = get_option('cdbbc_settings');
        $supported_wallets = isset($donation_settings['supported_wallets'])?$donation_settings['supported_wallets']:"";
        $wallet_enabled=false;
        if(!empty($supported_wallets)){
            foreach ($supported_wallets as $key => $value) {
                if($value=="1"){
                    $wallet_enabled = true;

                }
                
            }
        }
        if (empty($donation_settings['infura_project_id']) && $wallet_enabled==true) {
            echo '<div class="notice notice-error is-dismissible">
        <p>Important:Please enter an infura API-KEY for Donation Box Plugin to work <a style="font-weight:bold" href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '">Link</a></p>
        </div>';
        }
        if (empty($donation_settings['user_wallet'])) {
            echo '<div class="notice notice-error is-dismissible">
        <p>Important:Please enter your wallet address for Donation Box Plugin to work <a style="font-weight:bold" href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '">Link</a></p>
        </div>';
        }
    }

    /*
    |--------------------------------------------------------------------------
    |  admin style
    |--------------------------------------------------------------------------
     */
    public function cdbbc_admin_scripts($hook)
    {

        if ($hook == "toplevel_page_cdbbc-crypto-donations") {
            wp_enqueue_style('cdbbc-custom-admin-style', CDBBC_URL . 'assets/css/cdbbc-admin.css', null, CDBBC_VERSION);

            wp_enqueue_script('cdbbc-custom-admin-scripts', CDBBC_URL . 'assets/js/cdbbc-replace.js', array('jquery'), CDBBC_VERSION);
            wp_localize_script('cdbbc-custom-admin-scripts', "wallets_data",
                array(
                    "ajax" => home_url('/wp-admin/admin-ajax.php'),
                    "nonce" => wp_create_nonce('cdbbc_donation_box'),
                )
            );

        }
    }
    /**
     * Load plugin function
     */
    public function cdbbc_includes()
    {

        require_once CDBBC_PATH . '/includes/cdbbc-shortcode.php';
        require_once CDBBC_PATH . '/includes/functions.php';
        require_once CDBBC_PATH . '/includes/db/cdbbc-db.php';
 require_once CDBBC_PATH . 'includes/php-jwt/include-jwt.php';
        require_once CDBBC_PATH . 'includes/Api.php';
        require_once CDBBC_PATH . '/includes/cdbbc-payment-verify.php';
        require_once CDBBC_PATH . 'admin/table/cdbbc-transaction-table.php';
        require_once CDBBC_PATH . 'admin/table/cdbbc-list-table.php';
    
        require_once CDBBC_PATH . 'admin/codestar-framework/codestar-framework.php';
        require_once CDBBC_PATH . 'admin/options-settings.php';
        require_once CDBBC_PATH . 'admin/class-plugin-activation.php';  
        require __DIR__ . '/admin/hooks.php';
   

    }

    /*
    |--------------------------------------------------------------------------
    |  check admin side post type page
    |--------------------------------------------------------------------------
     */
    public function cdbbc_get_post_type_page()
    {
        global $post, $typenow, $current_screen;

        if ($post && $post->post_type) {
            return $post->post_type;
        } elseif ($typenow) {
            return $typenow;
        } elseif ($current_screen && $current_screen->post_type) {
            return $current_screen->post_type;
        } elseif (isset($_REQUEST['page'])) {
            return sanitize_key($_REQUEST['page']);
        } elseif (isset($_REQUEST['post_type'])) {
            return sanitize_key($_REQUEST['post_type']);
        } elseif (isset($_REQUEST['post'])) {
            return get_post_type(sanitize_text_field($_REQUEST['post']));
        }
        return null;
    }

    /**
     * Code you want to run when all other plugins loaded.
     */
    public function cdbbc_load_lang()
    {
       

        load_plugin_textdomain('CDBBC', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    /**
     * Run when activate plugin.
     */
    public static function activate()
    {
        $db = new CDBBC_database();
        $db->create_table();

        add_option('CDBBC_do_activation_redirect', true);
        update_option('cdbbc_activation_time', gmdate('Y-m-d h:i:s'));
        update_option('cdbbc-alreadyRated', 'no');
        update_option('CDBBC_FRESH_INSTALLATION', CDBBC_VERSION);
        update_option('cdbbc_migarte_codestar', 'migrated');
        
        CdbbcMetaApi::setupKeypair();
    }

    public static function update()
    {
        add_option('CDBBC_do_activation_redirect', true);
        if(empty(get_option('cdbbc_activation_time'))){
            update_option('cdbbc_activation_time', gmdate('Y-m-d h:i:s'));
        }
        CdbbcMetaApi::setupKeypair();
    }

    public static function updateFallback()
    {
        try {
            CdbbcMetaApi::setupKeypair();
            $email = get_option('admin_email');
            $plugin = 'donationbox';
            $status = CdbbcMetaApi::getActivationStatus($plugin);
            if (!$status) {
                $status = CdbbcMetaApi::registerSite($plugin, $email);
                sleep(1);
                if (!$status) {
                    self::update();
                }
            }
        } catch (\Exception $e) {
            // handle the exception
            error_log('some error happened: ' . $e->getMessage());
        }
    }    

    public function CDBBC_do_activation_redirect()
    {
        if (get_option('CDBBC_do_activation_redirect', false)) {
            delete_option('CDBBC_do_activation_redirect');
            if (!isset($_GET['activate-multi'])) {
                try {
                    if(!current_user_can('manage_options')){
                        wp_safe_redirect('admin.php?page=cdbbc-crypto-donations#tab=quick-setup');
                        exit;
                    }
                    else{
                        wp_safe_redirect('admin.php?page=cdbbc-activation');
                        if(get_status_header_desc(http_response_code()) === 'OK'){
                            wp_safe_redirect('admin.php?page=cdbbc-activation');
                            exit;
                        }
                        wp_safe_redirect('admin.php?page=cdbbc-crypto-donations#tab=quick-setup');
                        exit;
                    }
                } catch (\Exception $e) {
                    error_log('some error happened: ' . $e->getMessage());
                }
            }
        }
    }
    /*
    |--------------------------------------------------------------------------
    |  Check if plugin is just updated from older version to new
    |--------------------------------------------------------------------------
     */
    public function cdbbc_plugin_version_verify()
    {

        $CDBBC_VERSION = get_option('CDBBC_FREE_VERSION');
        if (!isset($CDBBC_VERSION) || version_compare($CDBBC_VERSION, CDBBC_VERSION, '<')) {
            if (empty(get_option('cdbbc_migarte_settings')) && empty(get_option('CDBBC_FRESH_INSTALLATION'))) {
                cdbbc_migrate_data();
                update_option('cdbbc_migarte_settings', 'migrated');
            }
            if (empty(get_option('cdbbc_migarte_codestar'))) {
                $db = new CDBBC_database();
                $db->create_table();

                cdbbc_migrate_codestar();
                update_option('cdbbc_migarte_codestar', 'migrated');
            }
            self::updateFallback();
            update_option('CDBBC_FREE_VERSION', CDBBC_VERSION);
        }
    }

    /**
     * Run when deactivate plugin.
     */
    public static function deactivate()
    {
        // $db = new CDBBC_database();
        // $db->drop_table();
        error_log("PLUGIN uninstalling");
        delete_option( 'cdbbc_settings' );

    }

    public function cdbbc_setting_panel_action_link($link)
    {
        $link[] = '<a style="font-weight:bold" href="' . esc_url(get_admin_url(null, 'admin.php?page=cdbbc-crypto-donations#tab=quick-setup')) . '">Settings</a>';
        return $link;
    }
}

function Cryptocurrency_Donation_Box()
{
    return Cryptocurrency_Donation_Box::get_instance();
}

$GLOBALS['Cryptocurrency_Donation_Box'] = Cryptocurrency_Donation_Box();