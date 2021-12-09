<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$user = Auth::getUser();
$followings = UserRepository::getAllFollowingsByUser($user);

[$blogs, $totalCount] = BlogRepository::getAllApprovedByUsersPaginated($followings, $page);
array_map(fn (Blog $blog) => $blog->content = $blog->truncatedContent(), $blogs);
echo json_encode([
  'data' => $blogs,
  'totalCount' => $totalCount,
]);
