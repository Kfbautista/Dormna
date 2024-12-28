TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER UpdateExpiryDateSync\nAFTER UPDATE ON `payment-transaction-tb`\nFOR EACH ROW\nBEGIN\n    IF NEW.expiry_date <> OLD.expiry_date THEN\n        UPDATE `property-info`\n        SET expiry_date = NEW.expiry_date\n        WHERE PID = NEW.PID;\n    END IF;\nEND'
sql_modes=1098907648
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_unicode_ci'
db_cl_names='utf8mb4_general_ci'
created=1713966385520576
