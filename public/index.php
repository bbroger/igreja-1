<?php

setlocale(LC_ALL, "pt_BR.UTF-8");

require '../vendor/autoload.php';

spl_autoload_register(function ($classname) {
    require ("../classes/" . $classname . ".php");
});


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "localhost";
$config['db']['user']   = "test";
$config['db']['pass']   = "test";
$config['db']['dbname'] = "igreja";

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App(["settings" => $config, 'view' => '\Slim\LayoutView', 'layout' => '../layouts/dashboard.phtml']);

$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    "users" => [
        "igreja" => "fausto"
    ]
]));

$app->get("/admin", function() {
    ob_start();
    phpinfo();
    $info = ob_get_contents();
    ob_end_clean();
    echo $info;
});


$container = $app->getContainer();

#######################################################################################
$container['view'] = new \Slim\View("../templates/");
$container['view']->setLayout("../layouts/layout.phtml");

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);

    return $pdo;
};

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

## EX ##
#   $this->logger->addInfo("ADD LOG");
##
#######################################################################################

/*
$app->get('/pessoas', function ($request, $response) {

    $pessoaQuery = new PessoaQuery($this->db);
    $pessoas = $pessoaQuery->getPessoas();

    $response = $this->view->render($response, "pessoas.phtml", ["pessoas" => $pessoas]);

    return $response;
});
*/

/*
$app->any('/bilhetes', function ($request, $response) {

    $args = $request->getParsedBody();
    $pessoaQuery = new PessoaQuery($this->db);
    $data = $pessoaQuery->getPessoas();

    $mensagem = nl2br($args['mensagem']);
 
    $response = $this->view->render($response, "bilhete.phtml", ["data" => $data, "mensagem" => $mensagem]);
    return $response;

});
*/


$app->get('/', function ($request, $response) {
    return $response->withRedirect("/membros");
});

$app->get('/membro/editar/{id}', function ($request, $response, $args) {

    $pessoaQuery = new PessoaQuery($this->db);
    $pessoa = $pessoaQuery->get($args['id']);

    $response = $this->view->render($response, "editar.phtml", ["pessoa" => $pessoa]);

    return $response;
});

$app->get('/membro/{id}', function ($request, $response, $args) {

    $id = filter_var($args['id'], FILTER_SANITIZE_STRING);    

    $pessoaQuery = new PessoaQuery($this->db);
    $pessoa = $pessoaQuery->get($id);

    $pagamentoQuery = new PagamentoQuery($this->db);
    $pagamentos = $pagamentoQuery->getPagamentos($id);

    $response = $this->view->render($response, "pessoa.phtml", ["pessoa" => $pessoa, "pagamentos" => $pagamentos]);

    return $response;
});

$app->map(['GET', 'POST'], '/membros', function ($request, $response) {

    $args = $request->getParsedBody();
    $pessoaQuery = new PessoaQuery($this->db);

    $contagem = $pessoaQuery->numMembros();

    $l = ucwords(strtolower(filter_var($args['l'], FILTER_SANITIZE_STRING)));
    $pessoas = $pessoaQuery->getLike($l);


    $response = $this->view->render($response, "pessoas.phtml", ["pessoas" => $pessoas, "contagem" => $contagem]);

    return $response;
});

$app->post('/add', function ($request, $response) {

    $args = $request->getParsedBody();
    $pessoaQuery = new PessoaQuery($this->db);

    $nome           = ucwords(strtolower(filter_var($args['nome'], FILTER_SANITIZE_STRING)));
    $sobrenome      = ucwords(strtolower(filter_var($args['sobrenome'], FILTER_SANITIZE_STRING)));
    $status         = ucwords(strtolower(filter_var($args['status'], FILTER_SANITIZE_STRING)));
    $data_cadastro  = date('Y-m-d H:i:s');

    $pessoaQuery->add($nome, $sobrenome, $status, $data_cadastro);
    return $response->withRedirect("/membros");
});

$app->get('/add', function ($request, $response) {

    $response = $this->view->render($response, "add.phtml");

    return $response;
});

$app->post('/del', function ($request, $response) {

    $args = $request->getParsedBody();
    $pessoaQuery = new PessoaQuery($this->db);
    $id           = filter_var($args['id'], FILTER_SANITIZE_STRING);

    $pessoaQuery->del($id);

    return $response->withRedirect("/membros");
});

$app->post('/edit', function ($request, $response) {

    $args = $request->getParsedBody();
    $pessoaQuery = new PessoaQuery($this->db);

    $id             = filter_var($args['id'], FILTER_SANITIZE_STRING);
    $nome           = ucwords(strtolower(filter_var($args['nome'], FILTER_SANITIZE_STRING)));
    $sobrenome      = ucwords(strtolower(filter_var($args['sobrenome'], FILTER_SANITIZE_STRING)));
    $status         = ucwords(strtolower(filter_var($args['status'], FILTER_SANITIZE_STRING)));

    $pessoaQuery->alt($id, $nome, $sobrenome, $status);

    return $response->withRedirect("/membros");
});

