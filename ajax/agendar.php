<?php 
require_once("../sistema/conexao.php");
require_once("../sistema/painel/paginas/evolution/WhatsAppAPI.php");

$whatsapp = new WhatsAppAPI(
    'testeApi',  // ID da instância
    'https://evo.rigsaasatende.com.br',  // URL da API
    'hkkgneylm94uvwtdkst2hj'  // Chave da API
);

$telefone = $_POST['telefone'];
$nome = $_POST['nome'];
$funcionario = $_POST['funcionario'];
$hora = @$_POST['hora'];
$servico = $_POST['servico'];
$obs = $_POST['obs'];
$data = $_POST['data'];
$id = @$_POST['id'];

if($hora == ""){
    echo 'Escolha um Horário para Agendar!';
}

//validar horario
$query = $pdo->query("SELECT * FROM agendamentos where data = '$data' and hora = '$hora' and funcionario = '$funcionario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
    echo 'Este horário não está disponível!';
    exit();
}

// Cadastrar o cliente caso não tenha cadastro
$query = $pdo->query("SELECT * FROM clientes where telefone LIKE '$telefone' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) == 0) {
    // Ignorar 'ultimo_servico' no INSERT
    $query = $pdo->prepare("INSERT INTO clientes (nome, telefone, data_cad, cartoes, alertado) VALUES (:nome, :telefone, curDate(), '0', 'Não')");

    $query->bindValue(":nome", "$nome");
    $query->bindValue(":telefone", "$telefone");
    $query->execute();
    $id_cliente = $pdo->lastInsertId();
} else {
    $id_cliente = $res[0]['id'];
}



if($id == ""){
    //marcar o agendamento
    $query = $pdo->prepare("INSERT INTO agendamentos SET funcionario = '$funcionario', cliente = '$id_cliente', hora = '$hora', data = '$data', usuario = '0', status = 'Agendado', obs = :obs, data_lanc = curDate(), servico = '$servico'");

    echo 'Agendado com Sucesso';
    
} else {
    //edito o agendamento
    $query = $pdo->prepare("UPDATE agendamentos SET funcionario = '$funcionario', hora = '$hora', data = '$data', usuario = '0', status = 'Agendado', obs = :obs, data_lanc = curDate(), servico = '$servico' where id = '$id'");

    echo 'Editado com Sucesso';
}

$query->bindValue(":obs", "$obs");
$query->execute();

// Enviar a mensagem
$numeroDestino = $_POST['telefone'];
$numeroLimpo = preg_replace('/\D/', '', $numeroDestino);
// Adiciona o código do país (55) no início
$numeroFormatado = '55' . $numeroLimpo;
$name = $_POST['nome'];
$mensagem = "Olá, $name!\n\n";
$mensagem .= "Estamos felizes em informar que seu agendamento foi realizado com sucesso! 🎉\n\n";
$mensagem .= "Agradecemos pela sua confiança e estamos ansiosos para atendê-lo. Se tiver alguma dúvida ou precisar de mais informações, não hesite em nos contatar.\n\n";
$mensagem .= "Até breve!";
//envia mensagem para gestor evolution gerar o qr-code para o cliente 
$resposta = $whatsapp->sendMessage($numeroFormatado, $mensagem);
?>
