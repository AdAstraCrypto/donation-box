 jQuery(document).ready(function ($) {
     let cdbbc_add_wallet = $('#adminmenu #toplevel_page_cdbbc-add-wallet ul li a[href=\"admin.php?page=cdbbc-add-wallet\"]')
     $(cdbbc_add_wallet).html("↳ Add Wallet/Coin")

});
 

//dynacmic change cmb2 group field title
jQuery(function ($) {
    var $box = $(document.getElementById('cdbbc_group_data_repeat'));

    var replaceTitles = function () {
        $box.find('.cmb-group-title').each(function () {
            var $this = $(this);
            var radio_val = $this.next().find('[type="radio"]')[1];

            if (radio_val.getAttribute('checked') == "checked") {

                var txt = $this.next().find('[coin_name_grp]').val().toUpperCase();
                var rowindex;

                if (!txt) {
                    txt = $box.find('[data-grouptitle]').data('grouptitle');
                    if (txt) {
                        rowindex = $this.parents('[data-iterator]').data('iterator');
                        txt = txt.replace('{#}', (rowindex + 1));
                    }
                }
            }
            else {
                const create_element = document.createElement("span");
                create_element.classList.add("cdbbc_coin_id");
                create_element.innerHTML = "Coin Id:- <code>" + $this.next().find('select option:selected').val() + "</code>";
                let add_elemt = $this.next().find('select');
                let remove_element = $this.next().find('.cdbbc_coin_id');
                remove_element.remove()
                $(add_elemt).parent().append(create_element);
                var txt = $this.next().find('select option:selected').text().toUpperCase();
                var rowindex;

                if (!txt) {
                    txt = $box.find('[data-grouptitle]').data('grouptitle');
                    if (txt) {
                        rowindex = $this.parents('[data-iterator]').data('iterator');
                        txt = txt.replace('{#}', (rowindex + 1));
                    }
                }

            }

            if (txt) {
                $this.text(txt);
            }

        });
    };


    var replaceOnKeyUp = function (evt) {

        var $this = $(evt.target);
        let remove_evt = evt.target.getAttribute('title');
        var check_type = evt.target.getAttribute('data-conditional-value');
        var check_name_field = evt.target.getAttribute('coin_name_grp');
        var id = 'title';
        if (check_type == "custom" && check_name_field == "Coin_name_grp") {
            if (evt.target.id.indexOf(id, evt.target.id.length - id.length) == -1) {
                $this.parents('.cmb-row.cmb-repeatable-grouping').find('.cmb-group-title').text($this.val());
            }
        }
        else if (check_type == "popular" || remove_evt == "Remove Wallet") {
            setTimeout(() => {

                replaceTitles();
            }, 1);
        }

    };

    $box.on('cmb2_add_row cmb2_remove_row cmb2_shift_rows_complete', replaceTitles).on('click keyup', replaceOnKeyUp);

    replaceTitles();

});