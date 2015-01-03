CREATE TABLE IF NOT EXISTS `tbl_project` (
`id_project` int(100) NOT NULL,
  `kode_project` varchar(200) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_platform` varchar(100) NOT NULL,
  `project_author` varchar(255) NOT NULL,
  `working_time` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_project`
--
ALTER TABLE `tbl_project`
 ADD PRIMARY KEY (`id_project`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_project`
--
ALTER TABLE `tbl_project`
MODIFY `id_project` int(100) NOT NULL AUTO_INCREMENT;

