-- phpMyAdmin SQL Dump
-- version 3.3.5.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost:3351
-- Genereertijd: 19 Sept 2012 om 20:38
-- Serverversie: 5.1.48
-- PHP-Versie: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sbodebrug`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `active` int(1) DEFAULT NULL,
  `parent` int(10) NOT NULL DEFAULT '0' COMMENT 'System crucial field',
  `folder` int(1) DEFAULT NULL COMMENT 'System crucial field',
  `objecttype_id` int(10) NOT NULL COMMENT 'System crucial field',
  `code` varchar(255) DEFAULT NULL COMMENT 'System crucial field',
  `title` varchar(255) DEFAULT NULL COMMENT 'System crucial field',
  `url` varchar(255) DEFAULT NULL COMMENT 'System crucial field',
  `sort` int(10) NOT NULL COMMENT 'System crucial field',
  `content` text COMMENT 'custom field',
  `integer1` int(10) DEFAULT NULL COMMENT 'custom field',
  `integer2` int(10) DEFAULT NULL COMMENT 'custom field',
  `integer3` int(10) DEFAULT NULL COMMENT 'custom field',
  `string1` varchar(255) DEFAULT NULL COMMENT 'custom field',
  `string2` varchar(255) DEFAULT NULL COMMENT 'custom field',
  `string3` varchar(255) DEFAULT NULL COMMENT 'custom field',
  `datetime1` datetime DEFAULT NULL COMMENT 'custom field',
  `datetime2` datetime DEFAULT NULL COMMENT 'custom field',
  `datetime3` datetime DEFAULT NULL COMMENT 'custom field',
  `text1` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `url` (`url`),
  KEY `FK_content_objecttype` (`objecttype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='the is a content table' AUTO_INCREMENT=328 ;

--
-- Gegevens worden uitgevoerd voor tabel `content`
--

