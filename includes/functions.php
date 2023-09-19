<?php
if (!defined('ABSPATH')) {
    exit();
}

/**
 * Get the value of a settings option field
 */

function CDBBC_get_option($option, $section, $default = '')
{
    $options = get_option($section);
    if (isset($options[$option])) {
        return $options[$option];
    }
    return $default;
}

/**
 * Fetch settings array
 */

function CDBBC_get_option_arr($section, $default = '')
{
    $options = get_option($section);
    if (isset($options) && is_array($options)) {
        return $options;
    }
    return $default;
}

/**
 * Supported donation coin array
 */

function CDBBC_supported_coins()
{
    return $coins = array(
        'bitcoin' => 'Bitcoin(BTC)',
        'ethereum' => 'Ethereum(ETH)',
        'metamask' => 'MetaMask(ETH-ERC20)',
        'tether' => 'Tether(USDT)',
        'cardano' => 'Cardano(ADA)',
        'xrp' => 'XRP(XRP) ',
        'polkadot' => 'Polkadot(DOT)',
        'binance-coin' => 'Binance Coin(BNB)',
        'litecoin' => 'Litecoin(LTC)',
        'chainlink' => 'Chainlink(LINK)',
        'stellar' => 'Stellar(XLM)',
        'bitcoin-cash' => 'Bitcoin Cash(BCH)',
        'dogecoin' => 'Dogecoin(DOGE)',
        'usdcoin' => 'USD COIN(USDC)',
        'aave' => 'Aave(AAVE)',
        'uniswap' => 'Uniswap(UNI)',
        'wrappedbitcoin' => 'Wrapped Bitcoin(WBTC)',
        'avalanche' => 'Avalanche(AVAX)',
        'bitcoin-sv' => 'Bitcoin SV(BSV)',
        'eos' => 'EOS(EOS)',
        'nem' => 'NEM(XEM)',
        'tron' => 'Tron(TRX)',
        'cosmos' => 'Cosmos(ATOM)',
        'monero' => 'Monero(XMR)',
        'tezos' => 'Tezos(XTZ)',
        'elrond' => 'Elrond(EGLD)',
        'iota' => 'IOTA(MIOTA)',
        'theta' => 'THETA(THETA)',
        'synthetix' => 'Synthetix(SNX)',
        'dash' => 'Dash(DASH)',
        'maker' => 'Maker(MKR)',
        'dai' => 'Dai(DAI)',
        'ethereum-classic' => 'Ethereum Classic(ETC)',
        'lisk' => 'Lisk (LSK)',
        'neo' => 'NEO (NEO)',
        'vechain' => 'VeChain(VET)',
        'qtum' => 'Qtum(QTUM)',
        'icon' => 'ICON(ICX)',
        'nano' => 'Nano(XNO)',
        'verge' => 'Verge (XVG)',
        'bytecoin-bcn' => 'Bytecoin(BCN)',
        'zcash' => 'Zcash(ZEC)',
        'ontology' => 'Ontology(ONT)',
        'aeternity' => 'Aeternity(AE)',
        'steem' => 'Steem(STEEM)',
        'digibyte' => 'Digibyte(DGB)',
        'polygon-matic' => 'Polygon(MATIC)',
    );
}


function convertScientificToDecimal($value) {
    if (preg_match('/^[0-9]+\.?[0-9]*e[+-]?[0-9]+$/i', $value)) {
        return number_format($value, strlen(substr(strrchr($value, "."), 1)) - 1, '.', '');
    }
    else {
        return $value;
    }
}


/**
 *  Get supported networks
 */
