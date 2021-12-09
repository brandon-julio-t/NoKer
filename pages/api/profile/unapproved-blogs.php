<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$user = UserRepository::getOneById($_GET['userId']);
[$blogs, $totalCount] = BlogRepository::getAllUnapprovedByUserPaginated($user, $page);
array_map(fn (Blog $blog) => $blog->content = $blog->truncatedContent(), $blogs);
echo json_encode([
  'data' => $blogs,
  'totalCount' => $totalCount,
]);
