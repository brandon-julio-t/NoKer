<?php

function useAuth()
{
  $isLoggedIn = Auth::check();
  if (!$isLoggedIn) {
    header('Location: /auth/login');
  }
}