$app->get('/bilhete', function ($request, $response) {
    $data = [];
    return $this->view->render($response, "bilhete.phtml", ["data" => $data]);
});

$app->post('/bilhete', function ($request, $response) {

    $args = $request->getParsedBody();
    $mensagem = $args['mensagem'];
    $colunas = $args['colunas'];

    $pessoaQuery = new PessoaQuery($this->db);
    $pessoas = $pessoaQuery->getPessoas();

    $pdf = new GerarPdf($pessoas, $mensagem, $colunas, 'Bilhetes');

    $pdf->abrirPdf();
});

$app->get('/ano', function ($request, $response) {
    
    $args = $request->getParsedBody();
    $anoQuery = new AnoQuery($this->db);

    $anos = $anoQuery->getAnos();

    return $this->view->render($response, "ano.phtml", ["anos" => $anos]);
});

$app->post('/ano/del', function($request, $response) {

    $args = $request->getParsedBody();
    $anoQuery = new AnoQuery($this->db);

    $ano             = filter_var($args['ano'], FILTER_SANITIZE_STRING);

    $anoQuery->del($ano);

    return $response->withRedirect("/ano");
});

$app->post('/ano', function ($request, $response) {

    $args = $request->getParsedBody();
    $anoQuery = new AnoQuery($this->db);

    $ano             = filter_var($args['ano'], FILTER_SANITIZE_STRING);
    $valor           = filter_var($args['valor'], FILTER_SANITIZE_STRING);

    $anoQuery->add($ano, $valor);

    return $response->withRedirect("/ano");
});

$app->post('/pagamento/add/programa', function ($request, $response) {

    $args = $request->getParsedBody();
    $pagamentoQuery = new PagamentoQuery($this->db);
    $pessoaQuery = new PessoaQuery($this->db);

    $nome           = ucwords(strtolower(filter_var($args['nome'], FILTER_SANITIZE_STRING)));
    $sobrenome      = ucwords(strtolower(filter_var($args['sobrenome'], FILTER_SANITIZE_STRING)));
    $tipo           = ucwords(strtolower(filter_var($args['tipo'], FILTER_SANITIZE_STRING)));
    $ano            = filter_var($args['ano'], FILTER_SANITIZE_STRING);

    $id = $pessoaQuery->getId($nome, $sobrenome);

    $pagamentoQuery->add($id, $ano, $tipo);

});

$app->post('/pagamento/add', function ($request, $response) {

    $args = $request->getParsedBody();

    $id_pessoa      = filter_var($args['id_pessoa'], FILTER_SANITIZE_STRING);
    $ano            = filter_var($args['ano'], FILTER_SANITIZE_STRING);
    $tipo           = filter_var($args['tipo'], FILTER_SANITIZE_STRING);
    $valor          = filter_var($args['valor'], FILTER_SANITIZE_STRING);

    $pagamentoQuery = new PagamentoQuery($this->db);
    $pagamentoQuery->add($id_pessoa, $ano, $tipo, $valor);

    return $response->withRedirect("/membro/".$id_pessoa);
});

$app->post('/pagamento/del', function ($request, $response) {

    $args = $request->getParsedBody();

    $id_pagamento   = filter_var($args['id_pagamento'], FILTER_SANITIZE_STRING);
    $id_pessoa      = filter_var($args['id_pessoa'], FILTER_SANITIZE_STRING);

    $pagamentoQuery = new PagamentoQuery($this->db);
    $pagamentoQuery->del($id_pagamento);

    return $response->withRedirect("/membro/".$id_pessoa);
});
/*
$app->get('/recibo', function ($request, $response) {
    $args = $request->getParsedBody();

    $id_pessoa          = filter_var($args['id_pessoa'], FILTER_SANITIZE_STRING);
    $tipo               = filter_var($args['tipo'], FILTER_SANITIZE_STRING);
    $valor              = filter_var($args['valor'], FILTER_SANITIZE_STRING);
    $nome               = filter_var($args['nome'], FILTER_SANITIZE_STRING);
    
    $anoouanos

    $pagamentoQuery = new PagamentoQuery($this->db);
    $pagamentoQuery->add($id_pessoa, $ano, $tipo, $valor);

    $pagamento = $pagamentoQuery->getLastIdValor($id_pessoa);

    $id_pagamento   = $pagamento['id_pagamento'];
    $valor          = $pagamento['valor'];

    $gerarRecibo = new GerarRecibo($id_pagamento, $valor, $nome);
    return $gerarRecibo->abrirPdf();

});
*/
$app->run();
