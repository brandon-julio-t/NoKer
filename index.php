<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoKer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body style="overflow-x: hidden;">
  <?php

  require_once './adapters/mysql.adapter.php';
  require_once './facades/auth.facade.php';
  require_once './facades/hash.facade.php';
  require_once './facades/storage.facade.php';
  require_once './hooks/admin.hook.php';
  require_once './hooks/auth.hook.php';
  require_once './hooks/csrf.hook.php';
  require_once './hooks/debug.hook.php';
  require_once './hooks/flash-alert.hook.php';
  require_once './hooks/guest.hook.php';
  require_once './hooks/http-method.hook.php';
  require_once './hooks/must-image.hook.php';
  require_once './hooks/now.hook.php';
  require_once './hooks/pretty-date.hook.php';
  require_once './hooks/profile-picture.hook.php';
  require_once './hooks/uuid.hook.php';
  require_once './models/blog.model.php';
  require_once './models/user.model.php';
  require_once './repositories/blog.repository.php';
  require_once './repositories/user.repository.php';

  $path = $_SERVER['REQUEST_URI'];
  $queryIdx = strpos($path, '?');
  if ($queryIdx)  $path = substr($path, 0, $queryIdx);

  if (!isset($_SESSION)) session_start();
  session_regenerate_id(true);

  $user = Auth::getUser();

  require_once './components/navbar.component.php';

  ?>

  <main class="my-3 container">
    <?php

    switch ($path) {
      case '/':
      case '/home':
        useRoute('home');
        break;

      case '/blogs':
        useRoute('/blogs/view');
        break;

      case '/admin/manage-blogs-queue':
      case '/admin/manage-users':
      case '/auth/login':
      case '/auth/register':
      case '/blogs/create':
      case '/blogs/update':
      case '/profile':
      case '/test':
        useRoute($path);
        break;

      case '/auth/logout':
        session_destroy();
        header('Location: /auth/login');
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
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
