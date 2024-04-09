
$(document).ready(function () {
    new window.prestashop.component.ChoiceTree('.choice-tree-actions');


    $('#selectAllCategories').click(function (){
        if($('#selectAllCategories').prop( "checked" )){
            $("#rule_category_id").hide()
            $("div.radio input[type=radio]").prop("checked", false)
        }else{
            $("#rule_category_id").show()
        }
    })

    if ($(".status-active").length > 0){
        $("#on").prop("checked", true)
    }

});


