<?php

useGuest();

$error = null;
$method = useHttpMethod();
if ($method === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm-password'];

  if ($password !== $confirmPassword) {
    $error = 'Password does not match.';
  } else {
    $user = new User(useUuid(), $name, $email, Hash::make($password));
    $isCreated = UserRepository::create($user);
    if (!$isCreated) {
      $error = 'An error occurred. Please try again.';
    } else {
      header('Location: /auth/login');
      return;
    }
  }
}

?>

<div class="vw-100 vh-100 d-flex justify-content-center align-items-center">
  <div class="card w-100" style="max-width: 320px;">
    <form action="" method="POST" class="card-body d-grid gap-2">
      <h4 class="card-title">Register</h4>

      <input type="text" name="name" placeholder="Name" class="form-control">
      <input type="email" name="email" placeholder="Email" class="form-control">
      <input type="password" name="password" placeholder="Password" class="form-control">
      <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">

      <?php if ($error) { ?>
        <div class="text-danger text-center"><?= $error ?></div>
      <?php } ?>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Register</button>
        <a class="btn btn-link" href="/auth/login">Login</a>
      </div>
    </form>
  </div>
</div>
