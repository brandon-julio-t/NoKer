<?php

useAuth();

$message = null;
$error = null;
$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  $image = $_FILES['image'];
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
      $blog = new Blog(
        useUuid(),
        $_POST['title'],
        $_POST['content'],
        $path,
        'unapproved',
        Auth::getUser()->id,
        isset($_POST['is_premium']),
        useNow()
      );
      $isCreated = BlogRepository::create($blog);
      if (!$isCreated) {
        useFlashAlert('An error occurred. Please try again.', 'danger');
      } else {
        useFlashAlert('Blog created successfully.', 'success');
      }
    }
  }
}

useFlashAlert();

?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Create Blog</h4>

    <form action="" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
      <?= useCsrfInput(false) ?>
      <input type="text" name="title" placeholder="Title" class="form-control" required>
      <textarea name="content" rows="10" class="form-control" placeholder="Content..." required></textarea>
      <input type="file" name="image" placeholder="Image" class="form-control form-control-sm" required>
      <div class="form-check">
        <input type="checkbox" name="is_premium" class="form-check-input" id="is_premium">
        <label class="form-check-label" for="is_premium">
          Premium
        </label>
      </div>
      <div class="d-grid">
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
    </form>
  </div>
</div>
