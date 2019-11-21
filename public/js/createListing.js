var descriptionEditor = CKEDITOR.replace('descriptionEditorArea', { 'height': '600' });

var titleLeaveValue = {
    blob: "",
    string: ""
};

var mainImageAsImage = {
    dom: document.getElementById("mainImageFile"),
    binary: null,
    string: null
};
var descriptionImageAsImage = {
    dom: document.getElementById("descriptionImageFile"),
    binary: null,
    string: null
};

var isShowing = false;
var retries = 0;

$(document).ready(function() {




    $("#categorySpinner").hide();
    // https://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded

    function readURL1(input) {

        if (input.files && input.files[0]) {

            var reader1 = new FileReader();

            reader1.onload = function(e) {

                $('#mainImage').attr('src', e.target.result);

                mainImageAsImage.string = e.target.result;

                var binaryStringReader = new FileReader();

                binaryStringReader.onload = (ee) => {

                    mainImageAsImage.binary = ee.target.result;


                }

                binaryStringReader.readAsBinaryString(mainImageAsImage.dom.files[0]);
            }

            reader1.readAsDataURL(input.files[0]);
        }
    }

    function readURL2(input) {

        if (input.files && input.files[0]) {

            var reader2 = new FileReader();

            reader2.onload = function(e) {

                $('#descriptionImage').attr('src', e.target.result);

                descriptionImageAsImage.string = e.target.result;

                var binaryStringReader2 = new FileReader();

                binaryStringReader2.onload = (ee) => {

                    descriptionImageAsImage.binary = ee.target.result;


                }

                binaryStringReader2.readAsBinaryString(descriptionImageAsImage.dom.files[0]);
            }

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




    function sendData() {

        if (retries > 5) {
            retries = 0;
            return;
        }
        let currentForm = document.getElementById("itemForm");
        $('#savetoebay').text('Saving...')
        var formDatatoSend = new FormData(currentForm);
        if ((!mainImageAsImage.binary && mainImageAsImage.dom.files.length > 0) && (!descriptionImageAsImage.binary && descriptionImageAsImage.dom.files.length > 0)) {
            retries++
            setTimeout(sendData, 10);
            return;
        }


        // formDatatoSend.delete('mainImageFile');
        //formDatatoSend.delete('descriptionImageFile');
        //console.log(formDatatoSend);
        //formDatatoSend.append('mainImageFile', mainImageAsImage.dom.files[0], mainImageAsImage.dom.files[0].name);
        // formDatatoSend.append('descriptionImageFile', descriptionImageAsImage.dom.files[0], descriptionImageAsImage.dom.files[0].name);


        var xhr = new XMLHttpRequest();

        var onprogressHandler = (evt) => {
            if (!isShowing) {
                isShowing = true;
                $('#uploadProgress').text('0%')
                $('#uploadProgress').attr('aria-valuenow', '0')
                $('#progress').show();
            }
            var percent = evt.loaded / evt.total * 100;
            $('#uploadProgress').attr('aria-valuenow', percent.toString())
            $('#uploadProgress').text(percent + '%')
            $('#savetoebay').text('Saving ' + percent + '%');

        }
        xhr.upload.addEventListener('progress', onprogressHandler, false);
        xhr.open('POST', postUrl, true);
        xhr.onload = function() {
            isShowing = false;
            $('#progress').hide();
            if (xhr.status === 200) {
                // File(s) uploaded.

                $('#savetoebay').text('Save');
            } else {

                $('#savetoebay').text('Save');
                alert('An error occurred!');
            }
        };

        xhr.send(formDatatoSend);
    }

    var formwatch = document.forms.namedItem('itemForm');
    formwatch.addEventListener("submit", function(event) {
        event.preventDefault();

        if (supportAjaxUploadWithProgress()) {
            sendData();
        } else {
            $("#progress").hide();
            $("#savetoebay").text("Save");
            alert(
                "Your browser is not supported at this time. Please use Google Chrome."
            );
            return;
        }
    });



})

//https://thoughtbot.com/blog/html5-powered-ajax-file-uploads
//by Pablo Brasero  July 30, 2010 UPDATED ON March 9, 2019
function supportAjaxUploadWithProgress() {
    return supportFileAPI() && supportAjaxUploadProgressEvents();

    function supportFileAPI() {
        var fi = document.createElement('INPUT');
        fi.type = 'file';
        return 'files' in fi;
    };

    function supportAjaxUploadProgressEvents() {
        var xhr = new XMLHttpRequest();
        return !!(xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
    };
}
