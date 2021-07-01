<!DOCTYPE html>
<html lang="en">

<head>
    <title>Log In</title>
    <base href="<?= $web_root ?>" />
    <link href="css/look.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/all.css" rel="stylesheet" type="text/css" />
  <script src="lib/jquery-3.4.1.min.js"></script>
  <script src="css/bootstrap/js/bootstrap.min.js"></script>
  <script src="lib/jquery-validation-1.19.1/jquery.validate.min.js"></script>
  <link href="css/stylebis.css" rel="stylesheet" type="text/css" />

    <script>
            
        
            $(function(){
                $('#loginForm').validate({
                    rules: {
                        pseudo: {
                            remote: {
                                url: 'user/pseudo_available_service',
                                type: 'post',
                                data:  {
                                    pseudo: function() { 
                                        return $("#pseudo").val() == "true" ? "false" : "true";
                                    }
                                }
                            },
                            required: true, 
                          
                        },
                        password: {
                          
                            required: true,
                          
                        }, 
                    },
                    messages: {
                        pseudo: {
                            remote: "No user with this user name. Please sign up.",
                            required: 'Pseudo is required',
                            
                        },
                        password: {
                            required: 'Password is required',
                           
                        },
                    },
                   
                });
                $("input:text:first").focus();
            });

    </script>
</head>


    <?php require("view/menu.php"); ?>

    <body class="bg p-5">
    
        <div class="container confirm_box mt-5 p-4 bg-light rounded">
            <div class="row">
                <div class="col text-center mb-2 pb-1 border-bottom">
                    <h2 class="text-muted">Sign in</h2>
                </div>
            </div>
            <form class="text-center" action="user/login" method="post" id ="loginForm">
                <div class="input-group mt-2">
                    <div class="input-group-append">
                        <span class="input-group-text rounded-left"><i class="fas fa-user"></i></span>
                    </div>
                    <input id = "pseudo" type="text" name="pseudo" placeholder="Pseudo" class="form-control" value="<?= $name ?>" />
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-append">
                        <span class="input-group-text rounded-left"><i class="fas fa-lock"></i></span>
                    </div>
                    <input id = "password" type="password" name="password" placeholder="Password" class="form-control" value="<?= $password ?>" />
                </div>
                <div class="input-group mt-3 d-flex justify-content-center">
                    <button class="btn btn-info dodgerb">Log in</button>
                </div>
            </form>
            <div id="errors"></div>
            <?php if (count($errors) != 0) : ?>
                <div class="alert alert-danger top_buffer red mt-4" role="alert">
                    <h4 class="alert-heading alert_title">Please correct the following errors!</h4>
                    <?php foreach ($errors as $error) : ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

    </body>

</html>