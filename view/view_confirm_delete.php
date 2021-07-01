<!DOCTYPE html>
<html lang="en">

<head>
  <title>Confirm <?= $target_type ?> delete</title>
  <base href="<?= $web_root ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/all.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <link href="css/stylebis.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <?php require_once("view/menu.php"); ?>

  <body class="bg p-5">
    <div class="container confirm_box mt-5 p-4 border rounded text-center">
      <div class="row">
        <div class="col text-center mb-2 pb-1 border-bottom">
          <i id="logo" class="far fa-trash-alt fa-7x"></i>
          <h3 class="pb-2 mt-2">Do you really want to delete the <?= $target_type ?> ?</h3>
        </div>
      </div>

      <form action="<?= $target_type ?>/delete" method="post">
        <div class="row">
          <div class="col">
            <button class="btn btn-dark btn-lg ml-4" name="confirm" value="<?= $item->get_id() ?>">Confirm</button>
          </div>
          <div class="col">
            <button class="btn btn-danger btn-lg" name="cancel" value="<?= $item->get_id() ?>">Cancel</button>
          </div>
        </div>
        <input type="hidden" value="<?=$item->get_id()?>" name="id"/>
      </form>

  </body>

</html>