var descriptionEditor = CKEDITOR.replace('descriptionEditorArea', { 'height': '600' });

var titleLeaveValue = {
    blob: "",
    string: ""
};
var mainImageAsImage = {
    blob: '',
    string: ''
};
var descriptionImageAsImage = '';

$(document).ready(function() {
    $("#categorySpinner").hide();
    // https://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded
    function readURL1(input) {
        if (input.files && input.files[0]) {

            var reader1 = new FileReader();
            reader1.onload = function(e) {
                $('#mainImage').attr('src', e.target.result);
                mainImageAsImage.string = e.target.result;
            }
            console.log(input.files)
            mainImageAsImage.blob = input.files[0];
            reader1.readAsDataURL(input.files[0]);
        }
    }

    function readURL2(input) {
        if (input.files && input.files[0]) {

            var reader2 = new FileReader();
            reader2.onload = function(e) {
                $('#descriptionImage').attr('src', e.target.result);
                descriptionImageAsImage.string = e.target.result;
            }
            console.log(input.files)
            descriptionImageAsImage.blob = input.files[0];
            reader2.readAsDataURL(input.files[0]);
        }
    }

    $("#mainImageFile").on('change', function() {
        readURL1(this);
    });
    $("#descriptionImageFile").on('change', function() {
        readURL2(this);
    });
    //------------------------------------------------------------------------------------
    $selectStatus = $('#paymentProfileList');
    $selectStatus.on('change', function(event) {

        var before_change = $(this).data('pre');
        if (before_change !== "") {
            $('#' + before_change).hide();
        }
        var valuedrop = $(this).val();
        $('#' + valuedrop).show();
        $(this).data('pre', $(this).val())

    }).data('pre', $selectStatus.val());
    let is_searching = false;

    var getSuggestedCategories = query => {
        let url = "/api/get/suggestions?title=" + query;
        $.ajax({
            url: url,
            type: "GET"
        }).done(data4 => {
            $("#categorySpinner").hide();
            $("#categorySuggestion").empty();
            $("#categorySuggestion").html(data4);
            is_searching = false;
            $("#catsearchbutton").removeClass("disabled");
        });
    };

    $("#catsearchbutton").on('click', (event) => {
        event.preventDefault();
        let title = $("#ebaytitle").val();
        if (title < 4 || title > 350) {
            $("#categorySpinner").hide();
            return;
        }
        $("#categorySpinner").show();
        if (!is_searching) {
            is_searching = true;
            getSuggestedCategories(title);
        }



    });
    $("#title").focusout((event) => {
        titleLeaveValue = $("#title").val();
        $("#categorySpinner").show();
        $("#catsearchbutton").addClass("disabled");
        getSuggestedCategories(titleLeaveValue);
    })
    $("#title").keyup((event) => {
        if (titleLeaveValue !== $("#title").val()) {
            $("#categorySuggestion").empty();
        }
    })
    var getShippingOptions = () => {
        $.ajax({
            url: "/api/get/shippingpolicies",
            type: "GET"
        }).done(
            (data3) => {

                $("#shipping").empty()
                $("#shipping").html(data3);

            });
    };

    var getPaymentOptions = () => {
        $.ajax({
            url: "/api/get/paymentpolicies",
            type: 'GET',
        }).done(
            (data1) => {
                $("#payments").empty()
                $("#payments").html(data1);


            }
        );

    }

    var getReturnOptions = () => {
        $.ajax({
            url: "/api/get/returnpolicies",
            type: 'GET',
        }).done(
            (data2) => {


                $('#returns').empty()
                $('#returns').html(data2);


            }
        );
    }

    getPaymentOptions();
    getShippingOptions();
    getReturnOptions();

    var itemform = document.getElementById('itemForm');

    itemform.onsubmit((event) => {
        event.preventDefault();

        if (supportAjaxUploadWithProgress) {


            $('#savetoebay').text('Saving...')


            var file1 = document.getElementById('mainImageFile');
            var file2 = document.getElementById('descriptionImageFile');
            var image1 = file1.files[0];
            var image2 = file2.files[0];
            var formData = new FormData();
            if (!image1.type.match('image.*') || !image2.type.match('image.*')) {
                alert("Not an image file.")
                return;
            }
            formData.append('mainImageFile', image1, image1.name);
            formData.append('descriptionImageFile', image2, image2.name);

            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', onprogressHandler, false);
            xhr.open('POST', postUrl, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // File(s) uploaded.
                    $('#savetoebay').text('Save');
                } else {
                    alert('An error occurred!');
                }
            };
            xhr.send(formData);



        } else {

            alert("Your browser is not supported at this time. Please use Google Chrome.")

        }

    });

    function onprogressHandler(evt) {
        var percent = evt.loaded / evt.total * 100;
        $('#savetoebay').text('Saving ' + percent + '%');

    }

})

//https://thoughtbot.com/blog/html5-powered-ajax-file-uploads
//by Pablo Brasero  July 30, 2010 UPDATED ON March 9, 2019
function supportAjaxUploadWithProgress() {
    return supportFileAPI() & amp; & amp;
    supportAjaxUploadProgressEvents();

    function supportFileAPI() {
        var fi = document.createElement('INPUT');
        fi.type = 'file';
        return 'files' in fi;
    };

    function supportAjaxUploadProgressEvents() {
        var xhr = new XMLHttpRequest();
        return !!(xhr & amp; & amp;
            ('upload' in xhr) & amp; & amp;
            ('onprogress' in xhr.upload));
    };
}
