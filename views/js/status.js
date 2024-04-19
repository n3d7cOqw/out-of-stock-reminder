$(document).ready(function () {
    $("tbody").on("click", function (event){
        if ($(event.target).is("input[type='radio']")){
            const toggle = $(event.target)
            const link =  toggle.parent().data("toggle-url")
            $.post(link, function(responce){
                if (responce.success){
                    $(".growl").text(responce.text)
                    $(".notification-block-message").removeClass("d-none")
                    $(".notification-block-message").hide().fadeIn("slow")

                    setTimeout(function (){
                        $(".notification-block-message").addClass("d-none")
                    }, 3500)

                }else{


                }
            }).fail(function(){
                console.error("AJAX error")
            })
        }
    })

    $(".growl-close").on("click", function (){
        $(".notification-block-message").addClass("d-none")
    })
});