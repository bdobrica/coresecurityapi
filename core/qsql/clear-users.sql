DELETE FROM `ca_users` WHERE id>1;
ALTER TABLE `ca_users` AUTO_INCREMENT=1;
DELETE FROM `ca_usermeta` WHERE user_id>1;
ALTER TABLE `ca_usermeta` AUTO_INCREMENT=36;
