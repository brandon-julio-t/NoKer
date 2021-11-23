<?php

useAuth();

$blogs = BlogRepository::getAll();

array_map(function (Blog $blog) {
  if (strlen($blog->content) > 100) {
    $blog->content = substr($blog->content, 0, 100) . '...';
  }
}, $blogs);

?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
  <?php foreach ($blogs as $blog) { ?>
    <div class="col">
      <a href="/blogs?id=<?= $blog->id ?>" class="text-reset text-decoration-none">
        <div class="card">
          <img src="<?= $blog->image_path ?>" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
          <div class="card-body">
            <h4 class="card-title"><?= $blog->title ?></h4>
            <p class="card-text"><?= $blog->content ?></p>
            <small class="card-text">
              <?= $blog->user->name ?> &bull; <?= usePrettyDate($blog->created_at) ?>
            </small>
          </div>
      </a>
    </div>
</div>
<?php } ?>
</div>
