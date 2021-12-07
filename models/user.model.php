<?php

class User
{
  public function __construct(
    public string $id,
    public string $name,
    public string $email,
    public string $password,
    public string $profile_picture,
    public int $balance,
    public bool $is_premium,
    public ?string $blocked_at,
  ) {
  }

  public static function fromStdClass(?stdClass $obj)
  {
    if (!$obj) return null;
    return new User(
      $obj->id,
      $obj->name,
      $obj->email,
      $obj->password,
      $obj->profile_picture,
      $obj->balance,
      $obj->is_premium,
      $obj->blocked_at
    );
  }
}
