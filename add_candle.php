<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção: Garante que apenas o administrador consegue aceder a esta página
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$mensagem = "";
$pref_estilo = 'Simples';

// Processa o formulário quando o botão é clicado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_produto = trim($_POST['nome_produto']);
    $aroma = trim($_POST['aroma']); 
    $cor = trim($_POST['cor']);
    $estilo = trim($_POST['estilo']);
    $preco = (float)$_POST['preco'];
    $stock_atual = (int)$_POST['stock_atual'];
    $imagem = trim($_POST['imagem']); // CAPTURA O NOME DO FICHEIRO ENVIADO

    // Se o administrador deixar em branco, usa a imagem por defeito
    if (empty($imagem)) {
        $imagem = 'default_candle.jpg';
    }

    if (!empty($nome_produto) && !empty($preco)) {
        try {
            // Garante que a coluna created_by existe na tabela antes de inserir
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'velas_artigos' AND COLUMN_NAME = 'created_by'");
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() == 0) {
                $pdo->exec("ALTER TABLE velas_artigos ADD COLUMN created_by VARCHAR(100) NULL");
            }

            // Faz a inserção incluindo o campo da imagem
            $stmt = $pdo->prepare("INSERT INTO velas_artigos (nome_produto, aroma, cor, estilo, preco, stock_atual, created_by, imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome_produto, $aroma, $cor, $estilo, $preco, $stock_atual, 'admin', $imagem]);
            
            $mensagem = "<div class='alert success'>✨ Vela adicionada ao inventário com sucesso!</div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert error'>❌ Erro ao adicionar à base de dados: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $mensagem = "<div class='alert error'>❌ Por favor, preenche o Nome e o Preço da vela.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Nova Vela | Bougivon Admin</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #fdfaf6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h2 { color: #8d6e63; font-family: 'Georgia', serif; font-size: 1.8em; margin-bottom: 25px; text-align: center; }
        
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 8px; color: #5d4037; font-size: 0.9em; }
        input[type="text"], input[type="number"], select { padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1em; background-color: #fff; }
        input:focus, select:focus { border-color: #8d6e63; outline: none; }
        
        .btn-submit { 
            background: #8d6e63; 
            color: white; 
            padding: 14px 30px; 
            border: none; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 1em; 
            cursor: pointer; 
            transition: 0.3s; 
            display: block;
            margin: 20px auto 10px auto; 
        }
        .btn-submit:hover { background: #6d4c41; }
        
        .btn-back { display: inline-block; text-align: center; color: #8d6e63; text-decoration: none; font-weight: bold; margin-top: 20px; font-size: 0.9em; }
        .btn-back:hover { text-decoration: underline; }
        
        .alert { padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px; }
        .success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .error { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

<div class="container">
    <h2>Adicionar Nova Vela ao Catálogo</h2>
    
    <?= $mensagem ?>

    <form action="add_candle.php" method="POST">
        <div class="form-group">
            <label for="nome_produto">Nome da Vela:</label>
            <input type="text" id="nome_produto" name="nome_produto" placeholder="Ex: Vela Cilíndrica Aromática" required>
        </div>

        <div class="form-group">
            <label for="imagem">Nome do Ficheiro da Imagem (Ex: baunilha.png):</label>
            <input type="text" id="imagem" name="imagem" placeholder="Ex: baunilha.png, vela_rosa.jpg">
        </div>

        <div class="form-group">
            <label for="aroma">Aroma / Essência:</label>
<select id="aroma" name="aroma">
                <option value="Baunilha">🌼 Baunilha 🌼</option>
                <option value="Chocolate">🍫 Chocolate 🍫</option>
                <option value="Morango">🍓 Morango 🍓</option>
                <option value="Framboesa">🍓🍇 Framboesa 🍓🍇</option>
                <option value="Eucalipto">🌿 Eucalipto 🌿</option>
                <option value="Coco">🥥 Coco 🥥</option>
                <option value="Papaya e Manga">🍈🥭 Papaya e Manga 🍈🥭</option>
                <option value="Jasmim e Bamboo">🌸🎋 Jasmim e Bamboo 🌸🎋</option>
                <option value="Lavanda">🪻 Lavanda 🪻</option>
                <option value="Citronela">🌿🦟 Citronela 🌿🦟</option>
                <option value="Oceano">🌊 Oceano 🌊</option>
                <option value="Black Citrus">🍋🍋‍🟩 Black Citrus 🍋🍋‍🟩</option>
                <option value="Ozono Spa">🫧🍃 Ozono Spa 🫧🍃</option>
                <option value="Pinho">🌲 Pinho 🌲</option>
                <option value="Maçã Verde">🍏 Maçã Verde 🍏</option>
                <option value="Lemongrass"> 🍋🌿 Lemongrass 🍋🌿</option>
                <option value="Cânfora">🌿 Cânfora 🌿</option>
                <option value="Flores Brancas">💐 Flores Brancas 💐</option>
                <option value="Luxury">💍 Luxury 💍</option>
                <option value="Maracujá">🫐🍋 Maracujá 🫐🍋</option>
                <option value="Árvore Nevada">🏔️ Árvore Nevada 🏔️</option>
                <option value="Black Cherry">🍒 Black Cherry 🍒</option>
                <option value="Caramelo">🍯 Caramelo 🍯</option>
                <option value="Chocolate com Leite">🍫🥛 Chocolate com Leite 🍫🥛</option>
                <option value="Doces de Natal">🍬🎄 Doces de Natal 🍬🎄</option>
                <option value="Azevinho">🎄🍒 Azevinho 🎄🍒</option>
                <option value="Boneco de Gengibre">🍪🎄 Boneco de Gengibre 🍪🎄</option>
                <option value="Canela">🪵🍯 Canela 🪵🍯</option>
                <option value="Cookie de Chocolate">🍪🍫 Cookie de Chocolate 🍪🍫</option>
                <option value="Bengala Doce">🍬🌿 Bengala Doce 🍬🌿</option>
                <option value="Lótus">🪷 Lótus 🪷</option>
                <option value="Frutos Vermelhos">🍒🍓 Frutos Vermelhos 🍒🍓</option>
                <option value="Café Extra">☕ Café Extra ☕</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cor">Cor da Vela:</label>
            <select id="cor" name="cor">
                <option value="Branco">⚪ Branco / Natural ⚪</option>
                <option value="Bege">🥟 Bege / Creme 🥟</option>
                <option value="Rosa">🌸 Rosa Pastel / Rosa Velho 🌸</option>
                <option value="Azul">🩵🔷 Azul Celeste / Azul Escuro 🩵🔷</option>
                <option value="Verde">🌿 Verde Sálvia / Verde Menta 🌿</option>
                <option value="Lavanda">🪻 Lavanda / Roxo 🪻</option>
                <option value="Amarelo">💛 Amarelo Pastel 💛</option>
                <option value="Laranja">🍊 Laranja / Terracota 🍊</option>
                <option value="Vermelho">❤️ Vermelho / Borgonha ❤️</option>
                <option value="Castanho">🪵🍫 Castanho / Chocolate 🪵🍫</option>
                <option value="Preto">🖤🩶 Preto / Cinza Escuro 🖤🩶</option>
            </select>
        </div>

        <div class="form-group">
            <label for="estilo">Estilo / Tema da Vela:</label>
            <select id="estilo" name="estilo">
                <option value="Simples" <?= $pref_estilo == 'Simples' ? 'selected' : '' ?>>🕯️ Básico / Simples 🕯️</option>
                <option value="Copo" <?= $pref_estilo == 'Copo' ? 'selected' : '' ?>>🫙 No Copo 🫙</option>
                <option value="Natal" <?= $pref_estilo == 'Natal' ? 'selected' : '' ?>>🎄 Natal 🎄</option>
                <option value="Halloween" <?= $pref_estilo == 'Halloween' ? 'selected' : '' ?>>🎃 Halloween 🎃</option>
                <option value="Páscoa" <?= $pref_estilo == 'Páscoa' ? 'selected' : '' ?>>🐰 Páscoa 🐰</option>
                <option value="S. Valentim" <?= $pref_estilo == 'S. Valentim' ? 'selected' : '' ?>>❤️ Dia dos Namorados ❤️</option>
            </select>
        </div>

        <div class="form-group">
            <label for="preco">Preço (€):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label for="stock_atual">Quantidade em Stock:</label>
            <input type="number" id="stock_atual" name="stock_atual" min="0" value="10" required>
        </div>

        <button type="submit" class="btn-submit">Gravar Vela no Inventário</button>
    </form>

    <div style="text-align: center;">
        <a href="dashboard.php" class="btn-back">← Voltar ao Painel Principal</a>
    </div>
</div>

</body>
</html>