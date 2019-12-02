
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

