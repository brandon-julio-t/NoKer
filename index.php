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
  require_once './facades/storage.facade.php';
  require_once './hooks/flash-alert.hook.php';
  require_once './hooks/auth.hook.php';
  require_once './hooks/csrf.hook.php';
  require_once './hooks/debug.hook.php';
  require_once './hooks/guest.hook.php';
  require_once './hooks/http-method.hook.php';
  require_once './hooks/now.hook.php';
  require_once './hooks/pretty-date.hook.php';
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
  ?>

  <nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand" href="/">NoKer</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php if ($user) { ?>
            <li class="nav-item me-auto">
              <a class="nav-link <?= useActiveStyle('/auth/login') ?>" aria-current="page" href="/">Home</a>
            </li>
          <?php } ?>
        </ul>

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <?php if (!$user) { ?>
            <li class="nav-item">
              <a class="nav-link <?= useActiveStyle('/auth/login') ?>" href="/auth/login">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= useActiveStyle('/auth/register') ?>" href="/auth/register">Register</a>
            </li>
          <?php } else { ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $user->name ?>
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li>
                  <a class="dropdown-item" href="/profile">Profile</a>
                </li>
                <li>
                  <a class="dropdown-item" href="/blogs/create">Create Blog</a>
                </li>
                <hr class="dropdown-divider">
                <li>
                  <a class="dropdown-item" href="/auth/logout">Logout</a>
                </li>
              </ul>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </nav>

  <main class="mt-3 container">
    <?php

    switch ($path) {
      case '/':
      case '/home':
        useRoute('home');
        break;

      case '/blogs':
        useRoute('/blogs/view');
        break;

      case '/auth/login':
      case '/auth/register':
      case '/blogs/create':
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

    function useActiveStyle($pathToMatch)
    {
      global $path;
      return $path === $pathToMatch ? 'active' : '';
    }

    ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
