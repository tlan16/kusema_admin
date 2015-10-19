TRUNCATE TABLE `useraccount`;
INSERT INTO `useraccount` (`id`, `username`, `password`, `personId`, `source`, `refId`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(10, '075ae3d2fc31640504f814f60e5ef713', 'disabled', 10, NULL, '5624440f3f2f5c83422798bf', 1, '2014-03-06 19:47:35', 10, '2015-10-18 14:14:57', 10),
(24, 'frank', 'b85b2c37a170c7fa6100dcca17ba66d370207744', 24, NULL, '5624429c3f2f5c83422798be', 1, '2014-12-20 13:23:44', 10, '2015-10-18 14:08:46', 10),
(25, 'testuser', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 2514, NULL, NULL, 1, '2015-09-03 10:43:17', 10, '2015-10-19 01:07:34', 10);