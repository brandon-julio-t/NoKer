create table blogs (
  `id` varchar(255) primary key,
  `title` varchar(255) not null,
  `content` text not null,
  `image_path` varchar(255) not null,
  `status` varchar(255) not null,
  `user_id` varchar(255) not null,
  `created_at` datetime not null,
  foreign key `fk_user` (`user_id`) references `users` (`id`)
)
