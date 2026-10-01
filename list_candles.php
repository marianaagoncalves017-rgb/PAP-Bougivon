<?php
include 'config.php';

// Proteção básica: O cliente precisa de estar logado para comprar
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Procura todos os artigos (velas) ativos na base de dados
$stmt = $pdo->query("SELECT * FROM velas_artigos ORDER BY nome_produto ASC");
$velas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Velas | Bougivon</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #fdfaf6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 1100px; margin: 40px auto; }
        
        /* Cabeçalho da página */
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #8d6e63; padding-bottom: 15px; }
        .header-area h2 { color: #8d6e63; font-family: 'Georgia', serif; font-size: 2em; margin: 0; }
        .back-btn { text-decoration: none; color: #8d6e63; font-weight: bold; background: #fff; padding: 10px 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.3s; }
        .back-btn:hover { background: #8d6e63; color: #fff; }

        /* Grelha de produtos */
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        
        /* Cartão de cada vela */
        .candle-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); text-align: center; border: 1px solid #f3ebe6; transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; justify-content: space-between; }
        .candle-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(141, 110, 99, 0.1); }
        
        .candle-icon { font-size: 3em; margin-bottom: 10px; display: block; }
        .candle-title { font-family: 'Georgia', serif; font-size: 1.3em; color: #5d4037; margin: 10px 0 5px 0; }
        .candle-meta { font-size: 0.85em; color: #888; margin-bottom: 15px; line-height: 1.5; }
        .candle-price { font-size: 1.4em; font-weight: bold; color: #8d6e63; margin-bottom: 20px; display: block; }
        
        /* Botão de compra */
        .add-btn { display: inline-block; background-color: #8d6e63; color: white; padding: 12px 20px; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 0.95em; transition: 0.3s; width: 80%; margin: 0 auto; }
        .add-btn:hover { background-color: #6d4c41; }
        .out-of-stock { display: inline-block; background-color: #e0e0e0; color: #757575; padding: 12px 20px; border-radius: 10px; font-weight: bold; font-size: 0.95em; width: 80%; margin: 0 auto; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container">
    
    <div class="header-area">
        <h2>As Nossas Velas Artesanais de Soja 🌿</h2>
        <a href="user.php" class="back-btn">← Voltar ao Menu</a>
    </div>

    <div class="products-grid">
        <?php if (empty($velas)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: #888; font-size: 1.1em; padding: 40px;">De momento não existem artigos registados no catálogo.</p>
        <?php else: ?>
            <?php foreach ($velas as $vela): ?>
                <div class="candle-card">
                    <div>
                        <span class="candle-icon">🕯️</span>
                        <h3 class="candle-title"><?= htmlspecialchars($vela['nome_produto']) ?></h3>
                        <div class="candle-meta">
                            <strong>Aroma:</strong> <?= htmlspecialchars($vela['aroma']) ?><br>
                            <strong>Cor:</strong> <?= htmlspecialchars($vela['cor']) ?><br>
                            <strong>Estilo:</strong> <?= htmlspecialchars($vela['estilo_festividade']) ?>
                        </div>
                    </div>
                    
                    <div>
                        <span class="candle-price"><?= number_format($vela['preco'], 2) ?>€</span>
                        
                        <?php if (isset($vela['stock']) && $vela['stock'] <= 0): ?>
                            <span class="out-of-stock">Esgotado</span>
                        <?php else: ?>
                            <a href="add_cart.php?id=<?= $vela['id'] ?>" class="add-btn">Adicionar 🛒</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>