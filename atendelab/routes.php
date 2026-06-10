<?php

require_once __DIR__ . '/app/Controllers/UsuarioController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {
    $usuariosController = new usuariosController();

    switch ($action) {
        
        case 'listar':
            $usuariosController->listar();
            break;
        
        case 'buscar':
            $usuariosController->buscarPorId();
            break;
        case 'criar':
            $usuariosController->criar();
            break;
        case 'atualizar':
            $usuariosController->atualizar();
            break;
        case 'excluir':
            $usuariosController->excluir();
            break;
        default:
            echo 'Ação de usuários não encontrada';
            break;
    }
}
elseif ($controller === 'pessoas') {
    $pessoasController = new PessoasController();
    switch ($action) {
        
        case 'listar':
            $pessoasController->listar();
            break;
        
        case 'buscar':
            $pessoasController->buscarPorId();
            break;
        case 'criar':
            $pessoasController->criar();
            break;
        case 'atualizar':
            $pessoasController->atualizar();
            break;
        case 'excluir':
            $pessoasController->excluir();
            break;
        default:
            echo 'Ação de pessoas não encontrada';
            break;
    }
}
elseif ($controller === 'atendimentos') {
    $atendimentosController = new AtendimentosController();
    switch ($action) {
        
        case 'listar':
            $atendimentosController->listar();
            break;
        
        case 'buscar':
            $atendimentosController->buscarPorId();
            break;
        case 'criar':
            $atendimentosController->criar();
            break;
        case 'atualizar':
            $atendimentosController->atualizar();
            break;
        case 'excluir':
            $atendimentosController->excluir();
            break;
        default:
            echo 'Ação de atendimentos não encontrada';
            break;
    }
}
elseif ($controller === 'tiposatendimentos') {
    $tiposAtendimentosController = new TiposAtendimentosController();
    switch ($action) {
        
        case 'listar':
            $tiposAtendimentosController->listar();
            break;
        
        case 'buscar':
            $tiposAtendimentosController->buscarPorId();
            break;
        case 'criar':
            $tiposAtendimentosController->criar();
            break;
        case 'atualizar':
            $tiposAtendimentosController->atualizar();
            break;
        case 'excluir':
            $tiposAtendimentosController->excluir();
            break;
        default:
            echo 'Ação de tipos de atendimentos não encontrada';
            break;
    }
}
else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto  em execução. Use ?controller=usuarios&action=listar para testar.</p>';
}
