-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 03:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `natconinv`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `excel_filename` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `company_address`, `excel_filename`, `created_at`) VALUES
(54, 'DOLE 9', 'EVANGELISTA STREET, STA. CATALINA, ZAMBOANGA CITY', 'DOLE9_natcon_registration.xlsx', '2025-08-04 01:33:32'),
(55, 'PSME', 'Quezon City', 'PSME_natcon_registration.xlsx', '2025-08-05 12:09:54'),
(56, 'Applied Thermal Technology Solution Corp', '1208 Coherco Financial Tower Madrigal Alabang Muntinlupa', 'AppliedThermalTechnologySolutionCorp_natcon_registration.xlsx', '2025-08-06 00:18:41'),
(57, 'Testing', '123 Test', 'Testing_natcon_registration.xlsx', '2025-08-07 07:37:34'),
(58, 'PSME BULK', 'null123', 'PSMEBULK_natcon_registration.xlsx', '2025-08-13 05:33:36'),
(59, 'Hann Philippines Inc.', 'Bldg. 5399 M.A Roxas Highway Clark 2023 Freeport Zone Pampanga Philippines 2023', 'HannPhilippinesInc_natcon_registration.xlsx', '2025-08-15 08:05:15'),
(60, 'YAMASHITA MOLD PHILIPPINES CORPORATION', 'Lot 8, Block 1, Daiichi Industrial Park-SEZ, Maguyam, Silang, Cavite', 'YAMASHITAMOLDPHILIPPINESCORPORATION_natcon_registration.xlsx', '2025-08-21 01:48:12'),
(61, 'PETER PAUL PHILIPPINE CORPORATION', 'PAHINGA NORTE CANDELARIA QUEZON', 'PETERPAULPHILIPPINECORPORATION_natcon_registration.xlsx', '2025-08-26 08:32:14');

-- --------------------------------------------------------

--
-- Table structure for table `delegates`
--

CREATE TABLE `delegates` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middle` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `emailid` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `mobilenumber` varchar(50) DEFAULT NULL,
  `prc_license_type` varchar(255) DEFAULT NULL,
  `prc_license_number` varchar(255) DEFAULT NULL,
  `prc_license_expiration_date` date DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `chapter` varchar(100) DEFAULT NULL,
  `sector` varchar(50) DEFAULT NULL,
  `register_type` varchar(50) DEFAULT NULL,
  `isPWD` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delegates`
--

