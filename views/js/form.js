
$(document).ready(function () {
    window.prestashop.component.initComponents([
        'ChoiceTree',
        'TranslatableField',
    ]);


    new window.prestashop.component.ChoiceTree('#rule_category_id');

    $("#rule_clear_categories").parent().addClass("d-flex")
    $("#rule_clear_categories").parent().addClass("flex-row-reverse")

    // $('#rule_select_all_categories_1').click(function (){
    //     if($('#rule_select_all_categories_1').prop( "checked" )){
    //
    //         $("#rule_category_id").parent().parent().hide()
    //         $("#rule_clear_categories").parent().removeClass("d-flex")
    //         $("#rule_clear_categories").parent().hide()
    //         $("div.radio input[type=radio]").prop("checked", false)
    //         $("#rule_product").parent().parent().hide()
    //
    //     }
    // })
    //
    // $('#rule_select_all_categories_0').click(function (){
    //     if($('#rule_select_all_categories_0').prop( "checked" )) {
    //         $("#rule_category_id").parent().parent().show();
    //         $("#rule_clear_categories").parent().addClass("d-flex")
    //         $("#rule_clear_categories").parent().show()
    //         $("#rule_product").parent().parent().show()
    //
    //
    //     }
    // })

    $("#rule_clear_categories").click(function (){
        $("#rule_product").parent().parent().show()
        $("div.radio input[type=radio]").prop("checked", false)

    })

    // $("#rule_product").on("input", function (e){
    //     if ($(this).val() !== ""){
    //         $("#rule_category_id").parent().parent().hide()
    //         $("#rule_clear_categories").parent().removeClass("d-flex")
    //         $("#rule_clear_categories").parent().hide()
    //         $("div.radio input[type=radio]").prop("checked", false)
    //         $("#rule_select_all_categories_0").parent().parent().parent().parent().hide()
    //     }else{
    //         $("#rule_category_id").parent().parent().show();
    //         $("#rule_clear_categories").parent().addClass("d-flex")
    //         $("#rule_clear_categories").parent().show()
    //         $("#rule_select_all_categories_0").parent().parent().parent().parent().show()
    //     }
    // })

    $("#rule_category_id").on('input', function (){
        $("#rule_product").parent().parent().hide()
    })

    // if ($('#rule_select_all_categories_1').prop("checked") || $("input[name='rule[category_id]']:checked").length === 1){
    //     $("#rule_product").parent().parent().hide()
    // }

    // if ($("#rule_status_0").prop("checked") === false && $("#rule_status_1").prop("checked") === false){
    //     $("#rule_status_1").prop("checked", true)
    // }
    //
    // if ( $("#rule_product").val() !== ""){
    //     $("#rule_category_id").parent().parent().hide()
    //     $("#rule_clear_categories").parent().removeClass("d-flex")
    //     $("#rule_clear_categories").parent().hide()
    //     $("#rule_select_all_categories_0").parent().parent().parent().parent().hide()
    // }




});


