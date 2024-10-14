<!-- botao.php -->
<div class="">
    <a class="btn btn-primary" data-toggle="modal" data-target="#modalForm">
        <i class="fa fa-plus" aria-hidden="true"></i> Adicionar WhatsApp
    </a>
</div>


<input type="text" id="whatsapp" placeholder="Insira o WhatsApp" />
<input type="hidden" id="id" value="<?php echo $_SESSION['user_id']; ?>" /> <!-- ID do usuário -->

<script>
function inserir() {
    const whatsapp = document.getElementById('whatsapp').value;
    const idUsuario = document.getElementById('id').value;

    // Validar o número de WhatsApp
    if (whatsapp === '' || !/^55\d{11}$/.test(whatsapp)) {
        alert('Por favor, insira um número de WhatsApp válido no formato 5547XXXXXXXX.');
        return;
    }

    // Fazendo a requisição AJAX para enviar o WhatsApp ao backend
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'whatsapp/inserir.php', true); // Caminho para o arquivo inserir.php
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert('WhatsApp salvo com sucesso!');
                // Limpar o campo de WhatsApp após o sucesso
                document.getElementById('whatsapp').value = '';
            } else {
                alert('Erro ao salvar o WhatsApp: ' + response.message);
            }
        }
    };

    // Usar encodeURIComponent para evitar problemas com caracteres especiais
    xhr.send(`whatsapp=${encodeURIComponent(whatsapp)}&id=${encodeURIComponent(idUsuario)}`);
}
</script>