INSERT INTO `content` (`id`, `active`, `parent`, `folder`, `objecttype_id`, `code`, `title`, `url`, `sort`, `content`, `integer1`, `integer2`, `integer3`, `string1`, `string2`, `string3`, `datetime1`, `datetime2`, `datetime3`, `text1`) VALUES
(1, 1, 0, 1, 9, 'ROOT', 'Content', 'url', 2, 'content', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 1, 1, 1, 9, 'DATA', 'Data', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 1, 1, 1, 9, 'CONTENT', 'Pagina''s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 1, 201, NULL, 7, 'FOTOSET1', 'Fotoset 1', '/foto_s/fotoset_1', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 1, 324, NULL, 12, 'REGISTER_TEXT', 'Registreren', NULL, 3, 'Hier kunt u een account aanvragen.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 1, 325, 1, 13, NULL, 'Frankrijk onderzoekt dood Arafat ', '/nieuws1', 0, '<p>Het verhaal gaat dat de Palestijnse leider is vergiftigd. De Palestijnen verdenken Isra&euml;l daar achter te zitten.</p>\r\n<p>De openbaar aanklager in Nanterre, een voorstad van Parijs, komt in actie na aangifte van de weduwe van Arafat, Suha vorige maand. Tegen wie de aangifte is gedaan, is niet bekend.</p>\r\n<center class="articlebodyad"></center>\r\n<p>Een laboratorium in Zwitserland ontdekte onlangs verhoogde waarden van het radioactieve polonium op spullen van Arafat. Het lab heeft inmiddels toestemming van de weduwe en de Palestijnse autoriteiten om het stoffelijk overschot van Arafat te onderzoeken.</p>\r\n<p>De Palestijnse Autoriteit is tevreden dat er een officieel onderzoek komt naar de dood van Arafat. Volgens een woordvoerder heeft de Palestijnse president Mahmoud Abbas zijn Franse collega Fran&ccedil;ois Hollande gevraagd te helpen bij het onderzoek naar de omstandigheden waaronder Arafat overleed. ''''We zullen de volle waarheid en wie hier achter zit achterhalen.''''</p>', NULL, NULL, NULL, NULL, NULL, NULL, '2012-08-24 03:23:00', '2012-08-30 04:29:00', NULL, 'Palestijnse president stierf in 2004 onder onduidelijke omstandigheden.'),
(126, 1, 324, NULL, 12, 'USER_REG_MAIL_TEMPLATE', 'User Register Mail Template', NULL, 2, 'Hallo, \r\n\r\nEr is een nieuwe account op de website aangemaakt. Klik <a href="%url%">hier</a> om er iets mee te doen :) \r\n\r\nGreets,\r\nThe System', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 1, 324, NULL, 12, 'ADMIN_MAIL_ADRES', 'Admin mail adres', NULL, 1, 'jonathan@vanrij.org', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 1, 324, NULL, 12, 'USER_REG_MAIL_TITLE', 'User Register Mail Title', NULL, 4, 'Nieuwe gebruiker op de website', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 1, 95, NULL, 6, NULL, 'IMG_8705', '/fotoset_1/img_8705', 1346013602, NULL, NULL, NULL, NULL, '{"name":"IMG_8705.JPG","type":"JPG","uid":"174_20120826_503a89a257d1f","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(175, 1, 95, NULL, 6, NULL, 'IMG_8721', '/fotoset_1/img_8721', 0, NULL, NULL, NULL, NULL, '{"name":"IMG_8721.JPG","type":"JPG","uid":"175_20120826_503a89a267413","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(176, 1, 95, NULL, 6, NULL, 'IMG_8722', '/fotoset_1/img_8722', 4, NULL, NULL, NULL, NULL, '{"name":"IMG_8722.JPG","type":"JPG","uid":"176_20120826_503a89a2750d1","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(177, 1, 95, NULL, 6, NULL, 'IMG_8771', '/fotoset_1/img_8771', 2, NULL, NULL, NULL, NULL, '{"name":"IMG_8771.JPG","type":"JPG","uid":"177_20120826_503a89a2848da","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(178, 1, 95, 1, 6, NULL, 'IMG_8787', '/fotoset_1/img_8787', 1, NULL, NULL, NULL, NULL, '{"name":"IMG_8787.JPG","type":"JPG","uid":"178_20120826_503a89a28d866","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(179, 1, 95, NULL, 6, NULL, 'IMG_8618', '/fotoset_1/img_8618', 1346014297, NULL, NULL, NULL, NULL, '{"name":"IMG_8618.JPG","type":"JPG","uid":"179_20120826_503a8c59a8a3a","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(180, 1, 95, NULL, 6, NULL, 'IMG_8619', '/fotoset_1/img_8619', 1346014297, NULL, NULL, NULL, NULL, '{"name":"IMG_8619.JPG","type":"JPG","uid":"180_20120826_503a8c59adba5","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(181, 1, 95, NULL, 6, NULL, 'IMG_8620', '/fotoset_1/img_8620', 1346014297, NULL, NULL, NULL, NULL, '{"name":"IMG_8620.JPG","type":"JPG","uid":"181_20120826_503a8c59b2d0b","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(182, 1, 95, 1, 6, NULL, 'IMG_8621', '/fotoset_1/img_8621', 5, NULL, NULL, NULL, NULL, '{"name":"IMG_8621.JPG","type":"JPG","uid":"182_20120826_503a8c59b94dc","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(183, 1, 95, NULL, 6, NULL, 'IMG_8622', '/fotoset_1/img_8622', 0, NULL, NULL, NULL, NULL, '{"name":"IMG_8622.JPG","type":"JPG","uid":"183_20120826_503a8c59be380","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(184, 1, 95, NULL, 6, NULL, 'IMG_8623', '/fotoset_1/img_8623', 3, NULL, NULL, NULL, NULL, '{"name":"IMG_8623.JPG","type":"JPG","uid":"184_20120826_503a8c59c3e5e","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(201, 1, 80, 1, 3, NULL, 'Foto''s', '/foto_s', 1, '<p>Hier vindt u verschillende foto sets.</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 0, 0, 1, 7, NULL, 'Mass Effect 3', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 0, 0, 1, 7, NULL, 'Wallpapers', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 1, 307, 1, 3, NULL, 'Algemeen', '/algemeen', 1, '<p>Dit is algemene content!</p>\r\n<p><a href="/file/index/id/322/field/string1">linkje</a></p>\r\n<p><img title="Back to the future/wallpapers12mei10.jpg" src="/file/index/id/300/field/string1" alt="Back to the future/wallpapers12mei10.jpg" width="337" height="210" /></p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 1, 307, 1, 3, NULL, 'Contact', '/algemeen/contact', 3, '<p>Dit is de contact pagina</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 1, 307, 1, 3, NULL, 'De omgeving', '/algemeen/de_omgeving', 2, '<p>xdfg</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 0, 0, 1, 7, NULL, 'Dierentuin', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 1, 325, NULL, 13, NULL, 'Duitse studenten uit de kleren voor extra leraren', '/Duitse_studenten_uit_de_kleren_voor_extra_leraren', 1, '<p>Aan de sportfaculteit heerst een enorm tekort aan docenten. De opleiding telt 1000 studenten, die moeten met zijn allen vier leraren delen.</p>\r\n<p><img title="Naakt/naakt.jpg" src="/file/index/id/305/field/string1" alt="Naakt/naakt.jpg" width="500" height="252" /></p>\r\n<p>Dat kan zo niet langer, vinden de leerlingen, dus hebben ze een ludieke idee opgevat om meer leraren aan de opleiding te binden. De jonge mensen staan naakt op een <a href="http://www.wtf.nl/grenzeloos/4190/uit-de-kleren-voor-een-diploma-fotoserie.html" target="_blank">kalender</a>, die voor 39,90 euro te koop is. Van de opbrengst moeten extra leerkrachten worden betaald.</p>\r\n<center class="articlebodyad"></center>\r\n<p>"Er staat 3 jaar voor het halen van je bachelor. Slechts 20 procent van ons haalt dat", klaagt de 23-jarige Karo Kaiser tegen <a href="http://www.bild.de/regional/berlin/aktfotografie/nackte-studenten-25900140.bild.html" target="_blank">Bild</a>. ''''Door het tekort aan docenten zijn er te weinig plekken in de klas.''''</p>\r\n<p>De wachtlijsten zijn zo lang dat veel studenten een jaar tot anderhalf jaar langer over hun opleiding doen dan ze gepland hadden.</p>', NULL, NULL, NULL, NULL, NULL, NULL, '2012-08-26 00:00:00', '2012-08-31 00:24:00', NULL, 'AMSTERDAM - Een groep van 24 studenten van de universiteit in Berlijn is uit de kleren gegaan om aandacht te vragen voor het lerarentekort. '),
(257, 1, 325, NULL, 13, NULL, 'SP wil ''illegaal'' downloaden afkopen ', '/SP_wil__illegaal__downloaden_afkopen_', 2, '<p>Dat stelt de partij in het&nbsp;<a href="http://www.sp.nl/service/rapport/120823-ict-knooppunten-oplossingen.pdf" target="_blank">ICT-rapport</a> dat SP-kamerlid Sharon Gesthuizen heeft uitgebracht. Ze ziet niets in een downloadverbod dat volgens haar niet te handhaven is. Daarom moet er een kopieertoeslag komen op alle apparaten die gebruikt kunnen worden om muziek of films op te slaan of af te spelen.</p>\r\n<p>''''Er moet verdere uitwerking gegeven worden aan de plannen van onder andere de Consumentenbond om apparatenvergoeding over te laten gaan in een bredere internetvergoeding zodra meer dan 70 procent van de thuiskopie&euml;n van internet afkomstig is'''', meldt het rapport.</p>\r\n<center class="articlebodyad"></center>\r\n<p>''''Op de lange termijn zal de toekomst voor makers van muziek en film meer liggen in op een slimme manier gebruik maken van de mogelijkheden die internet en ICT in het algemeen bieden'''', stelt Gesthuizen in het rapport. ''''Voor veel makers is het net nu al een prachtig platform dat hen juist geld oplevert.''''</p>', NULL, NULL, NULL, NULL, NULL, NULL, '2012-08-08 00:00:00', '2012-11-22 01:00:00', NULL, 'DEN HAAG â€“ Als het aan de SP ligt blijft downloaden legaal. Er komt een tijdelijke heffing. Ook moet Nederland bij ernstige ICT-incidenten in het buitenland ingrijpen. '),
(299, 0, 0, 1, 7, NULL, 'Losse foto''s', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 0, 0, NULL, 6, NULL, 'Back to the future', NULL, 1, NULL, NULL, NULL, NULL, '{"name":"wallpapers12mei10.jpg","type":"jpg","uid":"300_20120828_503d1af40c5c1","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":false}', NULL, NULL, NULL, NULL, NULL, NULL),
(304, 1, 79, NULL, 9, NULL, 'Fotos', '/Fotos', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 1, 326, 1, 6, NULL, 'Naakt', '/Naakt', 0, NULL, NULL, NULL, NULL, '{"name":"naakt.jpg","type":"jpg","uid":"305_20120829_503d40b6ca70d","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":false}', NULL, NULL, NULL, NULL, NULL, NULL),
(307, 1, 80, 1, 3, NULL, 'Home', '/homepage', 1, '<p>dit is de homepage</p>\r\n<p><img title="annefleur/5207837050_0fe1fe0d50_o.jpg" src="/file/index/id/308/field/string1" alt="annefleur/5207837050_0fe1fe0d50_o.jpg" width="374" height="562" /></p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(308, 1, 305, NULL, 6, NULL, 'annefleur', '/annefleur', 0, NULL, NULL, NULL, NULL, '{"name":"5207837050_0fe1fe0d50_o.jpg","type":"jpg","uid":"308_20120901_5042049e6f871","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":false}', NULL, NULL, NULL, NULL, NULL, NULL),
(309, 1, 201, NULL, 7, 'STEVEN1', 'steven test', '/foto_s/steven_test', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 1, 309, NULL, 6, NULL, 'IMG_5539', '/foto_s/steven_test/img_5539', 1346504601, NULL, NULL, NULL, NULL, '{"name":"IMG_5539.JPG","type":"JPG","uid":"311_20120901_50420799b96b3","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(312, 1, 309, NULL, 6, NULL, 'IMG_5541', '/foto_s/steven_test/img_5541', 1346504601, NULL, NULL, NULL, NULL, '{"name":"IMG_5541.JPG","type":"JPG","uid":"312_20120901_50420799c01c7","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(313, 1, 309, NULL, 6, NULL, 'IMG_5542', '/foto_s/steven_test/img_5542', 1346504601, NULL, NULL, NULL, NULL, '{"name":"IMG_5542.JPG","type":"JPG","uid":"313_20120901_50420799c6ef5","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(314, 1, 309, NULL, 6, NULL, 'IMG_5543', '/foto_s/steven_test/img_5543', 1346504601, NULL, NULL, NULL, NULL, '{"name":"IMG_5543.JPG","type":"JPG","uid":"314_20120901_50420799cdc89","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(315, 1, 309, NULL, 6, NULL, 'IMG_6089', '/foto_s/steven_test/img_6089', 1346504601, NULL, NULL, NULL, NULL, '{"name":"IMG_6089.JPG","type":"JPG","uid":"315_20120901_50420799d675f","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(316, 1, 309, NULL, 6, NULL, 'portret_zw', '/foto_s/steven_test/portret_zw', 1346504601, NULL, NULL, NULL, NULL, '{"name":"portret_zw.jpg","type":"jpg","uid":"316_20120901_50420799ea029","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(317, 1, 309, NULL, 6, NULL, 'smart', '/foto_s/steven_test/smart', 1346504601, NULL, NULL, NULL, NULL, '{"name":"smart.jpg","type":"jpg","uid":"317_20120901_50420799f1add","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(318, 1, 309, NULL, 6, NULL, 'steven_icon', '/foto_s/steven_test/steven_icon', 1346504602, NULL, NULL, NULL, NULL, '{"name":"steven_icon.jpg","type":"jpg","uid":"318_20120901_5042079a0525a","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(319, 1, 309, NULL, 6, NULL, 'th_koran', '/foto_s/steven_test/th_koran', 1346504602, NULL, NULL, NULL, NULL, '{"name":"th_koran.jpg","type":"jpg","uid":"319_20120901_5042079a0eb6e","folder":"\\\\..\\\\upload\\\\","mime":"image\\/jpeg","batch":true}', NULL, NULL, NULL, NULL, NULL, NULL),
(321, 1, 79, NULL, 9, NULL, 'Bestanden', '/Bestanden', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 1, 321, NULL, 10, NULL, 'Document', '/Document', 1346578865, NULL, NULL, NULL, NULL, '{"name":"document.docx","type":"docx","uid":"322_20120902_504329b19b180","folder":"\\\\..\\\\upload\\\\","batch":false,"mime":"text\\/plain"}', NULL, NULL, NULL, NULL, NULL, NULL),
(323, 1, 1, 1, 1, NULL, 'Systeem Data', '/Data', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 1, 323, 1, 1, NULL, 'Systeem Teksten', '/Systeem_Teksten', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 1, 79, 1, 14, 'NEWS', 'Nieuws', '/Nieuws', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 1, 79, 1, 14, 'Fotos1', 'Fotos1', '/Fotos1', 326, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 1, 324, NULL, 3, 'DASHBOARD', 'Dashboard tekst', '/Dashboard_tekst', 5, '<h2><strong>Hallo gebruiker, welkom in de backend van de website :)</strong></h2>\r\n<p><strong>Hier wat regeltjes van het systeem!<br /></strong></p>\r\n<p><em>Admin</em> is een rol die in principe alles mag met content en gebruikers. Alleen de hoofdverantwoordelijke van de website mag deze rol gebruiken.</p>\r\n<p>Ook is er een gebruiker met de naam <em>god</em>. deze gebruiker kan naast alles met content ook contenttypes aanpassen, rollen aanmaken en aanpassen. Het is verstandig zo min mogelijk met deze gebruiker in te loggen.</p>\r\n<p>Druk op de content item met de linkermuisknop om te kijken wat je er mee kunt doen. Om een content item te verplaatsen kan je hem verslepen. Wel heb je voor al deze mogelijkheden de rechten nodig.</p>\r\n<p>De <em>Frontend</em> &amp; <em>Backend</em> rollen bepalen of een gebruiker in de frontend of backend mag. Het heeft geen zin deze rollen verdere rechten te geven.</p>\r\n<p>Afbeeldingen kunnen met een <a href="/backend/index/import">batch geimporteerd</a> worden. Hiervoor moet je een map opgeven waar ze in terecht komen. Plaats deze afbeeldingen in de import map via een ftp account.</p>\r\n<p>Soms moet je als gebruiker een specifieke folder aangeven (bijv. bij een image <a href="/backend/index/import">import</a>), gebruik hier het code veld voor.</p>\r\n<p>De gebruiker <em>frontend_guest</em> wordt gebruikt aan de voorkant als er geen bekende user is ingelogd. Deze gebruiker kan je niet verwijderen. Deze gebruiker moet voorzien zijn van rechten op alle publieke content.</p>\r\n<p>Bestanden die in een item geupload zijn kunnen in de wysiwyg gebruikt worden bij het aanmaken van een link.</p>\r\n<p>Afbeeldingen die via een item geupload zijn kunnen in de wysiwyg gebruikt worden.</p>\r\n<p>Bestanden worden buiten de web map opgeslagen en kunnen alleen maar via de code (en security) van het systeem geserveerd worden.</p>\r\n<p>Bestanden uit de <a href="/backend/index/import">import</a> kunnen niet in de content gebruikt worden.</p>\r\n<p>Als een item verwijdert wordt zal het niet in de database weg zijn. Alleen de gerelateerde bestanden, code veld en url veld zullen niet terug te halen zijn.</p>\r\n<p>Een rol kan 5 permissies geven per item type:</p>\r\n<ul>\r\n<li>Lezen</li>\r\n<li>Bewerken</li>\r\n<li>Aanmaken &amp; Verwijderen</li>\r\n<li>Kinderen aanmaken</li>\r\n<li>Verplaatsen</li>\r\n</ul>\r\n<p>Via de <a href="/backend/iframe/contentedit/object_id/backend/index/movegroup">groepopties</a> kunnen alle gebruikers van een bepaalde rol van de rol ontheven worden of een andere rol krijgen.</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Beperkingen voor gedumpte tabellen
--

--
-- Beperkingen voor tabel `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `FK_content_objecttype` FOREIGN KEY (`objecttype_id`) REFERENCES `objecttype` (`id`);
