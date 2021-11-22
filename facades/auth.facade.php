<?php

class Auth
{
  public static function check()
  {
    $user = $_SESSION['user'];
    return $user instanceof User;
  }

  public static function getUser()
  {
    $user = $_SESSION['user'];
    return $user instanceof User ? $user : null;
  }

  public static function login(string $email, string $password)
  {
    $user = UserRepository::getOneByEmail($email);
    if (!$user) return false;

    $isMatch = Hash::compare($password, $user->password);
    if (!$isMatch) return false;

    $_SESSION['user'] = $user;
    return true;
  }
}
