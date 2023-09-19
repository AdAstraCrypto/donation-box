
jQuery(document).ready(function ($) {
   //Add Enable Disble  labels on networks
    

function cdbbc_dynamic_title_change(evt){          
            let coin_ids = $(evt).find('.csf-field-select span.cdbbc_selected_coin');       
            let coin_type = $(evt).find('.cdbbc_coin_type input:checked'); 
            let coin_name = $(evt).find('.cdbbc_custom_coin input');       
            let popular_coin = $(evt).find('.cdbbc_popular_coin select :selected')
            let data = $(evt).find('.csf-cloneable-value ');           
            $(coin_ids).html('Coin id:-<code>' + $(popular_coin).val() + '</code>')
            if ($(coin_type).val() == "custom") {
                $(data).html($(coin_name).val())
            }
            else if ($(coin_type).val() == "popular") {
                $(data).html($(popular_coin).text())
            }
            $.each(data, function (index, value) {              
              
                
               
             //   setTimeout(() => {
                    let replaced_text = "";
                    if ($(this).text().indexOf("| 1") != "-1") {                       
                        replaced_text = $(this).text().replace("| 1", '<span class="cpmw_enabled">Enabled</span>')                        
                    }
                    else if ($(this).text().indexOf("| 0") != "-1") {                        
                        replaced_text = $(this).text().replace("| 0", '<span class="cpmw_disabled">Disabled</span>')                        
                    }
                    else if ($(this).text().indexOf(" | ") != "-1") {                        
                        replaced_text = $(this).text().replace(" | ", '<span class="cpmw_disabled">Disabled</span>')                        
                    }
                    else {
                        replaced_text = $(this).html()
                    }

                    $(this).html(replaced_text);

              //  }, 100);

            });
            //console.log($this)
        }
    
    let input = document.querySelectorAll('.csf-cloneable-item')
    $(window).load(function name(params) {
        $(input).trigger("click")
    })  
    $(input).on('click change keyup load', function () {
        cdbbc_dynamic_title_change(this);
    })

    let reg_selctor = document.querySelectorAll('#cdbbc_register_site')
    $(reg_selctor).on('click', function () {
        let request_data = {
            'action': 'meta_auth_activate_site',
            'nonce': wallets_data.nonce,
        };
        let append_html = $('.cdbbc_admin_email .cdbbc_response_msg')
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: wallets_data.ajax,
            data: request_data,
            success: function (data) {
                console.log(data);
                jQuery(append_html).html("<span style='color:green'>" + data.message + "</span>")
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                jQuery(append_html).html("<span style='color:red'>Error: "+ errorThrown+"</span>")
                console.log("Status: " + textStatus + "Error: " + errorThrown);
            }

        });
    })
  
})