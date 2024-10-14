<?php

require '../vendor/autoload.php'; 

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Iniciando a sessão cURL
$curl = curl_init();

// Definindo a URL da requisição
curl_setopt_array($curl, [
    CURLOPT_URL => "https://evo.rigsaasatende.com.br/instance/connect/teste2?number=5547974002478",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "apikey: dc4dacb15880f8fa4a0d4052827b951c"
    ],
]);

// Executando a requisição
$response = curl_exec($curl);
$err = curl_error($curl);

// Fechando a sessão cURL
curl_close($curl);

// Tratando o resultado da requisição
if ($err) {
    echo "cURL Error #:" . $err; // Mostra o erro, se houver
} else {
    // Mostra a resposta da API
    echo "Resposta da API: " . $response . "\n";

    // Tenta decodificar o JSON da resposta
    $data = json_decode($response, true);

    // Verifica se a decodificação foi bem-sucedida
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "JSON decodificado com sucesso.\n";
        
        // Debugar para verificar a estrutura do JSON
        var_dump($data);

        // Verifique a estrutura do JSON antes de tentar acessar a string base64
        if (isset($data['data']) && is_array($data['data']) && isset($data['data']['qrCode'])) {
            $qrCodeData = $data['data']['qrCode'];

            // Verificar se a string base64 é válida
            if (base64_encode(base64_decode($qrCodeData, true)) === $qrCodeData) {
                // Gerar QR Code com a string Base64
                $qrCode = new QrCode($qrCodeData); // Utilize a string base64 diretamente

                $writer = new PngWriter();

                // Especificar o caminho para salvar a imagem do QR Code
                $filePath = 'caminho/do/arquivo.png'; // Altere para o caminho desejado
                $result = $writer->write($qrCode);
                $result->saveToFile($filePath);

                echo 'Código QR gerado com sucesso! Salvo em: ' . $filePath . "\n";
            } else {
                echo "A string base64 não é válida.\n";
            }
        } else {
            echo "Dados do QR Code não encontrados no JSON.\n";
            // Mostre toda a estrutura de $data para ver como acessá-la corretamente
            var_dump($data);
        }
    } else {
        echo "Erro ao decodificar JSON: " . json_last_error_msg() . "\n";
    }
}
?>
