CREATE TABLE IF NOT EXISTS `tbl_user` (
`id_user` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `id_level` int(1) NOT NULL,
  `id_status` int(1) NOT NULL,
  `activate` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `creator` int(1) NOT NULL,
  `changed` datetime DEFAULT NULL,
  `changer` int(1) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gcm_id` varchar(255) DEFAULT NULL,
  `id_key` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `tbl_user`
 ADD PRIMARY KEY (`id_user`);
