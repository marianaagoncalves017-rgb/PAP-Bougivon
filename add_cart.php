<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Captação de parâmetros universais (suporta GET e POST)
$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$qtd = isset($_REQUEST['qtd']) ? (int)$_REQUEST['qtd'] : 1;
$opcao = $_REQUEST['opcao'] ?? $_REQUEST['opcao_personalizada'] ?? 'simples';

if ($id <= 0) {
    http_response_code(400);
    exit('ID de produto inválido.');
}

if ($qtd < 1) {
    $qtd = 1;
}

// 2. Inicializar estrutura do carrinho na sessão
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// 3. Gerar chave única para o produto + personalização
$item_key = $id . '_' . md5($opcao);

if (isset($_SESSION['carrinho'][$item_key])) {
    if (is_array($_SESSION['carrinho'][$item_key])) {
        $_SESSION['carrinho'][$item_key]['qtd'] += $qtd;
    } else {
        $_SESSION['carrinho'][$item_key] += $qtd;
    }
} else {
    $_SESSION['carrinho'][$item_key] = [
        'id' => $id,
        'qtd' => $qtd,
        'opcao' => $opcao
    ];
}

// 4. Resposta de sucesso
http_response_code(200);
echo "OK";
exit();
?>