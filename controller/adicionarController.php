<?php
include_once "../config/conexao.php";

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Campos obrigatórios
$camposObrigatorios = [
    'nome' => 'nome',
    'custo' => 'custo',
    'dataLimite' => 'dataLimite'
];

foreach ($camposObrigatorios as $campo => $descricao) {
    if (empty($dados[$campo])) {
        $retorna = [
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: necessário preencher o campo de $descricao!</div>"
        ];
        echo json_encode($retorna);
        exit;
    }
}

try {
    // Checagem de nome repetido
    $query_check_nome = "SELECT nome FROM tarefas WHERE nome = ?";
    $stmt_check_nome = $conexao->prepare($query_check_nome);
    $stmt_check_nome->bind_param('s', $dados['nome']);
    $stmt_check_nome->execute();
    $stmt_check_nome->store_result();

    if ($stmt_check_nome->num_rows > 0) {
        echo json_encode([
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tarefa já cadastrada!</div>"
        ]);
        $stmt_check_nome->close();
        exit;
    }
    $stmt_check_nome->close();

    // Determinar a próxima ordem
    $query_max_ordem = "SELECT COALESCE(MAX(ordem), 0) AS max_ordem FROM tarefas";
    $resultado_max_ordem = $conexao->query($query_max_ordem);
    $max_ordem = $resultado_max_ordem->fetch_assoc()['max_ordem'];

    // Inserção no banco de dados
    $query_tarefa = "INSERT INTO tarefas (nome, custo, dataLimite, ordem) VALUES (?, ?, ?, ?)";
    $cad_tarefa = $conexao->prepare($query_tarefa);
    $nova_ordem = $max_ordem + 1;
    $cad_tarefa->bind_param('sdsi', $dados['nome'], $dados['custo'], $dados['dataLimite'], $nova_ordem);
    $cad_tarefa->execute();

    if ($cad_tarefa->affected_rows > 0) {
        $retorna = [
            'status' => true,
            'msg' => "<div class='alert alert-success' role='alert'>Tarefa cadastrada com sucesso!</div>"
        ];
    } else {
        $retorna = [
            'status' => false,
            'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tarefa não cadastrada!</div>"
        ];
    }

    $cad_tarefa->close();

} catch (Exception $e) {
    $retorna = [
        'status' => false,
        'msg' => "<div class='alert alert-danger' role='alert'>Erro: Ocorreu um erro inesperado. Tente novamente mais tarde. {$e->getMessage()}</div>"
    ];
}

header('Content-Type: application/json');
echo json_encode($retorna);
?>
