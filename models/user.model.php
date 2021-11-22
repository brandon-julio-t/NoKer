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

  public static function fromStdClass(stdClass $obj)
  {
    return new User(
      $obj->id,
      $obj->name,
      $obj->email,
      $obj->password
    );
  }
}
