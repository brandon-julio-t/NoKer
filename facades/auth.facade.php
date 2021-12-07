<?php

class Auth
{
  public static function check()
  {
    if (!isset($_SESSION['user'])) return false;
    $user = $_SESSION['user'];
    return $user instanceof User;
  }

  public static function getUser()
  {
    if (!isset($_SESSION['user'])) return null;
    $user = $_SESSION['user'];
    return $user instanceof User ? $user : null;
  }

  public static function login(string $email, string $password)
  {
    $user = UserRepository::getOneByEmail($email);
    if (!$user) return false;
    if ($user->blocked_at) return false;

    $isMatch = Hash::compare($password, $user->password);
    if (!$isMatch) return false;

    $_SESSION['user'] = $user;
    return true;
  }
}
