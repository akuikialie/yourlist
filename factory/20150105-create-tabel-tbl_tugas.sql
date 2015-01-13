CREATE TABLE IF NOT EXISTS `tbl_tugas` (
`id_tugas` int(255) NOT NULL,
  `kode_project` varchar(200) NOT NULL,
  `module` varchar(255) NOT NULL,
  `time_do` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `working_date_start` datetime NOT NULL,
  `working_date_end` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_tugas`
--
ALTER TABLE `tbl_tugas`
 ADD PRIMARY KEY (`id_tugas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_tugas`
--
ALTER TABLE `tbl_tugas`
MODIFY `id_tugas` int(255) NOT NULL AUTO_INCREMENT;

