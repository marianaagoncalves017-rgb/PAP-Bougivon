<?php
session_start();

// Verifica se foi passada a chave (key) ou o id
if (isset($_GET['key'])) {
    $key = $_GET['key'];
    if (isset($_SESSION['carrinho'][$key])) {
        unset($_SESSION['carrinho'][$key]);
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
    }
}

// Redireciona de volta para a página do carrinho
header("Location: cart.php");
exit();
?>