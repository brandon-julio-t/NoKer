<?php

function useMustImage(mixed $file)
{
  $mime = mime_content_type($file['tmp_name']);
  return str_contains($mime, 'image');
}
