<?php

$user = UserRepository::getOneById($_GET['id']);
$blogs = BlogRepository::getAllApprovedByUser($user);

?>

<div class="row">
  <div class="col">
    <div class="card sticky-top" style="top: 4.5rem;">
      <div class="card-body">
        <img src="<?= $user->profile_picture ?>" class="rounded-circle mx-auto w-100" style="height: 250px;">
        <h2 class="text-center"><?= $user->name ?></h2>
        <?php if ($user->id === Auth::getUser()->id) { ?>
          <div>Balance: <b><?= $user->balance ?></b></div>
        <?php } ?>
        <div>
          Account type:
          <span class="badge rounded-pill bg-<?= $user->is_premium ? 'primary' : 'secondary' ?>">
            <?= $user->is_premium ? 'Premium' : 'Regular' ?>
          </span>
        </div>
        <div></div>
      </div>
    </div>
  </div>
  <div class="col-9">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
      <?php foreach ($blogs as $blog) { ?>
        <div class="col">
          <a href="/blogs?id=<?= $blog->id ?>" class="text-reset text-decoration-none">
            <div class="card shadow-sm">
              <img src="<?= $blog->image_path ?>" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title"><?= $blog->title ?></h5>
                <p class="card-text"><?= $blog->truncatedContent() ?></p>
                <small class="card-text">
                  <span><?= $blog->user->name ?></span>
                  &bull;
                  <span><?= usePrettyDate($blog->created_at) ?></span>
                </small>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
