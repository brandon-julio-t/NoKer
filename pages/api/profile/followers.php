<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;
[$followers, $totalCount] = UserRepository::getAllFollowersByUserPaginated(Auth::getUser(), $page);
echo json_encode([
  'data' => $followers,
  'totalCount' => $totalCount,
]);
