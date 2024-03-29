-- phpMyAdmin SQL Dump
-- version 4.4.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 30-03-2016 a las 04:33:48
-- Versión del servidor: 5.5.42
-- Versión de PHP: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `getsirmx_payme`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `idclients` int(11) NOT NULL,
  `email` varchar(80) NOT NULL,
  `name` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `company` varchar(45) DEFAULT NULL,
  `users_idusers` int(11) NOT NULL,
  `createdon` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`idclients`, `email`, `name`, `lastname`, `company`, `users_idusers`, `createdon`) VALUES
(1, 'ogascon@iasanet.com.mx', 'Oscar', 'Gascon', 'IASA COMUNICACIÓN', 50, '2016-03-28 00:00:00'),
(2, 'mpadilla.espinosa@gmail.com', 'Miriam', 'Padilla', 'GETSIR', 51, '2016-03-29 03:01:06'),
(3, 'josafatbusio@gmail.com', 'Josafat', 'Busio', 'Sofingware', 51, '2016-03-29 03:01:48'),
(4, 'gloriabusio@hotmail.com', 'Gloria', 'Busio', 'CASA', 50, '2016-03-29 03:02:32'),
(5, 'kjashdkjashdjkas@gmail.com', 'juan', 'perez', 'nada', 51, '2016-03-29 17:23:39'),
(6, 'danieltrejomx@gmail.com', 'DANIEL', 'TREJO', 'IASA', 50, '2016-03-29 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `projects`
--

CREATE TABLE `projects` (
  `idprojects` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `cost` double NOT NULL,
  `paidup` tinyint(1) NOT NULL DEFAULT '0',
  `logo_image` varchar(45) DEFAULT NULL,
  `createdon` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleteon` datetime DEFAULT NULL,
  `clients_idclients` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `projects`
--

INSERT INTO `projects` (`idprojects`, `description`, `cost`, `paidup`, `logo_image`, `createdon`, `deleted`, `deleteon`, `clients_idclients`) VALUES
(1, 'Desarrollo app Movil', 14000, 0, NULL, '2016-03-28 00:00:00', 0, NULL, 4),
(2, 'Sitio web', 9000, 0, NULL, '2016-03-28 00:00:00', 0, NULL, 1),
(3, 'Reparacion de computadora', 988, 1, NULL, '2016-03-28 00:00:00', 0, NULL, 2),
(4, 'App mobile', 9, 1, NULL, '2016-03-28 00:00:00', 0, NULL, 2),
(5, 'Mantenimiento página web', 4000, 0, NULL, '2016-03-29 00:00:00', 0, NULL, 1),
(6, 'App para concurso', 13000, 0, NULL, '2016-03-29 00:00:00', 0, NULL, 3),
(7, 'APP PAYME', 1, 0, NULL, '2016-03-29 00:00:00', 0, NULL, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reminders`
--

CREATE TABLE `reminders` (
  `idreminders` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `send` tinyint(1) NOT NULL DEFAULT '0',
  `createdon` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleteon` datetime DEFAULT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  `responseByClient` varchar(1000) DEFAULT NULL,
  `projects_idprojects` int(11) NOT NULL,
  `templates_idtemplates` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `reminders`
--

INSERT INTO `reminders` (`idreminders`, `date`, `send`, `createdon`, `deleted`, `deleteon`, `isread`, `responseByClient`, `projects_idprojects`, `templates_idtemplates`) VALUES
(1, '2016-03-31 00:00:00', 0, '2016-03-29 00:00:00', 0, NULL, 0, NULL, 1, 1),
(2, '2016-04-01 00:00:00', 0, '2016-03-29 00:00:00', 0, NULL, 0, NULL, 1, 2),
(3, '2016-03-29 20:30:01', 1, '2016-03-29 00:00:00', 0, NULL, 0, NULL, 2, 1),
(4, '2016-03-30 09:00:00', 0, '2016-03-29 00:00:00', 0, NULL, 0, NULL, 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `templates`
--

CREATE TABLE `templates` (
  `idtemplates` int(11) NOT NULL,
  `text` varchar(1000) NOT NULL,
  `category` varchar(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `templates`
--

INSERT INTO `templates` (`idtemplates`, `text`, `category`) VALUES
(1, 'This is a remainder that you debt', 'BASICA'),
(2, 'Usted tuvo que realizar un pago y no lo ha realizado.', 'DEUDOR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `idusers` int(11) NOT NULL,
  `email` varchar(80) NOT NULL,
  `name` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `password` varchar(100) NOT NULL,
  `activation_code` varchar(100) DEFAULT NULL,
  `createdon` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `activation_date` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `reset_password_code` varchar(100) DEFAULT NULL,
  `text_account` varchar(500) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`idusers`, `email`, `name`, `lastname`, `password`, `activation_code`, `createdon`, `active`, `activation_date`, `lastlogin`, `reset_password_code`, `text_account`) VALUES
(50, 'osjobu@gmail.com', 'Oscar', 'Busio', '37b62fac547cb5b6fb7ade94fd9db15f', 'BB2E633C32E86C4BAF454F4EF6643107_ACTIVATED', '2016-03-29 02:42:31', 1, '2016-03-29 02:42:48', NULL, NULL, NULL),
(51, 'danieltrejomx@gmail.com', 'Daniel', 'Trejo', '37b62fac547cb5b6fb7ade94fd9db15f', 'BB2E633C32E86C4BAF454F4EF6643108_ACTIVATED', '2016-03-29 00:00:00', 1, '2016-03-29 00:00:00', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`idclients`,`users_idusers`),
  ADD UNIQUE KEY `idclients_UNIQUE` (`idclients`),
  ADD KEY `fk_clients_users_idx` (`users_idusers`);

--
-- Indices de la tabla `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`idprojects`,`clients_idclients`),
  ADD UNIQUE KEY `idprojects_UNIQUE` (`idprojects`),
  ADD KEY `fk_projects_clients1_idx` (`clients_idclients`);

--
-- Indices de la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`idreminders`,`projects_idprojects`,`templates_idtemplates`),
  ADD UNIQUE KEY `idreminders_UNIQUE` (`idreminders`),
  ADD KEY `fk_reminders_projects1_idx` (`projects_idprojects`),
  ADD KEY `fk_reminders_templates1_idx` (`templates_idtemplates`);

--
-- Indices de la tabla `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`idtemplates`),
  ADD UNIQUE KEY `idtemplates_UNIQUE` (`idtemplates`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idusers`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `idusers_UNIQUE` (`idusers`),
  ADD UNIQUE KEY `activation_code_UNIQUE` (`activation_code`),
  ADD UNIQUE KEY `reset_password_code_UNIQUE` (`reset_password_code`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `idclients` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `projects`
--
ALTER TABLE `projects`
  MODIFY `idprojects` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `reminders`
--
ALTER TABLE `reminders`
  MODIFY `idreminders` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `templates`
--
ALTER TABLE `templates`
  MODIFY `idtemplates` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `idusers` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_clients_users` FOREIGN KEY (`users_idusers`) REFERENCES `users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_clients1` FOREIGN KEY (`clients_idclients`) REFERENCES `clients` (`idclients`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `fk_reminders_projects1` FOREIGN KEY (`projects_idprojects`) REFERENCES `projects` (`idprojects`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reminders_templates1` FOREIGN KEY (`templates_idtemplates`) REFERENCES `templates` (`idtemplates`) ON DELETE NO ACTION ON UPDATE NO ACTION;