function cdbbc_get_supported_network($type)
{
    $donation_settings = get_option('cdbbc_settings');
    $supported_network = isset($donation_settings['custom_networks']) ? $donation_settings['custom_networks'] : "";
    $data = array();
    $currency = [];
    $rpc_url = [];
    if (is_array($supported_network)) {
        foreach ($supported_network as $key => $value) {
            if ($value['enable'] == '1') {
                $rpc_url[$value['chainId']] = $value['rpcUrls'];
                $data[$value['chainId']] = array(
                    'chainId' => '0x' . dechex($value['chainId']),
                    'chainName' => $value['chainName'],
                    'nativeCurrency' => array(
                        'name' => $value['nativeCurrency']['name'],
                        'symbol' => $value['nativeCurrency']['symbol'],
                        'decimals' => (int) $value['nativeCurrency']['decimals'],
                        'token_price' => convertScientificToDecimal($value['nativeCurrency']['token_price']),
                        'image' => !empty($value['nativeCurrency']['image'])?$value['nativeCurrency']['image']: CDBBC_URL . 'assets/images/default-logo.png',
                    ),
                    'recever_wallet' => $value['recever_wallet'],
                    'rpcUrls' => array($value['rpcUrls']),
                    'blockExplorerUrls' => array($value['blockExplorerUrls']),
                    'token_price' => isset($value["token_price"]) ? convertScientificToDecimal($value["token_price"]) : "",
                );
                if (isset($value["currencies"])) {
                    foreach ($value["currencies"] as $keys => $values) {
                        if ($values['enable'] == '1') {
                            $data[$value['chainId']]["currencies"][] = array(
                                'symbol' => $values['symbol'],
                                'contract_address' => $values['contract_address'],
                                'image' => !empty($values['image'])?$values['image']:CDBBC_URL . 'assets/images/default-logo.png',
                                'token_price' => convertScientificToDecimal($values['token_price']),
                            );

                        }
                    }

                }

            }

        }
    }
    if ($type == "network") {
        return $data;
    } else if ($type == "rpc_url") {
        return $rpc_url;

    }

}

/**
 * Contract address array for metamask
 */

function cdbbc_wallet_html($modal = true)
{
    $settings = get_option('cdbbc_settings');
    $settings = $settings['supported_wallets'];
    $modal = ($modal == true) ? 'cdbbc-connector-modal-overlay' : '';
    $contract_address = ' <div class="cdbbc-connector-modal ' . $modal . '"><div class="cdbbc-modal-content" ><ul class="cdbbc-wallets" >';
    if (isset($settings['metamask_wallet']) && $settings['metamask_wallet'] == "1") {
        $contract_address .= ' <li class="cdbbc-wallet" id="metamask_wallet">
                            <div class="cdbbc-wallet-icon" ><img src="' . CDBBC_URL . 'assets/images/metamask.png" alt="metamask" ></div>
                            <div class="cdbbc-wallet-title" >' . __("MetaMask", "cdbbc") . '</div>
                            </li>';
    }
    if (isset($settings['trust_wallet']) && $settings['trust_wallet'] == "1") {
        $contract_address .= '  <li class="cdbbc-wallet" id="trust_wallet">
                            <div class="cdbbc-wallet-icon" ><img src="' . CDBBC_URL . 'assets/images/trustwallet.png" alt="trustwallet" ></div>
                            <div class="cdbbc-wallet-title" >' . __("Trust Wallet", "cdbbc") . '</div>
                            </li>';
    }
    if (isset($settings['binance_wallet']) && $settings['binance_wallet'] == "1") {
        $contract_address .= ' <li class="cdbbc-wallet" id="Binance_wallet">
                            <div class="cdbbc-wallet-icon" ><img src="' . CDBBC_URL . 'assets/images/binancewallet.png" alt="binancewallet" ></div>
                            <div class="cdbbc-wallet-title" >' . __("Binance Wallet", "cdbbc") . '</div>
                            </li>';
    }
    if (isset($settings['wallet_connect']) && $settings['wallet_connect'] == "1") {
        $contract_address .= ' <li class="cdbbc-wallet" id="wallet_connect">
                            <div class="cdbbc-wallet-icon" ><img src="' . CDBBC_URL . 'assets/images/walletconnect.png" alt="walletconnect" ></div>
                            <div class="cdbbc-wallet-title" >' . __("WalletConnect", "cdbbc") . '</div>
                            </li>';
    }
    // if (isset($settings['qr']) && $settings['qr'] == "1") {
    //     $contract_address .= ' <li class="cdbbc-wallet" id="qr">
    //                         <div class="cdbbc-wallet-icon" ><img src="' . CDBBC_URL . 'assets/images/walletconnect.png" alt="walletconnect" ></div>
    //                         <div class="cdbbc-wallet-title" >' . __("WalletConnect", "cdbbc") . '</div>
    //                         </li>';
    // }
    $contract_address .= '</ul></div></div>';
    return $contract_address;
}

