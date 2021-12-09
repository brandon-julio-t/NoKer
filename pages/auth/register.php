<?php

useGuest();

$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm-password'];

  if ($password !== $confirmPassword) {
    useFlashAlert('Password does not match.', 'danger');
  } else {
    $user = new User(
      useUuid(),
      $name,
      $email,
      Hash::make($password),
      'https://i.pravatar.cc/300',
      0,
      false,
      null
    );
    $isCreated = UserRepository::create($user);
    if (!$isCreated) {
      useFlashAlert('An error occurred. Please try again.', 'danger');
    } else {
      header('Location: /auth/login');
      return;
    }
  }
}

useFlashAlert();

?>

<div class="d-flex justify-content-center align-items-center">
  <div class="card w-100" style="max-width: 350px;">
    <form action="" method="POST" class="card-body d-grid gap-2">
      <h4 class="card-title">Register</h4>

      <?= useCsrfInput() ?>
      <input type="text" name="name" placeholder="Name" class="form-control">
      <input type="email" name="email" placeholder="Email" class="form-control">
      <input type="password" name="password" placeholder="Password" class="form-control">
      <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Register</button>
        <a class="btn btn-link" href="/auth/login">Login</a>
      </div>
    </form>
  </div>
</div>
