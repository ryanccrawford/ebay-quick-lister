
    var descriptionEditor = CKEDITOR.replace("descriptionEditorArea", {
        height: "600"
    });
    //descriptionHtml
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
    var selectBoxesReady = 0;
    const numberOfSelectBoxes = 4;
    var buttonPressed = "none";
    $(document).ready(function() {
        $("#item-image").hide();
        disableElement("savetoebay");

        $("#categorySpinner").hide();
        // https://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded

        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader1 = new FileReader();

                reader1.onload = function(e) {
                    $("#mainImage").attr("src", e.target.result);

                    $("#item-image").attr("src", e.target.result);
                    $("#item-image").show();

                    mainImageAsImage.string = e.target.result;

                    var binaryStringReader = new FileReader();

                    binaryStringReader.onload = ee => {
                        mainImageAsImage.binary = ee.target.result;
                    };

                    binaryStringReader.readAsBinaryString(
                        mainImageAsImage.dom.files[0]
                    );
                };

                reader1.readAsDataURL(input.files[0]);
            }
        }

        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader2 = new FileReader();

                reader2.onload = function(e) {
                    $("#descriptionImage").attr("src", e.target.result);
                    let mImage = e.target.result;
                    let temp = descriptionHtml.replace(
                        "@descriptionImage",
                        mImage
                    );
                    let tempTitle = temp.replace(
                        "@title",
                        $("#ebaytitle").val()
                    );
                    descriptionEditor.setData(tempTitle);

                    descriptionImageAsImage.string = e.target.result;

                    var binaryStringReader2 = new FileReader();

                    binaryStringReader2.onload = ee => {
                        descriptionImageAsImage.binary = ee.target.result;
                    };

                    binaryStringReader2.readAsBinaryString(
                        descriptionImageAsImage.dom.files[0]
                    );
                };

                reader2.readAsDataURL(input.files[0]);
            }
        }

        $("#mainImageFile").on("change", function() {
            readURL1(this);
        });
        $("#descriptionImageFile").on("change", function() {
            readURL2(this);
        });
        //------------------------------------------------------------------------------------
        $selectStatus = $("#paymentProfileList");
        $selectStatus
            .on("change", function(event) {
                var before_change = $(this).data("pre");
                if (before_change !== "") {
                    $("#" + before_change).hide();
                }
                var valuedrop = $(this).val();
                $("#" + valuedrop).show();
                $(this).data("pre", $(this).val());
            })
            .data("pre", $selectStatus.val());
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
                if (selectBoxesReady !== numberOfSelectBoxes) {
                    selectBoxesReady++;
                }
                if (selectBoxesReady === numberOfSelectBoxes) {
                    enableElement("savetoebay");
                }
            });
        };

        $("#catsearchbutton").on("click", event => {
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
        $("#ebaytitle").focusout(event => {
            if (titleLeaveValue !== $("#ebaytitle").val()) {
                titleLeaveValue = $("#ebaytitle").val();
                $("#categorySpinner").show();
                disableElement("catsearchbutton");
                $("#catsearchbutton").click();
            }
        });
        $("#ebaytitle").keyup(event => {
            let tempTitle = descriptionHtml.replace(
                "@title",
                $("#ebaytitle").val()
            );
            descriptionEditor.setData(tempTitle);
            if (titleLeaveValue !== $("#ebaytitle").val()) {
                $("#categorySuggestion").empty();
            }
        });
        var getShippingOptions = () => {
            $.ajax({
                url: "/api/get/shippingpolicies",
                type: "GET"
            }).done(data3 => {
                $("#shipping").empty();
                $("#shipping").html(data3);
                if (selectBoxesReady !== numberOfSelectBoxes) {
                    selectBoxesReady++;
                }
                if (selectBoxesReady === numberOfSelectBoxes) {
                    enableElement("savetoebay");
                }
            });
        };

        var getPaymentOptions = () => {
            $.ajax({
                url: "/api/get/paymentpolicies",
                type: "GET"
            }).done(data1 => {
                $("#payments").empty();
                $("#payments").html(data1);

                if (selectBoxesReady !== numberOfSelectBoxes) {
                    selectBoxesReady++;
                }
                if (selectBoxesReady === numberOfSelectBoxes) {
                    enableElement("savetoebay");
                }
            });
        };

        var getReturnOptions = () => {
            $.ajax({
                url: "/api/get/returnpolicies",
                type: "GET"
            }).done(data2 => {
                $("#returns").empty();
                $("#returns").html(data2);
                if (selectBoxesReady !== numberOfSelectBoxes) {
                    selectBoxesReady++;
                }
                if (selectBoxesReady === numberOfSelectBoxes) {
                    enableElement("savetoebay");
                }
            });
        };

        getPaymentOptions();
        getShippingOptions();
        getReturnOptions();

        $("#savetoebay").on("click", function(event) {
            buttonPressed = event.target.id;
        });
        $("#saveforlater").on("click", function(event) {
            buttonPressed = event.target.id;
        });

        function sendData(url, button) {
            if (retries > 5) {
                retries = 0;
                return;
            }
            var htmljq = descriptionEditor.getData();
            var tempdoc = document.implementation.createHTMLDocument(
                "ebay Item Description"
            );

            tempdoc.body.innerHTML = htmljq;

            $(tempdoc)
                .find("title")
                .text($("#ebaytitle").val());
            $(tempdoc)
                .find("#image")
                .attr("src", "@image");

            let currentForm = document.getElementById("itemForm");
            currentForm.descriptionEditorArea.value = tempdoc.body.innerHTML;
            $("#" + button).text("Saving...");
            const formData = new FormData(currentForm);

            const xhr = new XMLHttpRequest();

            var onprogressHandler = evt => {
                if (!isShowing) {
                    isShowing = true;
                    if (buttonPressed === "savetoebay") {
                        $("#uploadProgress").text("0%");
                        style = "width: 0%;";
                        $("#uploadProgress").css("width", "0%");

                        $("#uploadProgress").attr("aria-valuenow", "0");
                        $("#progress").show();
                    }
                }
                var percent = (evt.loaded / evt.total) * 100;
                if (buttonPressed === "savetoebay") {
                    $("#uploadProgress").attr(
                        "aria-valuenow",
                        percent.toString()
                    );
                    $("#uploadProgress").text(percent + "%");
                    $("#uploadProgress").css("width", percent + "%");
                    $("#savetoebay").text("Saving " + percent + "%");
                }
            };
            xhr.upload.addEventListener("progress", onprogressHandler, false);
            console.log(url);
            xhr.open("POST", url, true);
            xhr.onload = function() {
                isShowing = false;
                $("#progress").hide();
                if (xhr.status === 200) {
                    console.log(xhr);
                    $("#result").html(xhr.response);

                    $("#".buttonPressed).text("Saved");
                    setTimeout(function() {
                        if (buttonPressed === "savetoebay") {
                            $("#savetoebay").text("Verify and List");
                        } else {
                            $("#saveforlater").text("Save For Later");
                        }
                    }, 2000);
                } else {
                    if (buttonPressed === "savetoebay") {
                        $("#savetoebay").text("Verify and List");
                    } else {
                        $("#saveforlater").text("Save For Later");
                    }
                    $("#result").html(xhr.response);
                }
                enableElement(buttonPressed);
            };

            xhr.send(formData);
        }

        var formwatch = document.forms.namedItem("itemForm");
        formwatch.addEventListener("submit", function(event) {
            console.log(buttonPressed);
            event.preventDefault();
            let url = "";

            disableElement(buttonPressed);
            if (this.id === "savetoebay") {
                url = postUrlVerify;
            } else {
                url = postUrl;
            }

            if (supportAjaxUploadWithProgress()) {
                sendData(url, buttonPressed);
            } else {
                $("#progress").hide();
                if (buttonPressed === "savetoebay") {
                    $("#savetoebay").text("Verify and List");
                } else {
                    $("#saveforlater").text("Save For Later");
                }
                alert(
                    "Your browser is not supported at this time. Please use Google Chrome."
                );
                enableElement(buttonPressed);
                return;
            }
        });
    });

    function enableElement(id) {
        $("#" + id).prop("disabled", false);
        $("#" + id).removeClass("disabled");
    }

    function disableElement(id) {
        $("#" + id).prop("disabled", true);
        $("#" + id).addClass("disabled");
    }

    //https://thoughtbot.com/blog/html5-powered-ajax-file-uploads
    //by Pablo Brasero  July 30, 2010 UPDATED ON March 9, 2019
    function supportAjaxUploadWithProgress() {
        return supportFileAPI() && supportAjaxUploadProgressEvents();

        function supportFileAPI() {
            var fi = document.createElement("INPUT");
            fi.type = "file";
            return "files" in fi;
        }

        function supportAjaxUploadProgressEvents() {
            var xhr = new XMLHttpRequest();
            return !!(xhr && "upload" in xhr && "onprogress" in xhr.upload);
        }
    }




    var htmleditor = CKEDITOR.replace('HTMLeditor1', { 'height': '600' });
    var rawHTML = '';
    $(document).ready(function(e) {
        var templateLoaded = false;
        $.get("../files/policy.html", data => {
            rawHTML = data;
            templateLoaded = true;
        });
        var uploadFinished = $('.js-upload-finished');
        var progressBar = $('.progress');
        var imageButton = $('#upload-image-submit');
        var progressValue = $('#upload-progress-bar');
        var textCompleted = $('#text-complete');
        progressValue.val('0');
        textCompleted.val('');
        uploadFinished.hide();
        progressBar.hide();


        var dropZone = document.getElementById('upload-image-drop-zone');


        $('#desc').keyup((e) => {
            if (templateLoaded) {
                updateHTML();
            }
        });

        $('#ebaytitle').keyup((e) => {
            if (templateLoaded) {
                updateHTML();
            }

        });

        $('#sku').keyup((e) => {
            if (templateLoaded) {
                updateHTML();
            }
        });

        $('#copyhtml').on('click', function(e) {
            e.preventDefault()
            var clipboard = htmleditor.getData();
            navigator.permissions.query({ name: 'clipboard-write' }).then(result => {
                if (result.state == 'granted') {
                    updateClipboard(clipboard);
                } else if (result.state == 'prompt') {
                    updateClipboard(clipboard);
                }

            });
        })

        $("#savetoebay").on('click', function(e) {

            e.preventDefault()

        })






        $(imageButton).on('click', function(e) {
            e.preventDefault()
            var uploadFile = document.getElementById('upload-image').files[0];

            var formData = new FormData();
            formData.append('file', uploadFile);
            var action_now = $('#upload-image-form').attr('data-action');
            var axiosOptions = {
                method: 'POST',
                url: action_now,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            };
            axios(axiosOptions)
                .then(response => {
                    console.log(response.data)
                    if (response.data) {
                        var image = "images/" + response.data.file
                        var itemFinished = $('<div>').addClass('list-group-item', 'list-group-item-success')
                        var spanItem = $('<span>').addClass('badge alert-success pull-right').text('Success')
                        itemFinished.append(spanItem).text(image)
                        $('.list-group').append(itemFinished)
                        $('#item-image').attr('src', image).css('max-width', '300px').show()
                        $.get("policy.html", (data) => {
                            var valoftitle = $('#ebaytitle').val() + " | " + $('#sku').val();
                            var newHtml = data.replace("@title", valoftitle);
                            var newHtmlImage = newHtml.replace("@image", image);
                            var detext = $('#desc').val()
                            var newHtmlDesc = newHtmlImage.replace("@description", detext);
                            htmleditor.setData(newHtmlDesc)
                        });
                    } else {

                        var message = response.data.message
                        console.log(message)
                        var itemFinished = $('<div>').addClass('list-group-item', 'list-group-item-error')
                        var spanItem = $('<span>').addClass('badge alert-danger pull-right').text('Error')
                        itemFinished.append(spanItem).text(message)
                        $('.list-group').append(itemFinished)
                        $('#item-image').attr('src', '').hide()
                    }
                }).catch(response => {
                    console.log(response)
                })
        })

        dropZone.ondrop = function(e) {
            e.preventDefault();
            this.className = 'upload-image-drop-zone';

            startUpload(e.dataTransfer.files)
        }

        dropZone.ondragover = function() {
            this.className = 'upload-image-drop-zone drop';
            return false;
        }

        dropZone.ondragleave = function() {
            this.className = 'upload-image-drop-zone';
            return false;
        }

        function createAlert(title, message) {
            //
            /*
                <div class="alert alert-warning alert-dismissible fade show" role="alert" id="mainalertmessage">
                    <strong id="messagealert"></strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

            */
            var alert = $('<div>').addClass('alert').addClass('alert-warning').addClass('alert-dismissible').addClass('fade').addClass('show')
            alert.attr('role', 'alert')

            var alertTitle = $('<strong>').text(title)
            var dissmissAlert = $('<button>').addClass('close').attr('data-dismiss', 'alert').attr('aria-label', 'Close')

            var dissmissLable = $('<span>').attr('aria-hidden', 'true').text('X')
            alert.append(alertTitle)
            alert.text(message)
            dissmissAlert.append(dissmissLable)
            alert.append(dissmissAlert)

        }

        function updateHTML() {
            var detext = $("#desc").val();
            var valoftitle = $("#ebaytitle").val() + " | " + $("#sku").val();
            var newHtml = rawHTML.replace("@title", valoftitle);
            var image = $("#item-image").attr("src");
            var newHtmlImage = newHtml.replace("@image", image);
            var newHtmlDesc = newHtmlImage.replace("@description", detext);
            htmleditor.setData(newHtmlDesc);
        }

        function updateClipboard(newClip) {
            navigator.clipboard.writeText(newClip).then(() => {
                var al = '<div class="alert alert-primary alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>X</span></button><p>HTML was copied to the clipboard!</p></div>'

                $('#alertarea2').html(al).show();

            }, function() {
                $('#alertarea3').show();
            });
        }
    })


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

