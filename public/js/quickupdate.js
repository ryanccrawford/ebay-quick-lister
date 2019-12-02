$('.change-price')


$('.change-qoh')

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


})
