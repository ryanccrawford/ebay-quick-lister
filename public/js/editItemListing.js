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
    $("#HTMLeditor1").ckeditor(function(textarea) {
        let desImage = $(htmleditor.document).find("[attr=alt]");
        console.log(desImage);
    });


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