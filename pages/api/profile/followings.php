<?php

$page = isset($_GET['page']) ? $_GET['page'] : 1;
[$followings, $totalCount] = UserRepository::getAllFollowingsByUserPaginated(Auth::getUser(), $page);
echo json_encode([
  'data' => $followings,
  'totalCount' => $totalCount,
]);
