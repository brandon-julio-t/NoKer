<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoKer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
  <?php

  require_once './adapters/mysql.adapter.php';
  require_once './facades/auth.facade.php';
  require_once './facades/hash.facade.php';
  require_once './hooks/auth.hook.php';
  require_once './hooks/debug.hook.php';
  require_once './hooks/guest.hook.php';
  require_once './hooks/http-method.hook.php';
  require_once './hooks/uuid.hook.php';
  require_once './hooks/uuid.hook.php';
  require_once './models/user.model.php';
  require_once './repositories/user.repository.php';

  $path = $_SERVER['REQUEST_URI'];

  session_start();
  session_regenerate_id(true);

  switch ($path) {
    case '/':
    case '/home':
      useRoute('home');
      break;

    case '/auth/login':
    case '/auth/register':
      useRoute($path);
      break;

    default:
      require_once './pages/errors/404.php';
      break;
  }

  function useRoute($path)
  {
    require_once "./pages/$path.php";
  }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