/**
 * function to fetch general option values
 */

function cdbbc_get_gen_option($key = '', $default = false)
{
    $opts = get_option('cdbbc-add-wallet', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

/**
 * function to fetch extra option values
 */
function cdbbc_get_extra_option($key = '', $default = false)
{
    $opts = get_option('cdbbc-coin-settings', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

/**
 * Settings migrate function from cmb2 framework to codestar
 */

function cdbbc_migrate_codestar()
{
    $donation_settings = get_option('cdbbc_settings');
    $migrate_template = [];
    $gen_tokens = cdbbc_get_gen_option('cdbbc_group_data');
    $gen_tokens = !empty($gen_tokens) ? $gen_tokens : "";
    $extra_setting = get_option('cdbbc-coin-settings');
    $migrate_data = [];
    $wallets = [];
    $main_wallets = "";
    do_action('csf_cdbbc_settings_save');
    if (is_array($gen_tokens) && !empty($gen_tokens)) {
        foreach ($gen_tokens as $key => $values) {
            if (isset($values['coin']) && (strpos($values['coin'], 'metamask') === 0)) {
                $wallets[$values['coin']] = $values['wallet_address'];
                $main_wallets = $values['wallet_address'];
            } else {
                $migrate_data[] = $values;
            }
        }

        $migrate_template['general_tokens'] = $migrate_data;
        $migrate_template['enable_wallet_in_design'] = array('wallet_tab_style' => '1', 'wallet_list_style' => '1', 'wallet_popup_style' => '1');
        $migrate_template['tile_desc_settings'] = array('main_title' => $extra_setting['main_title'], 'main_desc' => $extra_setting['main_desc']);
        $migrate_template['user_wallet'] = !empty($main_wallets) ? $main_wallets : "";
// $migrate_template['infura_project_id'] = $donation_settings['infura_project_id'];
        $migrate_template['supported_wallets'] = array('metamask_wallet' => '1', 'binance_wallet' => '1', 'trust_wallet' => '1', 'wallet_connect' => '1');

        $migrate_template['share_user_data']['admin_email'] = get_option('admin_email');

        $migrate_template['custom_networks'] = cdbbc_default_networks($wallets);
        $migrate_template['donation_settings_style'] = array('main_title_typography' => array('color' => cdbbc_get_extra_option('main_title_color')),
            'main_content_typography' => array('color' => cdbbc_get_extra_option('main_content_color')), 'main_bg_color' => cdbbc_get_extra_option('main_bg_color'),
        );
        $migrate_template['donation_wallet_style'] = array('wallet_bg_color' => '#fff');

        update_option('cdbbc_settings', $migrate_template);

    }

}
/**
 * Settings migrate function from Titan framework to cmb2
 */

function cdbbc_migrate_data()
{
    // $db->drop_table();
    // $db = new CDBBC_database();
    // $db->create_table();
    $all_coin_wall1 = CDBBC_get_option_arr('wallet_addresses');
    $all_coin_wall2 = CDBBC_get_option_arr('wallet_addresses1');
    $all_coin_list1 = !empty($all_coin_wall1) ? $all_coin_wall1 : array();
    $all_coin_list2 = !empty($all_coin_wall2) ? $all_coin_wall2 : array();
    $all_coin_wall_add343 = array_merge($all_coin_list1, $all_coin_list2);
    $new_gen_settigs = get_option('cdbbc-add-wallet');
    $new_extra_settigs = get_option('cdbbc-coin-settings');
    $old_wallet = array_filter($all_coin_wall_add343);

    $migrate_data = [];
    if (is_array($old_wallet) && !empty($old_wallet)) {
        foreach ($old_wallet as $key => $values) {

            if (strpos($key, 'tag') !== false) {
                continue;
            }
            $migrate_data['cdbbc_group_data'][] = array(
                "coin_type" => "popular",
                "coin" => $key,
                'wallet_address' => $old_wallet[$key],
                'tag_note' => !empty($old_wallet[$key . '_tag']) ? $old_wallet[$key . '_tag'] : "",
            );
        }
    }

    $extra_settings = array(
        'metamask_title' => !empty(CDBBC_get_option('maintitle-metamask', 'coolplugins_advanced')) ? CDBBC_get_option('maintitle-metamask', 'coolplugins_advanced') : 'Donate With MetaMask',
        'metamask_desc' => !empty(CDBBC_get_option('cdb-metamask-desc', 'coolplugins_advanced')) ? CDBBC_get_option('cdb-metamask-desc', 'coolplugins_advanced') : 'Donate ETH Via PAY With Metamask',
        'main_title' => !empty(CDBBC_get_option('main-title', 'coolplugins_advanced')) ? CDBBC_get_option('main-title', 'coolplugins_advanced') : 'Donate [coin-name] to this address',
        'main_desc' => !empty(CDBBC_get_option('cdb-desc', 'coolplugins_advanced')) ? CDBBC_get_option('cdb-desc', 'coolplugins_advanced') : 'Scan the QR code or copy the address below into your wallet to send some [coin-name]',
        'metamask_amount' => '0.005',
    );
    update_option('cdbbc-add-wallet', $migrate_data);
    update_option('cdbbc-coin-settings', $extra_settings);

}

function cdbbc_default_networks($default_wallets = null)
{
    $networks = [];
    $defautlt_eth = "";
    $defautlt_bnb = "";

    if (isset($default_wallets['metamask'])) {
        $defautlt_eth = $default_wallets['metamask'];

    } elseif (isset($default_wallets['metamask-eth_usdt'])) {
        $defautlt_eth = $default_wallets['metamask-eth_usdt'];

    } elseif (isset($default_wallets['metamask-eth_busd'])) {
        $defautlt_eth = $default_wallets['metamask-eth_busd'];

    } elseif (isset($default_wallets['metamask-eth_bnb'])) {
        $defautlt_eth = $default_wallets['metamask-eth_bnb'];

    }

    if (isset($default_wallets['metamask-bnb'])) {
        $defautlt_bnb = $default_wallets['metamask-bnb'];

    } elseif (isset($default_wallets['metamask-bnb_busd'])) {
        $defautlt_bnb = $default_wallets['metamask-bnb_busd'];

    } elseif (isset($default_wallets['metamask-bnb_usdt'])) {
        $defautlt_bnb = $default_wallets['metamask-bnb_usdt'];

    } elseif (isset($default_wallets['metamask-bnb_eth'])) {
        $defautlt_bnb = $default_wallets['metamask-bnb_eth'];

    }

    $networks[] = array(
        'chainName' => 'Ethereum Main Network(Mainnet)',
        'rpcUrls' => '',
        'chainId' => '1',
        'recever_wallet' => (isset($defautlt_eth)) ? $defautlt_eth : "",
        'blockExplorerUrls' => 'https://etherscan.io/',
        'enable' => true,
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'Ethereum',
            'symbol' => 'ETH',
            'decimals' => 18,
            'token_price' => 0.0005,
            'image' => CDBBC_URL . '/assets/images/ETH.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => 'USDT',
                'contract_address' => '0xdac17f958d2ee523a2206206994597c13d831ec7',
                'image' => CDBBC_URL . '/assets/images/USDT.svg',
                'token_price' => 10,
                'enable' => true,
            ),
            array(
                'symbol' => 'BUSD',
                'contract_address' => '0x4Fabb145d64652a948d72533023f6E7A623C7C53',
                'image' => CDBBC_URL . '/assets/images/BUSD.svg',
                'token_price' => 10,
                'enable' => true,
            ),
            array(
                'symbol' => 'BNB',
                'contract_address' => '0xB8c77482e45F1F44dE1745F52C74426C631bDD52',
                'image' => CDBBC_URL . '/assets/images/BNB.svg',
                'token_price' => 0.005,
                'enable' => true,
            ),

        ),
    );

    $networks[] = array(
        'chainName' => 'Goerli Test Network',
        'rpcUrls' => '',
        'chainId' => '5',
        'blockExplorerUrls' => 'https://goerli.etherscan.io/',
        'enable' => false,
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'Ethereum',
            'symbol' => 'ETH',
            'token_price' => 0.0005,
            'decimals' => 18,

            'image' => CDBBC_URL . '/assets/images/ETH.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => '',
                'contract_address' => '',
                'image' => '',
                'enable' => false,
                'token_price' => 0,
            ),

        ),

    );

    $networks[] = array(
        'chainName' => 'Binance Smart Chain',
        'rpcUrls' => 'https://bsc-dataseed.binance.org/',
        'chainId' => '56',
        'blockExplorerUrls' => 'https://bscscan.com/',
        'enable' => true,
        'recever_wallet' => (isset($defautlt_bnb)) ? $defautlt_bnb : "",
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'BNB',
            'symbol' => 'BNB',
            'decimals' => 18,
            'token_price' => 0.005,
            'image' => CDBBC_URL . '/assets/images/BNB.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => 'BUSD',
                'contract_address' => '0xe9e7cea3dedca5984780bafc599bd69add087d56',
                'image' => CDBBC_URL . '/assets/images/BUSD.svg',
                'enable' => true,
                'token_price' => 10,
            ),
            array(
                'symbol' => 'USDT',
                'contract_address' => '0x55d398326f99059ff775485246999027b3197955',
                'image' => CDBBC_URL . '/assets/images/USDT.svg',
                'enable' => true,
                'token_price' => 10,
            ),
            array(
                'symbol' => 'ETH',
                'contract_address' => '0x2170ed0880ac9a755fd29b2688956bd959f933f8',
                'image' => CDBBC_URL . '/assets/images/ETH.svg',
                'enable' => true,
                'token_price' => 0.005,
            ),
            array(
                'symbol' => 'BTCB',
                'contract_address' => '0x7130d2A12B9BCbFAe4f2634d864A1Ee1Ce3Ead9c',
                'image' => CDBBC_URL . '/assets/images/BTC.svg',
                'enable' => true,
                'token_price' => 0.005,
            ),

        ),
    );

    $networks[] = array(
        'chainName' => 'Binance Smart Chain Test Network',
        'rpcUrls' => 'https://data-seed-prebsc-1-s1.binance.org:8545',
        'chainId' => '97',
        'blockExplorerUrls' => 'https://testnet.bscscan.com',
        'enable' => false,
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'BNB',
            'symbol' => 'BNB',
            'decimals' => 18,
            'token_price' => 0.005,
            'image' => CDBBC_URL . '/assets/images/BNB.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => 'BUSD',
                'contract_address' => '0xeD24FC36d5Ee211Ea25A80239Fb8C4Cfd80f12Ee',
                'image' => CDBBC_URL . '/assets/images/BUSD.svg',
                'enable' => true,
                'token_price' => 10,
            ),

        ),
    );
    $networks[] = array(
        'chainName' => 'Polygon Mainnet',
        'rpcUrls' => 'https://polygon-rpc.com/',
        'chainId' => '137',
        'blockExplorerUrls' => 'https://polygonscan.com/',
        'enable' => false,
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'MATIC',
            'symbol' => 'MATIC',
            'decimals' => 18,
            'token_price' => 10,
            'image' => CDBBC_URL . '/assets/images/MATIC.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => '',
                'contract_address' => '',
                'image' => '',
                'enable' => false,
                'token_price' => 0,
            ),

        ),
    );
    $networks[] = array(
        'chainName' => 'Mumbai-Polygon Test Network',
        'rpcUrls' => 'https://matic-mumbai.chainstacklabs.com/',
        'chainId' => '80001',
        'blockExplorerUrls' => 'https://mumbai.polygonscan.com/',
        'enable' => false,
        'nativeCurrency' => array(
            'enable' => true,
            'name' => 'MATIC',
            'symbol' => 'MATIC',
            'decimals' => 18,
            'token_price' => 0.0005,
            'image' => CDBBC_URL . '/assets/images/MATIC.svg',
        ),
        'currencies' => array(
            array(
                'symbol' => '',
                'contract_address' => '',
                'image' => '',
                'token_price' => 0,
                'enable' => false,
            ),

        ),
    );
    return $networks;

}

