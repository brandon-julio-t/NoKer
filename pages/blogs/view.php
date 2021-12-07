<?php

$blog = BlogRepository::getOneById($_GET['id']);
$user = Auth::getUser();
$isCurrentUserTheCreator = $user ? $user->id === $blog->user->id : false;
$method = useHttpMethod();
if ($method === 'POST' && $_POST['_method'] === 'DELETE') {
  $isDeleted = BlogRepository::delete($blog);
  if ($isDeleted) {
    useFlashAlert('Blog deleted successfully.', 'success');
    header('Location: /home');
    return;
  } else {
    useFlashAlert('An error occurred. Please try again.', 'danger');
  }
}

useFlashAlert();

?>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h4 class="card-title"><?= htmlspecialchars($blog->title) ?></h4>
      <?php if ($isCurrentUserTheCreator) { ?>
        <div class="row gx-2">
          <div class="col">
            <a href="/blogs/update?id=<?= $blog->id ?>" class="btn btn-dark">Update</a>
          </div>
          <form method="POST" class="col">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      <?php } ?>
    </div>
    <a href="/profile?id=<?= $blog->user_id ?>" class="text-decoration-none text-reset">
      <small class="card-title">
        <img src="<?= $blog->user->profile_picture ?>" alt="Profile picture" class="rounded-circle me-2" style="width: 2.5rem; height: 2.5rem;">
        <?= htmlspecialchars($blog->user->name) ?>
        &bull;
        <?= usePrettyDate($blog->created_at); ?>
      </small>
    </a>
  </div>
  <img src="<?= $blog->image_path ?>" alt="Blog image" class="w-full px-3">
  <div class="card-body">
    <div class="card-text" style="white-space: pre-wrap;"><?= htmlspecialchars($blog->content) ?></div>
  </div>
</div>
