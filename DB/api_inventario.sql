
CREATE DATABASE api_inventario;

USE api_inventario;

-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','usuario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `login` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$12$rr/XJHrM0ibYsKpsoIKlp.HZEb2ndxmpEyq0TJh4/GGa9f0MCrG.6', 'admin');

-- --------------------------------------------------------

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `cod` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT 0,
  `ubicacion` varchar(255) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` varchar(5) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `productos` (`id`, `cod`, `nombre`, `descripcion`, `precio`, `stock`, `ubicacion`, `last_update`, `estado`) VALUES
(1, '4020', 'Prueba 1', 'Prueba 1', 20000, 10, 'Medellin - salento', '2025-04-29 02:45:41', 'A'),
(2, '9659', 'Prueba 2', 'Prueba 2', 20000, 10, 'Medellin - salento', '2025-04-29 02:46:40', 'A'),
(3, '5404', 'Prueba 3', 'Prueba 3', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:13', 'A'),
(4, '8092', 'Prueba 4', 'Prueba 4', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:19', 'A'),
(5, '4647', 'Prueba 5', 'Prueba 5', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:25', 'A'),
(6, '9288', 'Prueba 6', 'Prueba 6', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:31', 'A'),
(7, '6870', 'Prueba 7', 'Prueba 7', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:37', 'A'),
(8, '1796', 'Prueba 8', 'Prueba 8', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:43', 'A'),
(9, '3778', 'Prueba 9', 'Prueba 9', 20000, 10, 'Medellin - salento', '2025-04-29 02:47:49', 'A'),
(10, '9832', 'Prueba 10', 'Prueba 10', 20000, 10, 'Medellin - salento', '2025-04-29 02:48:36', 'A');

-- --------------------------------------------------------

CREATE TABLE `session_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `session_tokens` (`id`, `user_id`, `token`, `estado`) VALUES
(11, 1, 'cbbda4ff12f9285951d172a16f6077615b162460bba2754358a88c96b6bd04fb', 'A');

-- --------------------------------------------------------

ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

  -- --------------------------------------------------------

ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cod` (`cod`),
  ADD KEY `idx_cod` (`cod`),
  ADD KEY `idx_nombre` (`nombre`);

  -- --------------------------------------------------------

ALTER TABLE `session_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`);

  -- --------------------------------------------------------

ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

  -- --------------------------------------------------------

ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

-- --------------------------------------------------------

ALTER TABLE `session_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------

ALTER TABLE `session_tokens`
  ADD CONSTRAINT `session_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE;
COMMIT;