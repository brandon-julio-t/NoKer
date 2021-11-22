<?php

class Hash
{
  public static function compare(string $text, string $hash)
  {
    return password_verify($text, $hash);
  }

  public static function make(string $text)
  {
    return password_hash($text, PASSWORD_DEFAULT);
  }
}
