<?php

function useGuest()
{
  $isLoggedIn = Auth::check();
  if ($isLoggedIn) {
    header('Location: /home');
  }
}
