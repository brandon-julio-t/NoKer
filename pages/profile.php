<?php

$user = UserRepository::getOneById($_GET['id']);

if (useHttpMethod() === 'POST') {
  useCheckCsrf();

  $action = $_POST['action'];
  $frienderId = Auth::getUser()->id;
  $friendeeId = $user->id;
  $model = new UserFriend($frienderId, $friendeeId, useNow());

  $isSuccess = false;
  if ($action === 'follow') {
    $isSuccess = UserFriendRepository::create($model);
  } else if ($action === 'unfollow') {
    $isSuccess = UserFriendRepository::delete($model);
  }

  useFlashAlert(
    $isSuccess
      ? "Success $action <b>{$user->name}</b>"
      : "An error occurred while $action <b>{$user->name}</b>",
    $isSuccess ? 'success' : 'danger'
  );
}

$blogs = BlogRepository::getAllApprovedByUser($user);
$followers = UserRepository::getAllFollowersByUser($user);
$followings = UserRepository::getAllFollowingsByUser($user);
$isLoggedIn = Auth::check();
$isSelf = $isLoggedIn && $user->id === Auth::getUser()->id;

$currentUserIsFollowing = count(array_filter($followers, fn (User $follower) => $isLoggedIn && $follower->id == Auth::getUser()->id)) > 0;

?>

<div class="row">
  <div class="col">
    <div class="card sticky-top" style="top: 4.5rem;">
      <div class="card-body">
        <img src="<?= $user->profile_picture ?>" class="rounded-circle mx-auto w-100 mb-3" style="height: 250px;">
        <h2 class="card-title text-center mb-3"><?= $user->name ?></h2>
        <?php if ($isSelf) { ?>
          <div class="card-text mb-1">Balance: <b><?= $user->balance ?></b></div>
        <?php } ?>
        <div class="card-text mb-3">
          Account type:
          <span class="badge rounded-pill bg-<?= $user->is_premium ? 'primary' : 'secondary' ?>">
            <?= $user->is_premium ? 'Premium' : 'Regular' ?>
          </span>
        </div>
        <div class="card-text row row-cols-1 row-cols-sm-2 fw-bold">
          <div class="d-flex flex-column justify-content-center" style="cursor: pointer;">
            <span class="text-center">Followers</span>
            <span class="text-center"><?= count($followers) ?></span>
          </div>
          <div class="d-flex flex-column justify-content-center" style="cursor: pointer;">
            <span class="text-center">Followings</span>
            <span class="text-center"><?= count($followings) ?></span>
          </div>
        </div>
        <?php if ($isLoggedIn && !$isSelf && !$currentUserIsFollowing) { ?>
          <form action="" method="POST" class="d-grid mt-3">
            <?= useCsrfInput() ?>
            <input type="hidden" name="action" value="follow">
            <button class="btn btn-primary">Follow</button>
          </form>
        <?php } ?>
        <?php if ($isLoggedIn && !$isSelf && $currentUserIsFollowing) { ?>
          <form action="" method="POST" class="d-grid mt-3">
            <?= useCsrfInput() ?>
            <input type="hidden" name="action" value="unfollow">
            <button class="btn btn-danger">Unfollow</button>
          </form>
        <?php } ?>
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
