<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$token_ingresso = $_POST['token_ingresso'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null; 
$dispositivo_uuid = $_POST['dispositivo_uuid'] ?? 'NAO_INFORMADO'; 

if (!$token_ingresso || !$usuario_id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit;
}

$conexao = new Conexao();
$db = $conexao->getConexao();

try {
    $db->beginTransaction();

    $stmt = $db->prepare("
        SELECT i.id as ingresso_id, i.status, i.titular_nome, i.titular_documento, e.nome AS evento_nome, d.id as dispositivo_db_id
        FROM ingresso i
        JOIN pedido p ON i.pedido_id = p.id
        JOIN setor s ON p.setor_id = s.id
        JOIN evento e ON s.evento_id = e.id
        LEFT JOIN dispositivo_checkin d ON d.uuid = ?
        WHERE i.identificador_unico = ?
        AND e.status = 'publicado'
        FOR UPDATE
    ");
    $stmt->execute([$dispositivo_uuid, $token_ingresso]);
    $ingresso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ingresso) {
        if ($db->inTransaction()) $db->rollBack();
        echo json_encode(['status' => 'invalido', 'mensagem' => 'Ingresso não encontrado ou inválido.']);
        exit;
    }
    
    $ingresso_id = $ingresso['ingresso_id'];
    $dispositivo_db_id = $ingresso['dispositivo_db_id'];

    $tentativa_duplicada = false;
    $status_resposta = '';
    $mensagem = '';

    $detalhes = [
        'nome' => $ingresso['titular_nome'],
        'documento' => $ingresso['titular_documento'],
        'evento' => $ingresso['evento_nome']
    ];

    if ($ingresso['status'] === 'utilizado') {
        $tentativa_duplicada = true;
        $mensagem = 'ATENÇÃO: Ingresso já utilizado! ';
        $status_resposta = 'utilizado';

    } elseif ($ingresso['status'] === 'emitido' || $ingresso['status'] === 'transferido') { 

        $stmt_update = $db->prepare("
            UPDATE ingresso SET status = 'utilizado', data_uso = NOW() WHERE id = ?");
        $stmt_update->execute([$ingresso_id]);

        $mensagem = 'CHECK-IN REALIZADO COM SUCESSO.';
        $status_resposta = 'sucesso';
        
    } else {
        $mensagem = "Ingresso em status '{$ingresso['status']}' não pode ser utilizado.";
        $status_resposta = 'invalido';
    }

    $stmt_checkin = $db->prepare("
        INSERT INTO checkin (ingresso_id, dispositivo_id, data_hora, tentativa_duplicada)
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt_checkin->execute([
        $ingresso_id, 
        $dispositivo_db_id, 
        $tentativa_duplicada
    ]);
    
    $db->commit();

    echo json_encode([
        'status' => $status_resposta,
        'mensagem' => $mensagem,
        'detalhes' => $detalhes
    ]);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Erro no check-in: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno do servidor.']);
}
?>