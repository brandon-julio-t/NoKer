<?php

$user = Auth::getUser();

if (useHttpMethod() === 'POST') {
  useCheckCsrf();
  $amount = $_POST['amount'];
  $user->balance += $amount;
  $isSuccess = UserRepository::update($user);
  useFlashAlert(
    $isSuccess ? 'Top-up success.' : 'An error occurred while updating your balance.',
    $isSuccess ? 'success' : 'danger',
  );
}

useFlashAlert();

?>

<form action="" method="POST" class="card">
  <div class="card-body">
    <?= useCsrfInput() ?>
    <h2 class="mb-3">Top-Up Balance</h2>
    <div class="mb-3">
      Current balance: <b><?= $user->balance ?></b>
    </div>
    <input type="number" name="amount" placeholder="Top up amount" required class="form-control mb-3">
    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </div>
</form>
