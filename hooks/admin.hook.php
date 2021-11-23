<?php

function useAdmin()
{
  useAuth();
  $user = Auth::getUser();
  $isLoggedIn = !!$user;
  $isAdmin = $user && $user->email === 'admin@email.com';
  if (!$isLoggedIn || !$isAdmin) {
    header('Location: /auth/login');
  }
}
