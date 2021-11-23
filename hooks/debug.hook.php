<?php

function useDebug(mixed $value)
{
  echo '<pre>';
  var_export($value);
  echo '</pre>';
  die();
}
