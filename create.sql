use `noker`;

drop table if exists `user_blog_activities`;
drop table if exists `user_friends`;
drop table if exists `bookmarks`;
drop table if exists `blogs`;
drop table if exists `users`;

create table `users` (
  `id` varchar(255) primary key,
  `name` varchar(255) not null,
  `email` varchar(255) not null unique,
  `password` varchar(255) not null,
  `profile_picture` varchar(255) not null,
  `balance` int not null,
  `is_premium` boolean not null,
  `blocked_at` datetime
);

create table `blogs` (
  `id` varchar(255) primary key,
  `title` varchar(255) not null,
  `content` text not null,
  `image_path` varchar(255) not null,
  `status` varchar(255) not null,
  `category` varchar(255) not null,
  `user_id` varchar(255) not null,
  `created_at` datetime not null,
  `is_premium` boolean not null,
  foreign key `blogs_fk_user` (`user_id`) references `users` (`id`) on update cascade on delete cascade
);

create table `bookmarks` (
  `blog_id` varchar(255) not null,
  `user_id` varchar(255) not null,
  `created_at` datetime not null,
  primary key (`blog_id`, `user_id`)
);

create table `user_friends` (
  `friender_id` varchar(255) not null,
  `friendee_id` varchar(255) not null,
  primary key (`friender_id`, `friendee_id`)
);

create table `user_blog_activities` (
  `id` varchar(255) primary key,
  `description` varchar(255) not null,
  `user_id` varchar(255) not null,
  `blog_id` varchar(255) not null,
  `created_at` datetime not null,
  foreign key `user_blog_activities_fk_user` (`user_id`) references `users` (`id`) on update cascade on delete cascade,
  foreign key `user_blog_activities_fk_blog` (`blog_id`) references `blogs` (`id`) on update cascade on delete cascade
);
