$(document).ready(function () {

    // $("#selectAllCategories").prop("checked", true)
    // $("#rule_category_id").hide()
    if(!$("#rule_select_all_categories_1").prop("checked") && !$("#rule_select_all_categories_0").prop("checked")){
        $("#rule_select_all_categories_0").prop("checked", true)
    }

});