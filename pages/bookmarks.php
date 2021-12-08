<?php

useAuth();

$bookmarks = BookmarkRepository::getAllByUser(Auth::getUser());

if (useHttpMethod() === 'POST') {
  useCheckCsrf();
  $bookmark = array_filter($bookmarks, fn ($bookmark) => $bookmark->blog->id === $_POST['blog_id'])[0];
  $isSuccess = BookmarkRepository::delete($bookmark);
  if ($isSuccess) $bookmarks = array_filter($bookmarks, fn ($bookmark) => $bookmark->blog->id !== $_POST['blog_id']);
  useFlashAlert(
    $isSuccess ? 'Bookmark removed.' : 'An error occurred while removing bookmark.',
    $isSuccess ? 'success' : 'danger',
  );
}

useFlashAlert();

?>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
  <?php foreach ($bookmarks as $bookmark) { ?>
    <div class="col">
      <div x-data @click="location.href = '/blogs?id=<?= $bookmark->blog->id ?>'" class="card shadow-sm" style="cursor: pointer;">
        <img src="<?= $bookmark->blog->image_path ?>" class="card-img-top" style="max-height: 150px; object-fit: cover;">
        <div class="card-body">
          <?php if ($bookmark->blog->is_premium) { ?>
            <span class="badge bg-primary mb-3">Premium</span>
          <?php } ?>
          <h5 class="card-title"><?= htmlspecialchars($bookmark->blog->title) ?></h5>
          <p class="card-text"><?= htmlspecialchars($bookmark->blog->truncatedContent()) ?></p>
          <small class="card-text">
            <span><?= htmlspecialchars($bookmark->blog->user->name) ?></span>
            &bull;
            <span><?= usePrettyDate($bookmark->blog->created_at) ?></span>
          </small>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
