CREATE TABLE `access_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `licenses` (
  `id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `hwid` varchar(255) DEFAULT NULL,
  `ip` varchar(255) NOT NULL,
  `status` int(11) DEFAULT 0,
  `discord_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `license_notes` (
  `id` int(11) NOT NULL,
  `license_code` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `access_tokens`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `licenses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `license_notes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `access_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `licenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `license_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;