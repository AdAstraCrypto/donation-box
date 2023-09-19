<?php
if (!defined('ABSPATH')) {
	exit();
}
if (!class_exists('CDBBC_REVIEW_NOTICE')) {
    class CDBBC_REVIEW_NOTICE {
        /**
         * The Constructor
         */
        public function __construct() {
            // register actions
         
            if(is_admin()){
                add_action( 'admin_notices',array($this,'atlt_admin_notice_for_reviews'));
                add_action( 'wp_ajax_cdbbc_dismiss_notice',array($this,'atlt_dismiss_review_notice' ) );
            }
        }

    // ajax callback for review notice
    public function atlt_dismiss_review_notice(){
        $rs=update_option( 'cdbbc-alreadyRated','yes' );      
        echo  json_encode( array("success"=>"true") );
        exit;
    }
   // admin notice  
    public function atlt_admin_notice_for_reviews(){
        
        if( !current_user_can( 'update_plugins' ) ){
            return;
         }
        $installation_date = get_option( 'cdbbc_activation_time' );
			if ( is_numeric( $installation_date ) ) {
				$installation_date = gmdate( 'Y-m-d h:i:s', $installation_date );
			}
       
       	$alreadyRated = get_option( 'cdbbc-alreadyRated' ) != false ? get_option( 'cdbbc-alreadyRated' ) : 'no';

			if ( null != get_option( 'cdbbc_spare_me' ) ) {
				$spare_me = get_option( 'cdbbc_spare_me' );
				if ( $spare_me == true ) {
					$alreadyRated = 'yes';
				}
			}
          
			 // check user already rated
			if ( $alreadyRated == 'yes' ) {
				return;
			}
           
            
            // grab plugin installation date and compare it with current date
            $display_date = gmdate( 'Y-m-d h:i:s' );
            $install_date= new DateTime( $installation_date );
            $current_date = new DateTime( $display_date );
            $difference = $install_date->diff($current_date);
            $diff_days= $difference->days;
          
            // check if installation days is greator then week
			if (isset($diff_days) && $diff_days>=3) {
                echo $this->atlt_create_notice_content();
             }
       }  

       // generated review notice HTML
       function atlt_create_notice_content(){
        
        $ajax_url=admin_url( 'admin-ajax.php' );
        $ajax_callback='cdbbc_dismiss_notice';
        $wrap_cls="notice notice-info is-dismissible";
        $img_path=CDBBC_URL . 'assets/images/cryptodonation-logo.png';

        $p_name='Cryptocurrency Donation Box';
        $like_it_text='Rate Now! ★★★★★';
        $already_rated_text=esc_html__( 'I already rated it', 'atlt2' );
        $not_like_it_text=esc_html__( 'Not Interested', 'atlt2' );
        $p_link = esc_url('https://wordpress.org/support/plugin/cryptocurrency-donation-box/reviews/#new-post');

 
        $message="Thanks for using <b>$p_name</b> - WordPress plugin.
        We hope you liked it ! <br/>Please give us a quick rating, it works as a boost for us to keep working on more !<br/>";
      
        $html='<div data-ajax-url="%8$s"  data-ajax-callback="%9$s" class="cdbbc-feedback-notice-wrapper %1$s">
        <div class="logo_container"><a href="%5$s"><img src="%2$s" alt="%3$s" style="max-width:80px;"></a></div>
        <div class="message_container">%4$s
        <div class="callto_action">
        <ul>
            <li class="love_it"><a href="%5$s" class="like_it_btn button button-primary" target="_new" title="%6$s">%6$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button cdbbc_dismiss_notice" title="%7$s">%7$s</a></li>  
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button cdbbc_dismiss_notice" title="%10$s">%10$s</a></li>           
        </ul>
        <div class="clrfix"></div>
        </div>
        </div>
        </div>';
        
        $style = '<style>.cdbbc-feedback-notice-wrapper.notice.notice-info.is-dismissible {
            padding: 5px;
            display: table;
            width: fit-content;
            max-width: 820px;
            clear: both;
            border-radius: 5px;
            border: 2px solid #b7bfc7;
        }
        .cdbbc-feedback-notice-wrapper .logo_container {
            width: 100px;
            display: table-cell;
            padding: 5px;
            vertical-align: middle;
        }
        .cdbbc-feedback-notice-wrapper .logo_container a,
        .cdbbc-feedback-notice-wrapper .logo_container img {
            width:fit-content;
            height:auto;
            display:inline-block;
        }
        .cdbbc-feedback-notice-wrapper .message_container {
            display: table-cell;
            padding: 5px 20px 5px 5px;
            vertical-align: middle;
        }
        .cdbbc-feedback-notice-wrapper ul li {
            float: left;
            margin: 0px 10px 0 0;
        }
        .cdbbc-feedback-notice-wrapper ul li.already_rated a:after {
            color: #e86011;
            content: "\f153";
            display: inline-block;
            vertical-align: middle;
            margin: -1px 0 0 5px;
            font-size: 15px;
            font-family: dashicons;
        }
        .cdbbc-feedback-notice-wrapper ul li .button-primary {
            background: #e86011;
            text-shadow: none;
            border-color: #943b07;
            box-shadow: none;
        }
        .cdbbc-feedback-notice-wrapper ul li .button-primary:hover {
            background: #222;
            border-color: #000;
        }
        .cdbbc-feedback-notice-wrapper a {
            color: #008bff;
        }
        
        /* This css is for license registration page */
        .cdbbc-notice-red.uninstall {
            max-width: 700px;
            display: block;
            padding: 8px;
            border: 2px solid #157d0f;
            margin: 10px 0;
            background: #13a50b;
            font-weight: bold;
            font-size: 13px;
            color: #ffffff;
        }
        .clrfix{
            clear:both;
        }</style>';

        $script = '<script>
        jQuery(document).ready(function ($) {
            $(".cdbbc_dismiss_notice").on("click", function (event) {
                var $this = $(this);
                var wrapper=$this.parents(".cdbbc-feedback-notice-wrapper");
                var ajaxURL=wrapper.data("ajax-url");
                var ajaxCallback=wrapper.data("ajax-callback");
                
                $.post(ajaxURL, { "action":ajaxCallback }, function( data ) {
                    wrapper.slideUp("fast");
                  }, "json");
        
            });
        });
        </script>';

        $html .= '
        '.$style.'
        '.$script;

 return sprintf($html,
        $wrap_cls,
        $img_path,
        $p_name,
        $message,
        $p_link,
        $like_it_text,
        $already_rated_text,
        $ajax_url,// 8
        $ajax_callback,//9
        $not_like_it_text//10
      
        );
        
       }

    } //class end

    new CDBBC_REVIEW_NOTICE();

} 



