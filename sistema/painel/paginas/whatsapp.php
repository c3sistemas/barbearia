<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar o buffer de saída
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar a sessão apenas se ainda não estiver ativa
}

// Verifique se o usuário está logado
if (isset($_SESSION['id'])) {
    // Acessando as variáveis de sessão
    $userId = $_SESSION['id'];
    $userNivel = $_SESSION['nivel'];
    $userName = $_SESSION['nome'];
} else {
    echo "Usuário não está logado.";
    exit(); // Saia se o usuário não estiver logado
}

// Incluir o arquivo da classe WhatsApp
require_once(__DIR__ . '/whatsapp/Whatsapp.php'); 
require_once(__DIR__ . '/evolution/WhatsAppAPI.php'); 

// Inicializar a classe com os parâmetros da API
$agentEvo = new WhatsAppAPI(
    'testeApi',  // ID da instância
    'https://evo.rigsaasatende.com.br',  // URL da API
    'hkkgneylm94uvwtdkst2hj'  // Chave da API
);

// Inicializar parâmetros da API
$instance = 'barbearia';
$url = 'https://evo.rigsaasatende.com.br'; // URL do seu servidor
$apikey = 'dc4dacb15880f8fa4a0d4052827b951c';

$whatsappAPI = new WhatsAppAPI($instance, $url, $apikey);

// Conectar ao banco de dados
require_once(__DIR__ . '/../../conexao.php');
$whatsappObj = new WhatsApp($pdo); // Instanciar a classe

// Variável para armazenar o WhatsApp inserido e o valor do input
$whatsappInserido = '';
$whatsappInputValue = ''; // Variável para manter o valor do input

// Tratamento do POST e verificar se o botão "Criar Instância" foi clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_instancia'])) {
    // Verifique se os dados foram enviados
    if (empty($_POST['whatsapp']) || empty($_POST['id'])) {
        echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        exit();
    }

    // Obtendo os dados do POST
    $whatsapp = $_POST['whatsapp'];
    $idUsuario = $_POST['id'];

    // Chamada da função para inserir o número no banco de dados
    $response = $whatsappObj->inserirWhatsApp($whatsapp, $idUsuario);

    // Chamar a função para enviar a mensagem para o Agente Evo, somente após o sucesso da inserção
    if ($response['success']) {
        // Armazenar o WhatsApp inserido
        $whatsappInserido = $whatsapp;
        $whatsappInputValue = ''; // Limpar o input após salvar

        // Enviar a mensagem
        $numeroDestino = '5547974002478';
        $contatoEvo = $_SESSION['nome'];
        $mensagem = "Olá, eu sou $contatoEvo, meu número de WhatsApp é $whatsapp, e acabei de fazer a solicitação para a ativação da minha instância de notificação dos clientes."; 
        //envia mensagem para gestor evolution gerar o qr-code para o cliente 
        $resposta = $agentEvo->sendMessage($numeroDestino, $mensagem);

        // Definir os parâmetros para criar a instância
        $instanceName = $whatsapp;
        $token = bin2hex(random_bytes(6));
        $number = $whatsapp; 
        // Cria uma instancia com o nome o número do whatsapp do cliente
        $response = $whatsappAPI->createInstance($instanceName, $token, $number);


        // Exibir a mensagem de sucesso ou erro
        if ($resposta) {
            echo "<p align='center'>Mensagem enviada com sucesso para $numeroDestino.</p>";
        } else {
            echo "<p align='center'>Erro ao enviar a mensagem para $numeroDestino.</p>";
        }
    } else {
        echo json_encode($response);
        exit(); // Para evitar a execução do código HTML abaixo após o POST
    }
}

// Buscar WhatsApp do usuário ao carregar a página
$whatsappSalvo = $whatsappObj->buscarWhatsApp($userId); // Buscar WhatsApp do usuário

// Verifique se há um WhatsApp salvo
$whatsappJaSalvo = !empty($whatsappSalvo);
?>

<form id="form" method="POST">
    <div class="modal-body">
        <p>
            Você está criando uma instância de WhatsApp para realizar contato com seus clientes no momento de agendamento do serviço e para notificá-los uma hora antes do atendimento. 
            Por favor, insira o número comercial no formato correto: <strong>55XXXXXXXXXX</strong> (Ex: 5547974002478).
        </p>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="whatsapp">Número de WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="55XXXXXXXXXX" required maxlength="13" value="<?php echo htmlspecialchars($whatsappInputValue); ?>" <?php echo $whatsappJaSalvo ? 'disabled' : ''; ?>>
                    <small class="text-muted">Formato: 55 + código de área + número (somente números)</small>
                </div>
            </div>
        </div>

        <input type="hidden" name="id" id="id" value="<?php echo $_SESSION['id']; ?>"> <!-- ID do usuário -->

        <br>
        <small>
            <div id="mensagem" align="center"></div>
        </small>
    </div>

    <div class="modal-footer">      
        <button type="submit" name="criar_instancia" class="btn btn-primary" <?php echo $whatsappJaSalvo ? 'disabled' : ''; ?>>Criar Instância</button>
    </div>
</form>

<?php
// Exibir o WhatsApp inserido abaixo do formulário, se houver
if (!empty($whatsappSalvo)) {
    // Obtenha o nome do utilizador a partir da sessão
    $userName = htmlspecialchars($_SESSION['nome']);
    echo "<div align='center' style='text-align: justify;'>
            <strong>Caríssimo(a) {$userName},</strong>
            <p>
                A sua instância foi criada com sucesso com o número <strong>{$whatsappSalvo}</strong>. 
                A nossa equipe foi notificada, e logo entraremos em contato para realizar a liberação. 
                Obrigado por confiar nos nossos serviços!
            </p>
          </div>";
}

// Limpar o buffer de saída e enviar o conteúdo para o navegador
ob_end_flush();
?>
