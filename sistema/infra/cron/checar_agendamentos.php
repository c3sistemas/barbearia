<?php
require_once('/var/www/html/sistema/conexao.php');
require_once('/var/www/html/sistema/painel/paginas/evolution/WhatsAppAPI.php');
require_once('/var/www/html/sistema/vendor/autoload.php');

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html/sistema');
$dotenv->load();

file_put_contents('/var/log/checar_agendamentos.log', "Script Executado em: " . date('d-m-Y H:i:s') . "\n", FILE_APPEND);

// Hora atual
$hora_atual = date('d-m-Y H:i:s');

// SQL para buscar agendamentos que estão a menos de 1 hora de acontecer, juntando com a tabela clientes
$sql = "SELECT ag.id, ag.cliente, ag.data, ag.hora, c.nome, c.telefone 
        FROM agendamentos ag
        LEFT JOIN clientes c ON ag.cliente = c.id  
        WHERE ag.whatsapp_enviado = 0 
        AND TIMESTAMPDIFF(MINUTE, '$hora_atual', CONCAT(ag.data, ' ', ag.hora)) BETWEEN 0 AND 60";

$result = $pdo->query($sql);

// Se houver agendamentos encontrados
if ($result->rowCount() > 0) {
    // Inicializar a classe com os parâmetros da API
    $whatsapp = new WhatsAppAPI(
        $_ENV['NAME_INSTANCE'],   // nome da instancia
        $_ENV['API_URL'],  // URL da API
        $_ENV['API_KEY']   // Chave da API
    );

    // Função para extrair apenas os números de uma string de telefone e adicionar o código do país
    function formatarTelefone($telefone) {
        $numero = preg_replace('/\D/', '', $telefone); // Remove todos os caracteres que não são dígitos
        return '55' . $numero; // Adiciona o código do país antes do número
    }

    // Loop pelos agendamentos
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
        $clienteNome = $row['nome']; // Nome do cliente da tabela clientes
        $numeroDestinoFormatado = $row['telefone']; // Telefone do cliente da tabela clientes
        $id = $row['id'];
        $data_agendamento = $row['data'] . ' ' . $row['hora'];

        // Log do agendamento encontrado
        file_put_contents('/var/log/checar_agendamentos.log', "Agendamento encontrado: Cliente $clienteNome, ID $id, Data: $data_agendamento\n", FILE_APPEND);

        // Formatar o número de destino
        $numeroDestino = formatarTelefone($numeroDestinoFormatado);

        // Mensagem personalizada
        $mensagem = "Olá $clienteNome, lembrete: seu agendamento é em $data_agendamento. O horário está próximo!\n";  

        // Log antes de enviar a mensagem
        file_put_contents('/var/log/checar_agendamentos.log', "Enviando mensagem para $numeroDestino: $mensagem\n", FILE_APPEND);

        // Chamar a função para enviar a mensagem
        $resposta = $whatsapp->sendMessage($numeroDestino, $mensagem);

        // Log da resposta da API
        file_put_contents('/var/log/checar_agendamentos.log', "Resposta da API: " . print_r($resposta, true) . "\n", FILE_APPEND);
        
        // Verificar se a resposta é bem-sucedida
        if ($resposta && isset($resposta['success']) && $resposta['success'] === true) {
            file_put_contents('/var/log/checar_agendamentos.log', "Mensagem enviada com sucesso para $numeroDestino.\n", FILE_APPEND);
        } else {
            file_put_contents('/var/log/checar_agendamentos.log', "Falha ao enviar mensagem para $numeroDestino. Resposta: " . print_r($resposta, true) . "\n", FILE_APPEND);
        }

        // Atualizar o status para evitar envio duplicado
        $sql_update = "UPDATE agendamentos SET whatsapp_enviado = 1 WHERE id = :id";
        
        $stmt = $pdo->prepare($sql_update);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
