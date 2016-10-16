CREATE TABLE IF NOT EXISTS `Tattler` (
  `Created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UserToken` varchar(36) NOT NULL,
  `Channel` varchar(255) NOT NULL,
  PRIMARY KEY (`UserToken`,`Channel`),
  KEY `Modified` (`Modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;