<?php

function useFlashAlert(string $message = '', string $type = '')
{
  if ($message) {
    $_SESSION['flash'] = compact('message', 'type');
    return;
  }

  $flash = null;
  if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
  }

  if ($flash) {
    echo '<div class="alert alert-' . $flash['type'] . '" role="alert">'
      . $flash['message']
      . '</div>';
  }
}
