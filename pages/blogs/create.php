<?php

$message = null;
$error = null;
$method = useHttpMethod();
if ($method === 'POST') {
  useCheckCsrf();

  [$ok, $path] = Storage::save($_FILES['image']);
  if (!$ok) {
    $error = 'Unable to upload image.';
  } else {
    $blog = new Blog(
      useUuid(),
      $_POST['title'],
      $_POST['content'],
      $path,
      'unapproved',
      Auth::getUser()->id,
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

useFlashAlert();

?>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Create Blog</h4>

    <form action="" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
      <?= useCsrfInput() ?>
      <input type="text" name="title" placeholder="Title" class="form-control">
      <textarea name="content" rows="10" class="form-control" placeholder="Content..."></textarea>
      <input type="file" name="image" placeholder="Image" class="form-control form-control-sm">
      <div class="d-grid">
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>
