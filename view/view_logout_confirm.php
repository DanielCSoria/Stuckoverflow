<!DOCTYPE html>
<html lang="en">
<html>

<head>
  <title>Confirm log out</title>
  <base href="<?= $web_root ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/all.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/stylebis.css" rel="stylesheet" type="text/css" />
  <link href="css/style.css" rel="stylesheet" type="text/css" />

</head>

<body>
  <?php require_once("view/menu.php"); ?>

  <body class="bg p-5">
    <div class="container confirm_box mt-5 p-4 border rounded text-center bg-light">
      <div class="row">
        <div class="col text-center mb-2 pb-1 border-bottom">
          <i id="logo" class="fas fa-sign-out-alt fa-7x"></i>
          <h3 class="pb-2 mt-2">Do you really want to sign out ?</h3>
        </div>
      </div>

      <form action="user/logout_confirm" method="post">
        <div class="row">
          <div class="col">
            <button class="btn btn-dark btn-lg btn ml-4  " name="confirm" value="accept">Confirm </button>
          </div>
          <div class="col">
            <button class="btn btn-danger btn-lg  " name="confirm" value="cancel">Cancel </button>
          </div>
        </div>
      </form>

  </body>

</html>