$(document).ready(function(e) {

    var uploadFinished = $('.js-upload-finished');
    var progressBar = $('.progress');
    var imageButton = $('#upload-image-submit');
    var progressValue = $('#upload-progress-bar');
    var textCompleted = $('#text-complete');
    progressValue.val('0');
    textCompleted.val('');
    uploadFinished.hide();
    progressBar.hide();
    var template = '';
    var templateLoaded = false
    $.get("policy.html", (data) => {
        template = data
        templateLoaded = true;
    })
    var dropZone = document.getElementById('upload-image-drop-zone');

 
    $('#desc').keyup((e) => {
        if (templateLoaded) {
            var detext = $('#desc').val()
            var valoftitle = $('#ebaytitle').val() + " | " + $('#sku').val();
            var newHtml = template.replace("@title", valoftitle);
            var image = $('#item-image').attr('src')
            var newHtmlImage = newHtml.replace("@image", image);
            var newHtmlDesc = newHtmlImage.replace("@description", detext);
            htmleditor.setData(newHtmlDesc)
        }
    });

    $('#ebaytitle').keyup((e) => {
        if (templateLoaded) {
            var detext = $('#desc').val()
            var valoftitle = $('#ebaytitle').val() + " | " + $('#sku').val();
            var newHtml = template.replace("@title", valoftitle);
            var image = $('#item-image').attr('src')
            var newHtmlImage = newHtml.replace("@image", image);
            var newHtmlDesc = newHtmlImage.replace("@description", detext);
            htmleditor.setData(newHtmlDesc)
        }

    });

    $('#sku').keyup((e) => {
        if (templateLoaded) {
            var detext = $('#desc').val()
            var valoftitle = $('#ebaytitle').val() + " | " + $('#sku').val();
            var newHtml = template.replace("@title", valoftitle);
            var image = $('#item-image').attr('src')
            var newHtmlImage = newHtml.replace("@image", image);
            var newHtmlDesc = newHtmlImage.replace("@description", detext);
            htmleditor.setData(newHtmlDesc)
        }
    });

    $('#copyhtml').on('click', function(e) {
        e.preventDefault()
        var clipboard = htmleditor.getData();
        navigator.permissions.query({ name: 'clipboard-write' }).then(result => {
            if (result.state == 'granted') {
                updateClipboard(clipboard);
            } else if (result.state == 'prompt') {
                updateClipboard(clipboard);
            }

        });
    })

    $("#savetoebay").on('click', function(e) {

        e.preventDefault()

        var sku = $('#sku').val();
        var descrip = htmleditor.getData();
        var product = {
            title: $('#ebaytitle').val(),
            description: descrip,
            condition: 1000,
            sku: $('#sku').val(),
            imageUrls: ['https://www.3starinc.com/ebaymaker/' + $('#item-image').attr('src')],
            packageWeightAndSize: {
                dimensions: {
                    height: 5,
                    length: 10,
                    width: 15,
                    unit: "INCH"
                },
                packageType: "MAILING_BOX",
                weight: {
                    value: 15,
                    unit: "POUND"
                }
            },
            availability: {
                shipToLocationAvailability: {
                    quantity: $('#qty').val().toString()
                }
            }
        }

        $.ajax({
            url: 'ebay.php?sku=' + $('#sku').val(),
            method: 'POST',
            data: JSON.stringify(product),
            dataType: "json",
            success: function(data) {
                if (data) {
                    console.log(data)
                    $('#alertarea').append(createAlert('Success', data.message))
                }
            },
            error: function(data) {
                console.log(data);
                $('#alertarea').append(createAlert('Error', data.responseText))
                var message = data.message
                console.log(message)
                var itemFinished = $('<div>').addClass('list-group-item', 'list-group-item-error')
                var spanItem = $('<span>').addClass('badge alert-danger pull-right').text('Error')
                itemFinished.append(spanItem).text(message)
                $('.list-group').append(itemFinished)
                $('#item-image').attr('src', '').hide()
            }
        })
    })



    $(imageButton).on('click', function(e) {
        e.preventDefault()
        var uploadFile = document.getElementById('upload-image').files[0];

        var formData = new FormData();
        formData.append('file', uploadFile);
        var action_now = $('#upload-image-form').attr('data-action');
        var axiosOptions = {
            method: 'POST',
            url: action_now,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };
        axios(axiosOptions)
            .then(response => {
                console.log(response.data)
                if (response.data) {
                    var image = "images/" + response.data.file
                    var itemFinished = $('<div>').addClass('list-group-item', 'list-group-item-success')
                    var spanItem = $('<span>').addClass('badge alert-success pull-right').text('Success')
                    itemFinished.append(spanItem).text(image)
                    $('.list-group').append(itemFinished)
                    $('#item-image').attr('src', image).css('max-width', '300px').show()
                    $.get("policy.html", (data) => {
                        var valoftitle = $('#ebaytitle').val() + " | " + $('#sku').val();
                        var newHtml = data.replace("@title", valoftitle);
                        var newHtmlImage = newHtml.replace("@image", image);
                        var detext = $('#desc').val()
                        var newHtmlDesc = newHtmlImage.replace("@description", detext);
                        htmleditor.setData(newHtmlDesc)
                    });
                } else {

                    var message = response.data.message
                    console.log(message)
                    var itemFinished = $('<div>').addClass('list-group-item', 'list-group-item-error')
                    var spanItem = $('<span>').addClass('badge alert-danger pull-right').text('Error')
                    itemFinished.append(spanItem).text(message)
                    $('.list-group').append(itemFinished)
                    $('#item-image').attr('src', '').hide()
                }
            }).catch(response => {
                console.log(response)
            })
    })

    dropZone.ondrop = function(e) {
        e.preventDefault();
        this.className = 'upload-image-drop-zone';

        startUpload(e.dataTransfer.files)
    }

    dropZone.ondragover = function() {
        this.className = 'upload-image-drop-zone drop';
        return false;
    }

    dropZone.ondragleave = function() {
        this.className = 'upload-image-drop-zone';
        return false;
    }

    function createAlert(title, message) {
        //
        /*
            <div class="alert alert-warning alert-dismissible fade show" role="alert" id="mainalertmessage">
                <strong id="messagealert"></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

        */
        var alert = $('<div>').addClass('alert').addClass('alert-warning').addClass('alert-dismissible').addClass('fade').addClass('show')
        alert.attr('role', 'alert')

        var alertTitle = $('<strong>').text(title)
        var dissmissAlert = $('<button>').addClass('close').attr('data-dismiss', 'alert').attr('aria-label', 'Close')

        var dissmissLable = $('<span>').attr('aria-hidden', 'true').text('X')
        alert.append(alertTitle)
        alert.text(message)
        dissmissAlert.append(dissmissLable)
        alert.append(dissmissAlert)

    }

    function updateClipboard(newClip) {
        navigator.clipboard.writeText(newClip).then(() => {
            var al = '<div class="alert alert-primary alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>X</span></button><p>HTML was copied to the clipboard!</p></div>'

            $('#alertarea2').html(al).show();

        }, function() {
            $('#alertarea3').show();
        });
    }
})