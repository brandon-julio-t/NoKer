<?php

class User
{
  public function __construct(
    public string $id,
    public string $name,
    public string $email,
    public string $password
  ) {
  }

  public static function fromStdClass(?stdClass $obj)
  {
    if (!$obj) return null;
    return new User(
      $obj->id,
      $obj->name,
      $obj->email,
      $obj->password
    );
  }
}