//Add all constant messages
function cdbbc_const_messages()
{
    $messages = "";

    $messages = array(
        'metamask_wallet' => __(" MetaMask Wallet", "cdbbc"),
        'trust_wallet' => __("Trust Wallet", "cdbbc"),
        'binance_wallet' => __("Binance Wallet", "cdbbc"),
        'wallet_connect' => __(" Wallet Connect", "cdbbc"),
        // 'qr' => __("QR Code", "cdbbc"),
        'click_here' => __("Click Here", "cdbbc"),
        'extention_not_detected' => __("extention not detected", "cdbbc"),
        'connection_establish' => __("Please wait while connection establish", "cdbbc"),
        'user_rejected_the_request' => __("User rejected the request", "cdbbc"),
        'donate_using' => __("Donate Using ", "cdbbc"),
        'enter_amount' => __("Please Enter Amount", "cdbbc"),
        'valid_amount' => __("Please Enter Valid Amount", "cdbbc"),
        'valid_email' => __("Please Enter Valid Email", "cdbbc"),
        'enter_email' => __("Please Enter Email", "cdbbc"),
        'switch_to' => __("Please switch to", "cdbbc"),
        'to_pay' => __("to pay", "cdbbc"),
        'confirm_transaction' => __("Confirm this transaction from the wallet", "cdbbc"),
        'transaction_process' => __("Transaction in Process ! Please Wait", "cdbbc"),
        'transaction_completed' => __("Transaction Completed Successfully !", "cdbbc"),
        'transaction_rejected' => __("Transaction Rejected", "cdbbc"),
        'invalid_recever' => __("Invalid Recever Address", "cdbbc"),
        'network_switching' => __("Network Switching in Process Please Wait!", "cdbbc"),
        'adding_connect' => __("Adding Network in Process Please Wait!", "cdbbc"),
        'insufficient_balance' => __("Insufficient Balance", "cdbbc"),
        "terms_condition" => __('I agree with the site\'s&nbsp Terms & Conditions.', "cdbbc"),
        "terms_condition_required" => __("Please accept our Terms & Conditions!", "cdbbc"),
        'blackWorks_msg' => __(' and accept these terms and privacy policy.', 'cdbbc'),
        "infura_msg" => __("Infura project id is required for WalletConnect to work", "cdbbc"),
        "select_currency" => __("Select Currency", "cdbbc"),
        "select_network" => __("Select Network", "cdbbc"),
        "enter_amount_lbl" => __("Enter Amount", "cdbbc"),
        "enter_email" => __("Email to receive payment confirmation", "cdbbc"),
    );
    return $messages;

}
