-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-05-2025 a las 05:57:14
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `educacion_vial_quibdo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `Id_certificado` bigint(20) UNSIGNED NOT NULL,
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT current_timestamp(),
  `codigo_certificado` varchar(20) DEFAULT NULL,
  `Id_modulo` bigint(20) UNSIGNED DEFAULT NULL,
  `puntuacion` int(11) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_reportes`
--

CREATE TABLE `comentarios_reportes` (
  `Id_comentario` bigint(20) UNSIGNED NOT NULL,
  `Id_reporte` bigint(20) UNSIGNED NOT NULL,
  `Id_funcionario` bigint(20) UNSIGNED NOT NULL,
  `comentario` text NOT NULL,
  `fecha_comentario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios_transito`
--

CREATE TABLE `funcionarios_transito` (
  `Id_funcionario` bigint(20) UNSIGNED NOT NULL,
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `rango` varchar(50) DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_educativos`
--

CREATE TABLE `modulos_educativos` (
  `Id_modulo` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `tipo_contenido` enum('video','quiz','infografía') NOT NULL,
  `estado` tinyint(1) DEFAULT 0,
  `descripcion` text DEFAULT NULL,
  `duracion_minutos` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_publicacion` timestamp NULL DEFAULT NULL,
  `nivel_dificultad` enum('básico','intermedio','avanzado') DEFAULT 'básico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opciones_respuesta`
--

CREATE TABLE `opciones_respuesta` (
  `Id_opcion` bigint(20) UNSIGNED NOT NULL,
  `Id_pregunta` bigint(20) UNSIGNED NOT NULL,
  `opcion` text NOT NULL,
  `es_correcta` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_quiz`
--

CREATE TABLE `preguntas_quiz` (
  `Id_pregunta` bigint(20) UNSIGNED NOT NULL,
  `Id_modulo` bigint(20) UNSIGNED NOT NULL,
  `pregunta` text NOT NULL,
  `tipo_pregunta` enum('opcion_multiple','verdadero_falso') NOT NULL,
  `puntos` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_usuarios`
--

CREATE TABLE `progreso_usuarios` (
  `Id_progreso` bigint(20) UNSIGNED NOT NULL,
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `Id_modulo` bigint(20) UNSIGNED NOT NULL,
  `porcentaje_completado` int(11) DEFAULT 0,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_incidentes`
--

CREATE TABLE `reportes_incidentes` (
  `Id_reporte` bigint(20) UNSIGNED NOT NULL,
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('pendiente','validado','rechazado') DEFAULT 'pendiente',
  `foto_url` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `ubicacion` varchar(200) DEFAULT NULL,
  `tipo_incidente` enum('exceso_velocidad','estacionamiento_ilegal','semáforo','carril','otros') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas_usuarios`
--

CREATE TABLE `respuestas_usuarios` (
  `Id_respuesta` bigint(20) UNSIGNED NOT NULL,
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `Id_pregunta` bigint(20) UNSIGNED NOT NULL,
  `Id_opcion` bigint(20) UNSIGNED NOT NULL,
  `fecha_respuesta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `Id_usuario` bigint(20) UNSIGNED NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `tipo_usuario` enum('estudiante','conductor','peatón','ciclista') NOT NULL,
  `codigo_verificacion` varchar(6) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre_completo` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `institucion` varchar(100) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL
) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`Id_certificado`),
  ADD UNIQUE KEY `codigo_certificado` (`codigo_certificado`),
  ADD KEY `Id_usuario` (`Id_usuario`),
  ADD KEY `Id_modulo` (`Id_modulo`);

--
-- Indices de la tabla `comentarios_reportes`
--
ALTER TABLE `comentarios_reportes`
  ADD PRIMARY KEY (`Id_comentario`),
  ADD KEY `Id_reporte` (`Id_reporte`),
  ADD KEY `Id_funcionario` (`Id_funcionario`);

--
-- Indices de la tabla `funcionarios_transito`
--
ALTER TABLE `funcionarios_transito`
  ADD PRIMARY KEY (`Id_funcionario`),
  ADD UNIQUE KEY `Id_usuario` (`Id_usuario`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- Indices de la tabla `modulos_educativos`
--
ALTER TABLE `modulos_educativos`
  ADD PRIMARY KEY (`Id_modulo`);

--
-- Indices de la tabla `opciones_respuesta`
--
ALTER TABLE `opciones_respuesta`
  ADD PRIMARY KEY (`Id_opcion`),
  ADD KEY `Id_pregunta` (`Id_pregunta`);

--
-- Indices de la tabla `preguntas_quiz`
--
ALTER TABLE `preguntas_quiz`
  ADD PRIMARY KEY (`Id_pregunta`),
  ADD KEY `Id_modulo` (`Id_modulo`);

--
-- Indices de la tabla `progreso_usuarios`
--
ALTER TABLE `progreso_usuarios`
  ADD PRIMARY KEY (`Id_progreso`),
  ADD UNIQUE KEY `Id_usuario` (`Id_usuario`,`Id_modulo`),
  ADD KEY `Id_modulo` (`Id_modulo`);

--
-- Indices de la tabla `reportes_incidentes`
--
ALTER TABLE `reportes_incidentes`
  ADD PRIMARY KEY (`Id_reporte`),
  ADD KEY `Id_usuario` (`Id_usuario`);

--
-- Indices de la tabla `respuestas_usuarios`
--
ALTER TABLE `respuestas_usuarios`
  ADD PRIMARY KEY (`Id_respuesta`),
  ADD KEY `Id_usuario` (`Id_usuario`),
  ADD KEY `Id_pregunta` (`Id_pregunta`),
  ADD KEY `Id_opcion` (`Id_opcion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `Id_certificado` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios_reportes`
--
ALTER TABLE `comentarios_reportes`
  MODIFY `Id_comentario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `funcionarios_transito`
--
ALTER TABLE `funcionarios_transito`
  MODIFY `Id_funcionario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos_educativos`
--
ALTER TABLE `modulos_educativos`
  MODIFY `Id_modulo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opciones_respuesta`
--
ALTER TABLE `opciones_respuesta`
  MODIFY `Id_opcion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `preguntas_quiz`
--
ALTER TABLE `preguntas_quiz`
  MODIFY `Id_pregunta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso_usuarios`
--
ALTER TABLE `progreso_usuarios`
  MODIFY `Id_progreso` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes_incidentes`
--
ALTER TABLE `reportes_incidentes`
  MODIFY `Id_reporte` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuestas_usuarios`
--
ALTER TABLE `respuestas_usuarios`
  MODIFY `Id_respuesta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `Id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD CONSTRAINT `certificados_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`),
  ADD CONSTRAINT `certificados_ibfk_2` FOREIGN KEY (`Id_modulo`) REFERENCES `modulos_educativos` (`Id_modulo`);

--
-- Filtros para la tabla `comentarios_reportes`
--
ALTER TABLE `comentarios_reportes`
  ADD CONSTRAINT `comentarios_reportes_ibfk_1` FOREIGN KEY (`Id_reporte`) REFERENCES `reportes_incidentes` (`Id_reporte`),
  ADD CONSTRAINT `comentarios_reportes_ibfk_2` FOREIGN KEY (`Id_funcionario`) REFERENCES `funcionarios_transito` (`Id_funcionario`);

--
-- Filtros para la tabla `funcionarios_transito`
--
ALTER TABLE `funcionarios_transito`
  ADD CONSTRAINT `funcionarios_transito_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`);

--
-- Filtros para la tabla `opciones_respuesta`
--
ALTER TABLE `opciones_respuesta`
  ADD CONSTRAINT `opciones_respuesta_ibfk_1` FOREIGN KEY (`Id_pregunta`) REFERENCES `preguntas_quiz` (`Id_pregunta`);

--
-- Filtros para la tabla `preguntas_quiz`
--
ALTER TABLE `preguntas_quiz`
  ADD CONSTRAINT `preguntas_quiz_ibfk_1` FOREIGN KEY (`Id_modulo`) REFERENCES `modulos_educativos` (`Id_modulo`);

--
-- Filtros para la tabla `progreso_usuarios`
--
ALTER TABLE `progreso_usuarios`
  ADD CONSTRAINT `progreso_usuarios_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`),
  ADD CONSTRAINT `progreso_usuarios_ibfk_2` FOREIGN KEY (`Id_modulo`) REFERENCES `modulos_educativos` (`Id_modulo`);

--
-- Filtros para la tabla `reportes_incidentes`
--
ALTER TABLE `reportes_incidentes`
  ADD CONSTRAINT `reportes_incidentes_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`);

--
-- Filtros para la tabla `respuestas_usuarios`
--
ALTER TABLE `respuestas_usuarios`
  ADD CONSTRAINT `respuestas_usuarios_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`),
  ADD CONSTRAINT `respuestas_usuarios_ibfk_2` FOREIGN KEY (`Id_pregunta`) REFERENCES `preguntas_quiz` (`Id_pregunta`),
  ADD CONSTRAINT `respuestas_usuarios_ibfk_3` FOREIGN KEY (`Id_opcion`) REFERENCES `opciones_respuesta` (`Id_opcion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
