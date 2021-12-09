<?php

$users = [];

$dummies = [
  new User(
    useUuid(),
    "Admin",
    "admin@email.com",
    Hash::make('admin'),
    'https://i.pravatar.cc/300',
    999999,
    true,
    null
  ),
  new User(
    useUuid(),
    "User",
    "user@email.com",
    Hash::make('user'),
    'https://i.pravatar.cc/300',
    999999,
    false,
    null
  ),
];

foreach ($dummies as $dummy) {
  UserRepository::create($dummy);
  $users[] = $dummy;
  echo "seeding {$dummy->name}<br>";
}

for ($i = 0; $i < 100; $i++) {
  $user = new User(
    useUuid(),
    "User #$i",
    "user$i@email.com",
    Hash::make('password'),
    'https://i.pravatar.cc/300',
    0,
    rand(0, 1),
    null
  );
  $users[] = $user;
  UserRepository::create($user);
  echo "seeding {$user->name}<br>";
}

$blogs = [];

for ($i = 0; $i < 100; $i++) {
  $blog = new Blog(
    useUuid(),
    "Lorem Ipsum $i",
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ac aliquet metus. Donec convallis sollicitudin turpis, ut posuere turpis eleifend facilisis. Sed maximus massa dui, ut varius nibh eleifend vel. Quisque lacinia condimentum tempor. Nulla venenatis iaculis massa in semper. Nullam dignissim quam vitae risus egestas dictum. Curabitur nec tempus velit, eget posuere libero. Aliquam iaculis porta eleifend. Curabitur maximus sapien sed tellus pulvinar vehicula. Morbi rutrum tempor orci, ut pharetra libero mattis ut. Morbi leo velit, hendrerit et finibus et, faucibus id justo. Nam laoreet, ipsum in sollicitudin fermentum, tellus ligula sodales ipsum, vel dignissim turpis ex et magna. Mauris placerat diam et nunc consequat gravida. Nulla porta libero justo, a molestie lectus molestie vitae. Sed ex nibh, tristique et maximus non, blandit a arcu.
Ut ut tincidunt ante. Phasellus ultrices nec diam eget tristique. Donec finibus dui eget felis iaculis, et dictum mauris sollicitudin. Nullam efficitur, arcu vel porta vestibulum, nibh erat gravida purus, et interdum leo erat non quam. Cras nulla orci, ultricies et lacus eget, sodales fringilla purus. Mauris pulvinar, odio efficitur bibendum faucibus, libero lectus mattis leo, sit amet tincidunt augue ante sed nunc. Integer malesuada, eros sollicitudin condimentum volutpat, nulla lorem fringilla elit, ac volutpat nunc massa a magna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Mauris porta a erat in sodales. Ut sodales mauris eget tortor condimentum ultrices. Donec placerat lorem in ipsum accumsan pharetra. Nam cursus efficitur gravida. Cras posuere ante vel nisi fermentum feugiat. In iaculis sem et sapien molestie tristique.
Aenean porta magna non eros consectetur consequat. Vestibulum vel ante eros. Sed sit amet mi sodales, euismod nisi id, luctus lectus. Mauris lacus nunc, ultrices a rhoncus sed, commodo eu mi. Cras porta sem vel dolor dignissim, sit amet tincidunt erat eleifend. Aliquam molestie risus sapien, id interdum ipsum rhoncus at. Ut vitae mi et lorem ornare convallis. Quisque vestibulum est in urna dignissim, non placerat lacus consequat. Nulla odio leo, sollicitudin quis nunc congue, vehicula rhoncus eros. Morbi sit amet mi id eros sodales ultricies vitae a quam. Ut suscipit mollis urna, eget fringilla purus maximus quis. Aenean porttitor, ex ut luctus vestibulum, purus ipsum rhoncus nibh, non lobortis eros mi vitae diam. Morbi quam lectus, varius eget nunc vel, posuere efficitur mi. Aenean vehicula felis lacinia, tincidunt ligula eget, consequat justo. Fusce quis risus nibh. Fusce nec congue sem.
Mauris id ligula ut nisl ornare elementum. Pellentesque condimentum efficitur lorem eget feugiat. Sed nec nisl et ipsum finibus egestas vel sit amet elit. Pellentesque lobortis diam sed laoreet pulvinar. Etiam vitae lacus ut tortor vehicula tempus. Pellentesque dignissim, enim vel hendrerit imperdiet, tortor quam fermentum purus, pulvinar sollicitudin odio lectus id magna. Cras in ante felis. Cras interdum elit quis scelerisque convallis. Donec vel enim iaculis, dapibus nisl vitae, egestas libero. In tristique scelerisque dui.
Interdum et malesuada fames ac ante ipsum primis in faucibus. Fusce blandit vulputate ornare. Curabitur dui leo, sollicitudin et purus ut, cursus lobortis arcu. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sagittis sem vitae sem dapibus sodales. Fusce in justo eget libero laoreet fermentum. Aliquam erat volutpat.',
    'https://picsum.photos/200',
    rand(0, 1) ? 'approved' : 'unapproved',
    $users[array_rand($users)]->id,
    rand(0, 1),
    useNow(),
  );
  $blogs[] = $blog;
  BlogRepository::create($blog);
  echo "seeding {$blog->title}<br>";
}

foreach ($users as $friender) {
  foreach ($users as $friendee) {
    if (rand(0, 1)) {
      UserFriendRepository::create(
        new UserFriend(
          $friender->id,
          $friendee->id
        )
      );
      echo "seeding friendship {$friender->name} => {$friendee->name}<br>";
    }
  }
}

foreach ($users as $user) {
  foreach ($blogs as $blog) {
    if (rand(0, 1) && $blog->status === 'approved') {
      BookmarkRepository::create(
        new Bookmark(
          $blog->id,
          $user->id,
          useNow(),
        )
      );
      echo "seeding bookmark {$user->name} => {$blog->title}<br>";
    }
  }
}

echo 'seeding success';
