--
-- Table structure for table `transport_account`
--

CREATE TABLE IF NOT EXISTS `#__transport_account` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `contact_number` bigint(15),
  `payment_details` varchar(600),
  `address` varchar(700),
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_transaction`
--

CREATE TABLE IF NOT EXISTS `#__transport_transaction` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `debit` DOUBLE(11,2) NOT NULL,
  `credit` DOUBLE(11,2) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `description` varchar(600),
  `date` date NOT NULL DEFAULT '0000-00-00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_vehicle`
--

CREATE TABLE IF NOT EXISTS `#__transport_vehicle` (
  `id` int(11) NOT NULL auto_increment,
  `registration_number` varchar(12) NOT NULL,
  `owner_name` varchar(100),
  `owner_contact` bigint(15),
  `driver_name` varchar(100),
  `driver_contact` bigint(15),
  `chakka` int(2),
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_chalan`
--

CREATE TABLE IF NOT EXISTS `#__transport_chalan` (
  `id` int(11) NOT NULL auto_increment,
  `vehicle_id` int(11) NOT NULL,
  `party_name` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `advance` DOUBLE(11,2),
  `total_freight` DOUBLE(11,2) NOT NULL,
  `drivers_contact` bigint(15) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_chalan_item`
--

CREATE TABLE IF NOT EXISTS `#__transport_chalan_item` (
  `id` int(11) NOT NULL auto_increment,
  `chalan_id` int(11) NOT NULL,
  `sender_party` int(11) NOT NULL,
  `receiver_party` int(11) NOT NULL,
  `trade_mark` varchar(100) NOT NULL,
  `weight` DOUBLE(5,2) NOT NULL,
  `units` DOUBLE(5,2) NOT NULL,
  `freight` DOUBLE(5,2) NOT NULL,
  `inam` DOUBLE(5,2) NOT NULL,
  `billt_paid_id` int(11),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_billt`
--

CREATE TABLE IF NOT EXISTS `#__transport_billt` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `vehicle_id` int(11) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `units` DOUBLE(5,2) NOT NULL,
  `weight` DOUBLE(5,2) NOT NULL,
  `freight` DOUBLE(5,2) NOT NULL,
  `advance` DOUBLE(5,2) NOT NULL,
  `remarks` varchar(100),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_billt_paid`
--

CREATE TABLE IF NOT EXISTS `#__transport_billt_paid` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` int(11) NOT NULL,
  `chalan_id` int(11) NOT NULL,
  `chalan_itemid` int(11) NOT NULL,
  `amount` DOUBLE(5,2) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `transport_freight`
--

CREATE TABLE IF NOT EXISTS `#__transport_freight` (
  `destination` varchar(100) NOT NULL,
  `box_weight` text NOT NULL,
  PRIMARY KEY  (`destination`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
