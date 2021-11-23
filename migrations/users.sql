create table users (
  `id` varchar(255) primary key,
  `name` varchar(255) not null,
  `email` varchar(255) not null unique,
  `password` varchar(255) not null,
  `profile_picture` varchar(255) not null,
  `blocked_at` datetime
);
