<?php

$blog = BlogRepository::getOneById($_GET['id']);

?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title"><?= $blog->title ?></h4>
    <small class="card-title">
      <?= useProfilePicture($blog->user->profile_picture) ?>
      <?= $blog->user->name ?>
      &bull;
      <?= usePrettyDate($blog->created_at); ?>
    </small>
  </div>
  <img src="<?= $blog->image_path ?>" alt="Blog image" class="w-full px-3">
  <div class="card-body">
    <div class="card-text"><?= $blog->content ?></div>
  </div>
</div>
