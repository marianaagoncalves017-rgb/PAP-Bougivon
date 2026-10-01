-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql104.infinityfree.com
-- Tempo de geração: 01-Out-2026 às 05:20
-- Versão do servidor: 11.4.13-MariaDB
-- versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_42214535_bougivon`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas`
--

CREATE TABLE `velas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `aroma` varchar(100) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `estilo` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_artigos`
--

CREATE TABLE `velas_artigos` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `nome_produto` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `aroma` varchar(100) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `estilo` varchar(50) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `stock_atual` int(11) DEFAULT 0,
  `created_by` varchar(100) DEFAULT NULL,
  `pending_price` tinyint(1) NOT NULL DEFAULT 0,
  `personalization_note` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT 'default_candle.jpg',
  `peso` int(3) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `velas_artigos`
--

INSERT INTO `velas_artigos` (`id`, `id_categoria`, `nome_produto`, `descricao`, `aroma`, `cor`, `estilo`, `preco`, `stock_atual`, `created_by`, `pending_price`, `personalization_note`, `imagem`, `peso`, `altura`, `largura`) VALUES
(1, NULL, 'Vela de Eucalipto', NULL, 'Eucalipto', 'Verde', 'Copo', '8.00', 10, 'admin', 0, NULL, 'eucalipto.png', 120, '8.50', '7.00'),
(2, NULL, 'Vela de Baunilha', NULL, 'Baunilha', 'Amarelo', 'Copo', '8.00', 10, 'admin', 0, NULL, 'baunilha.png', 120, '8.50', '7.00'),
(3, NULL, 'Vela de Lavanda', NULL, 'Lavanda', 'Lavanda', 'Copo', '8.00', 10, 'admin', 0, NULL, 'lavanda.png', 120, '8.50', '7.00'),
(4, NULL, 'Vela de Morango', NULL, 'Morango', 'Vermelho', 'Copo', '8.00', 10, 'admin', 0, NULL, 'morango.png', 120, '8.50', '7.00'),
(5, NULL, 'Vela de Coco', NULL, 'Coco', 'Branco', 'Copo', '8.00', 10, 'admin', 0, NULL, 'coco.png', 120, '8.50', '7.00'),
(6, NULL, 'Vela de Manga e Papaya', NULL, 'Papaya e Manga', 'Laranja', 'Copo', '8.00', 10, 'admin', 0, NULL, 'manga_papaya.png', 120, '8.50', '7.00'),
(7, NULL, 'Vela de Jasmim e Bamboo', NULL, 'Jasmim e Bamboo', 'Verde', 'Copo', '8.00', 10, 'admin', 0, NULL, 'jasmim_bamboo.png', 120, '8.50', '7.00'),
(9, NULL, 'Vela de Framboesa', NULL, 'Framboesa', 'Rosa', 'Copo', '8.00', 10, 'admin', 0, NULL, 'framboesa.png', 120, '8.50', '7.00'),
(11, NULL, 'Vela de Citronela', NULL, 'Citronela', 'Amarelo', 'Copo', '8.00', 10, 'admin', 0, NULL, 'citronela.png', 120, '8.50', '7.00'),
(12, NULL, 'Vela Oceano', NULL, 'Oceano', 'Azul', 'Copo', '8.00', 10, 'admin', 0, NULL, 'oceano.png', 120, '8.50', '7.00'),
(13, NULL, 'Bola de Flor', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '6.00', 10, 'admin', 0, NULL, 'bola_flor.png', 200, '8.00', '8.00'),
(14, NULL, 'Bubble e mini bubble', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'bubble.png', 35, '3.50', '3.50'),
(15, NULL, 'Concha', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '4.50', 10, 'admin', 0, NULL, 'concha.png', 140, '7.00', '9.00'),
(16, NULL, 'Peônia', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '4.00', 10, 'admin', 0, NULL, 'peonia.png', 65, '3.00', '7.00'),
(17, NULL, 'Ursinho', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '3.00', 10, 'admin', 0, NULL, 'ursinho.png', 50, '5.00', '4.00'),
(18, NULL, 'Lhama', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'llama.png', 40, '8.00', '3.00'),
(19, NULL, 'Vela Espiral', 'Personalizável -> cor e aroma', 'Baunilha', 'Branco', 'Simples', '5.50', 10, 'admin', 0, NULL, 'espiral.png', 180, '7.00', '7.00'),
(20, NULL, 'LOVE', NULL, 'Baunilha', 'Branco', 'S. Valentim', '4.00', 10, 'admin', 0, NULL, 'love.png', 50, '4.50', '14.50'),
(21, NULL, 'Coração Grande', NULL, 'Baunilha', 'Branco', 'S. Valentim', '6.00', 10, 'admin', 0, NULL, 'coracao.png', 190, '7.50', '9.00'),
(22, NULL, 'Corações pequenos', NULL, 'Baunilha', 'Branco', 'S. Valentim', '0.80', 10, 'admin', 0, NULL, 'coracoes.png', 15, '4.00', '4.00'),
(23, NULL, 'Rosa', NULL, 'Baunilha', 'Branco', 'S. Valentim', '3.00', 10, 'admin', 0, NULL, 'rosa.png', 50, '5.00', '4.00'),
(24, NULL, 'Copo com Rosa', NULL, 'Baunilha', 'Branco', 'S. Valentim', '7.00', 10, 'admin', 0, NULL, 'copo_rosa.png', 120, '8.50', '7.00'),
(25, NULL, 'Peônia (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'peonia_lembranca.png', 15, '2.00', '4.00'),
(26, NULL, 'Concha (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'concha_lembranca.png', 40, '4.50', '6.00'),
(27, NULL, 'Bubble (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'bubble_lembranca.png', 35, '3.50', '3.50'),
(28, NULL, 'Leão (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'leao_lembranca.png', 25, '5.50', '4.50'),
(29, NULL, 'Ursinho (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'urso_lembranca.png', 50, '5.00', '4.00'),
(30, NULL, 'Mini Lata (Lembrança)', 'Preço sob orçamento (depende da quantidade e do tipo de embrulho)\r\nEmbrulho apenas ilustrativo (pode ser personalizado)\r\nEtiquetas personalizadas\r\nVela personalizável (cor e aroma)', 'Baunilha', 'Branco', 'Simples', '2.50', 10, 'admin', 0, NULL, 'lembranca_lata.png', 30, '2.50', '5.50'),
(31, NULL, 'Asa de Anjo', NULL, 'Baunilha', 'Branco', 'Simples', '4.50', 10, 'admin', 0, NULL, 'angel_wing.png', 80, '12.00', '6.00'),
(32, NULL, 'Cruz com menino', NULL, 'Baunilha', 'Branco', 'Simples', '4.50', 10, 'admin', 0, NULL, 'cruz_menino.png', 80, '11.00', '6.00'),
(33, NULL, 'Cruz com menina', NULL, 'Baunilha', 'Branco', 'Simples', '4.50', 10, 'admin', 0, NULL, 'cruz_menina.png', 80, '11.00', '6.00'),
(34, NULL, 'Coelho da páscoa no ovo', NULL, 'Baunilha', 'Branco', 'Páscoa', '6.00', 10, 'admin', 0, NULL, 'easterbunny_egg.png', 135, '9.00', '5.00'),
(35, NULL, 'Cruz', NULL, 'Baunilha', 'Branco', 'Simples', '5.00', 10, 'admin', 0, NULL, 'cruz.png', 70, '9.50', '7.00'),
(36, NULL, 'Coelho da páscoa', NULL, 'Baunilha', 'Branco', 'Páscoa', '7.00', 10, 'admin', 0, NULL, 'easterbunny.png', 185, '10.00', '6.00'),
(37, NULL, 'Vela com jesus', NULL, 'Baunilha', 'Branco', 'Simples', '10.00', 10, 'admin', 0, NULL, 'jesus_candle.png', 335, '10.00', '8.00'),
(38, NULL, 'Happy easter', NULL, 'Baunilha', 'Branco', 'Páscoa', '4.00', 10, 'admin', 0, NULL, 'happyeaster.png', 40, '6.00', '11.00'),
(39, NULL, 'Nossa senhora', NULL, 'Baunilha', 'Branco', 'Simples', '6.00', 10, 'admin', 0, NULL, 'nossa_senhora.png', 90, '13.00', '6.00'),
(40, NULL, 'Copo Páscoa', NULL, 'Baunilha', 'Branco', 'Simples', '12.00', 10, 'admin', 0, NULL, 'copo_pascoa.png', 300, '5.50', '8.00'),
(41, NULL, 'Copo com flor', NULL, 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_flor.png', 290, '5.50', '8.00'),
(42, NULL, 'Vela Margarida', NULL, 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_margarida.png', 290, '5.50', '8.00'),
(43, NULL, 'Coruja', NULL, 'Baunilha', 'Branco', 'Halloween', '7.00', 10, 'admin', 0, NULL, 'coruja.png', 120, '7.00', '6.00'),
(44, NULL, 'Esquilo', NULL, 'Baunilha', 'Branco', 'Halloween', '6.00', 10, 'admin', 0, NULL, 'esquilo.png', 90, '7.00', '6.00'),
(45, NULL, 'Bolota', NULL, 'Baunilha', 'Branco', 'Halloween', '6.00', 10, 'admin', 0, NULL, 'bolota.png', 90, '5.50', '6.00'),
(46, NULL, 'Copo Abobora', NULL, 'Baunilha', 'Laranja', 'Halloween', '10.00', 10, 'admin', 0, NULL, 'copo_abobora.png', 320, '5.50', '8.00'),
(47, NULL, 'Pinha', NULL, 'Baunilha', 'Castanho', 'Halloween', '7.00', 10, 'admin', 0, NULL, 'pinha.png', 110, '7.00', '6.00'),
(48, NULL, 'Abobora', NULL, 'Baunilha', 'Laranja', 'Halloween', '2.00', 10, 'admin', 0, NULL, 'abobora1.png', 20, '3.00', '4.00'),
(49, NULL, 'Abobora', NULL, 'Baunilha', 'Laranja', 'Simples', '2.50', 10, 'admin', 0, NULL, 'abobora2.png', 30, '4.50', '5.50'),
(50, NULL, 'Abobora', NULL, 'Baunilha', 'Laranja', 'Halloween', '4.00', 10, 'admin', 0, NULL, 'abobora3.png', 60, '4.50', '5.50'),
(51, NULL, 'Abobora', NULL, 'Baunilha', 'Laranja', 'Halloween', '5.00', 10, 'admin', 0, NULL, 'abobora4.png', 100, '7.00', '6.00'),
(52, NULL, 'Copo de Outono', NULL, 'Baunilha', 'Branco', 'Copo', '12.00', 10, 'admin', 0, NULL, 'copo_outono.png', 300, '5.50', '8.00'),
(53, NULL, 'Fantasma', NULL, 'Baunilha', 'Branco', 'Halloween', '7.00', 10, 'admin', 0, NULL, 'fantasma.png', 120, '8.00', '7.00'),
(54, NULL, 'Trick Or Treat', NULL, 'Baunilha', 'Branco', 'Copo', '12.00', 10, 'admin', 0, NULL, 'trick_treat.png', 300, '5.50', '8.00'),
(55, NULL, 'Abobora com Fantasma', NULL, 'Baunilha', 'Branco', 'Halloween', '6.00', 10, 'admin', 0, NULL, 'abobora_fantasma.png', 90, '7.00', '5.50'),
(56, NULL, 'Copo com cérebro', NULL, 'Baunilha', 'Rosa', 'Halloween', '10.00', 10, 'admin', 0, NULL, 'brain.png', 320, '8.50', '7.00'),
(57, NULL, 'Abóbora com chapéu', NULL, 'Baunilha', 'Laranja', 'Halloween', '5.00', 10, 'admin', 0, NULL, 'abobora_hat.png', 55, '8.00', '5.00'),
(58, NULL, 'Copo Bruxa', NULL, 'Baunilha', 'Branco', 'Copo', '12.00', 10, 'admin', 0, NULL, 'copo_bruxa.png', 300, '5.50', '8.00'),
(59, NULL, 'Vela RIP', NULL, 'Baunilha', 'Branco', 'Halloween', '5.00', 10, 'admin', 0, NULL, 'rip.png', 70, '9.00', '6.00'),
(60, NULL, 'Boneco de Neve', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.00', 10, 'admin', 0, NULL, 'boneco.png', 75, '10.00', '5.00'),
(61, NULL, 'Gnomo de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.00', 10, 'admin', 0, NULL, 'gnomo.png', 75, '9.00', '3.50'),
(62, NULL, 'Bola de natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '8.00', 10, 'admin', 0, NULL, 'bola_natal.png', 210, '8.00', '7.00'),
(63, NULL, 'Bola da Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '6.50', 10, 'admin', 0, NULL, 'bola_natal2.png', 170, '7.00', '7.00'),
(64, NULL, 'Árvore de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '5.00', 10, 'admin', 0, NULL, 'arvore1.png', 30, '8.00', '8.00'),
(65, NULL, 'Árvore de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '6.00', 10, 'admin', 0, NULL, 'arvore2.png', 100, '10.00', '6.00'),
(66, NULL, 'Árvore de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '8.50', 10, 'admin', 0, NULL, 'arvore3.png', 175, '12.00', '8.00'),
(67, NULL, 'Árvore de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Simples', '5.00', 10, 'admin', 0, NULL, 'arvore4.png', 70, '9.00', '5.50'),
(68, NULL, 'Rena', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '2.50', 10, 'admin', 0, NULL, 'rena.png', 30, '5.50', '4.00'),
(69, NULL, 'Rena', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.50', 10, 'admin', 0, NULL, 'renas.png', 110, '10.00', '9.00'),
(70, NULL, 'Presépio', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.50', 10, 'admin', 0, NULL, 'presepio1.png', 80, '7.00', '3.50'),
(71, NULL, 'Presépio', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.50', 10, 'admin', 0, NULL, 'presepio2.png', 100, '7.50', '6.50'),
(72, NULL, 'Presépio', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '2.50', 10, 'admin', 0, NULL, 'presepio3.png', 40, '5.00', '4.00'),
(73, NULL, 'Quebra Nozes', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '9.00', 10, 'admin', 0, NULL, 'quebra_nozes.png', 260, '13.50', '7.00'),
(74, NULL, 'Árvore de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.00', 10, 'admin', 0, NULL, 'arvore.png', 40, '6.00', '5.00'),
(75, NULL, 'Pai Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.00', 10, 'admin', 0, NULL, 'santa.png', 50, '7.00', '4.00'),
(76, NULL, 'Pai Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '6.00', 10, 'admin', 0, NULL, 'santa2.png', 120, '9.00', '5.50'),
(77, NULL, 'Nossa Senhora', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.50', 10, 'admin', 0, NULL, 'nossasenhora.png', 85, '11.00', '4.00'),
(78, NULL, 'Floco de Neve', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.00', 10, 'admin', 0, NULL, 'snowflake.png', 40, '5.00', '5.00'),
(79, NULL, 'Floco de Neve', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.00', 10, 'admin', 0, NULL, 'snowflake2.png', 40, '6.00', '6.00'),
(80, NULL, 'Anjos', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.50', 10, 'admin', 0, NULL, 'angels.png', 50, '7.50', '7.00'),
(81, NULL, 'Gingerbread Cookie', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '3.00', 10, 'admin', 0, NULL, 'ginger.png', 40, '6.00', '5.00'),
(82, NULL, 'Gingerbread Cookie', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Simples', '3.50', 10, 'admin', 0, NULL, 'ginger2.png', 50, '7.00', '6.00'),
(83, NULL, 'Maria e Jesus', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '6.00', 10, 'admin', 0, NULL, 'maria_jesus.png', 140, '10.00', '6.00'),
(84, NULL, 'Casinha de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '7.00', 10, 'admin', 0, NULL, 'casa1.png', 140, '9.00', '6.00'),
(85, NULL, 'Casinha de Natal', '-> cor e aroma personalizáveis\r\n-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Natal', '4.50', 10, 'admin', 0, NULL, 'casa2.png', 80, '7.00', '5.00'),
(86, NULL, 'Copo de Pipocas', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_pipocas.png', 290, '5.50', '8.00'),
(87, NULL, 'Copo de Lotus', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Copo', '12.00', 10, 'admin', 0, NULL, 'copo_lotus.png', 310, '5.50', '8.00'),
(88, NULL, 'Copo de Doces', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€', 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_doce.png', 320, '8.50', '7.00'),
(89, NULL, 'Copo com Boneco de Neve', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_boneco.png', 280, '5.50', '8.00'),
(90, NULL, 'Copo com Árvore de Natal', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Copo', '12.00', 10, 'admin', 0, NULL, 'copo_arvore.png', 280, '5.50', '8.00'),
(91, NULL, 'Copo de Chocolate Quente', '-> tronco madeira-> 2€\r\n-> azevinho-> 0,80€\r\n', 'Baunilha', 'Branco', 'Copo', '10.00', 10, 'admin', 0, NULL, 'copo_choco.png', 360, '8.50', '7.00'),
(92, NULL, 'Caixinha duo de Natal', 'Podes montar o teu presente escolhendo duas das nossas velinhas pequenas:\r\n\r\n🕯️ Gingerbread cookies\r\n🕯️ Anjo pequeno\r\n🕯️ Flocos de neve\r\n🕯️ Pai Natal pequeno\r\n🕯️ Gnomo de natal\r\n\r\nUma ideia de presente fofa, perfumada e cheia de espírito natalício para oferecer a alguém especial', 'Baunilha', 'Branco', 'Natal', '7.50', 10, 'admin', 0, NULL, 'caixa_duo.png', NULL, NULL, NULL),
(93, NULL, 'Vela Surpresa', 'Uma vela dupla especial: primeiro acende a parte exterior na noite de 24… e guarda a pequena vela interior para iluminar o dia 25.\r\nPerfeita para dar continuidade à magia do Natal. 🎄💫\r\nEdição limitada — garante já a tua! 🎁', 'Baunilha', 'Branco', 'Natal', '10.00', 10, 'admin', 0, NULL, 'surprise.png', 180, '8.00', '7.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_carrinhos`
--

CREATE TABLE `velas_carrinhos` (
  `id` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `velas_carrinhos`
--

INSERT INTO `velas_carrinhos` (`id`, `id_utilizador`, `data_criacao`) VALUES
(7, 2, '2026-08-29 09:16:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_categorias`
--

CREATE TABLE `velas_categorias` (
  `id` int(11) NOT NULL,
  `nome_categoria` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_clientes`
--

CREATE TABLE `velas_clientes` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `palavra_passe` varchar(255) NOT NULL,
  `perfil_role` enum('admin','cliente') DEFAULT 'cliente',
  `morada` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `velas_clientes`
--

INSERT INTO `velas_clientes` (`id`, `nome_completo`, `email`, `palavra_passe`, `perfil_role`, `morada`, `telefone`, `codigo_postal`, `localidade`, `created_at`) VALUES
(1, 'Iara Gonçalves', 'iaragoncalves111@gmail.com', '$2y$10$rEBAtdjPnGXFa./TfAmt8u1ITkyUgvmro4CtfrsRHaoAk3RBtjmfW', 'admin', NULL, '918507203', NULL, NULL, '2026-06-12 09:05:31'),
(2, 'Mariana', 'marianaagoncalves017@gmail.com', '$2y$10$tx9kRuVap8NivCwoKl4ShOJELqPdNqw6/0W4.jbS6..xIlHWL6Afy', 'cliente', NULL, '915994380', NULL, NULL, '2026-06-12 09:05:31');

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_inventario`
--

CREATE TABLE `velas_inventario` (
  `id` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `tipo_movimento` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_movimento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_itens_venda`
--

CREATE TABLE `velas_itens_venda` (
  `id` int(11) NOT NULL,
  `id_carrinho` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `velas_pedidos`
--

CREATE TABLE `velas_pedidos` (
  `id` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `tracking_id` varchar(50) DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `data_encomenda` datetime DEFAULT current_timestamp(),
  `status_pagamento` varchar(50) DEFAULT 'pendente',
  `morada_entrega` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `velas`
--
ALTER TABLE `velas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `velas_artigos`
--
ALTER TABLE `velas_artigos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Índices para tabela `velas_carrinhos`
--
ALTER TABLE `velas_carrinhos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_utilizador_carrinho` (`id_utilizador`);

--
-- Índices para tabela `velas_categorias`
--
ALTER TABLE `velas_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `velas_clientes`
--
ALTER TABLE `velas_clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `velas_inventario`
--
ALTER TABLE `velas_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_stock` (`id_produto`);

--
-- Índices para tabela `velas_itens_venda`
--
ALTER TABLE `velas_itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carrinho_item` (`id_carrinho`),
  ADD KEY `fk_produto_item` (`id_produto`);

--
-- Índices para tabela `velas_pedidos`
--
ALTER TABLE `velas_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD KEY `fk_utilizador_pedido` (`id_utilizador`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `velas`
--
ALTER TABLE `velas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `velas_artigos`
--
ALTER TABLE `velas_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT de tabela `velas_carrinhos`
--
ALTER TABLE `velas_carrinhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `velas_categorias`
--
ALTER TABLE `velas_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `velas_clientes`
--
ALTER TABLE `velas_clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `velas_inventario`
--
ALTER TABLE `velas_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `velas_itens_venda`
--
ALTER TABLE `velas_itens_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `velas_pedidos`
--
ALTER TABLE `velas_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `velas_artigos`
--
ALTER TABLE `velas_artigos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `velas_categorias` (`id`);

--
-- Limitadores para a tabela `velas_carrinhos`
--
ALTER TABLE `velas_carrinhos`
  ADD CONSTRAINT `fk_utilizador_carrinho` FOREIGN KEY (`id_utilizador`) REFERENCES `velas_clientes` (`id`);

--
-- Limitadores para a tabela `velas_inventario`
--
ALTER TABLE `velas_inventario`
  ADD CONSTRAINT `fk_produto_stock` FOREIGN KEY (`id_produto`) REFERENCES `velas_artigos` (`id`);

--
-- Limitadores para a tabela `velas_itens_venda`
--
ALTER TABLE `velas_itens_venda`
  ADD CONSTRAINT `fk_carrinho_item` FOREIGN KEY (`id_carrinho`) REFERENCES `velas_carrinhos` (`id`),
  ADD CONSTRAINT `fk_produto_item` FOREIGN KEY (`id_produto`) REFERENCES `velas_artigos` (`id`);

--
-- Limitadores para a tabela `velas_pedidos`
--
ALTER TABLE `velas_pedidos`
  ADD CONSTRAINT `fk_utilizador_pedido` FOREIGN KEY (`id_utilizador`) REFERENCES `velas_clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
