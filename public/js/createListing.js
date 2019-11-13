var descriptionEditor = CKEDITOR.replace('descriptionEditorArea', {'height':'600'});

                               
                               
                                
$(document).ready(function(){
                    
    // https://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded
    function readURL1(input) {
        if (input.files && input.files[0]) {
         
            var reader1 = new FileReader();
                    reader1.onload = function(e) {
                        $('#mainImage').attr('src', e.target.result);
                    }
                
                    reader1.readAsDataURL(input.files[0]);
        }
    }

    function readURL2(input) {
        if (input.files && input.files[0]) {
         
            var reader2 = new FileReader();
                    reader2.onload = function(e) {
                        $('#descriptionImage').attr('src', e.target.result);
                    }
                
                    reader2.readAsDataURL(input.files[0]);
        }
    }

    $("#mainImageFile").on('change',function() {
            readURL1(this);
    });
    $("#descriptionImageFile").on('change',function() {
            readURL2(this);
    });
    //------------------------------------------------------------------------------------
    $selectStatus = $('#paymentProfileList');
    $selectStatus.on('change', function(event){
        
                    var before_change = $(this).data('pre');
                    if(before_change !== "") {
                            $('#'+before_change).hide();
                    }
                    var valuedrop = $(this).val();
                    $('#'+valuedrop).show();
                    $(this).data('pre', $(this).val())

    }).data('pre', $selectStatus.val());


    var doit = () => { $.ajax(
        {
            url: "/api/get/paymentpolicies",
            type: 'GET',
        }).done( 
            function(data) 
            {
                $('#payments').html(data.html);
            }
        );

        }
        $.ajax(
        {
           url: "/api/get/returnpolicies",
            type: 'GET',
        }).done( 
            (data) =>
            {
                $('#returns').html(data.html);
                doit();
            }
        );

       

})