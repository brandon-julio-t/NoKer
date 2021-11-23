<?php

useAdmin();

$users = UserRepository::getAll();
$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  $userId = $_POST['user_id'];
  $action = $_POST['action'];

  $user = array_filter($users, function (User $user) use ($userId) {
    return $user->id === $userId;
  })[0];

  if ($action === 'deactivate') {
    $user->blocked_at = useNow();
  } else {
    $user->blocked_at = null;
  }

  $isUpdated = UserRepository::update($user);
  if (!$isUpdated) {
    useFlashAlert('An error occurred. Please try again.', 'danger');
  } else {
    if ($action === 'deactivate') {
      useFlashAlert('User <b>' . $user->email . '</b> deactivated successfully.', 'success');
    } else {
      useFlashAlert('User <b>' . $user->email . '</b> activated successfully.', 'success');
    }
  }
}

useFlashAlert();

?>

<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col" colspan="2">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user) { ?>
        <tr>
          <td class="align-middle"><?= $user->name ?></td>
          <td class="align-middle"><?= $user->email ?></td>
          <td class="align-middle"><?= $user->blocked_at ? 'Inactive' : 'Active' ?></td>
          <td>
            <form method="POST">
              <?= useCsrfInput(); ?>
              <input type="hidden" name="user_id" value="<?= $user->id ?>">

              <?php if ($user->blocked_at) { ?>
                <input type="hidden" name="action" value="activate">
                <button type="submit" class="btn btn-dark">Activate</button>
              <?php } else { ?>
                <input type="hidden" name="action" value="deactivate">
                <button type="submit" class="btn btn-danger">Deactivate</button>
              <?php } ?>

            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
