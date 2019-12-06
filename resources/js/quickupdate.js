if (currentPath.startsWith('trading?')) {

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
            var itemId = $(event.target).attr("data-item");
            let currentInput = "#price_" + itemId;
            var newPriceAmount = $(currentInput).val();
            var oldPriceAmount = parseFloat($("#current_price_" + itemId).text());
            if (newPriceAmount !== oldPriceAmount) {
                let data = "?item_id=" + itemId + "&price=" + newPriceAmount;
                let url = $("#priceupdateurl").val() + data;
          
                let callbackF1 = response => {
                   console.log(response)
                    //{"qoh":78,"item_id":"371619138206","ebay":true}
                    if(response.price){
                        var newPrice = response.price;
                        var returnItemId = response.item_id;
                        var textBoxId = "#current_price_" + returnItemId;
                        $(textBoxId).text(newPrice.toFixed(2));
                        var errorElementid = "#error_" + itemId;
                        $(errorElementid).hide();
                        $(errorElementid).text('');
                        $(errorElementid).removeClass('alert-danger');
                        $(errorElementid).addClass('alert-success');
                        $(errorElementid).text('Success Changing Price!');
                        $(errorElementid).show();

                    }else{

                        var errorElementid = "#error_" + itemId;
                        $(errorElementid).hide();
                        $(errorElementid).text('');
                        $(errorElementid).removeClass('alert-success');
                        $(errorElementid).addClass('alert-danger');
                        $(errorElementid).text('Error Changing QOH!');
                        $(errorElementid).show();
                    }
                    
                };

                $.ajax({
                    url: url,
                    type: "GET",
                }).done(callbackF1);
            }
        });

        $(".change-qoh").on("click", function(event) {
            event.preventDefault();
            var itemId = $(event.target).attr("data-item");
            let currentInput = '#qoh_' + itemId;
            var newQuantityAmount = parseInt($(currentInput).val());
            var oldQuantityAmount = parseInt($("#current_qoh_" + itemId).text());
            if (newQuantityAmount !== oldQuantityAmount) {
                let data = "?item_id=" + itemId + "&qoh=" + newQuantityAmount;
                let url = $("#qohupdateurl").val() + data;


                let callbackF2 = response => {
                   console.log(response);
                    //{"qoh":78,"item_id":"371619138206","ebay":true}
                    if(response.qoh){
                        var newQoh = response.qoh;
                        var returnItemId = response.item_id;
                        var textBoxId = "#current_qoh_" + returnItemId;
                        $(textBoxId).text(newQoh);
                        var errorElementid = "#error_" + itemId;
                        $(errorElementid).hide();
                        $(errorElementid).text('');
                        $(errorElementid).removeClass('alert-danger');
                        $(errorElementid).addClass('alert-success');
                        $(errorElementid).text('Success Changing QOH!');
                        $(errorElementid).show();

                    }else{

                        var errorElementid = "#error_" + itemId;
                        $(errorElementid).hide();
                        $(errorElementid).text('');
                        $(errorElementid).removeClass('alert-success');
                        $(errorElementid).addClass('alert-danger');
                        $(errorElementid).text('Error Changing QOH!');
                        $(errorElementid).show();
                    }
                    
                };

                $.ajax({
                    url: url,
                    type: "GET",
                }).done(callbackF2);
            }
        });
    })
}