INSERT INTO `delegates` (`id`, `company_id`, `firstname`, `middle`, `lastname`, `suffix`, `dateofbirth`, `emailid`, `country`, `mobilenumber`, `prc_license_type`, `prc_license_number`, `prc_license_expiration_date`, `region`, `chapter`, `sector`, `register_type`, `isPWD`) VALUES
(15, 54, 'YUSEP', 'AMIL', 'ABDUHASAD', '', '1995-02-13', 'abduhasady@gmail.com', 'Philippines', '09361318365', 'Registered Mechanical Engineer', '0098702', '2026-02-13', 'Mindanao', 'Zambasulta', 'Government', 'Regular', 0),
(16, 54, 'JULIUS', 'TRUMATA', 'DESOACIDO', '', '1990-07-03', 'jtdesoacido@li.dole.goc.ph', 'Philippines', '09205133143', 'Registered Mechanical Engineer', '0081419', '2025-07-03', 'Mindanao', 'Zambasulta', 'Government', 'Regular', 0),
(17, 54, 'RASHNEEV', 'ANDILING', 'YNAWAT', '', '1990-12-08', 'rashneevynawat@gmail.com', 'Philippines', '09651668067', 'Registered Mechanical Engineer', '0098994', '2025-12-08', 'Mindanao', 'Zambasulta', 'Government', 'Regular', 0),
(18, 54, 'ARIS', 'HADRAQUE', 'TUGAHAN', '', '1977-07-21', 'artcancer77@gmail.com', 'Philippines', '09625142518', 'Registered Mechanical Engineer', '0059813', '2025-07-21', 'Mindanao', 'Zambasulta', 'Government', 'Regular', 0),
(19, 55, 'Jemuel', 'Bocala', 'Agban', '', '1999-09-15', 'jemuelagban09@gmail.com', 'Philippines', '09509859471', 'Registered Mechanical Engineer', '123', '2025-08-05', 'NCR', 'Marikina City', 'Private', 'Regular', 0),
(20, 56, 'jasper raven', 'romero', 'estrella', '', '1997-03-10', 'estrellajasperraven@gmail.com', 'Philippines', '09271548567', 'Registered Mechanical Engineer', '0107540', '2028-03-10', 'NCR', 'Las PiÃ±as-Muntinlupa', 'Private', 'Regular', 0),
(21, 56, 'Arlan', 'Ofracio', 'Olayes', '', '1996-04-26', 'arlanolayes@gmail.com', 'Philippines', '09563649235', 'Registered Mechanical Engineer', '0105365', '2028-04-26', 'NCR', 'Las PiÃ±as-Muntinlupa', 'Private', 'Regular', 0),
(22, 56, 'Marc Louise', 'Pebris', 'Pelaez', '', '2001-02-06', 'marclouisepebris.pelaez@gmail.com', 'Philippines', '09103459096', 'Registered Mechanical Engineer', '0123431', '2027-02-06', 'Luzon', 'Albay-Legazpi', 'Private', 'Regular', 0),
(23, 56, 'Jethro', 'Adra', 'Bremen', '', '2000-09-29', 'engr.jethrobremen@gmail.com', 'Philippines', '09495452457', 'Registered Mechanical Engineer', '0123611', '2027-09-29', 'Luzon', 'Albay-Legazpi', 'Private', 'Regular', 0),
(24, 56, 'Rich Mhar', 'Roque', 'Manguiat', '', '1996-10-22', 'richmharmanguiat22@gmail.com', 'Philippines', '09196472708', 'Registered Mechanical Engineer', '0102356', '2027-10-22', 'NCR', 'Las PiÃ±as-Muntinlupa', 'Private', 'Regular', 0),
(25, 56, 'Rojen Paul', 'Adante', 'Ojeda', '', '1999-10-03', 'rojenpauladante@gmail.com', 'Philippines', '09482719915', 'Registered Mechanical Engineer', '0124483', '2027-10-03', 'Luzon', 'Camarines Norte (Daet)', 'Private', 'Regular', 0),
(26, 56, 'Christian', 'Salunga', 'Crisostomo', '', '1998-09-19', 'christiancrisostomo52@gmail.com', 'Philippines', '09171028304', 'Registered Mechanical Engineer', '0115678', '2026-09-19', 'NCR', 'Las PiÃ±as-Muntinlupa', 'Private', 'Regular', 0),
(27, 57, 'Tigas', 'T', 'Tite', '', '2002-09-02', 't@gmail.com', 'Philippines', '09509859420', 'Registered Mechanical Engineer', '0123', '2028-09-02', 'NCR', 'Las PiÃ±as-Muntinlupa', 'Private', 'Regular', 1),
(28, 58, 'Dean', 'Encarnacion', 'Ragadio', '', '1963-12-25', 'ragadiodean63@gmail.com', 'Philippines', '09560326548', 'Registered Mechanical Engineer', '31553', '2028-12-25', 'NCR', 'Manila', 'Government', 'Regular', 0),
(29, 59, 'GEOFFREY', 'MACAPAGAL', 'LEAL', '', '1999-05-24', 'Geoffrey.Leal@hannresorts.com', 'Philippines', '09350144957', 'Registered Mechanical Engineer', '0119772', '2026-05-24', 'Luzon', 'Clark', 'Private', 'Regular', 0),
(30, 59, 'RYAN BALTAZAR', 'VIERNES', 'GARCIA', '', '1982-01-06', 'Ryan.Garcia@hannresorts.com', 'Philippines', '09752614526', 'ME Graduate', '000000', '2025-01-01', 'Luzon', 'Clark', 'Private', 'Regular', 0),
(31, 59, 'ROBERTO', 'LAGUNGUN', 'BILIWANG', 'JR', '1993-11-19', 'Roberto.Biliwang@hannresorts.com', 'Philippines', '09052497069', 'ME Graduate', '00000', '2025-01-01', 'Luzon', 'Clark', 'Private', 'Regular', 0),
(32, 59, 'NORMAN', 'RIVERA', 'GARCIA', '', '1998-11-09', 'hr.learninganddevelopment@hannresorts.com', 'Philippines', '09305997645', 'ME Graduate', '0000', '2025-01-01', 'Luzon', 'Clark', 'Private', 'Regular', 0),
(33, 60, 'DANTE', 'MALLARI', 'MIDEM', '', '1975-03-15', 'dante.midem@gmail.com', 'Philippines', '09989648862', 'Registered Mechanical Engineer', '52323', '2028-03-15', 'Luzon', 'Cavite-Carsigma', 'Private', 'Guest/Non-member', 0),
(34, 60, 'LARRY ', 'TRAN', 'MAGNO', '', '1975-06-16', 'rocky_ltm@yahoo.com', 'Philippines', '09192319481', 'Registered Mechanical Engineer', '52407', '2025-06-16', 'Luzon', 'Cavite-Carsigma', 'Private', 'Guest/Non-member', 0),
(35, 60, 'JADE MARTIN', 'LECCIO', 'PADILLA', '', '1996-01-13', 'jademartinpadilla@gmail.com', 'Philippines', '09678436140', 'Registered Mechanical Engineer', '114352', '2028-01-13', 'Luzon', 'Cavite-Carsigma', 'Private', 'Guest/Non-member', 0),
(36, 61, 'RISSEL', 'PARAGAS', 'IBRE', '', '1996-10-28', 'ibrerisselparagas@gmail.com', 'Philippines', '09676630692', 'Registered Mechanical Engineer', '0105987', '2025-10-28', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(37, 61, 'WALTER', 'ANDAL', 'HERNANDEZ', '', '1995-01-03', 'Iamwalter95@gmail.com', 'Philippines', '09081973044', 'Registered Mechanical Engineer', '0096649', '2026-01-03', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(38, 61, 'MARVIN', 'RABY', 'VILLALVA', '', '1993-07-16', 'villalvamarvin716@gmail.com', 'Philippines', '09569681709', 'Registered Mechanical Engineer', '0099722', '2027-07-16', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(39, 61, 'KIM SEANBERG', 'AGUILON', 'MANDANAS', '', '1996-10-21', 'kimmandanas06@gmail.com', 'Philippines', '0915361141', 'Registered Mechanical Engineer', '0101756', '2027-10-21', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(40, 61, 'VINCENT NEECO', 'DISTRAJO', 'APOSTOL', '', '1997-01-30', 'apostolvincentneeco@gmail.com', 'Philippines', '09664850982', 'Registered Mechanical Engineer', '0105565', '2025-01-03', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(41, 61, 'JOSEPH CLINTON', 'SAAVEDRA', 'OROGO', '', '1993-03-04', 'jcsorogo@gmail.com', 'Philippines', '09560466605', 'Registered Mechanical Engineer', '0086651', '2026-03-04', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(42, 61, 'CRISANTO', 'JAVIER', 'OJOS', '', '1997-10-04', 'ojoscris97@gmail.com', 'Philippines', '09665227836', 'Registered Mechanical Engineer', '0106305', '2025-10-04', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0),
(43, 61, 'PAUL', 'PENAVERDE', 'LAGRAZON', '', '1984-03-26', 'lagrazonp@gmail.com', 'Philippines', '0915286868', 'Registered Mechanical Engineer', '0076413', '2025-03-26', 'Luzon', 'Quezon Province', 'Private', 'Regular', 0);

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `salutation` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitations`
--

INSERT INTO `invitations` (`id`, `email`, `salutation`, `full_name`, `designation`, `company`, `address`, `status`, `created_at`, `event`) VALUES
(101, 'superaire.princesscaridad@gmail.com', 'Engr.', 'Princess Caridad', 'Engineering Supervisor', 'Super-Aire Airconditioning', '3a Saint Francis St. Immaculate Conception Cubao, Quezon City, Philippines', 'sent', '2025-07-31 06:28:34', '73rd PSME National Convention'),
(102, 'mvergabera@pcso.gov.ph', 'Engr.', 'MARK F. VERGABERA', 'ENGINEER IV', 'PHILIPPINE CHARITY SWEEPSTAKES OFFICE (PCSO)', 'SHAW BOULEVARD, MANDALUYONG', 'sent', '2025-07-31 06:30:29', '73rd PSME National Convention'),
(103, 'branosa@pcso.gov.ph', 'Engr.', 'BRIAN G. RAÃ‘OSA', 'ENGINEER III', 'PHILIPPINE CHARITY SWEEPSTAKES OFFICE (PCSO)', 'SHAW BOULEVARD, MANDALUYONG', 'sent', '2025-07-31 06:30:58', '73rd PSME National Convention'),
(104, 'engr.bliven.garcia@gmail.com', 'Engr.', 'Bliven U. Garcia', 'Head, Mechanical Engineering Section, IPPDO', 'Romblon State University, Main Campus', 'Romblon State University, Main Campus, Brgy. Liwanag, Odiongan, Romblon', 'sent', '2025-07-31 06:34:45', '73rd PSME National Convention'),
(105, 'ilao.adriel@jgc.com', 'Ms.', 'ADRIEL M. ILAO,', 'Site Maintenance Manager', 'JGC Phils., Inc.', 'Prime St., Madrigal Business Park, Muntinlupa City - 1780', 'sent', '2025-07-31 07:07:17', '73rd PSME National Convention'),
(106, 'johnmichaelvillardo.165484@gmail.com', 'Engr.', 'John Michael Villardo', 'Chief Process Control Director', 'Bakhresa Food Products Ltd.', 'Dar Es Salaam, Tanzania, East Africa', 'sent', '2025-07-31 07:18:49', '73rd PSME National Convention'),
(108, 'jpicar.university@gmail.com', 'Engr.', 'Jomari A. Picar', 'Instructor', 'Technological University of the Philippines - Cavite', 'Carlos Q. Trinidad Avenue, Salawag, DasmariÃ±as City, Cavite, Philippines', 'sent', '2025-07-31 07:27:31', '73rd PSME National Convention'),
(109, 'rpgalvez@pnoc-ec.com.ph', 'Engr.', 'Rolly P. Galvez', 'Mechanical Engineer', 'PNOC Exploration Corporation', 'PNOC EC Energy Center Bldg.1 Rizal Drive, BGC, Taguig City 1634', 'sent', '2025-07-31 07:28:11', '73rd PSME National Convention'),
(111, 'me.gene_silades@yahoo.com', 'Engr.', 'Generoso L. Silades Jr.', 'Project Engineer', 'PNOC Exploration Corporation', 'PNOC EC Energy Center Bldg.1 Rizal Drive, BGC, Taguig City 1634', 'sent', '2025-07-31 07:36:37', '73rd PSME National Convention'),
(112, 'jmacario4@yahoo.com', 'Engr.', 'James Caco Macario', 'Technical Officer', 'SMC Infrastructure', '11 Prudencia St Don Pedro Subd. Valenzuela City', 'sent', '2025-07-31 08:03:08', '73rd PSME National Convention'),
(113, 'jonmarc_matira@yahoo.com.ph', 'Engr.', 'Jonmarc A. Matira', 'Project Development Officer I', 'Batangas State University', 'Rizal Avenue Extension, Batangas City', 'sent', '2025-07-31 08:16:05', '73rd PSME National Convention'),
(114, 'matthewgalan.597@gmail.com', 'Engr.', 'Matthew A. Galan', 'Project Engineer', 'Kubota Kasui Philippines Corporation', '2nd floor, Admin Bldg. 2, Gate 3 Laguna Technopark Binan, Laguna.', 'sent', '2025-07-31 08:31:58', '73rd PSME National Convention'),
(115, 'edgianfranco@gmail.com', 'Engr.', 'Edralin Rodriguez', 'Interface Engineer', 'Dietsmann Operation and Maintenance Services', 'Doha Qatar', 'sent', '2025-07-31 08:42:28', '73rd PSME National Convention'),
(116, 'adrian.berania03@gmail.com', 'Engr.', 'Adrian Kiel B. Berania', 'Engineer I', 'Batangas State University', 'Rizal Avenue Extension , Batangas City', 'sent', '2025-07-31 08:44:45', '73rd PSME National Convention'),
(117, 'natconchair@psmeinc.org.ph', 'Mr.', 'Aron Resty Ramillano', 'NatCon Secretariat', 'Jachin-Boaz Corporation', 'Filsyn Compound, Don Jose, Santa Rosa, Laguna', 'sent', '2025-07-31 08:52:23', '73rd PSME National Convention'),
(118, 'tootserrado@gmail.com', 'Engr.', 'Mirick H. Serrado', 'Production Engineer', 'San Miguel Foods', 'Sta. Rita Industrial Estate, Sagurong, Pili, Camarines Sur', 'sent', '2025-07-31 09:28:01', '73rd PSME National Convention'),
(119, 'henryclarklaz22@gmail.com', 'Engr.', 'Henry Clark B. Laz', 'Cadet Engineer', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 10:01:51', '73rd PSME National Convention'),
(120, 'henryclarklaz22@gmail.com', 'Engr.', 'Henry Clark B. Laz', 'Cadet Engineer', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 10:03:00', '73rd PSME National Convention'),
(121, 'henryclarklaz22@gmail.com', 'Engr.', 'Henry Clark B. Laz', 'Cadet Engineer', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 10:04:14', '73rd PSME National Convention'),
(122, 'rizarafonselletimcang@gmail.com', 'Hon.', 'RIZA RAFONSELLE T. TIMCANG', 'Municipal Mayor ', 'Municipal Mayor ', 'Municipality of Socorro, Surigao Del Norte ', 'sent', '2025-07-31 10:06:35', '73rd PSME National Convention'),
(123, 'henryclarklaz22@gmail.com', 'Engr.', 'Henry Clark B. Laz', 'Cadet Engineer', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 10:06:35', '73rd PSME National Convention'),
(124, 'rizarafonselletimcang@gmail.com', 'Hon.', 'RIZA RAFONSELLE T. TIMCANG', 'Municipal Mayor ', 'Municipal Mayor ', 'Municipality of Socorro, Surigao Del Norte ', 'sent', '2025-07-31 10:06:46', '73rd PSME National Convention'),
(125, 'rizarafonselletimcang@gmail.com', 'Hon.', 'RIZA RAFONSELLE T. TIMCANG', 'Municipal Mayor ', 'Municipal Mayor ', 'Municipality of Socorro, Surigao Del Norte ', 'sent', '2025-07-31 10:07:00', '73rd PSME National Convention'),
(126, 'rizarafonselletimcang@gmail.com', 'Hon.', 'RIZA RAFONSELLE T. TIMCANG', 'Municipal Mayor ', 'Municipal Mayor ', 'Municipality of Socorro, Surigao Del Norte ', 'sent', '2025-07-31 10:13:16', '73rd PSME National Convention'),
(127, 'soritaivybernadettej@gmail.com', 'Engr.', 'Ma. Ivy Bernadette J. Sorita', 'Engineer Staff', 'Japan Cash Machine Global', 'BiÃ±an, Laguna. ', 'sent', '2025-07-31 10:13:52', '73rd PSME National Convention'),
(128, 'junmagno.atienza@g.batstate-u.edu.ph', 'Engr.', 'Jun Magno C. Atienza', 'Engineer I', 'Batangas State University - Pablo Borbon', 'Rizal Avenue Extension, Batangas City', 'sent', '2025-07-31 10:43:25', '73rd PSME National Convention'),
(129, 'jrosario@net.tonghsing.com.ph', 'Engr.', 'Jonald Rosario', 'Facilities Engineer', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna', 'sent', '2025-07-31 10:44:51', '73rd PSME National Convention'),
(130, 'jrosario@net.tonghsing.com.ph', 'Engr.', 'Aaron Jan Basa', 'Facilities Engineer', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna', 'sent', '2025-07-31 10:46:02', '73rd PSME National Convention'),
(131, 'jrosario@net.tonghsing.com.ph', 'Engr.', 'Jeremias Lumbres', 'Pollution Control/Safety Officer', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna ', 'sent', '2025-07-31 10:47:22', '73rd PSME National Convention'),
(132, 'jonaldrosario12@gmail.com', 'Engr.', 'Jonald Rosario', 'Facilities Engineer', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna ', 'sent', '2025-07-31 10:48:18', '73rd PSME National Convention'),
(133, 'rhielapayen@gmail.com', 'Engr.', 'Rhiela Paye E. Navarro', 'PDO I', 'Bataan Peninsula State University ', 'Capitol Compound, Tenejero, Balanga City, Bataan', 'sent', '2025-07-31 10:56:12', '73rd PSME National Convention'),
(134, 'rpitao@bipsu.edu.ph', 'Engr.', 'Ramon L. Pitao, Jr.', 'Chairperson Mechanical Engineering ', 'Biliran Province State University ', 'Naval, Biliran ', 'sent', '2025-07-31 11:46:23', '73rd PSME National Convention'),
(135, 'rpitao@bipsu.edu.ph', 'Dr.', 'Victor C. CaÃ±ezo, Jr.', 'University President ', 'Biliran Province State University ', 'Naval, Biliran ', 'sent', '2025-07-31 11:48:05', '73rd PSME National Convention'),
(136, 'stephenimmanuelgalang@gmail.com', 'Engr.', 'Stephen Immanuel E. Galang', 'Faculty', 'Tarlac State University', 'Romulo Blvd., Tarlac City, Tarlac', 'sent', '2025-07-31 12:44:40', '73rd PSME National Convention'),
(137, 'stephenimmanuelgalang@gmail.com', 'Engr.', 'Stephen Immanuel E. Galang', 'Faculty', 'Tarlac State University', 'Romulo Blvd., Tarlac City, Tarlac', 'sent', '2025-07-31 12:46:37', '73rd PSME National Convention'),
(138, 'henryclarklaz22@gmail.com', 'Engr.', 'Henry Clark B. Laz', 'Cadet Engineer', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 14:39:20', '73rd PSME National Convention'),
(139, 'henryclarklaz22@gmail.com', 'Engr.', 'Ramon Daniel D. Encarnacio', 'Engineering Manager', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 15:29:05', '73rd PSME National Convention'),
(140, 'henryclarklaz22@gmail.com', 'Engr.', 'John Jofel R. Ablog', 'Assistant Supervisor', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 15:30:33', '73rd PSME National Convention'),
(141, 'henryclarklaz22@gmail.com', 'Engr.', 'Ramon Daniel D. Encarnacion', 'Engineering Manager', 'Bluemantle Industries Inc.', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-07-31 15:32:32', '73rd PSME National Convention'),
(142, 'mara.atienza@g.batstate-u.edu.ph', 'Engr.', 'Mara C. Atienza', 'Project Development Officer I', 'Batangas State University', 'Rizal Ave. Extension, Batangas City', 'sent', '2025-08-01 00:14:57', '73rd PSME National Convention'),
(143, 'christianbonnmeren@yahoo.com', 'Engr.', 'Christian Bonn E. Meren', 'WRFO-A', 'Metro Kalibo Water District ', 'Estancia, Kalibo, Aklan ', 'sent', '2025-08-01 00:21:47', '73rd PSME National Convention'),
(144, 'jonaldrosario12@gmail.com', 'Engr.', 'Aaron Jan Basa', 'Facilities Engineer ', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna', 'sent', '2025-08-01 01:24:29', '73rd PSME National Convention'),
(145, 'jonaldrosario12@gmail.com', 'Engr.', 'Jeremias Lumbres', 'Pollution Control/Safety Officer', 'Tong Hsing Electronics Phils. Inc.', 'Carmelray International Park Canlubang, Calamba City, Laguna ', 'sent', '2025-08-01 01:25:53', '73rd PSME National Convention'),
(146, 'nickserrano03@yahoo.com', 'Engr.', 'NICANOR L. SERRANO', 'Mechanical Engineering Program Chair', 'Technological Institute of the Philippines - Manila', '1338 ARLEGUI STREET\r\nQUIAPO', 'sent', '2025-08-01 03:07:34', '73rd PSME National Convention'),
(147, 'henryclarklaz22@gmail.com', 'Engr.', 'Bonifacio R. Mana-ay III', 'Assistant Supervisor', 'Bluemantle Industries Inc. ', 'Blk 2, Lot 25 & 27 Marian Road 2, Marian Business Park, Brgy. San Martin De Porres, Paranaque City', 'sent', '2025-08-01 03:58:33', '73rd PSME National Convention'),
(148, 'johnjac101@gmail.com', 'Engr.', 'John Paul B. Jacalan', 'INSTRUCTOR', 'CEBU TECHNOLOGICAL UNIVERSITY - DUMANJUG EXTENSION CAMPUS', 'LIONG, DUMANJUG CEBU, 6035', 'sent', '2025-08-01 12:53:56', '73rd PSME National Convention'),
(149, 'fredporras@yahoo.com', 'Engr.', 'FREDERICK PORRAS, MPA', 'PROCUREMENT MANAGEMENT OFFICER V', 'PROCUREMENT SERVICE DEPARTMENT OF BUDGET AND MANAGEMENT', 'Sudlon, Lahug, Cebu City\r\nRegional Operations Group - Cebu', 'sent', '2025-08-02 15:08:54', '73rd PSME National Convention'),
(150, 'ahmad.arumpac2@gmail.com', 'Engr.', 'Ahmad Ryan Arumpac', 'Environmental Management Specialist II', 'MINISTRY OF ENVIRONMENT, NATURAL RESOURCES AND ENERGY.', 'AKMAD D. BRAHIM\r\nMinister\r\nMINISTRY OF ENVIRONMENT, NATURAL RESOURCES AND ENERGY.\r\nBangsamoro Government Center, Rosary Heights VII, Cotabato City.', 'sent', '2025-08-04 03:05:08', '73rd PSME National Convention'),
(151, 'apeo8560@gmail.com', 'Engr.', 'Andre M. Reyes', 'Provincial Engineer Head ', 'Provincial Government of Negros Oriental', 'Dumaguete City, Negros Oriental', 'sent', '2025-08-04 03:08:56', '73rd PSME National Convention'),
(152, 'governor.negor@gmail.com', 'Hon.', 'Honorable Manuel â€œChacoâ€ Sagarbarria', 'Governor', 'Provincial Government of Negros Oriental', 'Dumaguete City, Negros Oriental', 'sent', '2025-08-04 03:11:10', '73rd PSME National Convention'),
(153, 'equipmentpool02@gmail.com', 'Engr.', 'Al Manuel Z. Real', 'Equipment Pool Division', 'Provincial Government of Negros Oriental', 'Dumaguete City, Negros Oriental', 'sent', '2025-08-04 03:13:51', '73rd PSME National Convention'),
(154, 'Ryan.Garcia@hannresorts.com', 'Mr.', 'Ryan Baltazar Garcia', 'SMX Convention Center Manila', 'Hann Philippine, inc.', 'Bldg. 5399 M.A. Roxas Highway, Clark 2023 Freeport Zone, Pampanga, Philippines', 'sent', '2025-08-04 06:05:26', '73rd PSME National Convention'),
(155, 'Roberto.Biliwang@hannresorts.com', 'Mr.', 'Roberto Biliwang Jr. ', 'SMX Convention Center Manila', 'Hann Philippines, inc.', 'Bldg. 5399 M.A. Roxas Highway, Clark 2023 Freeport Zone, Pampanga, Philippines', 'sent', '2025-08-04 06:07:25', '73rd PSME National Convention'),
(156, 'Norman.Garcia@hannresorts.com', 'Mr.', 'Norman Garcia', 'SMX Convention Center Manila', 'Hann Philippines, Inc.', 'Bldg. 5399 M.A. Roxas Highway, Clark 2023 Freeport Zone, Pampanga, Philippines', 'sent', '2025-08-04 06:08:08', '73rd PSME National Convention'),
(157, 'Geoffrey.Leal@hannresorts.com', 'Mr.', 'Geoffry Leal', 'SMX Convention Center Manila', 'Hann Philippines, Inc.', 'Bldg. 5399 M.A. Roxas Highway, Clark 2023 Freeport Zone, Pampanga, Philippines', 'sent', '2025-08-04 06:09:04', '73rd PSME National Convention'),
(158, 'godofredo.domingo@hannresorts.com', 'Mr.', 'Godofredo Domingo, Jr', 'SMX Convention Center Manila', 'Hann Philippines, Inc.', 'Bldg. 5399 M.A. Roxas Highway, Clark 2023 Freeport Zone, Pampanga, Philippines', 'sent', '2025-08-04 06:09:36', '73rd PSME National Convention'),
(159, 'tung.nats0910@yahoo.com', 'Engr.', 'Stanley Tungcul', 'Engineer-IV', 'LGU Province of Cagayan', '1274 Degala Street Capitol Site Caggay', 'sent', '2025-08-05 14:38:50', '73rd PSME National Convention'),
(160, 'tung.nats0910@yahoo.com', 'Engr.', 'Stanley P. Tungcul', 'Engineer-IV', 'LGU Province of Cagayan', 'Provincial Engineer\'s Office \r\nProvince of Cagayan\r\nAlimanao Hills, Tuguegarao City', 'sent', '2025-08-05 14:41:39', '73rd PSME National Convention'),
(161, 'tung.nats0910@yahoo.com', 'Engr.', 'Stanley P. Tungcul', 'Engineer-IV', 'LGU Province of Cagayan', 'Provincial Engineer\'s Office Province of Cagayan Alimanao Hills Tuguegarao City', 'sent', '2025-08-05 14:44:12', '73rd PSME National Convention'),
(162, 'pangilinan.itm@gmail.com', 'Hon.', 'Lucy Torres Gomez', 'City Mayor', 'City Government of Ormoc', 'Ormoc City', 'sent', '2025-08-06 05:49:11', '73rd PSME National Convention'),
(163, 'joshuaicaro22@gmail.com', 'Engr.', 'Joshua Ezequiel O. Icaro', 'Procurement Management Officer III', 'Procurement Service - Department of Budget and Management', 'RR Road, Cristobal St., Paco, Manila ', 'sent', '2025-08-06 08:24:32', '73rd PSME National Convention'),
(164, 'csdelarosa27@gmail.com', 'Engr.', 'Clayton Sanchez Dela Rosa', 'PMO I', 'Procurement Service - DBM', 'RR Road Cristobal St., Paco, Manila, Philippines, 1007', 'sent', '2025-08-06 08:31:24', '73rd PSME National Convention'),
(165, 'sgarin@doe.gov.ph', 'Atty.', 'Sharon S. Garin', 'Secretary', 'Department of Energy', 'Energy Center, 34th St., Rizal Drive\r\nBonifacio Global City, Taguig City, 1632', 'sent', '2025-08-06 09:42:11', '73rd PSME National Convention'),
(166, 'rpdckarl2000@gmail.com', 'Engr.', 'Ricky P. Dela Cruz', 'Engr.B', 'Metro Kalibo Water District', 'Jaime Cardinal Sin Ave. Andagao Kalibo Aklan', 'sent', '2025-08-06 12:27:00', '73rd PSME National Convention'),
(167, 'roghinmarcvillar@gmail.com', 'Engr.', 'Roghin Marc Kristian Laca Villar', 'Technical Engineer', 'SAE Products Marketing Corporation', '133 Bel air drive bel air 1 don jose sta rosa city', 'sent', '2025-08-07 09:24:30', '73rd PSME National Convention'),
(168, 'bandelariaronald96@gmail.com', 'Mr.', 'Ronald Bandelaria', 'Single Proprietor', 'PAMM\'S FOOD HUB', 'Stall#3, SBBFI, SAN ANTONIO AVENUE, SAN ANTONIO VILLAGE, BRGY. DALIG, ANTIPOLO CITY', 'sent', '2025-08-10 03:25:10', '73rd PSME National Convention'),
(169, 'mrambaud.op.eng@gmail.com', 'Other', 'Marvin P. CaÃ±ero', 'OIC - DESFA', 'Office of the President of the Philippines', 'J.P. Laurel Street, San Miguel, Manila', 'sent', '2025-08-11 03:59:04', '73rd PSME National Convention'),
(170, 'mrambaud.op.eng@gmail.com', 'Atty.', 'Marvin P. CaÃ±ero', 'OIC - DESFA', 'Office of the President of the Philippines', 'J. P. Laurel Street, San Miguel, Manila', 'sent', '2025-08-11 04:01:27', '73rd PSME National Convention'),
(171, 'mrambaud.op.eng@gmail.com', 'Engr.', 'Margaux Ydha Marie R. Agapito', 'Engineer III', 'Office of the President of the Philippines', 'J. P. Laurel Street, San Miguel, Manila', 'sent', '2025-08-11 04:02:59', '73rd PSME National Convention'),
(172, 'mrambaud.op.eng@gmail.com', 'Atty.', 'Alexander G. Flores', 'Assistant Executive Secretary (AES), OIC, Office of the Deputy Executive Secretary for Support Services and Auxiliaries (ODESSSA)', 'Office of the President of the Philippines', 'J.P. Laurel Street, San Miguel, Manila', 'sent', '2025-08-11 09:28:02', '73rd PSME National Convention'),
(173, 'mrambaud.op.eng@gmail.com', 'Atty.', 'Alexander G. Flores', 'Assistant Executive Secretary (AES), OIC, ODESSSA', 'Office of the President of the Philippines', 'J. P. Laurel St., San Miguel, Manila', 'sent', '2025-08-11 09:36:15', '73rd PSME National Convention'),
(174, 'bernard.manaba@winfordmanila.com', 'Engr.', 'Bernard Manaba', 'Engineer', 'MJC', 'San Lazaro Tourism & business Park., MJC Drive & Consuelo St. Sta. Cruz Manila', 'sent', '2025-08-11 22:06:55', '73rd PSME National Convention'),
(175, 'bernard.manaba@winfordmanila.com', 'Engr.', 'Bernard Manaba', 'Engineer', 'MJC', 'San Lazaro Tourism & Business Park., MJC Drive & Consuelo St Sta. Cruz Manila', 'sent', '2025-08-11 22:09:06', '73rd PSME National Convention'),
(176, 'engr.noeldichoso@gmail.com', 'Engr.', 'Noel Dichoso', 'Director - Property Management & Engineering Services', 'Hospitality Innovators Inc.', 'Alabang', 'sent', '2025-08-12 07:33:32', '73rd PSME National Convention'),
(177, 'jgflorece@miescor.ph', 'Engr.', 'JOSHUA G. FLORECE', 'LEAD - Fleet Operations, Repair & Maintenance', 'MERALCO INDUSTRIAL ENGINEERING SERVICES CORPORATION', 'RENAISSANCE TOWER 1000, MERALCO AVENUE, ORTIGAS, PASIG CITY', 'sent', '2025-08-12 07:52:30', '73rd PSME National Convention'),
(178, 'john.petrolhead8416@gmail.com', 'Mr.', 'LEICEL JOHN ALFONSO BELMES', 'Service Engineer ', 'SANY Philippines', 'Grass Residences Brgy Sto Cristo Quezon City ', 'sent', '2025-08-12 08:30:11', '73rd PSME National Convention'),
(179, 'john.petrolhead8416@gmail.com', 'Mr.', 'LEICEL JOHN ALFONSO BELMES', 'Service Engineer ', 'SANY Philippines', 'Grass Residences Brgy Sto Cristo Quezon City ', 'sent', '2025-08-12 08:31:30', '73rd PSME National Convention'),
(180, 'melvinomibog@gmail.com', 'Engr.', 'John Melvin T. Omibog', 'Area Supervisor', 'Sunwest, Inc. - Equipment Management Division', 'Lidong, Sto. Domingo, Albay', 'sent', '2025-08-12 08:35:19', '73rd PSME National Convention'),
(181, 'ranmarconst@gmail.com', 'Mr.', 'Randy Francisco', 'Operation Manager', 'RANMAR  CONSTRUCTION SERVICES', '13 Catbalogan St Luzviminda Village, Batasan Hills, Quezon City', 'sent', '2025-08-12 08:37:33', '73rd PSME National Convention'),
(182, 'desideriopepito23@gmail.com', 'Mr.', 'Desiderio V. Pepito ', 'Dean, Mechanical Engineering Department ', 'Bataan Heroes College ', 'Roman Superhighway, Balanga City, Bataan ', 'sent', '2025-08-12 08:44:58', '73rd PSME National Convention'),
(183, 'splampano@yahoo.com', 'Engr.', 'Sherwin Lampano', 'General Manager', 'SPL CONSTRUCTION', 'Block 7 Macabulos Drive San Vicente Tarlac City', 'sent', '2025-08-12 08:46:41', '73rd PSME National Convention'),
(184, 'williamcunanan116@gmail.com', 'Mr.', 'William Cunanan', 'Project Manager', 'MMC Technical Solutions Corp.', 'Duale, Limay, Bataan', 'sent', '2025-08-12 08:50:13', '73rd PSME National Convention'),
(185, 'zac.quintillano@cohu.com', 'Engr.', 'Zechariah Quintillano', 'Facilities Mechanical Engineer', 'Delta Design Philippines LLC', 'Calamba City Laguna', 'sent', '2025-08-12 08:52:12', '73rd PSME National Convention'),
(186, 'eabarut@gmail.com', 'Engr.', 'EDUARD A.  BARUT', 'Assistant Manager, Procurement and Logistics Department', 'Coral Bay Nickel Corporation', 'Rio Tuba Export Processing Zone\r\nRio Tuba, Bataraza 5306 Palawan', 'sent', '2025-08-12 08:55:19', '73rd PSME National Convention'),
(187, 'niervarey03@gmail.com', 'Other', 'RAY O NIERVA', 'Engineer', 'DPWH REGION V', 'Brgy.15 Ilawod East', 'sent', '2025-08-12 08:59:42', '73rd PSME National Convention'),
(188, 'gmbuslig@yahoo.com', 'Mr.', 'Gilbert M. Buslig', 'Mechanical and Electrical Head', 'JFE Engineering Corporation', '23rd Floor Cyberscape Alpha Building, Sapphire and Garnet Roads, Ortigas Center, Pasig City', 'sent', '2025-08-12 09:00:57', '73rd PSME National Convention'),
(189, 'nol_ticoz@yahoo.com', 'Mr.', 'NOEL T. COSTAN', 'Sr.Project Mechanical Engineer MEPF QA/QC', 'Premium Megastructures Inc.', '237 PMI Tower, Cabanillas Corner Ocampo Sr. Extension Street Makati City M,Mla.1203 Philippines', 'sent', '2025-08-12 09:04:01', '73rd PSME National Convention'),
(190, 'johnjuliusbaldia@gmail.com', 'Engr.', 'John Julius Baldia', 'Managing Director', 'Guarantec', 'L64 B22 1K2 Kasiglahan Village\r\nSan Jose', 'sent', '2025-08-12 09:20:43', '73rd PSME National Convention'),
(191, 'mvmaghinang@gmail.com', 'Engr.', 'MARVIN V. MAGHINANG', 'PROCUREMENT OFFICER', 'ST. LUKE\'S MEDICAL CENTER', 'BGC TAGUIG CITY', 'sent', '2025-08-12 09:22:02', '73rd PSME National Convention'),
(192, 'johnjuliusbaldia@gmail.com', 'Engr.', 'John Julius Baldia', 'Managing Director', 'Guarantech IT Solutions', 'Lot 31 Block 6 Phase 2 Villa San Isidro, Brgy. San Isidro, Montalban, Rizal 1860', 'sent', '2025-08-12 09:22:04', '73rd PSME National Convention'),
(193, 'mvmaghinang@gmail.com', 'Engr.', 'MARVIN V. MAGHINANG', 'PROCUREMENT OFFICER', 'ST. LUKE\'S MEDICAL CENTER', 'BGC TAGUIG CITY', 'sent', '2025-08-12 09:23:11', '73rd PSME National Convention'),
(194, 'lezus0927@gmail.com', 'Engr.', 'Lezus Dean Nagales', 'Commercial Assistant Manager', 'DB&B Philippines, Inc.', '2304 PBCOM Tower corner V.A. Rufino Ayala Ave. Makati City', 'sent', '2025-08-12 09:27:35', '73rd PSME National Convention'),
(195, 'escalalorenz21@gmail.com', 'Engr.', 'Lorenz Gen E. Escala', 'Project Engineer ', 'San Miguel Global Power', 'Taltal, Masinloc, Zambales', 'sent', '2025-08-12 09:27:51', '73rd PSME National Convention'),
(196, 'petronio.balagulan.jr@gmail.com', 'Engr.', 'Petronio Jr Cimat Balagulan', 'Senior Procurement Specialist', 'Innovative Agro Ph Industries', 'GF Algeria Bldg., Chino Roces Ave. Makari City', 'sent', '2025-08-12 09:37:46', '73rd PSME National Convention'),
(197, 'antonio.dizon@shangri-la.com', 'Engr.', 'Antonio Dizon', 'Director of Engineering ', 'Shangri-La The Fort Manila ', '30th St. Cor. 5th Ave. BGC, Taguig MM', 'sent', '2025-08-12 10:02:04', '73rd PSME National Convention'),
(198, 'anicetocabellon@yahoo.com', 'Engr.', 'Aniceto Cabellon', 'Mechanical Head', 'Dmci mining corp', 'Sta cruz zambales', 'sent', '2025-08-12 10:04:38', '73rd PSME National Convention'),
(199, 'linshaikaalbacite@gmail.com', 'Ms.', 'Linshaika Marie Albacite', 'Project Engineer', 'Operation and Maintenance Technology Philippines, Inc.', 'Filsyn Plant Santa Rosa - Tagaytay Rd, City of Santa Rosa, 4026 Laguna', 'sent', '2025-08-12 10:08:14', '73rd PSME National Convention'),
(200, 'rhinodaniel25@gmail.com', 'Engr.', 'Daniel Rhino Dolores', 'Maintenance Planner', 'Zambales Chromite Mining Company Inc.', 'Bolitoc, Santa Cruz, Zambales', 'sent', '2025-08-12 10:23:48', '73rd PSME National Convention'),
(201, 'ava.jill.apilan@gmail.com', 'Engr.', 'Ava Jill Apilan', 'Project Development Officer II', 'Surigao del Norte State University', 'Narciso Street, Surigao City', 'sent', '2025-08-12 10:27:09', '73rd PSME National Convention'),
(202, 'rj_mozo@yahoo.com', 'Engr.', 'Joanah Pusta Mozo', 'CEO', 'JM TradeQuest FZ-LLC', 'Al Hamra Industrial Zone-FZ\r\nRas Al Khaima, United Arab Emirates', 'sent', '2025-08-12 10:34:45', '73rd PSME National Convention'),
(203, 'johnsuerte@gmail.com', 'Engr.', 'John N. Suerte', 'Engineering Manager', 'A.G.Araja Construction and Development Corp.', 'AGA bldg. Jaguar St., Mercado Village, Pulong Sta. Cruz, Sta. Rosa City, Laguna PH                                                                                 ', 'sent', '2025-08-12 10:40:06', '73rd PSME National Convention'),
(204, 'mrjnebril@pnri.dost.gov.ph', 'Engr.', 'Maria Reza J. Nebril', 'Science Research Assistant', 'DOST - Philippine Nuclear Research Institute', 'PHILIPPINE NUCLEAR RESEARCH INSTITUTE\r\nNART Building, Ground Floor, Nuclear Training Center\r\nCommonwealth Ave., Diliman, (Along Central Avenue)', 'sent', '2025-08-12 10:50:16', '73rd PSME National Convention'),
(205, 'rfsulit@pnri.dost.gov.ph', 'Engr.', 'Ramoncito F. Sulit', 'Senior Science Research Specialist ', 'DOST - Philippine Nuclear Research Institute', 'DOST - PHILIPPINE NUCLEAR RESEARCH INSTITUTE\r\nCOMMONWEALTH AVENUE DILIMAN QUEZON CITY', 'sent', '2025-08-12 10:52:40', '73rd PSME National Convention'),
(206, 'ancbarrida@pnri.dost.gov.ph', 'Engr.', 'Andrew C. Barrida', 'SENIOR SCIENCE RESEARCH SPECIALIST', 'DOST - Philippine Nuclear Research Institute', 'DOST - PHILIPPINE NUCLEAR RESEARCH INSTITUTE\r\nCOMMONWEALTH AVENUE DILIMAN QUEZON CITY ', 'sent', '2025-08-12 10:54:53', '73rd PSME National Convention'),
(207, 'noel.payongayong@luisitasugar.com', 'Mr.', 'Noel M. Payongayong', 'Vp for Operations/Resident Manager', 'Central Azucarera de Tarlac', 'Barangay Central, Tarlac City', 'sent', '2025-08-12 11:03:41', '73rd PSME National Convention'),
(208, 'noel.payongayong@luisitasugar.com', 'Mr.', 'Noel M. Payongayong', 'Vp for Operations/Resident Manager', 'Central Azucarera de Tarlac', 'Barangay Central, Tarlac City', 'sent', '2025-08-12 11:04:01', '73rd PSME National Convention'),
(209, 'bulacarlene87@gmail.com', 'Engr.', 'Arlene A. Bulac', 'Engineer III', 'DPWH, Regional Office IX', 'Veterans Avenue Extension, Tetuan, Zamboanga City', 'sent', '2025-08-12 11:20:21', '73rd PSME National Convention'),
(210, 'cabaddumarcangelo@gmail.com', 'Engr.', 'MARC ANGELO TONG CABADDU', 'INSTRUCTOR III', 'CAGAYAN STATE UNIVERSITY', 'CARIG SUR, TUGUEGARAO CITY, CAGAYAN', 'sent', '2025-08-12 11:36:24', '73rd PSME National Convention'),
(211, 'joel.rebleza@holcim.com', 'Engr.', 'JOEL D. REBLEZA', 'Mechanical Section Manger', 'Holcim Philippines Inc.', 'Holcim Lugait Plant,\r\nLugait, Misamis Oriental', 'sent', '2025-08-12 11:40:08', '73rd PSME National Convention'),
(212, 'jong@nipponpaint.com.ph', 'Engr.', 'Jeffrey Jimenez Ong', 'Maintenance & Facilities Manager', 'Nippon Paint (Coatings) Philippines Inc. ', 'LISP 1, Brgy. Diezmo, Cabuyao City, Laguna', 'sent', '2025-08-12 11:40:32', '73rd PSME National Convention'),
(213, 'valdezleo027@gmail.com', 'Engr.', 'Leo Corrales Valdez', 'Factory Engineer', 'Univanich Carmen Palm Oil Corporation ', 'Sayre Highway, Tacupan,Carmen, North Cotabato', 'sent', '2025-08-12 11:48:59', '73rd PSME National Convention'),
(214, 'ricky.carlos02@gmail.com', 'Engr.', 'Ricardo Carlos', 'Project Manager ', 'KMLC CONSTRUCTION ', 'Plaridel Bulacan ', 'sent', '2025-08-12 11:56:11', '73rd PSME National Convention'),
(215, 'rfongsi@ymail.com', 'Mr.', 'Romy S. Fongsi', 'Operation Manager', 'R.M.M Supply and Electro Mechanical Services', 'Upper Shamolog pico la trinidad Benguet', 'sent', '2025-08-12 11:57:33', '73rd PSME National Convention'),
(216, 'dhaddyjosh@gmail.com', 'Engr.', 'Joshua B. Pamaylaon', 'Supervising Water Utilities Development Management Officer', 'Baguio Water District', '#3 Utility Road\r\nBarangay Marcoville\r\nBaguio City 2600', 'sent', '2025-08-12 12:06:14', '73rd PSME National Convention'),
(217, 'johnnolandguico@yahoo.com.sg', 'Mr.', 'John Noland', 'Sales Manager', 'Rheem Manufacturing', 'Singapore', 'sent', '2025-08-12 12:34:34', '73rd PSME National Convention'),
(218, 'caratiquitrebhenson@gmail.com', 'Engr.', 'Rebhenson J. Caratiquit', 'Project Technical Assistant I', 'Department of Science and Technology Regional Office III', 'San Fernando, Pampanga', 'sent', '2025-08-12 12:47:29', '73rd PSME National Convention'),
(219, 'caratiquitrebhenson@gmail.com', 'Engr.', 'Rebhenson J. Caratiquit', 'Project Technical Assistant I', 'Department of Science and Technology Regional Office III', 'San Fernando, Pampanga', 'sent', '2025-08-12 12:48:16', '73rd PSME National Convention'),
(220, 'taga.dipag@gmail.com', 'Hon.', 'Darel Dexter T. Uy', 'Provincial Governor', 'Province of Zamboanga del Norte', 'Provincial Capitol Bldg.,\r\nEstaka, Dipolog City', 'sent', '2025-08-12 13:03:26', '73rd PSME National Convention'),
(221, 'ismaelescano1519@gmail.com', 'Mr.', 'Ismael Escano', 'Facilities Manager', 'VITRO Inc', '222 Nicanor Garcia St. Barangay Bel-Air Makati City', 'sent', '2025-08-12 13:04:27', '73rd PSME National Convention'),
(222, 'renato_ilisan@yahoo.com', 'Engr.', 'Renato Ilisan', 'General Manager', 'ILISAN Engineering Services', 'Costa Brava Barangay Banago Bacolod City', 'sent', '2025-08-12 13:24:04', '73rd PSME National Convention'),
(223, 'marceloisla89@gmail.com', 'Mr.', 'Marcelo B. Isla', 'Sales and Technical Manager', 'MA TECH SUPPLY AND ENGINEERING SERVICES', 'Luna St. Poblacion East, San Nicolas,Pangasinan', 'sent', '2025-08-12 13:27:57', '73rd PSME National Convention'),
(224, 'mardiccion@engineer.com', 'Engr.', 'Mar', 'Senior Planning Engineer', 'Energy Inc. ', '367 Villa Tres Maria\'s,Deparo', 'sent', '2025-08-12 14:06:29', '73rd PSME National Convention'),
(225, 'mardiccion@engineer.com', 'Engr.', 'Mar', 'Senior Planning Engineer', 'Energy Inc. ', '367 Villa Tres Maria\'s,Deparo', 'sent', '2025-08-12 14:08:05', '73rd PSME National Convention'),
(226, 'arribapatrick@yahoo.com', 'Engr.', 'patrick henry funtila', 'President', 'Rich Dadz Inc', '44 Azucena St., Tahanan Village, Brgy. BF Homes, Paranaque City', 'sent', '2025-08-12 14:13:06', '73rd PSME National Convention'),
(227, 'engelbert0831@gmail.com', 'Other', 'ENGELBERT RODRI PERALTA CASANOVA', 'Acting Municipal Fire Marshal', 'Burgos Fire Station, BFP R-1', '435 PUROK 7, PAGDALAGAN NORTE', 'sent', '2025-08-12 14:24:05', '73rd PSME National Convention'),
(228, 'edwinlaxamana1979@gmail.com', 'Engr.', 'EDWIN AVENIR LAXAMANA ', 'MEP MAINTENANCE ENGINEER ', 'Equipment & Technical Services Co.(ETECHS CO. )', 'P.O Box 92505 Riyadh 11663,\r\nSaudi Arabia', 'sent', '2025-08-12 15:14:01', '73rd PSME National Convention'),
(229, 'henry1.benamer@toshiba.co.jp', 'Engr.', 'Henry Maniaga Benamer', 'Engineer', 'TOSHIBA Information Equipment (Phils.), Inc.', '127 North Science, SEPZ, Laguna Technopark, BiÃ±an, Laguna', 'sent', '2025-08-12 16:41:23', '73rd PSME National Convention'),
(230, 'jan_velasco20@yahoo.com', 'Engr.', 'Jan Peter Anthony C. Velasco', 'Mechanical Engineer', 'Monford Group', '9877 Isarog Street, Umali Subdivision, Batong Malake', 'sent', '2025-08-12 19:22:04', '73rd PSME National Convention'),
(231, 'ryanpaulconde@gmail.com', 'Engr.', 'Ryan Paul C. Conde', 'Mechanical Engineer', 'Local Government Unit of Tiwi, Albay', 'Tigbi, Tiwi, Albay', 'sent', '2025-08-12 22:45:25', '73rd PSME National Convention'),
(232, 'augustos_dalisay@cocogen.com', 'Mr.', 'Augustos Cesar P. Dalisay ', 'Risk Surveyor III', 'Cocogen Insurance, Inc. ', '22F One Corporate Center, DoÃ±a Julia Vargas Avenue corner Meralco Avenue, Ortigas Center, Pasig City ', 'sent', '2025-08-12 23:18:10', '73rd PSME National Convention'),
(233, 'aodeguzman@gmail.com', 'Engr.', 'Arnaldo O. De Guzman', 'Head - GENERAL SERVICES', 'Perpetual Help Medical Center-Binan', '674 Modern Village Subdivision\r\nBarangay Paciano Rizal', 'sent', '2025-08-12 23:45:55', '73rd PSME National Convention'),
(234, 'dorosan.nerielyn1@jgc.com', 'Engr.', 'Nerielyn Manarpaac Dorosan', 'Marketing and Sales Department Manager', 'JGC Philippines, Inc.', 'JGC Philippines Inc. Building\r\n2109 Prime St. Madrigal Business Park, Alabang, Muntinlupa', 'sent', '2025-08-13 00:16:31', '73rd PSME National Convention'),
(235, 'iman.nazareta12@gmail.com', 'Engr.', 'Immanuel Nazareta', 'Mechanical Engineer', 'Compwell Builders Supply Inc', '823 Tramo St. Brgy Manuyo Uno Las Pinas City\r\n823', 'sent', '2025-08-13 00:19:33', '73rd PSME National Convention'),
(236, 'venjuan2018@gmail.com', 'Engr.', 'Joven Arienza Juan', 'Mechanical Engineer', 'CTP Construction and Mining Corporation', 'Brgy. Adlay, Carrascal, Surigao del Sur 8318', 'sent', '2025-08-13 00:20:41', '73rd PSME National Convention'),
(237, 'dbatin@yahoo.com', 'Engr.', 'Darwin Z. Batin', 'Sr. Manager', 'JGC Philippines Inc.', '2109 Prime Street, Madrigak Business Park, Alabang, Muntinlupa City', 'sent', '2025-08-13 00:31:29', '73rd PSME National Convention'),
(238, 'dennetlino@yahoo.com.ph', 'Engr.', 'DENNET LINO', 'Technical Consultant', 'EXOGEN ENGINEERING AND CONSULTING SERVICES ', '093D SAN JOSE CALAMBA CITY', 'sent', '2025-08-13 00:37:48', '73rd PSME National Convention'),
(239, 'gefa@niras.com', 'Engr.', 'Geronimo C. Farro', 'Sr. MEP Engr.', 'Niras Asia Manila Inc.', '17th flr IBP Tower, Jade Drive, San Antonio, Ortigas Center, Pasig City', 'sent', '2025-08-13 00:44:59', '73rd PSME National Convention'),
(240, 'reyrondrique@yahoo.com', 'Mr.', 'Rey P. Rondrique', 'Mechanical Engineer', 'Rondrique Airconditioning Services', 'Kaylawig, Catmon Sta. Maria, Bulacan', 'sent', '2025-08-13 00:55:59', '73rd PSME National Convention'),
(241, 'dorosan.nerielyn1@jgc.com', 'Engr.', 'Nerielyn Manarpaac-Dorosan', 'Marketing and Sales Department Manager', 'JGC Philippines, Inc.', 'JGC Philippines Inc Building 2109 Prime Street Madrigal Business Park Alabang Muntinlupa', 'sent', '2025-08-13 01:14:44', '73rd PSME National Convention'),
(242, 'forts.galos64@gmail.com', 'Engr.', 'Fortunato Galos', 'Operation Manager ', 'Hideco Sugar Milling Company, Inc.', 'Montebello, Kananga, Leyte', 'sent', '2025-08-13 02:00:33', '73rd PSME National Convention'),
(243, 'alvindecastro15@gmail.com', 'Mr.', 'Alvin De Castro', 'Facilities Lead', 'CGI Philippines Inc.', '2F One World Square Mckinley Hill Taguig', 'sent', '2025-08-13 02:16:33', '73rd PSME National Convention'),
(244, 'marlon.nangan@jotun.ph', 'Mr.', 'MARLON CAMPOSAGRADO NANGAN', 'Maintenance Manager', 'Jotun Philippines Inc', '27 Millennium Drive, Light Industry and Science Park III\r\nBrgy. Santa Anastacia, Sto. Tomas, Batangas Philippines', 'sent', '2025-08-13 02:16:54', '73rd PSME National Convention'),
(245, 'quimpangn@yahoo.com', 'Engr.', 'NILO BISMONTE QUIMPANG', 'PROJECT MANAGER', 'SAN MIGUEL FOODS, INC.', 'B6 L15 VILLA CONCHITA SUBDIVISION , BAGO GALLERA, DAVAO CITY', 'sent', '2025-08-13 02:21:06', '73rd PSME National Convention'),
(246, 'frederick.cabalce@smdevelopment.com', 'Engr.', 'Frederick M. abalce', 'Senior Safety Manager', 'SM Development Corporation', '141 Concordia, Alitagtag, Batangas 4205', 'sent', '2025-08-13 02:21:40', '73rd PSME National Convention'),
(247, 'John_Donaire@universalleaf.com.com.ph', 'Engr.', 'John Rey O. Donaire', 'Mechanical Engineering Manager 1', 'Universal Leaf Philippines, Inc.', 'Purok 7, Nappaccu Pequeno, Reina Mercedes, Isabela', 'sent', '2025-08-13 02:35:47', '73rd PSME National Convention'),
(248, 'eway_bastor@universalleaf.com.pj', 'Engr.', 'Eway B. Bastor', 'Mechanical Engineering Supervisor 1', 'Universal Leaf Philippines, Inc.', 'Purok 2, Napaccu Pequeno, Reina Mercedes, Isabela', 'sent', '2025-08-13 02:37:13', '73rd PSME National Convention'),
(249, 'reycastle791221@gmail.com', 'Dr.', 'Reynaldo Castillo', 'MEP Design Manager', 'ARCADIS ', 'B67-L26, Evergreen Drv. LBA-3, Binan City, Laguna', 'sent', '2025-08-13 02:41:40', '73rd PSME National Convention'),
(250, 'mamacatol@yahoo.com', 'Engr.', 'Mario A. Macatol', 'VP/ Technical Services- Gsses Head', 'Pryce Gases, Inc.', '18/F, Pryce Center Bldg., \r\n1179 Chino Roces Ave., cor.\r\nBagtikan St.\r\nSan Antonio, Makati City\r\n1203\r\nPhilippines', 'sent', '2025-08-13 02:42:09', '73rd PSME National Convention'),
(251, 'reynaldo.castillo@arcadis.com', 'Dr.', 'Reynaldo Castillo', 'MEP Design Manager', 'ARCADIS', 'Zuellig Building, Makati Ave. cor Paseo De Roxas, Makati City.', 'sent', '2025-08-13 02:44:09', '73rd PSME National Convention'),
(252, 'rey.palparan@mnf2.rohmphil.com', 'Mr.', 'Renante M. Palparan', 'Training & Education Division Manager', 'ROHM ELECTRONICS PHILIPPINES, INC.', 'PTC-SPECIAL ECONOMIC ZONE\r\nCARMONA CAVITE 4116', 'sent', '2025-08-13 02:48:30', '73rd PSME National Convention'),
(253, 'dayandayan.jc@energy.com.ph', 'Engr.', 'Jotham C. Dayandayan', 'Sr Maintenance Engineer', 'Energy Dev. Corp. ( EDC)', '2nd St. Silverhills Subd. brgy. Luna, Ormoc, City', 'sent', '2025-08-13 02:54:07', '73rd PSME National Convention'),
(254, 'dericmedroso@yahoo.com', 'Engr.', 'Roderick B Medroso ', 'Proprietor ', 'RBM Enterprises ', 'Blk 15 Lot 29A B.Estanislao St.,BFRV Talon Dos Las PiÃ±as City', 'sent', '2025-08-13 02:57:53', '73rd PSME National Convention'),
(255, 'rstinga77@gmail.com', 'Engr.', 'MARISSA T. CABANDAY/ & RONALDO SANTILLAN TINGA', 'OIC- City Engineer/ & Engineer-1', 'LGU- BAYUGAN CITY /CITY ENGINEERING OFFICE', 'P7A, BRGY. POBLACION BAYUGAN CITY', 'sent', '2025-08-13 03:45:50', '73rd PSME National Convention'),
(256, 'mike-gabriel.mercado@cwhomedepot.com', 'Mr.', 'Mathew Lonto', 'Engineering Head', 'CW Marketing and Development Corporation', 'Unit 402 & 407-408 Ortigas TechnoPoint Building 2, #1 DoÃ±a Julia Vargas Brgy. Ugong, Pasig City 1604', 'sent', '2025-08-13 03:58:41', '73rd PSME National Convention'),
(257, 'suzette_manliguez@yahoo.com', 'Engr.', 'SUZETTE C. MANLIGUEZ', 'ENGINEER-I', 'CITY GOVERNMENT OF SURIGAO ', 'Borromeo St. City Hall Compound,Surigao City', 'sent', '2025-08-13 05:27:01', '73rd PSME National Convention'),
(258, 'jerrymancia7@gmail.com', 'Mr.', 'Jerry C. Mancia', 'Shift Operation Engineer/PCO', 'Cebu Central Realty Corporation', 'Block 19, Lot 12 Cabancalan 1 Bulacao Cebu City', 'sent', '2025-08-13 06:12:19', '73rd PSME National Convention'),
(259, 'emdept.acemclegazpi@gmail.com', 'Mr.', 'Richard O. Rito', 'Chief Engineer', 'Allied Care Experts (ACE) Medical Center Legazpi Inc.', 'Legazpi-Daraga Diversion Road, Brgy. Bogtong Legazpi City', 'sent', '2025-08-13 06:23:24', '73rd PSME National Convention'),
(260, 'engelbert0831@gmail.com', 'Engr.', 'Engelbert Rodrigo P. Casanova', 'Acting Municipal Fire Marshal', 'Burgos Fire Station', 'Brgy. New Poblacion, Burgos, La Union', 'sent', '2025-08-13 06:43:52', '73rd PSME National Convention'),
(261, 'mel.arvin.atienza@texwipe.com', 'Engr.', 'Mel Arvin Atienza', 'Manufacturing Supervisor', 'Advanced Molding Company Inc.', 'No. 5 Circuit Street, Light Industry and Science Park 1, Cabuyao, Laguna, Philippines', 'sent', '2025-08-13 07:33:19', '73rd PSME National Convention'),
(262, 'rjsdavao@hotmail.com', 'Engr.', 'Rudy J. Sultan', 'Chairman of the Board', 'RJS Industrial Construction and Development Corporation', 'PUROK 1 MANGGANIAN VILLAGE, KM. 18, TIBUNGCO, DAVAO CITY', 'sent', '2025-08-13 07:34:40', '73rd PSME National Convention'),
(263, 'andayajoey29@gmail.com', 'Engr.', 'JOEY RONQUILLO ANDAYA', 'President / CEO', 'ONE JRAND INDL CORP', 'Blk 4 Lot 38, Doha St., Villa Conde, Brgy., Napindan, Taguig City', 'sent', '2025-08-13 07:57:31', '73rd PSME National Convention'),
(264, 'raymond.17212427@gmail.com', 'Engr.', 'Raymond Villanueva ', 'Sr. Manager ', 'PAGCOR', 'MET Live Building, Business Park 1-A, Macapagal Blvd. cor. Edsa Extension  Pasay City', 'sent', '2025-08-13 09:08:14', '73rd PSME National Convention'),
(265, 'jandeil@yahoo.com', 'Engr.', 'Daniel B. Marco', 'Manager', 'Marc Jandeil Engineering', 'Jp Rizal Street, Sta Cruz, Naga City, Camarines Sur', 'sent', '2025-08-13 10:04:38', '73rd PSME National Convention'),
(266, 'ralfiler@mmpc.ph', 'Mr.', 'Robert H. Alfiler', 'Project and Facilities Engineer', 'Mitsubishi Motors Philippines Corporation', 'No. 1 Autopark Avenue, Greenfield Automotive Park, SEZ, Don Jose, City of Santa Rosa, Laguna', 'sent', '2025-08-13 11:11:58', '73rd PSME National Convention'),
(267, 'maguenangel@yahoo.com', 'Engr.', 'Angel Maguen', 'Provincial Director- Mt. Province', 'DOST-CAR', '#016 Sitio Samoyao, Alapang, La Trinidad, Benguet 2601', 'sent', '2025-08-13 12:51:29', '73rd PSME National Convention'),
(268, 'dfuntilar@gmail.com', 'Engr.', 'Harold Funtilar', 'Assistant Refrigeration Supervisor', 'DYD Refrigeration System Inc.', '1388 Quezon Avenue Unit B 7th Floor DN Corporate Center, Quezon City', 'sent', '2025-08-13 13:34:40', '73rd PSME National Convention'),
(269, 'crismitchnuez@gmail.com', 'Engr.', 'Cristito V. Nuez', 'Dept. Manager ', 'SOLID EARTH Devt Corp ', 'North Poblacion, San Fernando Cebu ', 'sent', '2025-08-13 16:32:11', '73rd PSME National Convention'),
(270, 'jfuntila@yahoo.com', 'Engr.', 'joseph Funtila', 'General Manager', 'JMF Engineering Services', 'Southville Subd Sto Tomas Binan, Laguna\r\nnone', 'sent', '2025-08-13 21:47:44', '73rd PSME National Convention'),
(271, 'jerrytominez@gmail.com', 'Engr.', 'Jerry Fragata Tominez', 'Project Engineer', 'Johnson Controls Singapore', '329 Jurong East Ave 1 singapore 600329', 'sent', '2025-08-13 23:58:32', '73rd PSME National Convention'),
(272, 'rlpgapasin@rfm.com.ph', 'Engr.', 'Renzo Louis P. Gapasin', 'Engineering and Maintenance Supervisor', 'RFM Corporation - Cabuyao Plant', 'KM 47, RFM Road, RFM Corporation - Cabuyao Plant, Barangay Pulo, City of Cabuyao, 4025, Province of Laguna, Philippines', 'sent', '2025-08-14 00:03:34', '73rd PSME National Convention'),
(273, 'louidhermosa@yahoo.com', 'Dr.', 'Odellio Y. Ferrer, MD', 'Provincial Health Officer II', 'Agusan del Norte Provincial Hospital', 'B-Libertad , Butuan city', 'sent', '2025-08-14 01:40:50', '73rd PSME National Convention'),
(274, 'johnmarliecarlos1@gmail.com', 'Engr.', 'John Marlie D. Carlos', 'MEPF Engineer 1', 'DMCI Homes', '1321 Apolinario Street, Bangkal, Makati City, Metro Manila, PH ', 'sent', '2025-08-14 01:46:11', '73rd PSME National Convention'),
(275, 'ebrivera@mgen.com.ph', 'Engr.', 'Eric B. Rivera', 'Site Manager', 'Greenergy For Global Inc. Doing Business Under G4G', 'Tower 1 Rockwell Business Center Ortigas Avenue. Ugong 1604 City of Pasig NCR, Second District Philippines', 'sent', '2025-08-14 02:23:41', '73rd PSME National Convention'),
(276, 'markgreciansanchez@gmail.com', 'Mr.', 'Mark Grecian Sanchez', 'PROCUREMENT OFFICER', 'STA. MARIA INDUSTRIAL PARK CORPORATION', 'block 20 lot 19 section 7 phase 1\r\npabahay 2000, tilapia st., muzon', 'sent', '2025-08-14 02:40:35', '73rd PSME National Convention'),
(277, 'gasturbine28@gmail.com', 'Engr.', 'Eric B. Rivera', 'Site Manager', 'Greenergy For Global Inc. Doing Business Under G4G', 'Tower 1 Rockwell Business Center Ortigas Ave. Ugong 1604 City of Pasig NCR, Second District Philippines', 'sent', '2025-08-14 02:45:46', '73rd PSME National Convention'),
(278, 'mrdulman@mgen.com.ph', 'Engr.', 'MIGUEL R. DULMAN', 'SHIFT CHARGE ENGINEER', 'Greenergy for Global Inc. doing business under g4g', 'Tower 1 Rockwell Business Center Ortigas Ave. Ugong 1604 City of Pasig NCR, Second District Philippines', 'sent', '2025-08-14 02:47:23', '73rd PSME National Convention'),
(279, 'dulmanmiguel27@gmail.com', 'Engr.', 'MIGUEL R. DULMAN', 'SHIFT CHARGE ENGINEER', 'Greenergy for Global Inc. doing business under g4g', 'Tower 1 Rockwell Business Center Ortigas Ave. Ugong 1604 City of Pasig NCR, Second District Philippines', 'sent', '2025-08-14 02:50:55', '73rd PSME National Convention'),
(280, 'andalalpatrick@gmail.com', 'Engr.', 'Al Patrick G. Andal', 'QA/QC Engineer', 'Chevron Phillips Chemical Company', 'Doha Qatar', 'sent', '2025-08-14 03:17:17', '73rd PSME National Convention'),
(281, 'rgpasion.mechengr@gmail.com', 'Engr.', 'Romiel Gomez Pasion', 'Engineer I', 'LGU of Angeles City', '3rd Floor, Office of the City Building Official, Angeles City Hall,\r\nBrgy. Pulung Maragul, Angeles City, Pampanga', 'sent', '2025-08-14 03:43:08', '73rd PSME National Convention'),
(282, 'rcc05071988@gmail.com', 'Engr.', 'Robert C. Castro', 'Senior Supervisor of Factory Maintenance ', 'HIDECO SUGAR MILLING COMPANY, INC. ', 'PLANTSITE, BRGY. MONTEBELLO, KANANGA LEYTE 6531 PHILIPPINES. ', 'sent', '2025-08-14 04:40:44', '73rd PSME National Convention'),
(283, 'resuellorichard1005@gmail.com', 'Engr.', 'Richard M. Resuello', 'Pollution Control Officer & Safety Officer', 'Barbatos Ventures Corporation', 'Vitarich Compound, Sta. Rosa I, Marilao, Bulacan', 'sent', '2025-08-14 05:20:12', '73rd PSME National Convention'),
(284, 'lomanogrenatojr38@gmail.com', 'Engr.', 'Renato G. Lomanog Jr.', 'Operations Manager', 'One Jrand Indl Corp.', 'Napindan, Taguig city', 'sent', '2025-08-14 05:53:43', '73rd PSME National Convention'),
(285, 'raf41892@yahoo.com', 'Engr.', 'RAYMUND FONTANILLA', 'Site Manager', 'Republic Cement & Building Materials', '133 Urbiztondo, San Juan, La Union', 'sent', '2025-08-14 06:27:59', '73rd PSME National Convention'),
(286, 'monchmac@gmail.com', 'Engr.', 'Raymund Peralta Macaranas', 'Chief Science Research Specialist', 'Philippine Center for Postharvest Development and Mechanization', 'Purok 1\r\nMaligaya', 'sent', '2025-08-14 06:35:53', '73rd PSME National Convention'),
(287, 'monchmac@gmail.com', 'Engr.', 'Raymund Peralta Macaranas', 'Chief Science Research Specialist', 'Philippine Center for Postharvest Development and Mechanization (PHilMech)', 'KM 151, National Highway, Bantug, Science City of Munoz, Nueva Ecija', 'sent', '2025-08-14 06:36:53', '73rd PSME National Convention'),
(288, 'gaucoiam@gmail.com', 'Mr.', 'Iam B. Gauco', 'Engineer II', 'Provincial Government of Davao del Norte', 'Government Center, Mankilam, Tagum City', 'sent', '2025-08-14 06:42:59', '73rd PSME National Convention'),
(289, 'gaucoiam@gmail.com', 'Mr.', 'Crisanto D. Tictic', 'Engineer I', 'Provincial Government of Davao del Norte', 'Government Center, Mankilam, Tagum City', 'sent', '2025-08-14 06:43:59', '73rd PSME National Convention'),
(290, 'jeric.amados@gmail.com', 'Engr.', 'Jeric A. Amados', 'Engineering Assistant A', 'National Irrigation Administration Albay-Catanduanes Irrigation Management Office', 'Tuburan, Ligao City, Albay', 'sent', '2025-08-14 06:47:28', '73rd PSME National Convention'),
(291, 'ocansanayjr8@gmail.com', 'Engr.', 'OSCAR DE GUZMAN CANSANAY', 'Production and Maintenance Manager', 'Pacific Synergy food and beverage corp.', 'Km. 53, Brgy. Milagrosa, Calamba City, Laguna', 'sent', '2025-08-14 07:33:43', '73rd PSME National Convention'),
(292, 'jkennethsandiego@gmail.com', 'Mr.', 'John Kenneth San Diego', 'Project Engineer', 'Newly Weds Foods Philippines', 'Unit A, 103 Excellence Avenue, Carmelray Industrial Park I   \r\nCanlubang, Calamba, Laguna 4028  \r\n', 'sent', '2025-08-14 07:47:47', '73rd PSME National Convention'),
(293, 'ksandiego@newlywedsfoods.com.ph', 'Mr.', 'John Kenneth San Diego', 'Project Engineer', 'Newly Weds Foods Philippines', 'Unit A, 103 Excellence Avenue, Carmelray Industrial Park I, Canlubang, Calamba, Laguna 4028', 'sent', '2025-08-14 07:53:26', '73rd PSME National Convention'),
(294, 'jkennethsandiego@gmail.com', 'Mr.', 'John Kenneth San Diego', 'Project Engineer', 'Newly Weds Foods Philippines', 'Unit A, 103 Excellence Avenue, Carmelray Industrial Park I Canlubang, Calamba, Laguna 4028', 'sent', '2025-08-14 07:56:31', '73rd PSME National Convention'),
(295, 'tibaylester14@gmail.com', 'Mr.', 'Lester Tibay', 'Project Engineer', 'Newly Weds Foods Philippines', 'Unit A, 103 Excellence Avenue, Carmelray Industrial Park I Canlubang, Calamba, Laguna 4028', 'sent', '2025-08-14 07:58:36', '73rd PSME National Convention'),
(296, 'renzobalabag@gmail.com', 'Engr.', 'Renzo Jay L. Balabag', 'Faculty', 'University of Science and Technology and Southern Philippines', 'CM Recto, Lapasan, Cagayan de Oro City, Misamis Oriental Philippines', 'sent', '2025-08-14 08:36:26', '73rd PSME National Convention'),
(297, 'vargasgary26@gmail.com', 'Engr.', 'Gary Villare Vargas', 'Motorpool Unit Manager', 'Goodfound Cement Corporation', 'Purok 3, Palanog, Camalig, Albay, 4502', 'sent', '2025-08-14 10:07:11', '73rd PSME National Convention'),
(298, 'joelteguihanon@gmail.com', 'Engr.', 'JOEL BACATO', 'QAQC MEPF Manager', 'Shang Properties Inc', 'SAINT FRANCIS STREET', 'sent', '2025-08-14 10:18:23', '73rd PSME National Convention'),
(299, 'calumpitwd@yahoo.com', 'Engr.', 'RONNIE B. LARGADO', 'General Manager', 'Calumpit Water District', 'Corazon, Calumpit, Bulacan ', 'sent', '2025-08-14 10:21:14', '73rd PSME National Convention'),
(300, 'rodelmanalo020573@gmail.com', 'Engr.', 'Rodel T. Manalo', 'Engineer III', 'Office of the Provincial Engineer, Cavite', 'Provincial Capitol\r\nSan Agustin\r\nTrece Martires City\r\nCavite', 'sent', '2025-08-14 10:37:55', '73rd PSME National Convention'),
(301, 'rodelmanalo020573@gmail.com', 'Engr.', 'Rodel T. Manalo', 'Engineer III', 'Office of the Provincial Engineer, Cavite', 'Provincial Capitol\r\nSan Agustin\r\nTrece Martires City\r\nCavite', 'sent', '2025-08-14 10:39:19', '73rd PSME National Convention'),
(302, 'rodelmanalo020573@gmail.com', 'Engr.', 'Rodel T. Manalo', 'Engineer III', 'Office of the Provincial Engineer, Cavite', 'Provincial Capitol, San Austin, Trece Martires City, Cavite', 'sent', '2025-08-14 10:42:07', '73rd PSME National Convention'),
(303, 'entolosa@yahoo.com.ph', 'Engr.', 'Elmer N. Tolosa, PME', 'Academic Supervisor, ME Department', 'University of San Agustin', 'Gen. Luna Street, Iloilo City', 'sent', '2025-08-14 20:34:27', '73rd PSME National Convention');
INSERT INTO `invitations` (`id`, `email`, `salutation`, `full_name`, `designation`, `company`, `address`, `status`, `created_at`, `event`) VALUES
(304, 'karloray.sanmiguel@ph.nestle.com', 'Engr.', 'Karlo Ray T. San Miguel', 'Project Engineer', 'Nestle Philippines Inc.', 'Nestle CDO Factory, Tablon, Cagayan de Oro City', 'sent', '2025-08-14 22:34:02', '73rd PSME National Convention'),
(305, 'andalisjenecca@gmail.com', 'Engr.', 'Jenecca Andalis', 'AT-II', 'LGU-BUHI', 'SAN BUENAVENTURA BUHI CAMARINES SUR', 'sent', '2025-08-15 01:59:41', '73rd PSME National Convention'),
(306, 'raulvillareal.lcwd@gmail.com', 'Engr.', 'RAUL R. VILLAREAL', 'Property Supply Officer/Pollution Control Officer', 'Legazpi City Water District', 'Obrero-Barriada Rd. Brgy. Bitano, Legazpi City, Albay.', 'sent', '2025-08-15 02:00:35', '73rd PSME National Convention'),
(307, 'reinaasabio@gmail.com', 'Engr.', 'Reynald P. Sabio', 'PSME Davao ', 'RealPower Engine Parts Trading ', 'Brgy. Calumpang, General Santos City ', 'sent', '2025-08-15 03:49:28', '73rd PSME National Convention'),
(308, 'boy.plana@greenleafhotelgensan.com', 'Mr.', 'Demetrio Aponia Plana', 'Engg Manager ', 'Green Leaf Hotel', 'Blk 3,Lotus Subd. San Isidro General Santos City ', 'sent', '2025-08-15 03:50:11', '73rd PSME National Convention'),
(309, 'kaifleetsup@gmail.com', 'Engr.', 'Kyzelle Mae Cabonilas Macarandan', 'Associate Engineer ', 'BUSCO SUGAR MILLING CO INC', 'Butong, Quezon, Bukidnon ', 'sent', '2025-08-15 03:50:42', '73rd PSME National Convention'),
(310, 'hampacnelson@gmail.com', 'Engr.', 'NELSON D. HAMPAC', 'Consultant', 'F. T. Pontillo Engineering.', 'Blk 6, lot 26, Villa Senorita, Langub, Davao City', 'sent', '2025-08-15 03:51:30', '73rd PSME National Convention'),
(311, 'teddylagmay8@gmail.com', 'Engr.', 'Lagmay, Teddy, Mellejor ', 'Engineer 1', 'Local Government Unit of Maco ', 'Binuangan, Maco, Davao de Oro', 'sent', '2025-08-15 03:51:46', '73rd PSME National Convention'),
(312, 'absin.aprilrose@gmail.com', 'Engr.', 'April Rose M. Absin', 'Supervisor', 'Philfresh Meats Corporation ', 'Grandblocks bldg, St. Ignatius Street, Kauswagan , Cagayan de Oro City', 'sent', '2025-08-15 03:52:04', '73rd PSME National Convention'),
(313, 'vbb3291@yahoo.com', 'Engr.', 'Vernoulli B. Belgira', 'Consultant ', 'Freelance oil palm consultant ', 'Kenram , Isulan \r\n9805 Sultan Kudarat ', 'sent', '2025-08-15 03:54:24', '73rd PSME National Convention'),
(314, 'alexis_ponferrada@pjaccorp.com', 'Engr.', 'Alexis R. Ponferrada', 'senior supervisor', 'Philippine-Japan Active Carbon Corp.', 'Malagamot Rd,  Panacan , Davao City', 'sent', '2025-08-15 03:54:59', '73rd PSME National Convention'),
(315, 'elvishangca@yahoo.com', 'Engr.', 'ELVIS CLAVIDO HANGCAN', 'Institutional & ICT Operations Audit Division Supervisor', 'ZANECO', 'General Luna Street,Dipolog City, Zamboanga del Norte', 'sent', '2025-08-15 03:55:08', '73rd PSME National Convention'),
(316, 'spartan.rcsoria@gmail.com', 'Engr.', 'Romeo C. Soria', 'LDRRMO III', 'LGU Mahayag', 'Purok-2, Poblacion, Mahayag, \r\nZamboanga del Sur', 'sent', '2025-08-15 03:55:47', '73rd PSME National Convention'),
(317, 'renante.anding@franklinbaker.ph', 'Mr.', 'Renante E. Anding', 'Supervisor', 'Franklin Baker Company of the Philippines', 'Brgy. Coronon,  Sta. Cruz, Davao del Sur', 'sent', '2025-08-15 03:57:50', '73rd PSME National Convention'),
(318, 'baisa441@gmail.com', 'Engr.', 'RENZ JACOB D. BAISA', 'LABOR AND EMPLOYMENT OFFICER - ALI', 'DEPARTMENT OF LABOR AND EMPLOYMENT', '15 int. Damaso Reyes St., Pinagkamaligan, Tanay, Rizal', 'sent', '2025-08-15 05:51:13', '73rd PSME National Convention'),
(319, 'rglorono@gmail.com', 'Engr.', 'Rico G.Lorono', 'OBO- Division chief', 'LGU-OBO-CITY HALL,CDO', 'LGU-OBO CITY HALL,CAGAYAN DE ORO CITY', 'sent', '2025-08-15 05:51:39', '73rd PSME National Convention'),
(320, 'baisa441@gmail.com', 'Engr.', 'Renz Jacob D. Baisa', 'Labor and Employment Officer III - ALI', 'Department of Labor and Employment', '3/F & 4/F Andenson Bldg. II, Parian, Calamba City, Laguna', 'sent', '2025-08-15 06:00:22', '73rd PSME National Convention'),
(321, 'larvin.santiago@manilawater.com', 'Engr.', 'LARVIN INAMARGA SANTIAGO', 'Construction Manager', 'Manila Water', 'MWSS Administration Building, MWSS Complex, 489 Katipunan Avenue, Balara, Quezon City, 1105', 'sent', '2025-08-15 07:08:16', '73rd PSME National Convention'),
(322, 'prolandosanchez1124@gmail.com', 'Engr.', 'Rolando P. Sanchez', 'Municipal Assessor ', 'LGU Lapaz', 'Poblacion Lapaz Agusan Del Sur', 'sent', '2025-08-15 07:11:42', '73rd PSME National Convention'),
(323, 'prolandosanchez1124@gmail.com', 'Engr.', 'Rolando P. Sanchez', 'Municipal Assessor ', 'LGU Lapaz', 'Poblacion Lapaz Agusan Del Sur', 'sent', '2025-08-15 07:12:37', '73rd PSME National Convention'),
(324, 'prolandosanchez1124@gmail.com', 'Engr.', 'Rolando P. Sanchez', 'Municipal Assessor ', 'LGU Lapaz', 'Poblacion Lapaz Agusan Del Sur', 'sent', '2025-08-15 07:13:21', '73rd PSME National Convention'),
(325, 'prolandosanchez1124@gmail.com', 'Engr.', 'Rolando P. Sanchez', 'Municipal Assessor ', 'LGU Lapaz', 'Poblacion Lapaz Agusan Del Sur', 'sent', '2025-08-15 07:13:41', '73rd PSME National Convention'),
(326, 'prolandosanchez1124@gmail.com', 'Engr.', 'Rolando P. Sanchez', 'Municipal Assessor ', 'LGU Lapaz', 'Poblacion Lapaz Agusan Del Sur', 'sent', '2025-08-15 07:14:52', '73rd PSME National Convention'),
(327, 'assialana@cebulandmasters.com', 'Engr.', 'Angielou Sialana', 'MEPF Contracts', 'Cebu Land Masters Inc.', 'Latitude Corporate Center, Cebu Business park, Mindanao Ave. Cebu City', 'sent', '2025-08-15 08:58:11', '73rd PSME National Convention'),
(328, 'rommel.austria@aboitizpower.com', 'Engr.', 'Rommel Austria', 'Senior Manager, Central Maintenance Services', 'AP Renewables Inc.', 'Sitio Mabang, Parang Brgy. Limao Calauan Laguna 4012', 'sent', '2025-08-15 09:07:00', '73rd PSME National Convention'),
(329, 'carlobernal07@yahoo.com', 'Engr.', 'Carlo BERNAL', 'Owner', 'Chcb business consultancy', 'Lot 5b block 2 primitivo st., Gloria heights subd.', 'sent', '2025-08-15 10:35:03', '73rd PSME National Convention'),
(330, 'kenkimdumaguing@gmail.com', 'Engr.', 'Kenneth Kim B. Dumaguing', 'Production Engineer', 'Energy Development Corporation', 'Brgy. Tongonan, Ormoc City, Leyte 6541', 'sent', '2025-08-15 22:28:22', '73rd PSME National Convention'),
(331, 'diosyalferez@gmail.com', 'Engr.', 'Dioscoro Alferez', 'Resident Professional Mechanical Engineer', 'Santos Knight Frank, Inc.', 'Block 1, Lot 30, Phase 3C, Juana Complex,\r\nSan Francisco, Binan City, Laguna', 'sent', '2025-08-16 00:38:46', '73rd PSME National Convention'),
(332, 'galinatodexter@gmail.com', 'Mr.', 'Dexter D. Galinato', 'O/M Tech.-A', 'Napocor-Spug', 'Kumintang St. Tugbok Dist. ,Mintal, Davao City', 'sent', '2025-08-16 02:38:01', '73rd PSME National Convention'),
(333, 'galinatodexter@gmail.com', 'Mr.', 'Dexter D. Galinato', 'O/M Tech.-A', 'Napocor-Spug', 'Kumintang St. Mintal, Tugbok Dist. Davao City', 'sent', '2025-08-16 02:39:57', '73rd PSME National Convention'),
(334, 'joeysalalila@yahoo.com', 'Mr.', 'JOEY S. SALALILA', 'PRESIDENT', 'MARTINA CONSTRUCTION SERVICES', 'BLOCK 5 LOT 21 VILLA DE ESPERANZA TOWNHOUSE MALINTA VALENZUELA CITY', 'sent', '2025-08-16 04:24:04', '73rd PSME National Convention'),
(335, 'maurice.sorita@analog.com', 'Engr.', 'Maurice Gerard Sorita', 'Equipment Manager', 'Analog Devices', 'Gateway Business Park, General Trias, Cavite', 'sent', '2025-08-16 06:56:41', '73rd PSME National Convention'),
(336, 'vajr1920@yahoo.com', 'Engr.', 'Victoriano A. Andutan Jr.', 'Engineer II', 'Philippine Heart Center', 'East Avenue, Central, QUEZON CITY, NCR, SECOND DISTRICT', 'sent', '2025-08-16 10:01:29', '73rd PSME National Convention'),
(337, 'rmerca_72@yahoo.com', 'Engr.', 'Rodolfo A. Merca Jr.', 'GS Faculty', 'Camarines Sur Polytechnic Colleges Nabua', 'San Miguel Nabua, Camarines Sur 4434', 'sent', '2025-08-16 13:40:07', '73rd PSME National Convention'),
(338, 'Bernard.manaba@winfordmanila.com', 'Engr.', 'Bernard Manaba', 'Duty Engineer', 'Winford Manila Resort & Casino', 'San Lazaro Tourism & business Park., MJC Drive & Consuelo St., Sta. Cruz, Manila', 'sent', '2025-08-16 14:27:22', '73rd PSME National Convention'),
(339, 'Bernard.manaba@winfordmanila.com', 'Engr.', 'Bernard Manaba', 'Duty Engineer', 'Winford Manila Resorts & casino', 'San Lazaro Tourism & Business Park., MJC Drive & Conuelo St., Sta. Cruz, Manila', 'sent', '2025-08-16 14:30:01', '73rd PSME National Convention'),
(340, 'corfil@gmail.com', 'Engr.', 'Teofilo Corrales Jr ', 'President & CEO', 'Lexicor Trading Inc', 'Lexicor Alabang Zapote road corner San Francisco St Almanza Uno Las Pinas City ', 'sent', '2025-08-17 04:00:27', '73rd PSME National Convention'),
(341, 'rafbaylon16@gmail.com', 'Engr.', 'Raffy Baylon', 'PIC', 'MGS Construction Inc.', 'Empire Apartment 120, Cuneta Avenue, Pasay City', 'sent', '2025-08-17 15:03:16', '73rd PSME National Convention'),
(342, 'gmguevarra@sbma.com', 'Engr.', 'Giselle Marquez-Guevarra', 'Safety Specialist', 'SUBIC BAY METROPOLITAN AUTHORITY', 'SBFZ', 'sent', '2025-08-18 00:48:46', '73rd PSME National Convention'),
(343, 'equipmentpooldivisionp@gmail.com', 'Mr.', 'Jessie S. Petalcorin', 'Engineer IV', 'Provincial Government of Davao del Norte ', 'Government Center, Mankilam, Tagum City', 'sent', '2025-08-18 01:12:25', '73rd PSME National Convention'),
(344, 'equipmentpooldivisionp@gmail.com', 'Mr.', 'Euclid I. IbaÃ±ez', 'Engineer I', 'Provincial Government of Davao del Norte ', 'Government Center, Mankilam, Tagum City ', 'sent', '2025-08-18 01:16:04', '73rd PSME National Convention'),
(345, 'dante.midem@gmail.com', 'Mr.', 'DANTE MALLARI MIDEM', 'Production Section Manager', 'YAMASHITA MOLD PHILIPPINES CORPORATION', 'B42 L19-20 TIERRA SOLANA, BUENAVISTA III, GEN. TRIAS CITY', 'sent', '2025-08-18 02:21:22', '73rd PSME National Convention'),
(346, 'clifford.delacruz04@gmail.com', 'Mr.', 'Clifford De La Cruz', 'Engr. ', 'Philippine Geothermal Production Company Inc.', '14F 6750 Bldg., Ayala Avenue, Makati City ', 'sent', '2025-08-18 03:36:17', '73rd PSME National Convention'),
(347, 'clifford.delacruz04@gmail.com', 'Mr.', 'Clifford De La Cruz', 'Reservoir Engineer', 'Philippine Geothermal Production Company Inc.', '14F 6750 Bldg., Ayala Avenue, Makati City', 'sent', '2025-08-18 03:39:28', '73rd PSME National Convention'),
(348, 'knpsantos23@gmail.com', 'Engr.', 'Kim Nathaniel P. Santos', 'Acting Head, Leasing Team BPMU-PFMG', 'Development Bank of the Philippines', 'Sen Gil Puyat Ave. corner Makati Ave., Makati City', 'sent', '2025-08-18 04:49:57', '73rd PSME National Convention'),
(349, 'voltaire.diaz@yahoo.com', 'Mr.', 'Voltaire A.Diaz', 'Retired PME ', 'Retired ', '20 Hope Teresa Village QC', 'sent', '2025-08-18 07:58:58', '73rd PSME National Convention'),
(350, 'bddegala@gmail.com', 'Mr.', 'Bonnie Degala', 'Founder and CEO', 'RCV Initiatives Agri Trading', 'MacArthur, La Paz, Iloilo City', 'sent', '2025-08-18 20:53:11', '73rd PSME National Convention'),
(351, 'michaeldeguzman73@gmail.com', 'Engr.', 'Michael De Guzman', 'Engineering and Maintenance Supervisor', 'Quezon Poultry & Livestock Corporation', 'QPLC Compound Sitio Kapatagan Barangay Pinugay Baras, Rizal', 'sent', '2025-08-19 00:37:57', '73rd PSME National Convention'),
(352, 'maupo.sd@energy.com.ph', 'Mr.', 'Saturnino D. Maupo ', 'Production Shift head', 'EDC', 'Mlaitbog Power Plant, LGBU Kananga, Leyet', 'sent', '2025-08-19 01:26:50', '73rd PSME National Convention'),
(353, 'tingski89@gmail.com', 'Engr.', 'Dodong Neil L. Barrientos', 'Motorpool Unit Head', 'Visayas State University', 'Baybay City Leyte', 'sent', '2025-08-19 03:26:04', '73rd PSME National Convention'),
(354, 'archieortega@yahoo.com', 'Engr.', 'ARSENIO MANALOTO ORTEGA', 'BUSINESS OWNER', '4K CONVINIENCE STORE/ 4K CAFETERIA STORE/SELF EMPLOYED', '1. TUGUEGARAO CITY - LIBAG SUR , MARALLAG\r\n2. TUGUEGARAO CITY - BAGGAY , RAPHAEL GENERAL HOSPITAL\r\n3. PENABLANCA ,CAGAYAN - DODAN ', 'sent', '2025-08-20 01:22:10', '73rd PSME National Convention'),
(355, 'floresrandy.m@gmail.com', 'Other', 'Engr. Paquito T. Moreno, Jr., CESO III', '0231', 'DENR', 'DENR Compound, Gibraltar, Baguio City', 'sent', '2025-08-20 03:24:02', '73rd PSME National Convention'),
(356, 'racal.ja@acenergy.com.ph', 'Engr.', 'Jayson A. Racal', 'Superintendent', 'Bulacan Power Generation Corporation', 'Norzagaray Bulacan', 'sent', '2025-08-20 07:09:47', '73rd PSME National Convention'),
(357, 'twinoaksplacewesttower@gmail.com', 'Engr.', 'Rolando M. Lagman', 'Property Officer', 'Twin Oaks Place Condominium Corporation', '750 Shaw Boulevard, Greenfield District, mandaluyong City', 'sent', '2025-08-20 09:40:15', '73rd PSME National Convention'),
(358, 'edralin_bsme@yahoo.com', 'Engr.', 'Edralin Labiyo Casilihan', 'Steam and Power Generation Manager', 'Cagayan Corn Products Corporation', 'Purok 6, Tablon, Cagayan De Oro City, Misamis Oriental', 'sent', '2025-08-20 12:48:54', '73rd PSME National Convention'),
(359, 'vpm@viktomar.com', 'Mr.', 'VICTOR P MARANAN', 'CEO', 'VIKTOMAR INDUSTRIAL SUPPLY AND SERVICES', 'VIKTOMAR SITE 717 CAIMINO ROAD BITUNGOL NORZAGARAY BULACAN', 'sent', '2025-08-21 01:42:06', '73rd PSME National Convention'),
(360, 'info@jachinboaz.com.ph', 'Mr.', 'Jeresty B. Ramillano', 'General Manager', 'Jachin-Boaz Corporation', 'Filsyn Compound, Brgy. Don Jose, Santa Rosa, Laguna', 'sent', '2025-08-21 01:48:33', '73rd PSME National Convention'),
(361, 'info@greenfutureinnovations.com', 'Ms.', 'Jomelyn P. Pilar', 'Human Resources Manager', 'Green Future Innovations Inc.', 'Barangay Sta. Filomena, San Mariano, Isabela 3332.', 'sent', '2025-08-21 01:51:49', '73rd PSME National Convention'),
(362, 'butch.suliano@sharepro.com.ph', 'Engr.', 'Butch Gabriel D. Suliano', 'Mechanical Design Manager', 'SharePro Inc. (A Filinvest Company)', '8F Vector One Bldg. Nortgate Cyberzone, Filinvest Corporate City, Alabang, Muntinlupa City', 'sent', '2025-08-21 03:18:35', '73rd PSME National Convention'),
(363, 'rean.borazon4317@gmail.com', 'Engr.', 'Rean Cem V. Borazon', 'Teaching', 'Central Bicol State University of Agriculture', 'Mantalisay, Libmanan, Camarines Sur', 'sent', '2025-08-21 06:47:13', '73rd PSME National Convention'),
(364, 'engrjufor@yahoo.com', 'Engr.', 'JUVENAL B. FORMACION', 'C,RFSED', 'BUREAU OF FIRE PROTECTION R1', 'BRGY. PATAC, STO TOMAS , LA UNION', 'sent', '2025-08-21 10:04:57', '73rd PSME National Convention'),
(365, 'naldypp2@gmail.com', 'Engr.', 'Naldy Pepito', 'Plant Manager', 'Donau Carbon Philippines', 'Brgy. Cogon El Salvador City, Misamis Oriental', 'sent', '2025-08-21 11:39:44', '73rd PSME National Convention'),
(366, 'shayeeekaroline@gmail.com', 'Engr.', 'SHAYE CAROLINE G. MANLUPIG', 'Faculty ', 'Tarlac State University ', 'Romulo Blvd. Brgy. San Vicente, Tarlac City, Tarlac', 'sent', '2025-08-21 13:16:51', '73rd PSME National Convention'),
(367, 'r11.ems@nia.gov.ph', 'Engr.', 'Ivan Jay B. Banlawi', 'Equipment Management Section Head', 'National Irrigation Administration Regional Office 11', 'Bolton Street, Davao City', 'sent', '2025-08-22 00:51:00', '73rd PSME National Convention'),
(368, 'romelmelanio@gmail.com', 'Engr.', 'Romel C. Melanio', 'Production Head', 'East Pacific Star Bottlers Phils., Inc.', '327 Prenza Highway, San Fermin, Cauayan City, Isabela 3305', 'sent', '2025-08-22 00:58:50', '73rd PSME National Convention'),
(369, 'kris.tolentino@smprime.com', 'Mr.', 'Kristianne Joy Tolentino', 'Technical Support Operations Assistant Manager', 'SM Prime Holdings Incorporated', 'Pasay City', 'sent', '2025-08-22 03:25:51', '73rd PSME National Convention'),
(370, 'nonoyandy11@gmail.com', 'Engr.', 'Andy Anthony L Alsola', 'Fire Officer 1 - Building Plan Evaluator / Fire Truck Operator', 'Bureau of Fire Protection - Mondragon Fire Station', 'Barangay Chitongco, Mondragon, Northern Samar', 'sent', '2025-08-22 11:12:06', '73rd PSME National Convention'),
(371, 'k.alox143@gmail.com', 'Engr.', 'Khalid S. Alos', 'Engineer 3/Engineer III', 'Provincial Government of Lanao Del sur', 'Marawi City, Lanao Del Sur', 'sent', '2025-08-22 15:41:26', '73rd PSME National Convention'),
(372, 'k.alox143@gmail.com', 'Engr.', 'Khalid Saudagar Alos', 'Engineer III', 'Provincial Government of Lanao Del sur', 'Marawi City, Lanao Del Sur', 'sent', '2025-08-22 15:44:53', '73rd PSME National Convention'),
(373, 'guiruelaalni@gmail.com', 'Engr.', 'Alni M. Guiruela ', 'Senior Mechanic ', 'Pearl Energy Philippines Operating Inc.', 'Mauban Quezon ', 'sent', '2025-08-23 06:56:20', '73rd PSME National Convention'),
(374, 'antonio.farparan38@gmail.com', 'Mr.', 'Antonio Jimenez Farparan', 'Junior Philippine Society of Mechanical Engineers Camarines Norte State College ', 'N/a', 'Purok 2 Brgy. Lugui, Labo, Camarines Norte ', 'sent', '2025-08-23 07:56:39', '73rd PSME National Convention'),
(375, 'jnsmarina@yahoo.com', 'Engr.', 'JOSE NELSON G.SOBREVEGA', 'Supervising MIDS', 'Maritime Industry Authority -Regional Office IV', 'Hinch Bldg.II, Apacible St., Brgy.10, Batangas City', 'sent', '2025-08-23 13:00:45', '73rd PSME National Convention'),
(376, 'cmreponte@up.edu.ph', 'Mr.', 'Christyl John M. Reponte', 'Laboratory Technician ', 'University of the Philippines Cebu', 'Lahug, Cebu City, Cebu', 'sent', '2025-08-23 14:02:16', '73rd PSME National Convention'),
(377, 'nelsonatienza0073@gmail.com', 'Engr.', 'Nelson', 'Storage Custodian', 'Heindrich Trading Corporation', 'Makati City, Philippines', 'sent', '2025-08-25 10:10:16', '73rd PSME National Convention'),
(378, 'joecambarijan@yahoo.com', 'Engr.', 'Josito C. Cambarijan', 'NA', 'NA', 'Barangay 37D, Purok 7, Trading, Boulevard, Davao City', 'sent', '2025-08-25 23:58:11', '73rd PSME National Convention'),
(379, 'millareslisther24@gmail.com', 'Mr.', 'Millares', 'Facility Engineer', 'Personal Collection Direct Selling Inc', 'Quezon City', 'sent', '2025-08-26 05:20:37', '73rd PSME National Convention'),
(380, 'mpamular@klagroup.com', 'Engr.', 'Mark Lawrence Pamular', 'Project Lead', 'KLA Group', 'Centennial, CO 80122 USA', 'sent', '2025-08-26 06:18:57', '73rd PSME National Convention'),
(381, 'angelita.valenzon@gmail.com', 'Engr.', 'Angelita H. Valenzon', 'Faculty of the College of Engineering and Technology', 'Ramon Magsaysay Memorial Colleges', 'Pioneer Avenue, General Santos City', 'sent', '2025-08-26 07:37:30', '73rd PSME National Convention'),
(382, 'fjputis1101@gmail.com', 'Engr.', 'Francis Jade A. Putis', 'Plant Engineer - Central Warehouse & Measuring', 'Goldilocks Bakeshop Inc.', '16F Greenfield Tower, Greenfield District, Mandaluyong City\r\n', 'sent', '2025-08-26 07:38:35', '73rd PSME National Convention'),
(383, 'milmaulas@gmail.com', 'Mr.', 'MILO L. MAULAS II', 'Senior TESD Specialist', 'TECHNICAL EDUCATION AND SKILLS DEVELOPMENT AUTHORITY XII PROVINCIAL TRAINING CENTER MALAPATAN', 'Tuyan Malapatan Sarangani Province', 'sent', '2025-08-26 07:40:30', '73rd PSME National Convention'),
(384, 'gavilan.cesar@gmail.com', 'Engr.', 'Cesar Gato Gavilan', 'Production Division Manager A', 'Puerto Princesa City Water District', 'South National Highway, Bargy Sta. Monica, Puerto Princesa City, Palawan, 5300', 'sent', '2025-08-26 08:46:08', '73rd PSME National Convention'),
(385, 'darwin.tenoriio@aboitizpower.com', 'Engr.', 'Darwin Tenorio', 'Assistant Vice President for MGP Facility  ', 'AP Renewables Inc.', 'Sitio Mahabang Parang Brgy. Limao Calauan Laguna 4012', 'sent', '2025-08-26 09:10:00', '73rd PSME National Convention'),
(386, 'erwin.hiwatig03@gmail.com', 'Engr.', 'ERWIN HIWATIG', 'Mechanical Engineer ', 'Department of Transportation ', '0067 Sitio Silangan Brgy. Corazon San Antonio Quezon', 'sent', '2025-08-26 23:50:55', '73rd PSME National Convention'),
(387, 'jezreelpena32@gmail.com', 'Engr.', 'Jezreel Yenyen D. PeÃ±a', 'Maintenance Planner', 'RFM Corp. - Flour Div', 'RFM Corp. Pioneer cor. Sheridna St , Mandaluyong City', 'sent', '2025-08-27 00:06:47', '73rd PSME National Convention'),
(388, 'jezreelpena32@gmail.com', 'Engr.', 'Jezreel Yenyen D. PeÃ±a', 'Maintenance Planner', 'RFM Corp. - Flour Div.', 'RFM Corp. Pioneer cor. Sheridan St., Mandaluyong City ', 'sent', '2025-08-27 00:08:13', '73rd PSME National Convention'),
(389, 'milmaulas@gmail.com', 'Mr.', 'MILO L. MAULAS II', 'Senior TESD Specialist', 'TESDA XII PTC MALAPATAN', 'TUYAN, MALAPATAN, SARANGANI PROVINCE', 'sent', '2025-08-27 01:12:42', '73rd PSME National Convention'),
(390, 'charita.garces@ccep.com', 'Engr.', 'Charita Garces', 'Supply Chain Excellence Leader', 'Coca-Cola Europacific Aboitiz Phil., Inc', 'Coca-Cola Davao Plant 1\r\nMcArthur Highway, Ulas, Pob. Talomo, Davao City', 'sent', '2025-08-27 01:34:24', '73rd PSME National Convention'),
(391, 'merks.engineering@gmail.com', 'Engr.', 'Kevin Mercado', 'Owner', 'Merks Engineering', 'General Santos City', 'sent', '2025-08-27 01:43:52', '73rd PSME National Convention'),
(392, 'mfagtanac@rmi.sanmiguel.com.ph', 'Engr.', 'Marione Fagtanac', 'Operations Engineer', 'SMC REPAIRS AND MAINTENANCE INC.', '100 E Rodriguez Jr Avenue, C5 Road, Brgy. Ugong, Pasig City 1604', 'sent', '2025-08-27 02:00:27', '73rd PSME National Convention'),
(393, 'engrjosh2018@gmail.com', 'Engr.', 'Joshua R. Sudayon', 'Technical Sales Engineer', 'LG Lopez Industrial Sales and Services Inc.', '379, National Highway, Brgy. Real, Calamba City, Laguna', 'sent', '2025-08-27 02:42:44', '73rd PSME National Convention'),
(394, 'junjun.laluna@doleintl.com', 'Mr.', 'Jun Jun Laluna', 'Production Head', 'DOLE Philippines Inc', 'Cannery Site, Polomolok South Cotabato', 'sent', '2025-08-27 03:30:45', '73rd PSME National Convention'),
(395, 'advillena@yahoo.com', 'Engr.', 'Arvil Gregorio D.Villena', 'Department Manager Admin', 'Batangas City Water District', 'Km.4 National Hi-way, Alangilan Batangas City', 'sent', '2025-08-27 06:25:11', '73rd PSME National Convention'),
(396, 'mdbmacapagal@gmail.com', 'Engr.', 'Mark Daniel B. Macapagal', 'Facilities Manager', 'SM Mart, Inc.', 'SM CUBAO Bldg. Socorro-Cubao, Quezon City', 'sent', '2025-08-27 06:33:46', '73rd PSME National Convention'),
(397, 'ogoyryan8@gmail.com', 'Engr.', 'Ryan B. Ogoy', 'Science Research Analyst ', 'ITDI-DOST', 'DOST compound Gen. Santos Ave,  Bicutan Taguig City ', 'sent', '2025-08-27 07:16:00', '73rd PSME National Convention'),
(398, 'gbctemenia@yahoo.com', 'Engr.', 'Gefford Brayan C. Temenia', 'Division Manager C', 'Sablayan Water District', 'Buenavista, Sablayan, Occ.Mindoro', 'sent', '2025-08-27 07:18:10', '73rd PSME National Convention'),
(399, 'ermin_orendain@yahoo.com', 'Engr.', 'Ermin S. Orendain', 'Senior Science Research specialist ', 'ITDI-DOST', 'Dost Compound, General Santos Avenue, Bicutan Taguig City', 'sent', '2025-08-27 07:18:17', '73rd PSME National Convention'),
(400, 'ragpedroso@meralco.com.ph', 'Engr.', 'Roy anthony pedroso', 'Head contract management', 'Meralco', '2f b&g, meralco center, ortigas ave, pasig city', 'sent', '2025-08-27 07:27:03', '73rd PSME National Convention'),
(401, 'ragpedroso@meralco.com.ph', 'Engr.', 'Roy anthony pedroso', 'Head contract management', 'Meralco', '2f b&g, meralco center, ortigas ave, pasig city', 'sent', '2025-08-27 07:29:13', '73rd PSME National Convention'),
(402, 'ragpedroso@meralco.com.ph', 'Engr.', 'Roy anthony pedroso', 'Head contract management', 'Meralco', '2f b&g, meralco center, ortigas ave, pasig city', 'sent', '2025-08-27 07:29:45', '73rd PSME National Convention'),
(403, 'felanthonyramiso@yahoo.com', 'Engr.', 'Fel Anthony Ramiso ', 'General Services Department Head', 'Pacific Nickel Philippines Inc ', 'Brgy Talisay, Nonoc Island, Surigao City ', 'sent', '2025-08-27 10:08:43', '73rd PSME National Convention'),
(404, 'alfredopanuelajr@gmail.com', 'Engr.', 'Alfredo V. Panuela Jr.', 'Assistant Regional Director', 'TESDA Region 12 , Sarangani Province', 'TESDA, Provincial Office, GSNSAT Compound, Lagao, General Santos Coty', 'sent', '2025-08-27 10:59:22', '73rd PSME National Convention'),
(405, 'natconchair@psmeinc.org.ph', 'Mr.', 'Jeresty B. Ramillano', 'General Manager', 'Jachin-Boaz Corporation', 'Filsyn Compound, Don Jose, Santa Rosa, Laguna, 4026', 'sent', '2025-08-27 13:23:50', '73rd PSME National Convention'),
(406, 'andres.mayol@dlsu.edu.ph', 'Dr.', 'Andres Philip Mayol', 'Associate Professor', 'De La Salle University', '2401 Taft Avenue, Manila 0922, Philippines', 'sent', '2025-08-27 13:49:19', '73rd PSME National Convention'),
(407, 'andrey.mayol@aboitizpower.com', 'Engr.', 'Andrey Mayol', 'Maintenance supervisor ', 'Therma Marine Inc', 'Old VECO compound,  Bry Ermita, Cebu City', 'sent', '2025-08-27 14:16:37', '73rd PSME National Convention'),
(408, 'ledesmalvinsalgado1991@gmail.com', 'Engr.', 'ALVIN SALGADO LEDESMA', 'Project Superintendent ', 'Dole Philippines Inc.', 'Brgy. Cannery Site, Polomolok, South Cotabato, Philippines', 'sent', '2025-08-28 02:42:33', '73rd PSME National Convention'),
(409, 'aguilarjm@bsp.gov.ph', 'Mr.', 'Joseph M. Aguilar', 'Senior Production Supervisor', 'Bangko Sentral ng Pilipinas', 'Security Plant Complex East Ave. Diliman Quezon City', 'sent', '2025-08-28 02:46:51', '73rd PSME National Convention'),
(410, 'leemarquez1956@gmail.com', 'Mr.', 'Lee Abellana Marquez', 'Technical Consultant ', 'Syvorgz Finn Engineering  Services ', 'Tuyan, Naga, Cebu City', 'sent', '2025-08-28 03:15:27', '73rd PSME National Convention'),
(411, 'omd@psmeinc.org.ph', 'Ms.', 'Shiela Irlandez', 'OMD Head', 'PSME', 'null', 'sent', '2025-08-28 06:05:51', '73rd PSME National Convention'),
(412, 'jbescoto7@gmail.com', 'Engr.', 'Jeffrey B. Escoto', 'VP Supply Chain Mgmt & Technical Services/COO CDTN Services Inc/Head of Operations for NAC Diesel Power Plant', 'Nickel Asia Corporation', '29th Floor NAC Tower 32nd St BGC Taguig City MM', 'sent', '2025-08-28 07:32:05', '73rd PSME National Convention'),
(413, 'ppcbuildingofficial@gmail.com', 'Engr.', 'Neil Kenneth P. Guinto ', 'Engineer II ', 'Office of the City Building Official', 'New Green Cityhall, Brgy Sta Monica, Puerto Princesa City, Palawan, 5300', 'sent', '2025-08-28 11:09:07', '73rd PSME National Convention'),
(414, 'joenel_gallego@yahoo.com', 'Engr.', 'Joenel L. Gallego', 'n/a', 'Free Lance', 'Lo', 'sent', '2025-08-28 13:12:12', '73rd PSME National Convention'),
(415, 'roderico.tagaan@yahoo.com', 'Engr.', 'Roderico Taga-an', 'CGDH II ', 'Lapu-Lapu City Government', 'Pusok Hi-way, Lapu- Lapu City', 'sent', '2025-08-28 22:41:33', '73rd PSME National Convention'),
(416, 'romeo.mgbr2@yahoo.com', 'Engr.', 'Romeo O. Watchorna, Jr.', 'Sr. Science Reasearch Specialist', 'Mines & Geosciences Bureau, RO2', 'Carig Sur, Tuguegarao city', 'sent', '2025-08-28 23:14:23', '73rd PSME National Convention'),
(417, 'avashara15@gmail.com', 'Engr.', 'Yasmin Ava Shara R. Walanda', 'Engineer II', 'Mindanao State University', '3rd St. MSU Campus, Marawi City', 'sent', '2025-08-29 00:46:07', '73rd PSME National Convention'),
(418, 'avashara15@gmail.com', 'Engr.', 'Yasmin Ava Shara Walanda', 'Engineer II', 'Mindanao State University', 'Purok Orchids, Brgy. San Roque', 'sent', '2025-08-29 00:51:52', '73rd PSME National Convention'),
(419, 'gcgmmcmcco@gmail.com', 'Engr.', 'JOHN MELCHOR A. NAMOC', 'Engineer III', 'Gov. Celestino Gallares Memorial Medical Center', 'Miguel Parras St., Tagbilaran City, Bohol', 'sent', '2025-08-29 01:52:54', '73rd PSME National Convention'),
(420, 'joselito_olalo@yahoo.com', 'Dr.', 'Edgar P. Aban', 'OIC President', 'Camarines Norte State College', 'F. Pimentel Avenue Daet Camarines Norte', 'sent', '2025-08-29 02:05:44', '73rd PSME National Convention'),
(421, 'aldwinmendoza6969@gmail.com', 'Engr.', 'Aldwin Kylle S. Mendoza', 'Process Maintenance Engineer', 'Purefoods - Hormel Company, Inc.', 'Bo. De Fuego General Trias, Cavite', 'sent', '2025-08-29 02:10:03', '73rd PSME National Convention'),
(422, 'janjobeth.penascosas24@gmail.com', 'Engr.', 'Jan Jobeth P. PeÃ±ascosas ', 'Process Maintenance Engineer', 'Purefoods-Hormel Corp. Inc.', 'Brgy. De Fuego, Gen. Trias, Cavite', 'sent', '2025-08-29 02:19:42', '73rd PSME National Convention'),
(423, 'rcquiambao@gmail.com', 'Engr.', 'Ryan Quiambao', 'Engineer IV', 'Jose R. Reyes Memorial Medical Center', 'Rizal Ave., Sta Cruz, Manila ', 'sent', '2025-08-29 03:20:58', '73rd PSME National Convention'),
(424, 'samradiamoda14@gmail.com', 'Engr.', 'Saminoding A. Radiamoda', 'Engineer II', 'MPW-BARMM, Lanao del Sur 1st District Engineering Office', 'Brgy. Matampay, Marawi City, Lanao del Sur', 'sent', '2025-08-29 03:23:45', '73rd PSME National Convention'),
(425, 'mary.cansicio@goldilocks.com', 'Ms.', 'Mary Joy Evangeline G. Cansicio', 'Learning & Development Officer - Human Resources', 'Goldilocks Bakeshop Inc.', 'Greenfield Tower\r\nMayflower Street cor Williams Street, Greenfield District, Mandaluyong City', 'sent', '2025-08-29 06:07:28', '73rd PSME National Convention'),
(426, 'abm71@yahoo.com', 'Engr.', 'Aminoden B. Malawad', 'Chief, Equipment Management Section', 'MPW-BARMM, Lana Del Sur 2nd DEO', 'Matling, Malabang, Lanao Del Sur', 'sent', '2025-08-29 06:09:19', '73rd PSME National Convention'),
(427, 'dennisbigay@gmail.com', 'Engr.', 'Dennis A. Bigay', 'Proprietor', 'MD-DAB-ES', '72 Saint Luke Street, San Paulo Village, Novaliches', 'sent', '2025-08-29 07:11:57', '73rd PSME National Convention'),
(428, 'abdulhakimsambitory@gmail.com', 'Engr.', 'ABDULHAKIM MACUD SAMBITORY', 'Engineer I', 'MPW-BARMM, Lanao del Sur 1st District Engineering Office', 'Brgy. Matampay, Marawi City, Lanao Del Sur', 'sent', '2025-08-29 10:28:53', '73rd PSME National Convention');

-- --------------------------------------------------------

--
-- Table structure for table `soa_sequence`
--

CREATE TABLE `soa_sequence` (
  `id` int(11) NOT NULL,
  `soa_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soa_sequence`
--

INSERT INTO `soa_sequence` (`id`, `soa_number`, `created_at`) VALUES
(92, 'NATCON25-059', '2025-08-06 06:24:39'),
(94, 'NATCON25-060', '2025-08-06 07:39:59'),
(95, 'NATCON25-061', '2025-08-29 12:49:35'),
(96, 'NATCON25-062', '2025-08-29 12:54:31'),
(97, 'NATCON25-063', '2025-08-29 12:56:18'),
(98, 'NATCON25-064', '2025-08-29 13:04:47'),
(99, 'NATCON25-065', '2025-08-29 13:06:31'),
(100, 'NATCON25-066', '2025-08-29 13:08:48'),
(101, 'NATCON25-067', '2025-08-29 13:10:13'),
(102, 'NATCON25-068', '2025-08-29 13:10:37'),
(103, 'NATCON25-069', '2025-08-29 13:11:43'),
(104, 'NATCON25-070', '2025-08-29 13:11:47'),
(105, 'NATCON25-071', '2025-08-29 13:11:56'),
(106, 'NATCON25-072', '2025-08-29 13:12:35'),
(107, 'NATCON25-073', '2025-08-29 13:12:59'),
(108, 'NATCON25-074', '2025-08-29 13:13:18'),
(109, 'NATCON25-075', '2025-08-29 13:15:04'),
(110, 'NATCON25-076', '2025-08-29 13:17:46'),
(111, 'NATCON25-077', '2025-08-29 13:18:03'),
(112, 'NATCON25-078', '2025-08-29 13:18:07'),
(113, 'NATCON25-079', '2025-08-29 13:18:27'),
(114, 'NATCON25-080', '2025-08-29 13:18:49'),
(115, 'NATCON25-081', '2025-08-29 13:21:36'),
(116, 'NATCON25-082', '2025-08-29 13:25:33'),
(117, 'NATCON25-083', '2025-08-29 13:27:44'),
(118, 'NATCON25-084', '2025-08-29 13:31:22'),
(119, 'NATCON25-085', '2025-08-29 13:32:21'),
(120, 'NATCON25-086', '2025-08-29 13:32:29'),
(121, 'NATCON25-087', '2025-08-29 13:35:27'),
(122, 'NATCON25-088', '2025-08-29 13:35:40'),
(123, 'NATCON25-089', '2025-08-29 13:35:59'),
(124, 'NATCON25-090', '2025-08-29 13:36:16'),
(125, 'NATCON25-091', '2025-08-29 13:36:21'),
(126, 'NATCON25-092', '2025-08-29 13:37:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `delegates`
--
ALTER TABLE `delegates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soa_sequence`
--
ALTER TABLE `soa_sequence`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `delegates`
--
ALTER TABLE `delegates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=429;

--
-- AUTO_INCREMENT for table `soa_sequence`
--
ALTER TABLE `soa_sequence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delegates`
--
ALTER TABLE `delegates`
  ADD CONSTRAINT `delegates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
