<?php

class UserFriend
{
  public function __construct(
    public string $friender_id,
    public string $friendee_id,
  ) {
  }

  public static function fromStdClass(stdClass $obj)
  {
    if (!$obj) return null;
    return new UserFriend(
      $obj->friender_id,
      $obj->friendee_id,
    );
  }
}
