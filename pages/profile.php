<?php

useAuth();

$user = UserRepository::getOneById($_GET['id']);

if (useHttpMethod() === 'POST') {
  useCheckCsrf();

  $action = $_POST['action'];
  $frienderId = Auth::getUser()->id;
  $friendeeId = $user->id;
  $model = new UserFriend($frienderId, $friendeeId, useNow());

  if ($action === 'follow') {
    $isSuccess = UserFriendRepository::create($model);
    useFlashAlert(
      $isSuccess
        ? "Success $action <b>{$user->name}</b>"
        : "An error occurred while $action <b>{$user->name}</b>",
      $isSuccess ? 'success' : 'danger'
    );
  } else if ($action === 'unfollow') {
    $isSuccess = UserFriendRepository::delete($model);
    useFlashAlert(
      $isSuccess
        ? "Success $action <b>{$user->name}</b>"
        : "An error occurred while $action <b>{$user->name}</b>",
      $isSuccess ? 'success' : 'danger'
    );
  } else if ($action === 'change-profile-picture') {
    $image = $_FILES['profile-picture'];
    $isImage = useMustImage($image);
    if (!$isImage) {
      useFlashAlert('Please upload an image.', 'danger');
    } else if ($image['size'] > 1000000) {
      useFlashAlert('Image size must not exceed 1MB.', 'danger');
    } else {
      [$ok, $path] = Storage::save($image);
      if (!$ok) {
        useFlashAlert('Unable to upload image.', 'danger');
      } else {
        $user->profile_picture = $path;
        $isSuccess = UserRepository::update($user);
        useFlashAlert(
          $isSuccess ? 'Profile picture updated successfully.' : 'An error occurred while updating profile picture.',
          $isSuccess ? 'success' : 'danger',
        );
      }
    }
  } else if ($action === 'upgrade-account') {
    if ($user->balance - 399 < 0) {
      useFlashAlert('Insufficient balance.', 'danger');
    } else {
      $user->balance -= 399;
      $user->is_premium = true;
      $isSuccess = UserRepository::update($user);
      useFlashAlert(
        $isSuccess ? 'Account upgraded successfully.' : 'An error occurred while upgrading account.',
        $isSuccess ? 'success' : 'danger',
      );
    }
  }
}

$blogs = BlogRepository::getAllApprovedByUser($user);
$followers = UserRepository::getAllFollowersByUser($user);
$followings = UserRepository::getAllFollowingsByUser($user);

$isLoggedIn = Auth::check();
$isSelf = $isLoggedIn && $user->id === Auth::getUser()->id;
$currentUserIsFollowing = count(array_filter($followers, fn (User $follower) => $isLoggedIn && $follower->id == Auth::getUser()->id)) > 0;

useFlashAlert();

?>

<div x-data="{ show: '' }">
  <div class="row">
    <div class="col">
      <div class="card shadow-sm sticky-top mb-3" style="top: 4.5rem;">
        <div class="card-body">
          <?php if ($isSelf) { ?>
            <form id="change-profile-picture-form" action="" method="POST" class="d-none" enctype="multipart/form-data">
              <?= useCsrfInput() ?>
              <input type="hidden" name="action" value="change-profile-picture">
              <input id="profile-picture-input" type="file" name="profile-picture" @change="document.querySelector('#change-profile-picture-form').submit()">
            </form>

            <img x-data @click="document.querySelector('#profile-picture-input').click()" src="<?= $user->profile_picture ?>" class="rounded-circle mx-auto w-100 mb-3" style="height: 250px; object-fit: cover; cursor: pointer;">
          <?php } else { ?>
            <img src="<?= $user->profile_picture ?>" class="rounded-circle mx-auto w-100 mb-3" style="height: 250px; object-fit: cover;">
          <?php } ?>

          <h2 class="card-title text-center mb-3"><?= htmlspecialchars($user->name) ?></h2>
          <?php if ($isSelf) { ?>
            <div class="card-text mb-1">Balance: <b><?= $user->balance ?></b></div>
          <?php } ?>
          <div class="card-text mb-3">
            Account type:
            <span class="badge rounded-pill bg-<?= $user->is_premium ? 'primary' : 'secondary' ?>">
              <?= $user->is_premium ? 'Premium' : 'Regular' ?>
            </span>
          </div>
          <?php if ($isSelf && !$user->is_premium) { ?>
            <form action="" method="POST" class="d-grid mb-3">
              <?= useCsrfInput() ?>
              <input type="hidden" name="action" value="upgrade-account">
              <button class="btn btn-primary">Upgrade to Premium (399)</button>
            </form>
          <?php } ?>
          <div class="card-text row row-cols-1 row-cols-sm-2 fw-bold">
            <div x-data @click="show = 'followers'" class="d-flex flex-column justify-content-center" style="cursor: pointer;">
              <span class="text-center">Followers</span>
              <span class="text-center"><?= count($followers) ?></span>
            </div>
            <div x-data @click="show = 'followings'" class="d-flex flex-column justify-content-center" style="cursor: pointer;">
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
          <?php } else if ($isLoggedIn && !$isSelf && $currentUserIsFollowing) { ?>
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
                  <?php if ($blog->is_premium) { ?>
                    <span class="badge bg-primary mb-3">Premium</span>
                  <?php } ?>
                  <h5 class="card-title"><?= htmlspecialchars($blog->title) ?></h5>
                  <p class="card-text"><?= htmlspecialchars($blog->truncatedContent()) ?></p>
                  <small class="card-text">
                    <span><?= htmlspecialchars($blog->user->name) ?></span>
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

  <div x-cloak x-show="show === 'followers'" @click.self="show = ''" style="z-index: 1030; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.25);">
    <div x-cloak x-transition x-show="show === 'followers'" class="card mx-auto mt-3 overflow-scroll" style="max-width: 640px; max-height: 95%;">
      <div class="card-body">
        <h3 class="card-title d-flex justify-content-between mb-3">
          <span>Followers</span>
          <span x-data @click="show = ''" style="cursor: pointer;">&times;</span>
        </h3>
        <div class="card-text d-grid gap-3">
          <?php foreach ($followers as $follower) { ?>
            <div x-data @click="location.href = '/profile?id=<?= $follower->id ?>'" class="card shadow-sm" style="cursor: pointer;">
              <div class="card-body">
                <div class="card-text">
                  <img src="<?= $follower->profile_picture ?>" class="rounded-circle me-1" style="max-height: 32px; max-width: 32px;">
                  <?= htmlspecialchars($follower->name) ?>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div x-cloak x-show="show === 'followings'" @click.self="show = ''" style="z-index: 1030; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.25);">
    <div x-cloak x-transition x-show="show === 'followings'" class="card mx-auto mt-3 overflow-scroll" style="max-width: 640px; max-height: 95%;">
      <div class="card-body">
        <h3 class="card-title d-flex justify-content-between mb-3">
          <span>Followings</span>
          <span x-data @click="show = ''" style="cursor: pointer;">&times;</span>
        </h3>
        <div class="card-text d-grid gap-3">
          <?php foreach ($followings as $following) { ?>
            <div x-data @click="location.href = '/profile?id=<?= $following->id ?>'" class="card shadow-sm" style="cursor: pointer;">
              <div class="card-body">
                <div class="card-text">
                  <img src="<?= $follower->profile_picture ?>" class="rounded-circle me-1" style="max-height: 32px; max-width: 32px;">
                  <?= htmlspecialchars($follower->name) ?>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
