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

    $("#mainImageFile").change(function() {
            readURL1(this);
    });
    $("#descriptionImageFile").change(function() {
            readURL2(this);
    });
    //------------------------------------------------------------------------------------


})