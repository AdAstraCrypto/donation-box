(function($) {
    // Initial check
    if( $('.cmb-tabs').length ) {
        $('.cmb-tabs').each(function() {
            // Activate first tab
            if( ! $(this).find('.cmb-tab.active').length ) {
                $(this).find('.cmb-tab').first().addClass('active');

                $($(this).find('.cmb-tab').first().data('fields')).addClass('cmb-tab-active-item');
                
                // Support for groups and repeatable fields
                $($(this).find('.cmb-tab').first().data('fields')).find('.cmb-repeat .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
            }
        });
    }

    $('body').on('click.cmbTabs', '.cmb-tabs .cmb-tab', function(e) {
        var tab = $(this);

        if( ! tab.hasClass('active') ) {
            var tabs = tab.closest('.cmb-tabs');
            var form = tabs.next('.cmb2-wrap');

            // Hide current active tab fields
            form.find(tabs.find('.cmb-tab.active').data('fields')).fadeOut('fast', function() {
                $(this).removeClass('cmb-tab-active-item');

                form.find(tab.data('fields')).fadeIn('fast', function() {
                    $(this).addClass('cmb-tab-active-item');

                    // Support for groups and repeatable fields
                    $(this).find('.cmb-repeat-table .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
                });
            });

            // Update tab active class
            tabs.find('.cmb-tab.active').removeClass('active');
            tab.addClass('active');
        }
    });
   
    // Adding a new group element needs to get the active class also
    $('body').on('click', '.cmb-add-group-row', function() {
        $(this).closest('.cmb-repeatable-group').find('.cmb-row').addClass('cmb-tab-active-item');
    });

    // Adding a new repeatable element needs to get the active class also
    $('body').on('click', '.cmb-add-row-button', function() {
        $(this).closest('.cmb-repeat').find('.cmb-row').addClass('cmb-tab-active-item');
    });

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {

        if( widget.find('.cmb-tabs').length ) {

            widget.find('.cmb-tabs').each(function() {
                // Activate first tab
                if( ! $(this).find('.cmb-tab.active').length ) {
                    $(this).find('.cmb-tab').first().addClass('active');

                    $($(this).find('.cmb-tab').first().data('fields')).addClass('cmb-tab-active-item');

                    // Support for groups and repeatable fields
                    $($(this).find('.cmb-tab').first().data('fields')).find('.cmb-repeat .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
                }
            });

        }

    });

    jQuery(document).ready(function () {
        var choose_affi = $('[name="choose_affiliate_type"]');
        var checked = $('[name="choose_affiliate_type"]:checked').val();
        if (checked == "changelly_aff_id") {
            jQuery('.cmb2-id-affiliate-id').show();
            jQuery('.cmb2-id-other-affiliate-link').hide();
        } else if (checked == "any_other_aff_id") {
            jQuery('.cmb2-id-other-affiliate-link').show();
            jQuery('.cmb2-id-affiliate-id').hide();
        }
        choose_affi.on('change', function () {
            if ($(this).val() == "changelly_aff_id") {
                jQuery('.cmb2-id-affiliate-id').show();
                jQuery('.cmb2-id-other-affiliate-link').hide();
            } else {
                jQuery('.cmb2-id-other-affiliate-link').show();
                jQuery('.cmb2-id-affiliate-id').hide();
            }
        });
        // if (jQuery('.cmb2-id-choose-affiliate-type #choose_affiliate_type1').prop("checked", true)) {
        //     console.log(this);
        //     jQuery('.cmb2-id-affiliate-id').show();
        //     jQuery('.cmb2-id-other-affiliate-link').hide();
        // }
        // else{
        //     jQuery('.cmb2-id-other-affiliate-link').show();
        //     jQuery('.cmb2-id-affiliate-id').hide();
            
        // }
      /*   jQuery('#choose_affiliate_type').on('change', function (e) {
            if (jQuery('#choose_affiliate_type').val() == 'changelly_aff_id') {
                jQuery('.cmb2-id-affiliate-id').show();
                jQuery('.cmb2-id-other-affiliate-link').hide();
            }
            else {
                jQuery('.cmb2-id-affiliate-id').hide();
                jQuery('.cmb2-id-other-affiliate-link').show();
            }
        }); */
    });
    
})(jQuery);
