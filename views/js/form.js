
$(document).ready(function () {
    new window.prestashop.component.ChoiceTree('.choice-tree-actions');


    $('#selectAllCategories').click(function (){
        if($('#selectAllCategories').prop( "checked" )){
            $("#rule_category_id").hide()
        }else{
            $("#rule_category_id").show()
        }
    })


});


