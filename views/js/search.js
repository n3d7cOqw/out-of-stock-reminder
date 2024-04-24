$(document).ready(function () {

    $("#rule_product").attr("autocomplete", "off")

    $("#rule_product").on("input", function () {
        $(".selected-product").remove();
        // if ($("#rule_product").val().length > 2) {
            let searched_product = $(this).val()
            const link = $("#rule_product").data("url")
            $.post(link, {"search": searched_product}, function (response) {
                $("#search-input").remove()
                if (response.length > 0) {
                    let content = "<div id='search-input'>"
                    if (response[0].name) {
                        for (let elem of response) {
                            content += `<div class="d-flex mt-2 search-item" data-name = "${elem.name}" data-id ="${elem.id_product}" data-img ="${elem.img}"  data-ref = "${elem.reference}">
<div>
<img src="${elem.img}" alt="" width="100" height="100" class="pl-2">
</div>
<div class="item-container pt-2 pl-2 pr-2" >

 ${elem.name} (ref: ${elem.reference} ) 

</div>
</div>`

                        }
                    }
                    content += "</div>"
                    $("#rule_product").after(content)
                }else{
                    let content = `<div id='search-input' class="pt-1 pl-1 pr-1 pb-1"> No results found for "${searched_product}"</div>`
                $("#rule_product").after(content)
                }


            }).fail(function () {
                console.error("AJAX error")
            })
        // }else{
        //     if ($("#search-input")){
        //         $("#search-input").remove()
        //     }
        //

})

    //delete selected product

    $("#delete-selected-item").on("click", function (e){
        $(".selected-product").remove();
        $("#rule_product").val("")
        $("#rule_product_id").val("")
    })


    $(document).on("click", function (e){
        if(!e.target.closest(".search-item") && !e.target.closest("#rule_product")){
            $("#search-input").remove()
        }else if(e.target.closest(".search-item")){
            const elem = e.target.closest(".search-item");
            const id = $(elem).data("id")
            const name = $(elem).data("name")
            const img = $(elem).data("img")
            const ref = $(elem).data("ref")
            $("#rule_product").val(name)
            $("#rule_product_id").val(id)
            $("#search-input").remove()
            $(".selected-product").remove()
            let selected_product = `<div class="selected-product"><img src="${img}" alt="" width="200" height="200"> <div class="search-product-text"> <span ><i class="material-icons entity-item-delete" id="delete-selected-item">delete</i></span>  ${name} (ref: ${ref} )</div> </div>`

            $("#rule_product").after(selected_product)

            //delete added product
            $("#delete-selected-item").on("click", function (e){
                    $(".selected-product").remove();
                    $("#rule_product").val("")
                    $("#rule_product_id").val("")
            })
        }
    })




})
;