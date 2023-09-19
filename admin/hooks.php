<?php

/**
 * Handle AJAX activation request
 */
function cdbbc_activate_site()
{
    if (empty($_POST['email']) || empty($_POST['plugin'])) {
        exit(json_encode([
            'success' => false,
            'message' => __('Please enter your email address!', 'cdbbc')
        ]));
    }
    CdbbcMetaApi::setupKeypair();
    
    $email = sanitize_email($_POST['email']);
    $plugin = sanitize_title($_POST['plugin']);
    $status="";
    $status = CdbbcMetaApi::getActivationStatus($plugin);
       

    if (!$status) {
        $status = CdbbcMetaApi::registerSite($plugin, $email);      
        sleep(1);
        if (!$status) {
            exit(json_encode([
                'success' => false,
                'message' => __('Failed to register your site. Please try again!', 'cdbbc')
            ]));
        } else {
            if ($status === 'registered') {
                exit(json_encode([
                    'success' => true,
                    'message' => __('The plugin has been activated successfully!', 'cdbbc')
                ]));
            } 
            //wip no authentication email being sent
            // else {

            //     exit(json_encode([
            //         'success' => true,
            //         'message' => __('Please check your email for activation link!', 'cdbbc')
            //     ]));
            // }
        }
    } else {
        if ($status === 'registered') {
            exit(json_encode([
                'success' => true,
                'message' => __('The plugin has been activated successfully!', 'cdbbc')
            ]));
        } 
        //wip no authentication email being sent
        // else {

        //     exit(json_encode([
        //         'success' => true,
        //         'message' => __('Please check your email for activation link!', 'cdbbc')
        //     ]));
        // }
    }
}
add_action('wp_ajax_cdbbc_activate_site', 'cdbbc_activate_site');