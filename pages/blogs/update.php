<?php

useAuth();

$blog = BlogRepository::getOneById($_GET['id']);

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
      $blog->title = $_POST['title'];
      $blog->content = $_POST['content'];
      $blog->image_path = $path;
      $blog->is_premium = isset($_POST['is_premium']);

      $isUpdated = BlogRepository::update($blog);
      if ($isUpdated) {
        useFlashAlert('Blog updated successfully.', 'success');
      } else {
        useFlashAlert('An error occurred. Please try again.', 'danger');
      }
    }
  }
}

useFlashAlert();

?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Update Blog</h4>

    <form action="" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
      <?= useCsrfInput() ?>
      <input type="text" name="title" placeholder="Title" class="form-control" value="<?= $blog->title ?>">
      <textarea name="content" rows="10" class="form-control" placeholder="Content..."><?= $blog->content ?></textarea>
      <input type="file" name="image" placeholder="Image" class="form-control form-control-sm">
      <div>
        <label class="fw-bold">Previous image</label>
        <img src="<?= $blog->image_path ?>" class="img-thumbnail d-block mx-auto w-100" alt="Previous image">
      </div>
      <div class="form-check">
        <input type="checkbox" name="is_premium" class="form-check-input" id="is_premium" <?php if ($blog->is_premium) { ?> checked <?php } ?>>
        <label class="form-check-label" for="is_premium">
          Premium
        </label>
      </div>
      <button class="btn btn-primary" type="submit">Update</button>
    </form>
  </div>
</div>
