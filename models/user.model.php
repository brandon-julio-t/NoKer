<?php

class User
{
  public function __construct(
    public string $id = '00000000-0000-0000-0000-000000000000',
    public string $name = '',
    public string $email = '',
    public string $password = '',
    public string $profile_picture = '',
    public ?string $blocked_at = null,
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
      $obj->blocked_at
    );
  }
}
