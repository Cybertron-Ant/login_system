CREATE TABLE `users` (
   `id` int(11) not null auto_increment,
   `username` varchar(50) not null,
   `password` varchar(255) not null,
   `created_at` datetime default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2;

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES ('1', 'Antonio_Fuller', '$2y$10$zzrWfEx3u6Lttrq0xVuzCuUziiUREGnDdrMsG/9cw6ONYYgRvYSEW', '2023-08-21 15:01:11');