-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.28-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla bd_cristomo.administradortbl
CREATE TABLE IF NOT EXISTS `administradortbl` (
  `administrador_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres_apellidos` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `clavel` varchar(200) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_ultimo_acceso` datetime DEFAULT NULL,
  `is_activo` int(11) DEFAULT 1,
  `tipo_administrador_id` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`administrador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.administradortbl: ~2 rows (aproximadamente)
INSERT INTO `administradortbl` (`administrador_id`, `nombres_apellidos`, `email`, `clavel`, `fecha_creacion`, `fecha_ultimo_acceso`, `is_activo`, `tipo_administrador_id`) VALUES
	(1, 'renzo', 'renzo', 'renzo', '2016-03-09 08:56:17', '2016-03-09 08:56:19', 1, 1),
	(2, 'secre', 'secre', 'secre', '2025-02-08 08:25:16', '2025-02-08 08:25:16', 1, 1);

-- Volcando estructura para tabla bd_cristomo.comentarios
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `santo` enum('santa_rosa','senor_milagros','san_martin') NOT NULL,
  `texto` text NOT NULL,
  `tipo` enum('reaccion','peticion_breve','peticion_larga') NOT NULL,
  `estado` enum('aprobado','oculto','en_revision') NOT NULL DEFAULT 'aprobado',
  `riesgo` decimal(5,2) DEFAULT 0.00,
  `motivo` varchar(120) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_santo_estado` (`santo`,`estado`),
  FULLTEXT KEY `ft_texto` (`texto`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.comentarios: ~51 rows (aproximadamente)
INSERT INTO `comentarios` (`id`, `santo`, `texto`, `tipo`, `estado`, `riesgo`, `motivo`, `ip`, `user_agent`, `created_at`) VALUES
	(1, 'santa_rosa', 'Amén 🙏', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.1', 'Mozilla/5.0', '2025-08-28 12:20:22'),
	(2, 'senor_milagros', 'Amén ✝️', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.2', 'Mozilla/5.0', '2025-08-28 12:16:22'),
	(3, 'san_martin', 'Amén 🙏💜', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.3', 'Mozilla/5.0', '2025-08-28 12:10:22'),
	(4, 'santa_rosa', 'Amén', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.4', 'Mozilla/5.0', '2025-08-28 12:05:22'),
	(5, 'senor_milagros', 'Amén 🙏🙏', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.5', 'Mozilla/5.0', '2025-08-28 12:00:22'),
	(6, 'san_martin', 'Amén ✝️🙏', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.6', 'Mozilla/5.0', '2025-08-28 11:55:22'),
	(7, 'santa_rosa', 'Amén 💜', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.7', 'Mozilla/5.0', '2025-08-28 11:50:22'),
	(8, 'senor_milagros', 'Amén 🙏', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.8', 'Mozilla/5.0', '2025-08-28 11:45:22'),
	(9, 'san_martin', 'Amén ✝️', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.9', 'Mozilla/5.0', '2025-08-28 11:40:22'),
	(10, 'santa_rosa', 'Amén 🙏💜', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.10', 'Mozilla/5.0', '2025-08-28 11:35:22'),
	(11, 'senor_milagros', 'Amén', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.11', 'Mozilla/5.0', '2025-08-28 11:30:22'),
	(12, 'san_martin', 'Amén 🙏', 'reaccion', 'aprobado', 0.00, 'amen', '192.0.2.12', 'Mozilla/5.0', '2025-08-28 11:25:22'),
	(13, 'santa_rosa', 'Santa Rosa, te pido por la salud de mi hijo.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.13', 'Mozilla/5.0', '2025-08-28 11:25:22'),
	(14, 'senor_milagros', 'Señor de los Milagros, protege a mi familia en sus trabajos.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.14', 'Mozilla/5.0', '2025-08-28 11:20:22'),
	(15, 'san_martin', 'San Martín, ayúdame con este tratamiento médico.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.15', 'Mozilla/5.0', '2025-08-28 11:15:22'),
	(16, 'santa_rosa', 'Santa Rosa, fortaleza para mi madre en su operación.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.16', 'Mozilla/5.0', '2025-08-28 11:10:22'),
	(17, 'senor_milagros', 'Señor, consuelo para quienes están solos.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.17', 'Mozilla/5.0', '2025-08-28 11:05:22'),
	(18, 'san_martin', 'San Martín, encuentra paz en mi hogar.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.18', 'Mozilla/5.0', '2025-08-28 11:00:22'),
	(19, 'santa_rosa', 'Santa Rosa, guía para mis estudios.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.19', 'Mozilla/5.0', '2025-08-28 10:55:22'),
	(20, 'senor_milagros', 'Señor, salud para mi padre.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.20', 'Mozilla/5.0', '2025-08-28 10:50:22'),
	(21, 'san_martin', 'San Martín, trabajo digno para mi esposo.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.21', 'Mozilla/5.0', '2025-08-28 10:45:22'),
	(22, 'santa_rosa', 'Santa Rosa, claridad para mis decisiones.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.22', 'Mozilla/5.0', '2025-08-28 10:40:22'),
	(23, 'senor_milagros', 'Señor, bendiciones para mi comunidad.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.23', 'Mozilla/5.0', '2025-08-28 10:35:22'),
	(24, 'san_martin', 'San Martín, serenidad en mi corazón.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.24', 'Mozilla/5.0', '2025-08-28 10:30:22'),
	(25, 'santa_rosa', 'Santa Rosa, ayuda con mis finanzas.', 'peticion_breve', 'en_revision', 0.62, 'revisar', '192.0.2.25', 'Mozilla/5.0', '2025-08-28 10:25:22'),
	(26, 'senor_milagros', 'Señor, paciencia y unidad en casa.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.26', 'Mozilla/5.0', '2025-08-28 10:20:22'),
	(27, 'san_martin', 'San Martín, intercede por mi entrevista.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.27', 'Mozilla/5.0', '2025-08-28 10:15:22'),
	(28, 'santa_rosa', 'Santa Rosa, que mi hermano sane pronto.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.28', 'Mozilla/5.0', '2025-08-28 10:10:22'),
	(29, 'senor_milagros', 'Señor, calma mis ansiedades.', 'peticion_breve', 'en_revision', 0.60, 'revisar', '192.0.2.29', 'Mozilla/5.0', '2025-08-28 10:05:22'),
	(30, 'san_martin', 'San Martín, armonía con mis vecinos.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.30', 'Mozilla/5.0', '2025-08-28 10:00:22'),
	(31, 'santa_rosa', 'Santa Rosa, te pido por la salud de mi hijo y por la paz en nuestro hogar; danos claridad y esperanza en estos días difíciles.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.31', 'Mozilla/5.0', '2025-08-28 09:25:22'),
	(32, 'senor_milagros', 'Señor de los Milagros, protege a quienes viajan y dales regreso seguro; que no falte el trabajo ni la unidad en mi familia.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.32', 'Mozilla/5.0', '2025-08-28 09:20:22'),
	(33, 'san_martin', 'San Martín de Porres, consuela a los enfermos del barrio y ayúdame a ser generoso con quienes lo necesitan.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.33', 'Mozilla/5.0', '2025-08-28 09:15:22'),
	(34, 'santa_rosa', 'Santa Rosa, acompáñanos en este proceso médico, ilumina a los doctores y fortalece nuestro ánimo.', 'peticion_larga', 'en_revision', 0.66, 'revisar', '192.0.2.34', 'Mozilla/5.0', '2025-08-28 09:10:22'),
	(35, 'senor_milagros', 'Señor, te ruego por mis padres y abuelos, dales salud y serenidad, y ayúdame a cuidar de ellos con paciencia.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.35', 'Mozilla/5.0', '2025-08-28 09:05:22'),
	(36, 'san_martin', 'San Martín, abre caminos de reconciliación en mi familia, y permítenos trabajar unidos sin rencores.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.36', 'Mozilla/5.0', '2025-08-28 09:00:22'),
	(37, 'santa_rosa', 'Santa Rosa, ayuda a mi esposo a resolver sus problemas laborales y danos la serenidad para afrontarlos juntos.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.37', 'Mozilla/5.0', '2025-08-28 08:55:22'),
	(38, 'senor_milagros', 'Señor de los Milagros, mira con misericordia a quienes pasan necesidad; que no falte pan ni abrigo en sus casas.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.38', 'Mozilla/5.0', '2025-08-28 08:50:22'),
	(39, 'san_martin', 'San Martín, dame fuerzas para terminar mis estudios, conseguir trabajo y apoyar a mi familia con humildad.', 'peticion_larga', 'en_revision', 0.63, 'revisar', '192.0.2.39', 'Mozilla/5.0', '2025-08-28 08:45:22'),
	(40, 'santa_rosa', 'Santa Rosa, que mi corazón encuentre paz y gratitud cada día, aun en medio de las pruebas.', 'peticion_larga', 'aprobado', 0.00, NULL, '192.0.2.40', 'Mozilla/5.0', '2025-08-28 08:40:22'),
	(41, 'senor_milagros', 'Visita mi sitio http://example.com para ganar premios', 'peticion_breve', 'oculto', 0.95, 'spam_privacidad', '192.0.2.41', 'Mozilla/5.0', '2025-08-28 08:25:22'),
	(42, 'san_martin', 'Contáctame al +51 999999999 para ofertas', 'peticion_breve', 'oculto', 0.92, 'spam_privacidad', '192.0.2.42', 'Mozilla/5.0', '2025-08-28 08:20:22'),
	(43, 'santa_rosa', 'Mensaje inapropiado detectado automáticamente.', 'peticion_breve', 'oculto', 0.90, 'alto_riesgo', '192.0.2.43', 'Mozilla/5.0', '2025-08-28 08:15:22'),
	(44, 'senor_milagros', 'Otro mensaje con datos personales: correo@ejemplo.com', 'peticion_breve', 'oculto', 0.94, 'spam_privacidad', '192.0.2.44', 'Mozilla/5.0', '2025-08-28 08:10:22'),
	(45, 'san_martin', 'Publicación repetitiva y agresiva detectada.', 'peticion_breve', 'oculto', 0.91, 'alto_riesgo', '192.0.2.45', 'Mozilla/5.0', '2025-08-28 08:05:22'),
	(46, 'santa_rosa', 'Cadena: comparte esto para un milagro instantáneo.', 'peticion_breve', 'oculto', 0.93, 'spam_privacidad', '192.0.2.46', 'Mozilla/5.0', '2025-08-28 08:00:22'),
	(47, 'senor_milagros', 'Señor, gracia para comenzar de nuevo.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.47', 'Mozilla/5.0', '2025-08-28 07:55:22'),
	(48, 'san_martin', 'San Martín, bendiciones para mis vecinos mayores.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.48', 'Mozilla/5.0', '2025-08-28 07:50:22'),
	(49, 'santa_rosa', 'Santa Rosa, cuida a los niños del barrio.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.49', 'Mozilla/5.0', '2025-08-28 07:45:22'),
	(50, 'senor_milagros', 'Señor, danos trabajo y sabiduría.', 'peticion_breve', 'aprobado', 0.00, NULL, '192.0.2.50', 'Mozilla/5.0', '2025-08-28 07:40:22'),
	(51, 'santa_rosa', 'santa rosa ayudame a obtener lo que siempre pido todos los dias para apoyar a las personas que quiero🕊️', 'peticion_breve', 'aprobado', 0.00, NULL, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G973U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/14.2 Chrome/87.0.4280.141 Mobile Safari/537.36', '2025-08-28 12:43:08'),
	(52, 'santa_rosa', 'santa rosa te pido conseguir lo q quiero💜. Renzo', 'peticion_breve', 'aprobado', 0.00, NULL, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G973U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/14.2 Chrome/87.0.4280.141 Mobile Safari/537.36', '2025-08-28 12:57:15');

-- Volcando estructura para tabla bd_cristomo.comentarios_log
CREATE TABLE IF NOT EXISTS `comentarios_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.comentarios_log: ~0 rows (aproximadamente)
INSERT INTO `comentarios_log` (`id`, `ip`, `created_at`) VALUES
	(1, '127.0.0.1', '2025-08-28 12:43:08'),
	(2, '127.0.0.1', '2025-08-28 12:57:15');

-- Volcando estructura para tabla bd_cristomo.fotoxusu
CREATE TABLE IF NOT EXISTS `fotoxusu` (
  `fotoxusu_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_nom` varchar(200) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `is_valido` int(11) DEFAULT 1,
  `santo_id` enum('santa rosa','sr de los milagros','san martin de porres') DEFAULT NULL,
  `me_gusta` int(11) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT NULL,
  PRIMARY KEY (`fotoxusu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.fotoxusu: ~4 rows (aproximadamente)
INSERT INTO `fotoxusu` (`fotoxusu_id`, `usu_nom`, `img`, `is_valido`, `santo_id`, `me_gusta`, `fecha_creacion`) VALUES
	(1, 'renzo', '/pics/fotos/1.jpg', 1, 'sr de los milagros', 0, '2025-08-27 11:45:54'),
	(2, 'MABEL', '/pics/fotos/2.jpg', 1, 'sr de los milagros', 0, '2025-08-27 12:05:03'),
	(3, 'ana', '/pics/fotos/3.jpg', 1, 'san martin de porres', 0, '2025-08-27 14:43:06'),
	(4, 'julio', '/pics/fotos/4.jpg', 1, 'san martin de porres', 0, '2025-08-27 14:57:31');

-- Volcando estructura para tabla bd_cristomo.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) DEFAULT NULL,
  `orden` int(11) DEFAULT 1,
  `tipo_administrador_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.menu: ~3 rows (aproximadamente)
INSERT INTO `menu` (`menu_id`, `titulo`, `orden`, `tipo_administrador_id`) VALUES
	(1, 'Sistema', 1, 1),
	(2, 'Reportes', 0, 1),
	(3, 'Directo', 2, 1);

-- Volcando estructura para tabla bd_cristomo.msg
CREATE TABLE IF NOT EXISTS `msg` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_nom` varchar(200) DEFAULT NULL,
  `contenido_rem` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `santo_id` enum('santa rosa','sr de los milagros','san martin de porres') DEFAULT NULL,
  `me_gusta` int(11) DEFAULT 0,
  `is_valido` int(11) DEFAULT 0,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.msg: ~0 rows (aproximadamente)
INSERT INTO `msg` (`msg_id`, `usu_nom`, `contenido_rem`, `fecha_creacion`, `santo_id`, `me_gusta`, `is_valido`) VALUES
	(1, 'renzo ee', 'Santa Rosa de Lima , patrona de la policía nacional del Peru', '2025-08-27 11:16:45', 'santa rosa', 0, 1);

-- Volcando estructura para tabla bd_cristomo.pagweb
CREATE TABLE IF NOT EXISTS `pagweb` (
  `pagweb_id` int(11) NOT NULL AUTO_INCREMENT,
  `clave_txt` varchar(200) DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `metatag01` varchar(200) DEFAULT NULL,
  `metatag02` varchar(200) DEFAULT NULL,
  `url_img01` varchar(200) DEFAULT NULL,
  `url_img02` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pagweb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.pagweb: ~0 rows (aproximadamente)
INSERT INTO `pagweb` (`pagweb_id`, `clave_txt`, `titulo`, `metatag01`, `metatag02`, `url_img01`, `url_img02`) VALUES
	(1, 'PW_INICIO', 'inicio', '', '', '', '');

-- Volcando estructura para tabla bd_cristomo.parrweb
CREATE TABLE IF NOT EXISTS `parrweb` (
  `parrweb_id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) DEFAULT NULL,
  `contenido` mediumtext DEFAULT NULL,
  `url_video01` varchar(200) DEFAULT NULL,
  `url_video02` varchar(200) DEFAULT NULL,
  `url_video03` varchar(200) DEFAULT NULL,
  `url_video04` varchar(200) DEFAULT NULL,
  `url_img01` varchar(200) DEFAULT NULL,
  `url_img02` varchar(200) DEFAULT NULL,
  `url_img03` varchar(200) DEFAULT NULL,
  `url_img04` varchar(200) DEFAULT NULL,
  `pagweb_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`parrweb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.parrweb: ~0 rows (aproximadamente)

-- Volcando estructura para tabla bd_cristomo.submenu
CREATE TABLE IF NOT EXISTS `submenu` (
  `submenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `menu_id` int(11) NOT NULL,
  `target` varchar(200) NOT NULL DEFAULT '1',
  PRIMARY KEY (`submenu_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `submenu_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.submenu: ~15 rows (aproximadamente)
INSERT INTO `submenu` (`submenu_id`, `titulo`, `url`, `orden`, `menu_id`, `target`) VALUES
	(1, 'Variables sistema', '/admin/listavariables_sistema', 1, 1, '_self'),
	(2, 'usu', '/usu/inicio', 1, 3, '_self'),
	(3, 'Submenu', '/admin/listaSubmenu', 1, 1, '_self'),
	(4, 'Menu', '/admin/listaMenu', 1, 1, '_self'),
	(5, 'Administradores', '/listaAdministradores', 1, 1, '_self'),
	(6, 'slider', '/slider/inicio', 1, 3, '_self'),
	(7, 'fich', '/fich/inicio', 1, 3, '_self'),
	(8, 'tipoxusu', '/tipoxusu/inicio', 1, 3, '_self'),
	(9, 'serv', '/serv/inicio', 1, 3, '_self'),
	(10, 'neg', '/neg/inicio', 1, 3, '_self'),
	(11, 'av_etiq', '/av_etiq/inicio', 1, 3, '_self'),
	(12, 'Msg', '/msg/inicio', 1, 3, '_self'),
	(13, 'Pag Web', '/pagweb/inicio', 1, 3, '_self'),
	(14, 'Fotos', '/fotoxusu/inicio', 1, 3, '_self');

-- Volcando estructura para tabla bd_cristomo.tipo_administrador
CREATE TABLE IF NOT EXISTS `tipo_administrador` (
  `tipo_administrador_id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) DEFAULT NULL,
  `is_activo` int(11) NOT NULL DEFAULT 1,
  `submenu_inicio` int(11) DEFAULT 1,
  PRIMARY KEY (`tipo_administrador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.tipo_administrador: ~3 rows (aproximadamente)
INSERT INTO `tipo_administrador` (`tipo_administrador_id`, `descripcion`, `is_activo`, `submenu_inicio`) VALUES
	(1, 'ADMINISTRADOR', 1, 1),
	(2, 'OPER1', 1, 209),
	(3, 'OPER2', 1, 207);

-- Volcando estructura para tabla bd_cristomo.variables_sistema
CREATE TABLE IF NOT EXISTS `variables_sistema` (
  `variables_sistema_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_variable` varchar(200) NOT NULL,
  `valor` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`variables_sistema_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.variables_sistema: ~33 rows (aproximadamente)
INSERT INTO `variables_sistema` (`variables_sistema_id`, `nombre_variable`, `valor`) VALUES
	(1, 'ACTIVO_YOUTUBE', '0'),
	(2, 'IMG_MINI', '150'),
	(4, 'TITULO_SITIO', 'cristomo'),
	(5, 'DESCRIPCION_SITIO', 'Ernesto Diez Canseco 319 Drag&oacute;n de monta&ntilde;a'),
	(6, 'EMAIL_PARA_CONTACTO', 'ventas@marsad.com'),
	(7, 'TEXTO_CONTACTENOS', 'Av. Las Artes Sur 260 - San Borja'),
	(8, 'VISITAS', '0'),
	(9, 'FIRMA_AUTOR', '2025 &copy; REGENTIS - Todo los Derechos Reservados'),
	(10, 'ARCHIVO_COMUNICADO_INICIO', '1.png'),
	(11, 'KEY', 'meganfox'),
	(12, 'ACTIVO_API', '1'),
	(13, 'PICS_ESTADOS_MINI', '/pics/estados_mini/'),
	(14, 'PICS_FICH_FULL', '/pics/fich_full/'),
	(15, 'IMG_FULL', '800'),
	(18, 'API_KEY', '1995'),
	(19, 'MEMBRETE_INSTITUCION', 'CLUB SOCIAL LIMA NORTE'),
	(20, 'MEMBRETE_DEPENDENCIA', 'ADMIN'),
	(21, 'HAB_IMG_INICIO', '0'),
	(22, 'PICS_SLIDER_MINI', '/pics/slider_mini/'),
	(23, 'PICS_SLIDER_FULL', '/pics/slider_full/'),
	(24, 'PICS_FICH_MINI', '/pics/fich_mini/'),
	(25, 'PICS_ESTADOS_FULL', '/pics/estados_full/'),
	(28, 'PICS_AVATAR_MINI', '/pics/avatar_mini/'),
	(29, 'PICS_AVATAR_FULL', '/pics/avatar_full/'),
	(30, 'TITULO_PAG_WEB', 'PrinceApp'),
	(31, 'PICS_PAG_WEB_MINI', 'pics/pag_web_mini'),
	(32, 'PICS_PAG_WEB_FULL', 'pics/pag_web_full'),
	(33, 'PICS_PARR_WEB_FULL', 'pics/parr_web_full'),
	(34, 'PICS_PARR_WEB_MINI', 'pics/parr_web_mini'),
	(35, 'PICS_USU_MINI', '/pics/usu_mini/'),
	(36, 'PICS_USU_FULL', '/pics/usu_full/'),
	(37, 'PICS_USU_BLUR', '/pics/usu_blur/'),
	(38, 'PICS_FICH_BLUR', '/pics/fich_blur/');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
