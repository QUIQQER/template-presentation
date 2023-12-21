SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
                         `id` bigint NOT NULL,
                         `title` text,
                         `workingTitle` text,
                         `data` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

INSERT INTO `menus` (`id`, `title`, `workingTitle`, `data`) VALUES
                                                                (1, '{\"de\":\"Footer - Services\",\"en\":\"\"}', '{\"de\":\"\",\"en\":\"\"}', '{\"children\":[{\"title\":false,\"identifier\":\"01e9e4a6-9824-11ee-8908-0242a99e5b09\",\"data\":{\"site\":\"${siteurl:gallery}\",\"target\":\"\",\"menuType\":\"\",\"status\":1,\"rel\":\"\"},\"icon\":false,\"type\":\"QUI\\\\Menu\\\\Independent\\\\Items\\\\Site\"},{\"title\":false,\"identifier\":\"01e9e6f4-9824-11ee-bc07-0242a99e5b09\",\"data\":{\"site\":\"${siteurl:search}\",\"target\":\"\",\"menuType\":\"\",\"status\":1,\"rel\":\"\"},\"icon\":false,\"type\":\"QUI\\\\Menu\\\\Independent\\\\Items\\\\Site\"}]}'),
                                                                (2, '{\"de\":\"Footer - Rechtliches\",\"en\":\"\"}', '{\"de\":\"\",\"en\":\"\"}', '{\"children\":[{\"title\":false,\"identifier\":\"7dfdc66c-9828-11ee-b3bb-0242a99e5b09\",\"data\":{\"site\":\"${siteurl:legalNotice}\",\"target\":\"\",\"menuType\":\"\",\"status\":1,\"rel\":\"\"},\"icon\":false,\"type\":\"QUI\\\\Menu\\\\Independent\\\\Items\\\\Site\"},{\"title\":false,\"identifier\":\"7dfdc8b0-9828-11ee-a5bb-0242a99e5b09\",\"data\":{\"site\":\"${siteurl:privacyPolicy}\",\"target\":\"\",\"menuType\":\"\",\"status\":1,\"rel\":\"\"},\"icon\":false,\"type\":\"QUI\\\\Menu\\\\Independent\\\\Items\\\\Site\"},{\"title\":false,\"identifier\":\"7dfdcc16-9828-11ee-a99f-0242a99e5b09\",\"data\":{\"site\":\"${siteurl:generalTermsAndConditions}\",\"target\":\"\",\"menuType\":\"\",\"status\":1,\"rel\":\"\"},\"icon\":false,\"type\":\"QUI\\\\Menu\\\\Independent\\\\Items\\\\Site\"}]}');


ALTER TABLE `menus`
    ADD PRIMARY KEY (`id`);


ALTER TABLE `menus`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
