CREATE TABLE `users` (
  `id` varchar(36) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `token` text NOT NULL,
  `role` varchar(6) DEFAULT 'hunter',
  `active` char(1) DEFAULT 'N',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `bad_attempt` smallint(1) unsigned DEFAULT 0,
  `last_failed` datetime DEFAULT NULL,
  UNIQUE KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `templates` (
  `id` varchar(36) NOT NULL,
  `creator_id` varchar(36) NOT NULL,
  `title` varchar(200) NOT NULL,
  `severity` float(8,5) NOT NULL,
  `endpoint` text NOT NULL,
  `stepsToReproduce` longtext NOT NULL,
  `impact` longtext NOT NULL,
  `mitigation` longtext NOT NULL,
  `resources` longtext NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `reports` (
  `id` varchar(36) NOT NULL,
  `title` varchar(200) NOT NULL,
  `severity` float(8,5) NOT NULL,
  `endpoint` text NOT NULL,
  `template_id` varchar(36) NOT NULL,
  `program_id` varchar(36) NOT NULL,
  `stepsToReproduce` longtext NOT NULL,
  `impact` longtext NOT NULL,
  `mitigation` longtext NOT NULL,
  `resources` longtext NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `programs` (
  `id` varchar(36) NOT NULL,
  `scope` text NOT NULL,
  `status` varchar(5) DEFAULT 'open',
  `platform_id` varchar(36) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `platforms` (
  `id` varchar(36) NOT NULL,
  `name` varchar(200) NOT NULL,
  UNIQUE KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;