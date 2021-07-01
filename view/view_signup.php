<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <base href="<?= $web_root ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/look.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/all.css" rel="stylesheet" type="text/css" />
  <link href="css/stylebis.css" rel="stylesheet" type="text/css" />
  <script src="lib/jquery-3.4.1.min.js" ></script>
  <script src="css/bootstrap/js/bootstrap.min.js"></script>
  <script src="lib/jquery-validation-1.19.1/jquery.validate.min.js"></script>
  <script src="view/utilsJs/loadUtils.js"></script>


</head>

<body class="bg p-5">
  <?php require("menu.php"); ?>
  <div class="container confirm_box mt-5 p-4 border rounded top_buffer bg-light">
    <div class="row">
      <div class="col text-center mb-2 pb-1 border-bottom">
        <h2 class="text-muted">Sign up</h2>
      </div>
    </div>
    <form class="text-center" action="user/signup" method="post" id="signupForm">
      <div class="input-group mt-2">
        <div class="input-group-append">
          <span class="input-group-text rounded-left"><i class="fas fa-user"></i></span>
        </div>
        <input id="signupPseudo" type="text" name="signupPseudo" placeholder="Pseudo" class="form-control" value="<?= $name ?>" />
      </div>
      <div class="input-group mt-3">
        <div class="input-group-append">
          <span class="input-group-text rounded-left"><i class="fas fa-user"></i></span>
        </div>
        <input id="signupName" type="text" name="signupName" placeholder="Full Name" value="<?= $full_name ?>" class="form-control" />
      </div>
      <div class="input-group mt-3">
        <div class="input-group-append">
          <span class="input-group-text rounded-left"><i class="fas fa-key"></i></span>
        </div>
        <input id="signupPassword" type="password" name="signupPassword" placeholder="Password" value="<?= $password ?>" class="form-control" />
      </div>
      <div class="d-flex flex-column">
        <div class="input-group mt-3">
          <div class="input-group-append">
            <span class="input-group-text rounded-left"><i class="fas fa-lock"></i></span>
          </div>
          <input id="signupPasswordConfirm" type="password" name="signupPasswordConfirm" placeholder="Confirm Password" value="<?= $password_confirm ?>" class="form-control" />
        </div>
      </div>
      <div class="input-group mt-3">
        <div class="input-group-append">
          <span class="input-group-text rounded-left"><i class="fas fa-at"></i></span>
        </div>
        <input id="signupEmail" type="email" name="signupEmail" placeholder="E-mail" value="<?= $email ?>" class="form-control" />
      </div>
      <div class="input-group mt-3 d-flex justify-content-center">
        <button class="btn btn-info dodgerb pl-4 mt-2 pr-4">Sign up</button>
      </div>
    </form>

    <div id="errors"></div>
    <?php if (count($errors) != 0) : ?>
      <div class="alert alert-danger top_buffer red mt-4" role="alert">
        <h4 class="alert-heading alert_title">Please correct the following errors!</h4>
        <?php foreach ($errors as $error) : ?>
          <p class="mb-0"><?= $error ?></p>
        <?php endforeach; ?>
       
      </div>
    <?php endif; ?>
  </div>

</body>

</html>