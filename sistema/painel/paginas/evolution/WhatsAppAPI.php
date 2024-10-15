<?php

class WhatsAppAPI {
    private $instance;
    private $url;
    private $apikey;

    // Construtor para inicializar os parâmetros da API
    public function __construct($instance, $url, $apikey) {
        $this->instance = $instance;
        $this->url = $url;
        $this->apikey = $apikey;
    }

    // Função para enviar mensagens de texto
    public function sendMessage($number, $message, $delay = 0) {
        // Montar os dados da requisição
        $data = [
            "number" => $number,  // O número de destino
            "textMessage" => [
                "text" => $message  // A mensagem que será enviada
            ],
            "options" => [
                "delay" => $delay,  // Atraso opcional no envio (em milissegundos)
                "presence" => "composing",  // Indica que o remetente está a escrever
                "linkPreview" => true,  // Ativar visualização de links (se houver)
                "mentions" => [
                    "everyone" => false,  // Desativar menção a todos
                    "mentioned" => [$number]  // Número de telefone mencionado (opcional)
                ]
            ]
        ];

        // Inicializar cURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$this->url/message/sendText/$this->instance",  // URL da API
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),  // Dados a serem enviados como JSON
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: $this->apikey"
            ]
        ]);

        // Executa a requisição e armazena a resposta
        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);  // Código de status HTTP
        $err = curl_error($curl);

        // Fecha a conexão cURL
        curl_close($curl);

        // Verifica se houve erro no cURL
        if ($err) {
            return "Erro cURL: " . $err;
        } else {
            // Decodifica a resposta JSON
            $response_data = json_decode($response, true);

            // Retorna o código de status HTTP e os dados da resposta
            return [
                "status" => $http_status,
                "response" => $response_data
            ];
        }
    }

    // Função para criar uma nova instância
    public function createInstance($instanceName, $token, $number, $qrcode = true, $integration = "WHATSAPP-BAILEYS", $reject_call = true, $msgCall = "", $webhookUrl = "", $chatwootAccountId = 123) {
        // Montar os dados da requisição para criação da instância
        $data = [
            "instanceName" => $instanceName,
            "token" => $token,
            "number" => $number,
            "qrcode" => $qrcode,
            "integration" => $integration,
            "reject_call" => $reject_call,
            "msgCall" => $msgCall,
            "groupsIgnore" => true,
            "alwaysOnline" => true,
            "readMessages" => true,
            "readStatus" => true,
            "syncFullHistory" => true,
            "webhookUrl" => $webhookUrl,
            "rabbitmqEnabled" => true,
            "chatwootAccountId" => $chatwootAccountId,
            // Adicione mais campos conforme necessário
        ];

        // Inicializar cURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$this->url/instance/create",  // URL da API para criação de instância
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),  // Dados a serem enviados como JSON
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: $this->apikey"
            ],
        ]);

        // Executa a requisição e armazena a resposta
        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);  // Código de status HTTP
        $err = curl_error($curl);

        // Fecha a conexão cURL
        curl_close($curl);

        // Verifica se houve erro no cURL
        if ($err) {
            return "Erro cURL: " . $err;
        } else {
            // Decodifica a resposta JSON
            $response_data = json_decode($response, true);

            // Retorna o código de status HTTP e os dados da resposta
            return [
                "status" => $http_status,
                "response" => $response_data
            ];
        }
    }

    // Função para conectar a instância
    public function connectInstance() {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$this->url/instance/connect/$this->instance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "apikey: $this->apikey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "Erro cURL: " . $err;
        } else {
            return json_decode($response, true);  
        }
    }

    // Função para listar instâncias
    public function fetchInstances() {
        // Inicializar cURL
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => "$this->url/instance/fetchInstances",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "apikey: $this->apikey"
            ],
        ]);
    
        // Executa a requisição e armazena a resposta
        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
    
        // Fecha a conexão cURL
        curl_close($curl);
    
        // Verifica se houve erro no cURL
        if ($err) {
            return ["status" => 500, "error" => $err];
        } else {
            // Decodifica a resposta JSON
            $response_data = json_decode($response, true);
            return [
                "status" => $http_status,
                "response" => $response_data
            ];
        }
    }
    









    
}

?>
