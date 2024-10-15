<?php
require_once('/var/www/html/sistema/conexao.php');
require_once('../../painel/paginas/evolution/WhatsAppAPI.php');

file_put_contents('/var/log/checar_agendamentos.log', "Script Executado em: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Hora atual
$hora_atual = date('Y-m-d H:i:s');

// SQL para buscar agendamentos que estão a menos de 1 hora de acontecer
$sql = "SELECT id, cliente, data, hora FROM agendamentos 
        WHERE whatsapp_enviado = 0 
        AND TIMESTAMPDIFF(MINUTE, '$hora_atual', CONCAT(data, ' ', hora)) BETWEEN 0 AND 60";

$result = $pdo->query($sql); // Alterar para usar $pdo

// Se houver agendamentos encontrados
if ($result->rowCount() > 0) { // Usar rowCount para PDO
    // Inicializar a classe com os parâmetros da API
    $whatsapp = new WhatsAppAPI(
        '718e9425-38b0-4741-91e4-0d4ac4d1f797',  // ID da instância
        'https://evo.rigsaasatende.com.br',  // URL da API
        'hkkgneylm94uvwtdkst2hj'  // Chave da API
    );

    // Loop pelos agendamentos
    while($row = $result->fetch(PDO::FETCH_ASSOC)) { // Alterar para fetch do PDO
        $cliente = $row['cliente'];
        $id = $row['id'];
        $data_agendamento = $row['data'] . ' ' . $row['hora'];

        // Log do agendamento encontrado
        file_put_contents('/var/log/checar_agendamentos.log', "Agendamento encontrado: Cliente $cliente, ID $id, Data: $data_agendamento\n", FILE_APPEND);

        // Número de destino (substitua com o número do cliente)
        $numeroDestino = '5547974002478';  // Ajuste isso para o número do cliente
        $mensagem = "Olá $cliente, lembrete: seu agendamento é em $data_agendamento. O horário está próximo!\n";  // Mensagem de texto personalizada

        // Chamar a função para enviar a mensagem
        $resposta = $whatsapp->sendMessage($numeroDestino, $mensagem);

        // Exibir a resposta da API (opcional)
        file_put_contents('/var/log/checar_agendamentos.log', "Resposta da API: " . print_r($resposta, true) . "\n", FILE_APPEND);

        // Atualizar o status para evitar envio duplicado
        $sql_update = "UPDATE agendamentos SET whatsapp_enviado = 1 WHERE id = :id";
        
        $stmt = $pdo->prepare($sql_update); // Preparar a consulta
        $stmt->bindParam(':id', $id); // Vincular o parâmetro
        $stmt->execute(); // Executar a consulta
    }
} else {
    file_put_contents('/var/log/checar_agendamentos.log', "Nenhum agendamento encontrado.\n", FILE_APPEND);
}
?>
