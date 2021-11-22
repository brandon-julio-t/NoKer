<?php

useGuest();

$error = null;
$method = useHttpMethod();
if ($method === 'POST') {
  $isLoggedIn = Auth::login($_POST['email'], $_POST['password']);
  if ($isLoggedIn) {
    header('Location: /home');
    return;
  } else {
    $error = 'Invalid credentials.';
  }
}

?>

<div class="vw-100 vh-100 d-flex justify-content-center align-items-center">
  <div class="card w-100" style="max-width: 320px;">
    <form action="" method="POST" class="card-body d-grid gap-2">
      <h4 class="card-title">Login</h4>

      <input type="email" name="email" placeholder="Email" class="form-control">
      <input type="password" name="password" placeholder="Password" class="form-control">

      <?php if ($error) { ?>
        <div class="text-danger text-center"><?= $error ?></div>
      <?php } ?>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Login</button>
        <a class="btn btn-link" href="/auth/register">Register</a>
      </div>
    </form>
  </div>
</div>
