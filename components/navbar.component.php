<?php
function useActiveStyle(string|array $pathToMatch)
{
  global $path;
  if (is_array($pathToMatch)) {
    return in_array($path, $pathToMatch) ? 'active' : '';
  } else if (is_string($pathToMatch)) {
    return $path === $pathToMatch ? 'active' : '';
  } else {
    return '';
  }
}
?>

<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="/">NoKer</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <li class="nav-item me-auto">
          <a class="nav-link <?= useActiveStyle(['/', '/home']) ?>" aria-current="page" href="/home">Home</a>
        </li>
        <?php if ($user) { ?>
          <li class="nav-item me-auto">
            <a class="nav-link <?= useActiveStyle('/timeline') ?>" aria-current="page" href="/home">Timeline</a>
          </li>
          <li class="nav-item me-auto">
            <a class="nav-link <?= useActiveStyle('/explore/latest') ?>" aria-current="page" href="/home">Latest</a>
          </li>
          <li class="nav-item me-auto">
            <a class="nav-link <?= useActiveStyle('/explore/hottest') ?>" aria-current="page" href="/home">Hottest</a>
          </li>
        <?php } ?>
        <?php if ($user && $user->email === 'admin@email.com') { ?>
          <li class="nav-item me-auto">
            <a class="nav-link <?= useActiveStyle('/admin/manage-users') ?>" aria-current="page" href="/admin/manage-users">
              Manage Users
            </a>
          </li>
          <li class="nav-item me-auto">
            <a class="nav-link <?= useActiveStyle('/admin/manage-blogs-queue') ?>" aria-current="page" href="/admin/manage-blogs-queue">
              Manage Blogs Queue
            </a>
          </li>
        <?php } ?>
      </ul>

      <form action="/home" class="d-flex flex-fill mx-3">
        <input class="form-control me-2" type="search" name="q" placeholder="Search" aria-label="Search" value="<?= isset($_GET['q'])  ? $_GET['q'] : '' ?>">
        <button class="btn btn-outline-dark" type="submit">Search</button>
      </form>

      <ul class="navbar-nav mb-2 mb-lg-0">
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
            <ul class="dropdown-menu shadow" aria-labelledby="navbarDropdown">
              <li>
                <a class="dropdown-item" href="/profile?id=<?= Auth::getUser()->id ?>">Profile</a>
              </li>
              <li>
                <a class="dropdown-item" href="/top-up">Top-up Balance</a>
              </li>
              <li>
                <a class="dropdown-item" href="/bookmarks">My Bookmarks</a>
              </li>
              <li>
                <a class="dropdown-item" href="/blogs/create">Create Blog</a>
              </li>
              <?php if (Auth::check() && Auth::getUser()->email === 'admin@email.com') { ?>
                <li>
                  <a class="dropdown-item" href="/seed">Seed DB</a>
                </li>
              <?php } ?>
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
