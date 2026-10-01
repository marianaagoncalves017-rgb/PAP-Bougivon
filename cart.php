<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo->exec("SET NAMES utf8mb4");

// Lógica de Remoção de Artigo do Carrinho
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['key'])) {
    $key_to_remove = $_GET['key'];
    if (isset($_SESSION['carrinho'][$key_to_remove])) {
        unset($_SESSION['carrinho'][$key_to_remove]);
    }
    header("Location: cart.php");
    exit();
}

// Lógica de Atualização de Quantidades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['qtd']) && is_array($_POST['qtd'])) {
        foreach ($_POST['qtd'] as $key => $nova_qtd) {
            $nova_qtd = (int)$nova_qtd;
            if (isset($_SESSION['carrinho'][$key])) {
                if ($nova_qtd > 0) {
                    if (is_array($_SESSION['carrinho'][$key])) {
                        $_SESSION['carrinho'][$key]['qtd'] = $nova_qtd;
                    } else {
                        $_SESSION['carrinho'][$key] = $nova_qtd;
                    }
                } else {
                    unset($_SESSION['carrinho'][$key]);
                }
            }
        }
    }
    header("Location: cart.php");
    exit();
}

$carrinho = $_SESSION['carrinho'] ?? [];
$total_geral = 0;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Meu Carrinho | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfaf6; margin: 0; color: #333; }
        .navbar { background: #fff; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .navbar h2 { margin: 0; color: #8d6e63; font-family: 'Georgia', serif; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #5d4037; font-weight: bold; font-size: 0.95em; transition: 0.3s; }
        .nav-links a:hover { color: #8d6e63; }

        .container { padding: 40px; max-width: 1000px; margin: auto; }
        .page-title { font-family: 'Georgia', serif; color: #5d4037; font-size: 1.8em; margin-bottom: 20px; border-bottom: 2px solid #e0d4cc; padding-bottom: 10px; }

        .cart-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .cart-table th { background: #8d6e63; color: white; padding: 14px; text-align: left; font-weight: 600; }
        .cart-table td { padding: 14px; border-bottom: 1px solid #f0e6df; vertical-align: middle; }
        .cart-table tr:last-child td { border-bottom: none; }

        .product-item { display: flex; align-items: center; gap: 15px; }
        .product-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e0d4cc; }
        .product-title { font-weight: bold; color: #5d4037; display: block; font-size: 1em; }
        .product-details { font-size: 0.85em; color: #777; margin-top: 3px; line-height: 1.3; }

        .input-qtd { width: 55px; padding: 6px; border: 1px solid #8d6e63; border-radius: 6px; text-align: center; font-weight: bold; }
        
        .price { font-weight: bold; color: #8d6e63; }
        .btn-remove { color: #c62828; text-decoration: none; font-weight: bold; font-size: 1.2em; padding: 5px 10px; transition: 0.2s; }
        .btn-remove:hover { color: #b71c1c; background: #ffebee; border-radius: 50%; }

        .cart-summary { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .total-box { font-size: 1.3em; color: #5d4037; }
        .total-box span { font-weight: bold; color: #8d6e63; font-size: 1.2em; }

        .actions-group { display: flex; gap: 12px; }
        .btn-update { background: #6c757d; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-update:hover { background: #5a6268; }
        .btn-checkout { background: #28a745; color: white; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; transition: 0.2s; display: inline-block; }
        .btn-checkout:hover { background: #218838; }

        .empty-cart { text-align: center; padding: 50px 20px; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
        .empty-cart h3 { color: #5d4037; font-family: 'Georgia', serif; }
        .btn-back { display: inline-block; margin-top: 15px; background: #8d6e63; color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; }
        .btn-back:hover { background: #5d4037; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Bougivon Loja</h2>
        <div class="nav-links">
            <a href="user.php">🏪 Voltar à Loja</a>
            <a href="profile.php">👤 O Meu Perfil</a>
            <a href="logout.php" style="color: #dc3545;">Sair</a>
        </div>
    </div>

    <div class="container">
        <div class="page-title">O teu Carrinho de Compras 🛒</div>

        <?php if (empty($carrinho)): ?>
            <div class="empty-cart">
                <h3>O teu carrinho está vazio!</h3>
                <p>Explora as nossas velas artesanais e adiciona os teus aromas favoritos.</p>
                <a href="user.php" class="btn-back">Ver Velas Disponíveis</a>
            </div>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço Un.</th>
                            <th>Qtd</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($carrinho as $item_key => $item): 
                            if (is_array($item)) {
                                $id_real = (int)$item['id'];
                                $qtd = (int)$item['qtd'];
                                $opcao = $item['opcao'] ?? 'simples';
                            } else {
                                $partes = explode('_', $item_key);
                                $id_real = (int)$partes[0];
                                $qtd = (int)$item;
                                $opcao = 'simples';
                            }

                            $stmt = $pdo->prepare("SELECT * FROM velas_artigos WHERE id = ?");
                            $stmt->execute([$id_real]);
                            $produto = $stmt->fetch();

                            if (!$produto) continue;

                            $nome_produto = $produto['nome_produto'];
                            $preco_base = (float)$produto['preco'];
                            $imagem = !empty($produto['imagem']) ? strtolower($produto['imagem']) : 'default_candle.jpg';

                            $preco_unitario = $preco_base;
                            $detalhes_opcao = [];

                            if ($id_real === 14) {
                                $preco_unitario = 0;
                                if (str_contains($opcao, 'tamanho:grande')) {
                                    $preco_unitario += 5.00;
                                    $detalhes_opcao[] = "Tamanho: Grande";
                                }
                                if (str_contains($opcao, 'tamanho:pequeno') || str_contains($opcao, 'tamanho:grande_pequeno')) {
                                    if (str_contains($opcao, 'tamanho:grande_pequeno')) {
                                        $preco_unitario += 2.50;
                                        $detalhes_opcao[] = "Tamanhos: Grande + Pequeno";
                                    } else {
                                        $preco_unitario += 2.50;
                                        $detalhes_opcao[] = "Tamanho: Pequeno";
                                    }
                                }
                            }

                            if (str_contains($opcao, 'custom_cor:')) {
                                preg_match('/custom_cor:([^_]+)_aroma:([^_]+)/', $opcao, $matches);
                                if (!empty($matches)) {
                                    $detalhes_opcao[] = "Cor: " . $matches[1];
                                    $detalhes_opcao[] = "Aroma: " . $matches[2];
                                }
                            }

                            if (str_contains($opcao, 'caixa_duo_vela1:')) {
                                preg_match('/caixa_duo_vela1:([^_]+)_vela2:(.+)/', $opcao, $matches);
                                if (!empty($matches)) {
                                    $detalhes_opcao[] = "Vela 1: " . $matches[1];
                                    $detalhes_opcao[] = "Vela 2: " . $matches[2];
                                }
                            }

                            if (str_contains($opcao, 'decorada')) {
                                $preco_unitario += 1.00;
                                $detalhes_opcao[] = "Com Elemento Decorativo (+1,00€)";
                            }

                            if (str_contains($opcao, '_tronco:sim')) {
                                $preco_unitario += 2.00;
                                $detalhes_opcao[] = "Tronco de Madeira (+2,00€)";
                            }

                            if (str_contains($opcao, '_azevinho:sim')) {
                                $preco_unitario += 0.80;
                                $detalhes_opcao[] = "Azevinho (+0,80€)";
                            }

                            $subtotal = $preco_unitario * $qtd;
                            $total_geral += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <div class="product-item">
                                    <img src="imagens/<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($nome_produto) ?>">
                                    <div>
                                        <span class="product-title"><?= htmlspecialchars($nome_produto) ?></span>
                                        <?php if (!empty($detalhes_opcao)): ?>
                                            <div class="product-details">
                                                <?= htmlspecialchars(implode(' | ', $detalhes_opcao)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="price"><?= number_format($preco_unitario, 2, ',', '.') ?>€</td>
                            <td>
                                <input type="number" name="qtd[<?= htmlspecialchars($item_key) ?>]" value="<?= $qtd ?>" min="1" max="<?= $produto['stock_atual'] ?>" class="input-qtd">
                            </td>
                            <td class="price"><?= number_format($subtotal, 2, ',', '.') ?>€</td>
                            <td>
                                <a href="cart.php?action=remove&key=<?= urlencode($item_key) ?>" class="btn-remove" title="Remover Item">&times;</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <div class="total-box">
                        Total da Encomenda: <span><?= number_format($total_geral, 2, ',', '.') ?>€</span>
                    </div>
                    <div class="actions-group">
                        <button type="submit" name="update_cart" class="btn-update">🔄 Atualizar Qtds</button>
                        <a href="finalizar_encomenda.php" class="btn-checkout">Finalizar Encomenda ➔</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>