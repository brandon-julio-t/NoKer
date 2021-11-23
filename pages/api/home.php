<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;
[$blogs,] = BlogRepository::getAllApprovedPaginated($page);

array_map(function (Blog $blog) {
  if (strlen($blog->content) > 100) {
    $blog->content = substr($blog->content, 0, 100) . '...';
  }
}, $blogs);

echo json_encode($blogs);
