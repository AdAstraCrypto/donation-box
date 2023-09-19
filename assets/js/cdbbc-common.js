
/*
|--------------------------------------------------------------------------
|  Copy to cliboard & tab change funtion
|--------------------------------------------------------------------------
*/
jQuery(document).ready(function ($) {
    const buttons = document.querySelectorAll(".cdbbc_tab_btn");
    const sections = document.querySelectorAll(".cdbbc_tab_section");


    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            buttons.forEach((btn) => {
                btn.classList.remove("active");
            });
            btn.classList.add("active");
            const id = btn.id;
            sections.forEach((section) => {
                section.classList.remove("active");
            });
            const req = document.getElementsByClassName(`${id}`);
            req[0].classList.add("active");
        })
    })

    $('button.cdbbc_btn').click(function () {
        let current_input = $(this).prev().val();
        navigator.clipboard.writeText(current_input);
        $(this).prev().select();

    })


    $('.cdbbc-container ul.cdbbc-tabs li').click(function () {
        var random_id = $(this).attr('data-random')
        var tab_id = $(this).attr('data-tab');
        $('.cdbbc-tab-rand' + random_id + ' ul.cdbbc-tabs li').removeClass('current');
        $('.cdbbc-tab-rand' + random_id + ' .cdbbc-tabs-content').removeClass('current'); 
         $(this).addClass('current');
        $('#' + tab_id).addClass('current');
    })

}) 