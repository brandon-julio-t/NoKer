<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$q = isset($_GET['q']) ? $_GET['q'] : '';
[$blogs,] = BlogRepository::getAllApprovedPaginatedByKeyword($page, $q);
array_map(fn(Blog $blog) =>  $blog->content = $blog->truncatedContent(), $blogs);

echo json_encode($blogs);
