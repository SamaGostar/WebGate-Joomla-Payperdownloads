ALTER TABLE `#__payperdownloadplus_resource_licenses`
	ADD CONSTRAINT `#__payperdownloadplus_file_licenses_ibfk_1` FOREIGN KEY (`license_id`) 
		REFERENCES `#__payperdownloadplus_licenses` (`license_id`);	  
	
ALTER TABLE `#__payperdownloadplus_users_licenses`
	ADD CONSTRAINT `#__payperdownloadplus_users_licenses_ibfk_1` FOREIGN KEY (`license_id`) 
		REFERENCES `#__payperdownloadplus_licenses` (`license_id`);	
		
ALTER TABLE `#__payperdownloadplus_download_links`
	ADD CONSTRAINT `#__payperdownloadplus_download_links_ibfk_1` FOREIGN KEY (`resource_id`) 
		REFERENCES `#__payperdownloadplus_resource_licenses` (`resource_license_id`) ON DELETE CASCADE;	

ALTER TABLE `#__payperdownloadplus_affiliates_programs`
	ADD CONSTRAINT `#__payperdownloadplus_affiliates_programs_ibfk_1` FOREIGN KEY (`license_id`) 
		REFERENCES `#__payperdownloadplus_licenses` (`license_id`);	
		
ALTER TABLE `#__payperdownloadplus_affiliates_users`
	ADD CONSTRAINT `#__payperdownloadplus_affiliates_users_ibfk_1` FOREIGN KEY (`affiliate_program_id`) 
		REFERENCES `#__payperdownloadplus_affiliates_programs` (`affiliate_program_id`);	
		
ALTER TABLE `#__payperdownloadplus_affiliates_banners`
	ADD CONSTRAINT `#__payperdownloadplus_affiliates_banners_ibfk_1` FOREIGN KEY (`affiliate_program_id`) 
		REFERENCES `#__payperdownloadplus_affiliates_programs` (`affiliate_program_id`);
		