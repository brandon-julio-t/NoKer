<?php

function useProfilePicture(string $path)
{
  $url = $path;
  if (!$path || !Storage::exists($path)) {
    $url = 'https://i.pravatar.cc/300';
  }
  return '<img src="' . $url . '" alt="Profile picture" class="rounded-circle me-2" style="width: 2.5rem; height: 2.5rem;">';
}
