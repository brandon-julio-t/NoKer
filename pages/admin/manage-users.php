<?php

useAdmin();

$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  $userId = $_POST['user-id'];
  $action = $_POST['action'];

  $user = UserRepository::getOneById($userId);

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

$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
[$users, $totalCount] = UserRepository::getAllPaginated($currentPage);
$maxPage = ceil($totalCount / 10);

useFlashAlert();

?>

<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col" colspan="2">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $idx => $user) { ?>
        <tr>
          <td class="align-middle"><?= $idx + 1 ?></td>
          <td class="align-middle"><?= $user->name ?></td>
          <td class="align-middle"><?= $user->email ?></td>
          <td class="align-middle <?= $user->blocked_at ? 'text-danger' : 'text-success' ?>">
            <?= $user->blocked_at ? 'Inactive' : 'Active' ?>
          </td>
          <td>
            <form method="POST">
              <?= useCsrfInput(false); ?>
              <input type="hidden" name="user-id" value="<?= $user->id ?>">
              <input type="hidden" name="action" value="<?= $user->blocked_at ? 'activate' : 'deactivate' ?>">
              <button type="submit" class="btn btn-dark"><?= $user->blocked_at ? 'Activate' : 'Deactivate' ?></button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <div aria-label="Page navigation example" class="d-flex justify-content-center">
    <ul class="pagination">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="/admin/manage-users?page=1" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="/admin/manage-users?page=<?= $currentPage - 1 ?>" aria-label="Previous">
          <span aria-hidden="true">&lsaquo;</span>
        </a>
      </li>
      <li class="page-item"><span class="page-link"><?= $currentPage ?> / <?= $maxPage ?></span></li>
      <li class="page-item <?= $currentPage >= $maxPage ? 'disabled' : '' ?>">
        <a class="page-link" href="/admin/manage-users?page=<?= $currentPage + 1 ?>" aria-label="Next">
          <span aria-hidden="true">&rsaquo;</span>
        </a>
      </li>
      <li class="page-item <?= $currentPage >= $maxPage ? 'disabled' : '' ?>">
        <a class="page-link" href="/admin/manage-users?page=<?= $maxPage ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </div>
</div>
