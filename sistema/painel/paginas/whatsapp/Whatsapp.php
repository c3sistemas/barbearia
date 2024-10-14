<?php
class WhatsApp {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo; 
    }

    public function inserirWhatsApp($whatsapp, $usuarioId) {
        try {
            $query = $this->pdo->prepare("UPDATE usuarios SET whatsapp = :whatsapp WHERE id = :usuario_id");
            $query->bindValue(":whatsapp", $whatsapp);
            $query->bindValue(":usuario_id", $usuarioId);
            $query->execute();
            return ["success" => true, "message" => "WhatsApp salvo com sucesso!"];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Erro ao salvar o WhatsApp: " . $e->getMessage()];
        }
    }

    public function buscarWhatsApp($usuarioId) {
        try {
            $query = $this->pdo->prepare("SELECT whatsapp FROM usuarios WHERE id = :usuario_id");
            $query->bindValue(":usuario_id", $usuarioId);
            $query->execute();
            
            if ($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC)['whatsapp'];
            }
            return null; 
        } catch (Exception $e) {
            return null; 
        }
    }
}
?>
