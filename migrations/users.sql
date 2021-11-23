create table users (
  `id` varchar(255) primary key,
  `name` varchar(255) not null,
  `email` varchar(255) not null,
  `password` varchar(255) not null,
  `role` varchar(255) not null,
  `blocked_at` datetime
);
