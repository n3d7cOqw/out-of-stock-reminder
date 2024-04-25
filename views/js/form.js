$(document).ready(function () {
    window.prestashop.component.initComponents([
        'ChoiceTree',
        'TranslatableField',
    ]);


    new window.prestashop.component.ChoiceTree('#rule_category_id');

    $("#rule_clear_categories").parent().addClass("d-flex")
    $("#rule_clear_categories").parent().addClass("flex-row-reverse")

    let option = $("#rule_option");
    if ($("#rule_product").val() !== ""){
        option.val(1)
    }else {
        option.val(2)
    }


    function changeOptionContent(){
        switch (option.val()) {
            case "1":
                $("#rule_category_id").parent().parent().hide()
                $("#rule_clear_categories").parent().removeClass("d-flex")
                $("#rule_clear_categories").parent().hide()
                $("div.radio input[type=radio]").prop("checked", false)
                $("#rule_select_all_categories_0").parent().parent().parent().parent().hide()
                $('#rule_select_all_categories_0').prop("checked", true)
                $("#rule_product").parent().parent().show()
                break;
            case "2":
                if($(".selected-product")){
                    $(".selected-product").remove()
                }

                $("#rule_product").parent().parent().hide()
                $("#rule_product").val(null)
                $("#rule_category_id").parent().parent().show();
                $("#rule_clear_categories").parent().addClass("d-flex")
                $("#rule_clear_categories").parent().show()
                $("#rule_select_all_categories_0").parent().parent().parent().parent().show()
                break;
        }
    }
    changeOptionContent()

    option.on("change", changeOptionContent)


    $('#rule_select_all_categories_1').click(function (){
        if($('#rule_select_all_categories_1').prop( "checked" )){

            $("#rule_category_id").parent().parent().hide()
            $("#rule_clear_categories").parent().removeClass("d-flex")
            $("#rule_clear_categories").parent().hide()
            $("div.radio input[type=radio]").prop("checked", false)

        }
    })

    $('#rule_select_all_categories_0').click(function (){
        if($('#rule_select_all_categories_0').prop( "checked" )) {
            $("#rule_category_id").parent().parent().show();
            $("#rule_clear_categories").parent().addClass("d-flex")
            $("#rule_clear_categories").parent().show()


        }
    })

    $("#rule_clear_categories").click(function () {
        $("div.radio input[type=radio]").prop("checked", false)

    })


    if ($("#rule_status_0").prop("checked") === false && $("#rule_status_1").prop("checked") === false){
        $("#rule_status_1").prop("checked", true)
    }



});


