<?php

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
              <?= useCsrfInput(false) ?>
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
              <?= useCsrfInput(false) ?>
              <input type="hidden" name="action" value="upgrade-account">
              <button class="btn btn-primary">Upgrade to Premium (399)</button>
            </form>
          <?php } ?>
          <div class="card-text row row-cols-1 row-cols-sm-2 fw-bold">
            <div x-data="{ hover: false }" @mouseover="hover = true" @mouseover.away="hover = false" @click="show = 'followers'" class="d-flex flex-column justify-content-center rounded p-3" :class="{ 'bg-secondary': hover, 'text-white': hover }" style="cursor: pointer;">
              <span class="text-center">Followers</span>
              <span class="text-center"><?= count($followers) ?></span>
            </div>
            <div x-data="{ hover: false }" @mouseover="hover = true" @mouseover.away="hover = false" @click="show = 'followings'" class="d-flex flex-column justify-content-center rounded p-3" :class="{ 'bg-secondary': hover, 'text-white': hover }" style="cursor: pointer;">
              <span class="text-center">Followings</span>
              <span class="text-center"><?= count($followings) ?></span>
            </div>
          </div>
          <?php if ($isLoggedIn && !$isSelf && !$currentUserIsFollowing) { ?>
            <form action="" method="POST" class="d-grid mt-3">
              <?= useCsrfInput(false) ?>
              <input type="hidden" name="action" value="follow">
              <button class="btn btn-primary">Follow</button>
            </form>
          <?php } else if ($isLoggedIn && !$isSelf && $currentUserIsFollowing) { ?>
            <form action="" method="POST" class="d-grid mt-3">
              <?= useCsrfInput(false) ?>
              <input type="hidden" name="action" value="unfollow">
              <button class="btn btn-danger">Unfollow</button>
            </form>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-9" x-data="{
      currentUserId: '<?= $user->id ?>',
      type: 'approved',
      approvedBlogs: [],
      unapprovedBlogs: [],
      approvedBlogsPage: 1,
      unapprovedBlogsPage: 1,
      isLoading: false,
      approvedBlogsTotalLength: Infinity,
      unapprovedBlogsTotalLength: Infinity,
      async fetchBlogs() {
        if (this.isLoading) return;
        this.isLoading = true;

        if (this.type === 'approved') {
          const response = await (await fetch(`/api/profile/approved-blogs?page=${this.approvedBlogsPage}&userId=${this.currentUserId}`)).json();
          this.approvedBlogs = [...this.approvedBlogs, ...response.data];
          this.approvedBlogsTotalLength = response.totalCount;
          this.approvedBlogsPage++;
        } else if (this.type === 'unapproved') {
          const response = await (await fetch(`/api/profile/unapproved-blogs?page=${this.unapprovedBlogsPage}&userId=${this.currentUserId}`)).json();
          this.unapprovedBlogs = [...this.unapprovedBlogs, ...response.data];
          this.unapprovedBlogsTotalLength = response.totalCount;
          this.unapprovedBlogsPage++;
        }

        this.isLoading = false;
      },
      init() {
        this.fetchBlogs();
        this.isLoading = false;
        this.type = 'unapproved';
        this.fetchBlogs();
        this.type = 'approved';
      }
    }">
      <?php if ($isSelf) { ?>
        <div class="row mb-3">
          <div x-data @click="() => { type = 'approved'; }" class="col text-center border rounded p-3" :class="{ 'bg-primary': type === 'approved', 'text-white': type === 'approved' }">
            Approved
          </div>
          <div x-data @click="() => { type = 'unapproved'; }" class="col text-center border rounded p-3" :class="{ 'bg-primary': type === 'unapproved', 'text-white': type === 'unapproved' }">
            Unapproved
          </div>
        </div>
      <?php } ?>

      <div x-cloak x-transition x-show="type === 'approved'" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        <template x-for="blog in approvedBlogs" key="blog.id">
          <div class="col">
            <div x-data @click="location.href = `/blogs?id=${blog.id}`" class="card shadow-sm" style="cursor: pointer;">
              <img :src="blog.image_path" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
              <div class="card-body">
                <span x-show="blog.is_premium" class="badge bg-primary mb-3">Premium</span>
                <h5 class="card-title" x-text="blog.title"></h5>
                <p class="card-text" x-text="blog.content"></p>
                <small class="card-text">
                  <span x-text="blog.user.name"></span>
                  &bull;
                  <span x-text="new Date(blog.created_at).toDateString()"></span>
                </small>
              </div>
            </div>
          </div>
        </template>
      </div>
      <div x-cloak x-transition x-show="type === 'unapproved'" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        <template x-for="blog in unapprovedBlogs" key="blog.id">
          <div class="col">
            <div x-data @click="location.href = `/blogs?id=${blog.id}`" class="card shadow-sm" style="cursor: pointer;">
              <img :src="blog.image_path" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
              <div class="card-body">
                <span x-show="blog.is_premium" class="badge bg-primary mb-3">Premium</span>
                <h5 class="card-title" x-text="blog.title"></h5>
                <p class="card-text" x-text="blog.content"></p>
                <small class="card-text">
                  <span x-text="blog.user.name"></span>
                  &bull;
                  <span x-text="new Date(blog.created_at).toDateString()"></span>
                </small>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="d-flex justify-content-center">
        <svg x-cloak x-transition x-show="isLoading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
          <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
            <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
          </path>
        </svg>
      </div>

      <h3 x-cloak x-transition x-show="!isLoading && type === 'approved' && approvedBlogs.length === 0" class="text-center w-100 mt-3">
        This user has not written any blog.
      </h3>
      <h3 x-cloak x-transition x-show="!isLoading && type === 'unapproved' && unapprovedBlogs.length === 0" class="text-center w-100 mt-3">
        You have not written any blog yet.
      </h3>

      <div x-show="approvedBlogs.length < approvedBlogsTotalLength || unapprovedBlogs.length < unapprovedBlogsTotalLength" x-intersect="fetchBlogs()" style="height: 200px; width: 0;"></div>
    </div>
  </div>

  <div x-data="{
      currentUserId: '<?= $user->id ?>',
      followers: [],
      followings: [],
      followersPage: 1,
      followingsPage: 1,
      isLoading: false,
      followersTotalLength: Infinity,
      followingsTotalLength: Infinity,
      async fetch() {
        if (this.isLoading) return;
        this.isLoading = true;

        if (this.show === 'followers') {
          const response = await (await fetch(`/api/profile/followers?page=${this.followersPage}&userId=${this.currentUserId}`)).json();
          this.followers = [...this.followers, ...response.data];
          this.followersTotalLength = response.totalCount;
          this.followersPage++;
        } else if (this.show === 'followings') {
          const response = await (await fetch(`/api/profile/followings?page=${this.followingsPage}&userId=${this.currentUserId}`)).json();
          this.followings = [...this.followings, ...response.data];
          this.followingsTotalLength = response.totalCount;
          this.followingsPage++;
        }

        this.isLoading = false;
      },
      async init() {
        const [followers, followings] = await Promise.all([
          (await fetch(`/api/profile/followers?page=${this.followersPage}&userId=${this.currentUserId}`)).json(),
          (await fetch(`/api/profile/followings?page=${this.followingsPage}&userId=${this.currentUserId}`)).json()
        ]);

        this.followers = [...this.followers, ...followers.data];
        this.followersTotalLength = followers.totalCount;
        this.followersPage++;

        this.followings = [...this.followings, ...followings.data];
        this.followingsTotalLength = followings.totalCount;
        this.followingsPage++;
      }
  }">
    <div x-cloak x-show="show === 'followers'" @click.self="show = ''" style="z-index: 1030; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.25);">
      <div x-cloak x-transition x-show="show === 'followers'" class="card mx-auto mt-3 overflow-scroll" style="max-width: 640px; max-height: 95%;">
        <div class="card-body">
          <h3 class="card-title d-flex justify-content-between mb-3">
            <span>Followers</span>
            <span x-data @click="show = ''" style="cursor: pointer;">&times;</span>
          </h3>
          <div class="card-text d-grid gap-3">
            <template x-for="follower in followers">
              <div x-data @click="location.href = `/profile?id=${follower.id}`" class="card shadow-sm" style="cursor: pointer;">
                <div class="card-body">
                  <div class="card-text">
                    <img :src="follower.profile_picture" class="rounded-circle me-1" style="max-height: 32px; max-width: 32px;">
                    <span x-text="follower.name"></span>
                  </div>
                </div>
              </div>
            </template>

            <div class="d-flex justify-content-center">
              <svg x-cloak x-transition x-show="isLoading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
                  <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
                </path>
              </svg>
            </div>

            <h3 x-cloak x-transition x-show="!isLoading && followers.length === 0" class="text-center w-100 mt-3">
              This user has no followers.
            </h3>

            <div x-show="followers.length < followersTotalLength" x-intersect="fetch()" style="height: 200px; width: 0;"></div>
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
            <template x-for="follower in followings">
              <div x-data @click="location.href = `/profile?id=${follower.id}`" class="card shadow-sm" style="cursor: pointer;">
                <div class="card-body">
                  <div class="card-text">
                    <img :src="follower.profile_picture" class="rounded-circle me-1" style="max-height: 32px; max-width: 32px;">
                    <span x-text="follower.name"></span>
                  </div>
                </div>
              </div>
            </template>

            <div class="d-flex justify-content-center">
              <svg x-cloak x-transition x-show="isLoading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
                  <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
                </path>
              </svg>
            </div>

            <h3 x-cloak x-transition x-show="!isLoading && followings.length === 0" class="text-center w-100 mt-3">
              This user follows no one.
            </h3>

            <div x-show="followings.length < followingsTotalLength" x-intersect="fetch()" style="height: 200px; width: 0;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
