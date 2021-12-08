<?php

$blog = BlogRepository::getOneById($_GET['id']);
$isLoggedIn = Auth::check();
$user = Auth::getUser();
$bookmarks = $isLoggedIn ? BookmarkRepository::getAllByUser($user)  : [];

// useDebug(array_filter(
//   $bookmarks,
//   fn ($bookmark) => $bookmark->blog->id === $blog->id
// ));

$isCurrentUserTheCreator = $user ? $user->id === $blog->user->id : false;
$isBookmarked = count(
  array_filter(
    $bookmarks,
    fn ($bookmark) => $bookmark->blog->id === $blog->id
  )
) > 0;

$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  if (isset($_POST['method']) && $_POST['_method'] === 'DELETE') {
    $isDeleted = BlogRepository::delete($blog);
    if ($isDeleted) {
      useFlashAlert('Blog deleted successfully.', 'success');
      header('Location: /home');
      return;
    } else {
      useFlashAlert('An error occurred. Please try again.', 'danger');
    }
  } else {
    $action = $_POST['action'];

    if ($action === 'bookmark') {
      $isSuccess = BookmarkRepository::create(
        new Bookmark(
          $blog->id,
          $user->id,
          useNow(),
        )
      );
      useFlashAlert(
        $isSuccess ? 'Blog bookmarked successfully.' : 'An error occurred while bookmarking the blog.',
        $isSuccess ? 'success' : 'danger',
      );
      $isBookmarked = true;
    } else if ($action === 'unbookmark') {
      $currentBookmark = array_filter($bookmarks, fn ($bookmark) => $bookmark->blog->id = $blog->id)[0];
      $isSuccess = BookmarkRepository::delete($currentBookmark);
      useFlashAlert(
        $isSuccess ? 'Blog unbookmarked successfully.' : 'An error occurred while unbookmarking the blog.',
        $isSuccess ? 'success' : 'danger',
      );
      $isBookmarked = false;
    }
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
            <?= useCsrfInput(false) ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      <?php } ?>
      <form x-data @click="$el.submit()" action="" method="POST" style="cursor: pointer;">
        <?= useCsrfInput(false) ?>
        <?php if ($isLoggedIn && $isBookmarked) { ?>
          <input type="hidden" name="action" value="unbookmark">
          <i class="bi bi-bookmark-fill"></i>
        <?php } else if ($isLoggedIn && !$isBookmarked) { ?>
          <input type="hidden" name="action" value="bookmark">
          <i class="bi bi-bookmark"></i>
        <?php } ?>
      </form>
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
