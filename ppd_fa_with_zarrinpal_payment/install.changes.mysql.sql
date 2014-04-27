ALTER TABLE `#__payperdownloadplus_licenses`
	ADD COLUMN `max_download` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__payperdownloadplus_licenses`
	ADD COLUMN `license_image` varchar(255) NULL;
ALTER TABLE `#__payperdownloadplus_licenses`
	ADD COLUMN `aup` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__payperdownloadplus_licenses`
	ADD COLUMN `renew` int(11) DEFAULT '0' /*0 : always renew, 1: only if not active, 2: never*/; 
ALTER TABLE `#__payperdownloadplus_licenses`
	ADD COLUMN `user_group` int(11) NULL;

ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `expiration_date` datetime NULL;
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `creation_date` datetime NULL;
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `download_link` varchar(256) NULL;
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `download_hits` int(11) DEFAULT '0';
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `link_max_downloads` int(11) DEFAULT '0';
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `payed` int(11) NOT NULL DEFAULT 1;
	
ALTER TABLE `#__payperdownloadplus_resource_licenses`
	ADD COLUMN `shared` int(11) DEFAULT '1';
ALTER TABLE `#__payperdownloadplus_resource_licenses`
	ADD COLUMN `max_download` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__payperdownloadplus_resource_licenses`
	ADD COLUMN `payment_header` text;
	
ALTER TABLE `#__payperdownloadplus_users_licenses`
	ADD COLUMN `download_hits` int(11) DEFAULT '0';
 ALTER TABLE `#__payperdownloadplus_users_licenses`
	ADD COLUMN `license_max_downloads` int(11) DEFAULT '0';
ALTER TABLE `#__payperdownloadplus_users_licenses`
	ADD COLUMN `assigned_user_group` int(11) NULL;
ALTER TABLE `#__payperdownloadplus_users_licenses`
	ADD KEY expiration_date (expiration_date);
ALTER TABLE `#__payperdownloadplus_payments`
	ADD KEY `payment_date` (`payment_date`);

ALTER TABLE `#__payperdownloadplus_download_links`
	ADD COLUMN `user_id` int(11) NULL DEFAULT 0;
