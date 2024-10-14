<?php

// Incluindo o arquivo da classe WhatsAppAPI
require 'WhatsAppAPI.php';

// Definindo as credenciais da API
$url = 'https://evo.rigsaasatende.com.br'; // URL do seu servidor
$apiKey = 'dc4dacb15880f8fa4a0d4052827b951c'; // chave API (certifique-se de que esta seja a chave correta)

// Criando uma nova instância da classe WhatsAppAPI
$whatsappApi = new WhatsAppAPI("", $url, $apiKey); // A instância pode ser vazia para listar todas

// Listar instâncias
$instancesResponse = $whatsappApi->fetchInstances();

// Verificando a resposta
if (isset($instancesResponse)) {
    echo "Instâncias disponíveis:\n";
    print_r($instancesResponse); // Imprime as instâncias listadas
} else {
    echo "Falha ao listar instâncias.";
}
