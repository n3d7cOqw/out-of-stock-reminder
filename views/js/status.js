$(document).ready(function () {

    $(".column-status").each(function (){
        if ($.trim($(this).text()) === "1"){
            $(this).text("active");
        }else{
            $(this).text("disabled");
        }
    })
});