
$(document).ready(function () {
    window.prestashop.component.initComponents([
        'ChoiceTree',
        'TranslatableField',
    ]);


    new window.prestashop.component.ChoiceTree('#rule_category_id');

    $("#rule_clear_categories").parent().addClass("d-flex")
    $("#rule_clear_categories").parent().addClass("flex-row-reverse")

    $('#rule_select_all_categories_1').click(function (){
        if($('#rule_select_all_categories_1').prop( "checked" )){
            $("#rule_category_id").hide()
            $("div.radio input[type=radio]").prop("checked", false)
        }else{
            $("#rule_category_id").show()
        }
    })
    $('#rule_select_all_categories_0').click(function (){
        if($('#rule_select_all_categories_0').prop( "checked" )) {
            $("#rule_category_id").show();
        }
    })

    $("#rule_clear_categories").click(function (){

        $("div.radio input[type=radio]").prop("checked", false)

    })

    if ($(".status-active").length > 0){
        $("#on").prop("checked", true)
    }
});


