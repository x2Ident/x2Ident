-- MySQL-dump for x2Ident
-- version: 1.0.2
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth`
--
DROP TABLE `auth`;
CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text,
  `secret` text NOT NULL,
  `not_show` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--
DROP TABLE `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conf_key` text NOT NULL,
  `conf_value` text NOT NULL,
  `conf_default` text NOT NULL,
  `conf_info` text NOT NULL,
  `only_admin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `history`
--
DROP TABLE `history`;
CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwid` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=327 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `onetimekeys`
--
DROP TABLE `onetimekeys`;
CREATE TABLE IF NOT EXISTS `onetimekeys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwid` int(11) NOT NULL,
  `onetime` text NOT NULL,
  `real_pw` text NOT NULL,
  `pw_active` int(11) NOT NULL,
  `url` text NOT NULL,
  `pw_global` int(11) NOT NULL,
  `user` text NOT NULL,
  `sess_id` text NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=351 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session_proxy`
--
DROP TABLE `session_proxy`;
CREATE TABLE IF NOT EXISTS `session_proxy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session_user`
--
DROP TABLE `session_user`;
CREATE TABLE IF NOT EXISTS `session_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `sess_id` text NOT NULL,
  `js_id` text NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--
DROP TABLE `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `de` text NOT NULL,
  `en` text NOT NULL,
  `only_admin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Tabellenstruktur für Tabelle `language`
--
DROP TABLE `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `de` text NOT NULL,
  `en` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Daten für Tabelle `language`
--

INSERT INTO `language` (`id`, `key`, `de`, `en`) VALUES
(1, 'proxy_inaktiv', 'du bist nicht auf dem Proxy!', 'you''re not on the proxy!'),
(2, 'proxy_aktiv', 'du bist auf dem Proxy!', 'you are on the proxy!'),
(3, 'hallo', 'Hallo', 'hello'),
(4, 'admin_text', 'Verwalte deine Passwörter. Verwende diesen Modus nur in einer sicheren Umgebung.', 'Administrate your passwords. Use only in a secure environment.'),
(5, 'keygen_text', 'Logge dich mit Einmalkeys sicher in einer möglicherweise unsicheren Umgebung ein.', 'Log in with one-time-keys in a potentially unsecure environment.'),
(6, 'nicht_angemeldet', 'nicht angemeldet', 'not logged in'),
(7, 'admin_title', 'Admin', 'Admin'),
(8, 'keygen_title', 'Keygen', 'Keygen'),
(9, 'otk_create_title', 'Einmal-Key erstellen', 'Create a one-time-key'),
(10, 'angemeldet_als', 'Angemeldet als', 'Logged in as'),
(11, 'logout', 'Abmelden', 'Logout'),
(12, 'settings', 'Einstellungen', 'settings'),
(13, 'loginfirst_link', 'Bitte zuerst <a href="login">einloggen</a>', 'Please <a href="login">login</a> first'),
(14, 'key', 'Schlüssel', 'key'),
(15, 'value', 'Wert', 'value'),
(16, 'default', 'Standard', 'default'),
(17, 'info', 'Info', 'info'),
(18, 'save', 'Speichern', 'save'),
(19, 'einstellungen_erst_nach_login', 'Einige Einstellungen werden unter Umständen erst nach erneutem Login übernommen.', 'You need to login again to change some settings.'),
(20, 'title', 'Titel', 'title'),
(21, 'website', 'Webseite', 'website'),
(22, 'user', 'Benutzername', 'user'),
(23, 'otk', 'Einmal-Key', 'one time key'),
(24, 'global', 'Global', 'global'),
(25, 'expires_in', 'Läuft ab in', 'expires in'),
(26, 'last_login', 'Letzte Anmeldung', 'last login'),
(27, 'create_otk_button', 'Key erstellen', 'create key'),
(28, 'delete_otk_button', 'Löschen', 'delete key'),
(29, 'vor_zeit_1', 'vor', ''),
(30, 'noch_nie', 'noch nie', 'never'),
(31, 'sekunden', 'Sekunden', 'seconds'),
(32, 'session_noch_aktiv_1', 'Session noch', 'session expires in'),
(33, 'time_sekunden', 'Sekunde(n)', 'second(s)'),
(34, 'time_minuten', 'Minute(n)', 'minute(s)'),
(35, 'time_stunden', 'Stunde(n)', 'hour(s)'),
(36, 'time_tage', 'Tag(e)', 'day(s)'),
(37, 'time_monate', 'Monat(e)', 'month(s)'),
(38, 'session_noch_aktiv_2', 'aktiv', ''),
(39, 'vor_zeit_2', '', 'ago');
