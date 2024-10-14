<?php
// Incluir o arquivo da classe WhatsAppAPI
require 'WhatsAppAPI.php'; // caminho real do arquivo

// Inicializar parâmetros da API
$instance = 'barbearia';
$url = 'https://evo.rigsaasatende.com.br'; // URL do seu servidor
$apikey = 'dc4dacb15880f8fa4a0d4052827b951c'; // chave API

// Criar uma nova instância da classe WhatsAppAPI
$whatsappAPI = new WhatsAppAPI($instance, $url, $apikey);

// Definir os parâmetros para criar a instância
$instanceName = 'barbearia';
$token = 'g4Tj9kZqW88'; // Substitua pelo seu token
$number = '55974002478'; // Substitua pelo seu número

// Chamar o método createInstance
$response = $whatsappAPI->createInstance($instanceName, $token, $number);

// Exibir a resposta
echo "<pre>";
print_r($response);
echo "</pre>";
?>
