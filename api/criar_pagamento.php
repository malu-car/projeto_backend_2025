<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

$pedido_id  = (int)($_POST['pedido_id'] ?? 0);
$quantidade = (int)($_POST['quantidade'] ?? 0);

$quant_pagamento = 1;

if ($pedido_id <= 0 || $quantidade <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Dados inválidos'
    ]);
    exit;
}

try {
   
    MercadoPagoConfig::setAccessToken(
        'APP_USR-3871915922687609-121614-8d2abda2b85d71e57eb8ed2d56aa5aec-3067578484'
    );

    $db = (new Conexao())->getConexao();

    // Busca o pedido
    $stmt = $db->prepare("SELECT total_liquido FROM pedido WHERE id = ?");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        throw new Exception('Pedido não encontrado');
    }

    $total = (float)$pedido['total_liquido'];

    // Cria preferência
    $client = new PreferenceClient();

    $preference = $client->create([
        "items" => [
            [
                "title" => "Ingresso IF - Pedido {$pedido_id}",
                "quantity" => $quant_pagamento,
                "unit_price" => $total
            ]
        ],
        "external_reference" => "pedido_{$pedido_id}",
        "back_urls" => [
            "success" => "http://localhost/projeto_backend_2025/pages/sucesso.php",
            "failure" => "http://localhost/projeto_backend_2025/pages/erro.php",
        ],
        "auto_return" => "approved"
    ]);

    if (empty($preference->init_point)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Mercado Pago não retornou link de pagamento',
            'debug' => $preference
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'ok',
        'link_pagamento' => $preference->init_point
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'msg' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
