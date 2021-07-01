$(function(){ 
    //we talked about this hook, havnt find better yet
    $.ajax({
        async: false,
        dataType: "script",
        url: "lib/jquery-validation-1.19.1/jquery.validate.min.js",
        success: function(){
            $.validator.addMethod("regex", function (value, element, pattern) {
                if (pattern instanceof Array) {
                    for (p of pattern) {
                        if (!p.test(value))
                            return false;
                    }
                    return true;
                } else {
                    return pattern.test(value);
                }
            }, "Please enter a valid input.");
        },
         error: function(request,error) {
             alert('An error occurred attempting to get new e-number');
             // console.log(request, error);
         }
    });

    $.getScript('view/utilsJs/logIn.js', function () {
    });

    $.getScript('view/utilsJs/logout.js', function () {
    });

    $.getScript('view/utilsJs/signup.js', function () {
    });
    
});
