<?php

function useCsrf($regenerate)
{
  if ($regenerate) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
  } else {
    return $_SESSION['csrf_token'];
  }
}

function useCheckCsrf()
{
  $isMatch = $_SESSION['csrf_token'] === $_POST['csrf-token'];
  if (!$isMatch) {
    http_response_code(403);
    die("403 Forbidden");
  }
}

function useCsrfInput($regenerate = true)
{
  $token = useCsrf($regenerate);
  return "<input type='hidden' name='csrf-token' value='$token'>";
}
