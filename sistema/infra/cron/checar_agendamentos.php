<?php
require_once("../../conexao.php");

// Hora atual
$hora_atual = date('Y-m-d H:i:s');

// SQL para buscar agendamentos que estão a 1 hora de acontecer
$sql = "SELECT id, cliente, data_agendamento FROM agendamentos 
        WHERE whatsapp_enviado = 0 
        AND TIMESTAMPDIFF(MINUTE, '$hora_atual', data_agendamento) = 60";

$result = $pdo->query($sql); // Alterar para usar $pdo

// Se houver agendamentos encontrados
if ($result->rowCount() > 0) { // Usar rowCount para PDO
    // Loop pelos agendamentos
    while($row = $result->fetch(PDO::FETCH_ASSOC)) { // Alterar para fetch do PDO
        $cliente = $row['cliente'];
        $id = $row['id'];
        $data_agendamento = $row['data_agendamento'];

        // Aqui faz o disparo do WhatsApp usando o teu sistema
        // Exemplo de função de envio do WhatsApp
        enviar_whatsapp($cliente, $data_agendamento);

        // Atualizar o status para evitar envio duplicado
        $sql_update = "UPDATE agendamentos SET whatsapp_enviado = 1 WHERE id = :id";
        
        $stmt = $pdo->prepare($sql_update); // Preparar a consulta
        $stmt->bindParam(':id', $id); // Vincular o parâmetro
        $stmt->execute(); // Executar a consulta
    }
} else {
    echo "Nenhum agendamento encontrado.";
}

// Não precisa fechar a conexão manualmente com PDO, pois ele é encerrado automaticamente no final do script

// Função de envio de WhatsApp
function enviar_whatsapp($cliente, $data_agendamento) {
    // Aqui podes fazer a chamada ao sistema de envio de WhatsApp
    // Exemplo simples de log:
    echo "Enviando WhatsApp para o cliente $cliente sobre o agendamento em $data_agendamento\n";

    // Supondo que tenhas uma API ou função PHP que dispara o WhatsApp
    // (Implementa a chamada real ao teu sistema de disparo de WhatsApp aqui)
}
?>
