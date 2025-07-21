-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 21/07/2025 às 19:57
-- Versão do servidor: 5.7.44
-- Versão do PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `mini_erp`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo_desconto` enum('fixo','percentual') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_minimo_subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `data_validade` date NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produto_variacao_id` bigint(20) UNSIGNED NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `produto_variacao_id`, `quantidade`, `updated_at`) VALUES
(1, 1, 2, '2025-07-21 14:07:10'),
(2, 2, 10, '2025-07-21 14:09:42'),
(3, 3, 10, '2025-07-21 19:23:00'),
(4, 4, 98, '2025-07-21 19:24:08'),
(5, 5, 10, '2025-07-21 15:46:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hash_id` varchar(32) NOT NULL,
  `cliente_nome` varchar(255) NOT NULL,
  `cliente_email` varchar(255) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `valor_frete` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valor_total` decimal(10,2) NOT NULL,
  `status` enum('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `cupom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `hash_id`, `cliente_nome`, `cliente_email`, `cep`, `endereco`, `subtotal`, `valor_frete`, `desconto`, `valor_total`, `status`, `cupom_id`, `created_at`, `updated_at`) VALUES
(1, 'VhT8lJoEes9LAFPOarwiNDMzbHCxyS60', 'Matheus Correa', 'matheuscorreati@gmail.com', '24710120', 'Rua São Pedro Alcântara, 120 - Alcântara - São Gonçalo/RJ', 200.00, 0.00, 0.00, 200.00, 'pendente', NULL, '2025-07-21 22:23:00', '2025-07-21 22:23:00'),
(2, 'uoHGZOXwEAIq7vnzSMxgcD54kYmeFJl3', 'Matheus Marins Correa', 'matheuscorreati@gmail.com', '24710120', 'Rua São Pedro Alcântara, 9 - Alcântara - São Gonçalo/RJ', 30.00, 25.00, 0.00, 55.00, 'pendente', NULL, '2025-07-21 22:24:08', '2025-07-21 22:24:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pedido_id` bigint(20) UNSIGNED NOT NULL,
  `produto_variacao_id` bigint(20) UNSIGNED NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `nome_produto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_variacao_id`, `quantidade`, `preco_unitario`, `nome_produto`) VALUES
(1, 1, 3, 20, 10.00, 'Camiseta Manga Curta - Tamanho G'),
(2, 2, 4, 2, 15.00, 'Cueca Box - Tamanho M');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'Camiseta Manga Curta', 'Camiseta para RJ', NULL, '2025-07-21 15:10:13'),
(2, 'Produto 02', '', NULL, '2025-07-21 14:09:41'),
(3, 'Cueca Box', '', NULL, '2025-07-21 15:45:10'),
(4, 'Calcinha', '', NULL, '2025-07-21 15:46:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_variacoes`
--

CREATE TABLE `produto_variacoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produto_id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `produto_variacoes`
--

INSERT INTO `produto_variacoes` (`id`, `produto_id`, `sku`, `nome`, `preco`, `created_at`, `updated_at`) VALUES
(1, 1, 'SKU01_1', 'Tamanho P', 10.00, NULL, '2025-07-21 14:10:31'),
(2, 2, 'SKU02', 'Tamanho P', 30.00, NULL, '2025-07-21 14:09:41'),
(3, 1, 'SKU01_2', 'Tamanho G', 10.00, NULL, '2025-07-21 14:10:31'),
(4, 3, 'SKU03', 'Tamanho M', 15.00, NULL, '2025-07-21 15:45:10'),
(5, 4, 'SKU04', 'Tamanho P', 40.00, NULL, '2025-07-21 15:46:43');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produto_variacao_id` (`produto_variacao_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash_id` (`hash_id`),
  ADD KEY `fk_pedido_cupom` (`cupom_id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_pedido` (`pedido_id`),
  ADD KEY `fk_item_variacao` (`produto_variacao_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_variacao_produto` (`produto_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_variacao` FOREIGN KEY (`produto_variacao_id`) REFERENCES `produto_variacoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `fk_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_item_variacao` FOREIGN KEY (`produto_variacao_id`) REFERENCES `produto_variacoes` (`id`);

--
-- Restrições para tabelas `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  ADD CONSTRAINT `fk_variacao_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
