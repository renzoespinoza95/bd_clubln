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

-- Volcando estructura para tabla bd_cristomo.fotoxusu
CREATE TABLE IF NOT EXISTS `fotoxusu` (
  `fotoxusu_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_nom` varchar(200) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `is_valido` int(11) DEFAULT 1,
  `santo_id` int(11) DEFAULT NULL,
  `me_gusta` int(11) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT NULL,
  PRIMARY KEY (`fotoxusu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.fotoxusu: ~0 rows (aproximadamente)

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
  `santo_id` int(11) DEFAULT NULL,
  `me_gusta` int(11) DEFAULT 0,
  `is_valido` int(11) DEFAULT 0,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla bd_cristomo.msg: ~0 rows (aproximadamente)

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
	(12, 'Aviso', '/aviso/inicio', 1, 3, '_self'),
	(13, 'Pag Web', '/pagweb/inicio', 1, 3, '_self'),
	(14, 'chart', '/chart', 1, 3, '_self'),
	(15, 'oai_pt', '/oai_pt', 1, 3, '_self');

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
