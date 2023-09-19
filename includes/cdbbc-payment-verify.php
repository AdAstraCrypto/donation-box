<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('CDBBC_CONFIRM_TRANSACTION')) {
    class CDBBC_CONFIRM_TRANSACTION
    {
        public static function cdbbc_payment_verify()
        {
            $nonce = !empty($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : "";
            if (!wp_verify_nonce($nonce, 'cdbbc_donation_box')) {
                die('error');
            }
            $donation_settings = get_option('cdbbc_settings');
            $share_data = isset($donation_settings['share_user_data']['share_donars_data']) ? $donation_settings['share_user_data']['share_donars_data'] : true;
            $feedback_url = "http://localhost/repo/wp-json/donation/v1/donars_data";
            $data_object = !empty($_REQUEST['data_object']) ? $_REQUEST['data_object'] : "";
            $custom_network = !empty($data_object['network_name']) ? $data_object['network_name'] : "";
            $retry_check1 = false;
            $retry_check2 = false;
            if(!empty($data_object['donation_network'])){
                switch ($data_object['donation_network']) {
                case '0x38':
                    $selected_network = 'mainnet';
                    $retry_check1 = true;
                    break;
                case '0x61':
                    $selected_network = 'testnet';
                    $retry_check1 = true;
                    break;
                case '0x89':
                    $selected_network = 'mainnet';
                    $retry_check1 = true;
                    break;
                case '0x13881':
                    $selected_network = 'testnet';
                    $retry_check1 = true;
                    break;
                case '0x5':
                    $selected_network = 'goerli';
                    $retry_check1 = true;
                    break;
                case '0x1':
                    $selected_network = 'mainnet';
                    $retry_check1 = true;
                    break;
                default:
                    $selected_network = $custom_network;
                    break;
                }
            }
            else{
                $selected_network = $custom_network;
            }
            
            $sender = !empty($_REQUEST['sender']) ? sanitize_text_field($_REQUEST['sender']) : "";
            $wallet_name = !empty($data_object['wallet_name']) ? $data_object['wallet_name'] : "";

            $amount = !empty($data_object['donation_amount']) ? $data_object['donation_amount'] : "";

            $reciever = !empty($_REQUEST['recever']) ? sanitize_text_field($_REQUEST['recever']) : "";
            $currency = !empty($data_object['symbol']) ? $data_object['symbol'] : "";
            if(!empty($data_object['donation_network'])) {
                switch ($data_object['donation_network']) {
                case '0x38':
                    $blockchain = 'binance-smart-chain';
                    $retry_check2 = true; 
                    break;
                case '0x61':
                    $blockchain = 'binance-smart-chain';
                    $retry_check2 = true; 
                    break;
                case '0x89':
                    $blockchain = 'polygon';
                    $retry_check2 = true; 
                    break;
                case '0x13881':
                    $blockchain = 'mumbai-polygon';
                    $retry_check2 = true; 
                    break;
                case '0x5':
                    $blockchain = 'goerli';
                    $retry_check2 = true; 
                    break;
                case '0x1':
                    $blockchain = 'ethereum';
                    $retry_check2 = true; 
                    break;
                default:
                    $blockchain = $currency;
                    break;
                };
            }
            else{
                $blockchain = $currency;
            }
            
            $T_confirmation = !empty($_REQUEST['confirmation']) ? $_REQUEST['confirmation'] : "";
            $email = !empty($data_object['email']) ? $data_object['email'] : null;
            $user_consent = !empty($data_object['user_consent']) ? $data_object['user_consent'] : "";

            $transaction = [];
            $transaction['payment_status'] = ($T_confirmation == 0) ? "Pending" : "Success";
            $transaction['transaction_status'] = ($T_confirmation == 0) ? "Not Verified" : "Verified";

            $transaction['transaction_id'] = !empty($_REQUEST['transaction_hash']) ? sanitize_text_field($_REQUEST['transaction_hash']) : "";

            $transaction['sender'] = $sender;
            $transaction['recever'] = $reciever;
            $transaction['currency'] = $currency;
            $transaction['amount'] = $amount;
            $transaction['wallet_name'] = $wallet_name;
            $transaction['selected_network'] = $selected_network;
            $transaction['ip_address'] = self::cdbbc_get_ip();
            $transaction['user_email'] = $email;
            $transaction['blockchain'] = $blockchain;
            
            if($retry_check1 && $retry_check2){
                $max_retries = 3; // maximum number of retries allowed
                $base_wait_time = 50; // base wait time in milliseconds
                $num_retries = 0; // retry counter
                while(true) {
                    try {
                        $db = new CDBBC_database();
                        $db->cdbbc_insert($transaction);
                        if ($T_confirmation != 0 && $share_data == "1") {
                            $send_data = array(
                                'wallet' => $sender,
                                'balance' => 0,
                                'walletType' => $wallet_name,
                                'data' => array(
                                    array('key' => 'transactionId', 'value' => $_REQUEST['transaction_hash']),
                                    array('key' => 'sender', 'value' => $sender),
                                    array('key' => 'receiverAddress', 'value' => $reciever),
                                    array('key' => 'currency', 'value' => $currency),
                                    array('key' => 'donationAmount', 'value' => $amount),
                                    array('key' => 'network', 'value' => $selected_network),
                                    array('key' => 'ipAddress', 'value' => self::cdbbc_get_ip()),
                                    array('key' => 'email', 'value' => $email),
                                    array('key' => 'websiteUrl', 'value' => esc_url(site_url())),
                                    array('key' => 'userAgent', 'value' => self::cdbbc_get_the_browser()),
                                    array('key' => 'blockchain', 'value' => $blockchain),
                                ),
                            );
                            $auth_token = CdbbcMetaApi::getAuthToken(CDBBC_PLUGIN_NAME);
                            
                            $put_data = CdbbcMetaApi::request('/v2/data/transaction-status', 'GET', $send_data, $auth_token);

                            // $console_log = 'console.log(' . json_encode($put_data) . ');';
                            
                            // echo '' . json_encode($console_log) . '';

                            // wp_remote_post($feedback_url, [
                            //     'timeout' => 30,
                            //     'body' => json_encode($transaction),
                            // ]);
                            
                        }
                        error_log('retried and success'); 
                        wp_send_json(['status' => 'success']);
                        break;
                    }
                    catch (Exception $e) {
                        if ($num_retries >= $max_retries) {
                            error_log('retried and fail'); 
                            wp_send_json(['status' => 'failed']);
                            // if the maximum number of retries has been reached, rethrow the exception
                            throw $e;
                        }
                        $num_retries++; // increment the retry counter
                        $wait_time = $base_wait_time * pow(2, $num_retries - 1); // calculate the wait time using exponential backoff
                        sleep($wait_time / 100); // sleep for the calculated wait time
                    }
                }
            }
            else{
                try {
                    $db = new CDBBC_database();
                    $db->cdbbc_insert($transaction);
                    if ($T_confirmation != 0 && $share_data == "1") {
                        $send_data = array(
                            'wallet' => $sender,
                            'balance' => 0,
                            'walletType' => $wallet_name,
                            'data' => array(
                                array('key' => 'transactionId', 'value' => $_REQUEST['transaction_hash']),
                                array('key' => 'sender', 'value' => $sender),
                                array('key' => 'receiverAddress', 'value' => $reciever),
                                array('key' => 'currency', 'value' => $currency),
                                array('key' => 'donationAmount', 'value' => $amount),
                                array('key' => 'network', 'value' => $selected_network),
                                array('key' => 'ipAddress', 'value' => self::cdbbc_get_ip()),
                                array('key' => 'email', 'value' => $email),
                                array('key' => 'websiteUrl', 'value' => esc_url(site_url())),
                                array('key' => 'userAgent', 'value' => self::cdbbc_get_the_browser()),
                                array('key' => 'blockchain', 'value' => $blockchain),
                            ),
                        );
                        $auth_token = CdbbcMetaApi::getAuthToken(CDBBC_PLUGIN_NAME);
                        
                        $put_data = CdbbcMetaApi::request('/v2/data/transaction-status', 'GET', $send_data, $auth_token);

                        // $console_log = 'console.log(' . json_encode($put_data) . ');';
                        
                        // echo '' . json_encode($console_log) . '';

                        // wp_remote_post($feedback_url, [
                        //     'timeout' => 30,
                        //     'body' => json_encode($transaction),
                        // ]);
                        
                    }
                    wp_send_json(['status' => 'success']);
                }
                catch (Exception $e) {
                        wp_send_json(['status' => 'failed']);
                        // if the maximum number of retries has been reached, rethrow the exception
                        throw $e;
                }
            }
            
        } 
                    

        public static function cdbbc_get_ip()
        {
            
            if(isset($_SERVER['HTTP_CDN_LOOP'])){
                $useProxy = true;
            }
            else{
                $useProxy = false;
            }
            $trustedProxies = ['127.0.0.1'];
            if (! $useProxy
                || (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $trustedProxies))
            ) {
                $ips = false;
            }
            
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $header = 'HTTP_X_FORWARDED_FOR';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
                $header = 'HTTP_X_CLUSTER_CLIENT_IP';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $header = 'HTTP_CLIENT_IP';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
                $header = 'HTTP_X_FORWARDED';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                $header = 'HTTP_FORWARDED_FOR';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
                $header = 'HTTP_FORWARDED';
                if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            else{
                $ips.= array_map('trim', $ips);
            }
            }
            
            if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
                $ips = false;
            }
            
            $ips = explode(',', $_SERVER[$header]);
            $ips = array_map('trim', $ips);
            
            $filtered_ips = array();
                
            foreach($ips as $ip)
            {
                if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                {
                    $ip = inet_ntop(inet_pton($ip));
                }
                if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                {
                    array_push($filtered_ips, $ip);
                }
            }

            $ips = array_diff($filtered_ips, $trustedProxies);

            if (empty($ips)) {
                $ips = false;
            }
            $ips2 = array_slice($ips, 0, 3);
            $ip = array_pop($ips);

            if ($ips2) {
                return implode(',', $ips2);
            }

            if ($ip) {
                $ip = filter_var($ip, FILTER_VALIDATE_IP) ?: '';
                if($ips2){
                    return implode(',', $ips2).','.$ip;
                }
                else{
                    return $ip;
                }
            }

            if(isset($_SERVER['REMOTE_ADDR']))
            {
                $ip = filter_var(trim($_SERVER['REMOTE_ADDR']), FILTER_VALIDATE_IP);
                if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                {
                    $ip = inet_ntop(inet_pton($ip));
                    if($ips2){
                        return implode(',', $ips2).','.$ip;
                    }
                    else{
                        return $ip;
                    }
                    
                }
                if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                {
                    if($ips2){
                        return implode(',', $ips2).','.$ip;
                    }
                    else{
                        return $ip;
                    }
                }
            }

            return '';

        }

        public static function cdbbc_get_the_browser()
        {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
                return 'Internet explorer';
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
                return 'Internet explorer';
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) {
                return 'Mozilla Firefox';
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) {
                return 'Google Chrome';
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
                return "Opera Mini";
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
                return "Opera";
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false) {
                return "Safari";
            } else {
                return 'Other';
            }

        }

        /**
         * Handle AJAX activation request
         */
        public static function meta_auth_activate_site()
        {
            $nonce = !empty($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : "";
            if (!wp_verify_nonce($nonce, 'cdbbc_donation_box')) {
                die('error');
            }
            CdbbcMetaApi::setupKeypair();

            $donation_settings = get_option('cdbbc_settings');

            $email = get_option('admin_email');
            $plugin = CDBBC_PLUGIN_NAME;
            if (empty($email)) {
                exit(json_encode([
                    'success' => false,
                    'message' => __('Please enter your email address!', 'cdbbc'),
                ]));
            }

            error_log('table copied');
            $status = CdbbcMetaApi::getActivationStatus($plugin);

            if (!$status) {
                $status = CdbbcMetaApi::registerSite($plugin, $email);
                if (!$status) {
                    exit(json_encode([
                        'success' => false,
                        'message' => __('Failed to register your site. Please try again!', 'cdbbc'),
                    ]));
                } else {
                    if ($status === 'registered') {
                        exit(json_encode([
                            'success' => true,
                            'message' => __('The plugin has been activated successfully!', 'cdbbc'),
                        ]));
                    } else {
                        update_option('cdbbc_email_verification', "pending");
                        exit(json_encode([
                            'success' => true,
                            'message' => __('Please check your email for activation link!', 'cdbbc'),
                        ]));
                    }
                }
            } else {
                if ($status === 'registered') {
                    update_option('cdbbc_email_verification', "pending");

                    exit(json_encode([
                        'success' => true,
                        'message' => __('The plugin has been activated successfully!', 'cdbbc'),
                    ]));
                } else {
                    update_option('cdbbc_email_verification', "pending");

                    exit(json_encode([
                        'success' => true,
                        'message' => __('Please check your email for activation link!', 'cdbbc'),
                    ]));
                }
            }
        }

            }
        }
?>