CREATE TABLE `keys` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `key` varchar(40) NOT NULL,
      `level` int(2) NOT NULL,
      `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
      `date_created` int(11) NOT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

