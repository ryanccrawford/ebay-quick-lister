$(document).ready(function() {

    $("#ActiveList, #SoldList").on('change', function(event) {

        var target = event.currentTarget
        $(target).prop('checked', true);
        var view = $(target).val();
        console.log(view);
        view = "is" + view.toLowerCase() + "=1";
        var url = "/trading?page_num=1&limit=10&" + view;
        window.location = url;

    });

    $(".change-price").on('click', function(event) {
        event.preventDefault();
        var itemId = $(event.target.id).attr("data-item");
        let currentInput = "change_price_" + itemId;
        var newPriceAmount = $("#" + currentInput).val();
        let data = { item_id: itemId, price: newPriceAmount };
        let jasonData = JSON.stringify(data);
        let callbackF1 = data => {
            $("#current_price_" + itemId).text(newPriceAmount.toFixed(2));
        }
        let url = $("#priceupdateurl").val();
        $.ajax({
            url: url,
            data: jasonData,
            dataType: "json"
        }).done(callbackF1);
    });

    $(".change-qoh").on("click", function(event) {
        event.preventDefault();
        var itemId = $(event.target.id).attr("data-item");
        let currentInput = 'change_qoh_' + itemId;
        var newQuantityAmount = $('#' + currentInput).val();
        let data = { item_id: itemId, qoh: newQuantityAmount };
        let jasonData = JSON.stringify(data);

        let callbackF2 = data => {
            $("#current_qoh_" + itemId).text(newQuantityAmount.toFixed(2));
        };
        let url = $('#qohupdateurl').val();
        $.ajax({
            url: url,
            data: jasonData,
            dataType: "json"
        }).done(callbackF2);

    });

})
