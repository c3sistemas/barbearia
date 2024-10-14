<?php

// Incluir o arquivo da classe
include 'WhatsAppAPI.php';  // Descomentar se estiver a usar num arquivo separado

// Inicializar a classe com os parâmetros da API
$whatsapp = new WhatsAppAPI(
    'AbC1230160606df600367e3ea9de5f3f61775c',  // ID da instância
    'https://evo.rigsaasatende.com.br',  // URL da API
    'dc4dacb15880f8fa4a0d4052827b951c'  // Chave da API
);

// Enviar uma mensagem para o número de destino
$numeroDestino = '5547974002478';  // Número de telefone em formato internacional
$mensagem = 'Olá, tudo bem?';  // Mensagem de texto

// Chamar a função para enviar a mensagem
$resposta = $whatsapp->sendMessage($numeroDestino, $mensagem);

// Exibir a resposta da API
print_r($resposta);

?>
