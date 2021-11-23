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
    $admin = self::checkAdminLogin($email, $password);
    if ($admin) {
      $_SESSION['user'] = $admin;
      return true;
    }

    $user = UserRepository::getOneByEmail($email);
    if (!$user) return false;

    $isMatch = Hash::compare($password, $user->password);
    if (!$isMatch) return false;

    $_SESSION['user'] = $user;
    return true;
  }

  private static function checkAdminLogin(string $email, string $password)
  {
    $adminEmail = 'admin@email.com';
    $adminPassword = 'admin';
    if ($email === $adminEmail && $password === $adminPassword) {
      return new User('00000000-0000-0000-0000-000000000000', 'admin', $adminEmail);
    }
    return null;
  }
}
