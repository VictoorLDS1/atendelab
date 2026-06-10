<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT a.id,
                       a.pessoa_id,
                       p.nome           AS pessoa_nome,
                       a.tipo_atendimento,
                       t.nome           AS tipo_nome,
                       a.usuario_id,
                       u.nome           AS usuario_nome,
                       a.data_atendimento,
                       a.hora_atendimento,
                       a.descricao,
                       a.observacao,
                       a.status,
                       a.criado_em
                FROM atendimentos a
                JOIN pessoas              p ON p.id = a.pessoa_id
                JOIN tipos_atendimentos   t ON t.id = a.tipo_atendimento
                JOIN usuarios             u ON u.id = a.usuario_id
                ORDER BY a.id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $sql = 'SELECT a.id,
                       a.pessoa_id,
                       p.nome           AS pessoa_nome,
                       a.tipo_atendimento,
                       t.nome           AS tipo_nome,
                       a.usuario_id,
                       u.nome           AS usuario_nome,
                       a.data_atendimento,
                       a.hora_atendimento,
                       a.descricao,
                       a.observacao,
                       a.status,
                       a.criado_em
                FROM atendimentos a
                JOIN pessoas              p ON p.id = a.pessoa_id
                JOIN tipos_atendimentos   t ON t.id = a.tipo_atendimento
                JOIN usuarios             u ON u.id = a.usuario_id
                WHERE a.id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado']);
            return;
        }

        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id         = trim($_POST['pessoa_id']         ?? '');
        $tipo_atendimento  = trim($_POST['tipo_atendimento']  ?? '');
        $usuario_id        = trim($_POST['usuario_id']        ?? '');
        $data_atendimento  = trim($_POST['data_atendimento']  ?? '');
        $hora_atendimento  = trim($_POST['hora_atendimento']  ?? '');
        $descricao         = trim($_POST['descricao']         ?? '');
        $observacao        = trim($_POST['observacao']        ?? '');
        $status            = $_POST['status']                 ?? 'ABERTO';

        if ($pessoa_id === '' || $tipo_atendimento === '' || $usuario_id === ''
            || $data_atendimento === '' || $hora_atendimento === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento e descricao são obrigatórios']);
            return;
        }

        $statusValidos = ['ABERTO', 'EM_ANDAMENTO', 'CONCLUIDO', 'CANCELADO'];
        if (!in_array($status, $statusValidos, true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ' . implode(', ', $statusValidos)]);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos
                        (pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento, descricao, observacao, status)
                    VALUES
                        (:pessoa_id, :tipo_atendimento, :usuario_id, :data_atendimento, :hora_atendimento, :descricao, :observacao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',        $pessoa_id,        PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento', $tipo_atendimento, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id',       $usuario_id,       PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao',        $descricao);
            $stmt->bindValue(':observacao',       $observacao !== '' ? $observacao : null);
            $stmt->bindValue(':status',           $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar atendimento']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id                = trim($_POST['id']                ?? '');
        $pessoa_id         = trim($_POST['pessoa_id']         ?? '');
        $tipo_atendimento  = trim($_POST['tipo_atendimento']  ?? '');
        $usuario_id        = trim($_POST['usuario_id']        ?? '');
        $data_atendimento  = trim($_POST['data_atendimento']  ?? '');
        $hora_atendimento  = trim($_POST['hora_atendimento']  ?? '');
        $descricao         = trim($_POST['descricao']         ?? '');
        $observacao        = trim($_POST['observacao']        ?? '');
        $status            = $_POST['status']                 ?? 'ABERTO';

        if ($id === '' || $pessoa_id === '' || $tipo_atendimento === '' || $usuario_id === ''
            || $data_atendimento === '' || $hora_atendimento === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'id, pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento e descricao são obrigatórios']);
            return;
        }

        $statusValidos = ['ABERTO', 'EM_ANDAMENTO', 'CONCLUIDO', 'CANCELADO'];
        if (!in_array($status, $statusValidos, true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ' . implode(', ', $statusValidos)]);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET pessoa_id        = :pessoa_id,
                        tipo_atendimento = :tipo_atendimento,
                        usuario_id       = :usuario_id,
                        data_atendimento = :data_atendimento,
                        hora_atendimento = :hora_atendimento,
                        descricao        = :descricao,
                        observacao       = :observacao,
                        status           = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',        $pessoa_id,        PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento', $tipo_atendimento, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id',       $usuario_id,       PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao',        $descricao);
            $stmt->bindValue(':observacao',       $observacao !== '' ? $observacao : null);
            $stmt->bindValue(':status',           $status);
            $stmt->bindValue(':id',               $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Atendimento atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar atendimento']);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        try {
            $sql  = 'DELETE FROM atendimentos WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Atendimento excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir atendimento.']);
        }
    }
}