<?php

$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  $action = $_POST['action'];
  $blogId = $_POST['blog-id'];

  $blog = BlogRepository::getOneById($blogId);
  $blog->status = $action . 'd';
  $isUpdated = BlogRepository::update($blog);
  if ($isUpdated) {
    useFlashAlert('Blog <b>' . $blog->title . '</b> ' . $action . 'd successfully.', 'success');
  } else {
    useFlashAlert('An error occurred. Please try again.', 'danger');
  }
}

$unapprovedBlogs = BlogRepository::getAllUnapproved();
$approvedBlogs = BlogRepository::getAllApproved();

useFlashAlert();

?>

<div class="row row-cols-1-row-cols-md-2">
  <div class="col">
    <h2>Unapproved Blogs</h2>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Creator</th>
            <th scope="col">Created At</th>
            <th scope="col" colspan="2">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($unapprovedBlogs as $idx => $blog) { ?>
            <tr>
              <td class="align-middle"><?= $idx + 1 ?></td>
              <td class="align-middle"><?= $blog->title ?></td>
              <td class="align-middle"><?= $blog->user->name ?></td>
              <td class="align-middle"><?= $blog->created_at ?></td>
              <td class="align-middle text-capitalize <?= $blog->status === 'unapproved' ? 'text-danger' : 'text-success' ?>">
                <?= $blog->status ?>
              </td>
              <td>
                <form method="POST">
                  <?= useCsrfInput(false); ?>
                  <input type="hidden" name="blog-id" value="<?= $blog->id ?>">
                  <input type="hidden" name="action" value="<?= $blog->status === 'unapproved' ? 'approve' : 'unapprove' ?>">
                  <button type="submit" class="btn btn-dark"><?= $blog->status === 'unapproved' ? 'Approve' : 'Unapprove' ?></button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col">
    <h2>Approved Blogs</h2>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Creator</th>
            <th scope="col">Created At</th>
            <th scope="col" colspan="2">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($approvedBlogs as $idx => $blog) { ?>
            <tr>
              <td class="align-middle"><?= $idx + 1 ?></td>
              <td class="align-middle"><?= $blog->title ?></td>
              <td class="align-middle"><?= $blog->user->name ?></td>
              <td class="align-middle"><?= $blog->created_at ?></td>
              <td class="align-middle text-capitalize <?= $blog->status === 'unapproved' ? 'text-danger' : 'text-success' ?>">
                <?= $blog->status ?>
              </td>
              <td>
                <form method="POST">
                  <?= useCsrfInput(false); ?>
                  <input type="hidden" name="blog-id" value="<?= $blog->id ?>">
                  <input type="hidden" name="action" value="<?= $blog->status === 'unapproved' ? 'approve' : 'unapprove' ?>">
                  <button type="submit" class="btn btn-dark"><?= $blog->status === 'unapproved' ? 'Approve' : 'Unapprove' ?></button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>