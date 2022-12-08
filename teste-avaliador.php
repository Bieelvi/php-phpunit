<?php

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;

require 'vendor/autoload.php';

// preparado o código para ser testado -- arrange // given
$leilao = new Leilao('Corsinha Rebaixado');

$maria = new Usuario('Maria');
$joao = new Usuario('Joao');

$leilao->recebeLance(new Lance($maria, 2500));
$leilao->recebeLance(new Lance($joao, 2550));
$leilao->recebeLance(new Lance($maria, 3500));
$leilao->recebeLance(new Lance($joao, 3705));

$leiloeiro = new Avaliador();
// executo o código a ser testado -- act -- when
$leiloeiro->avalia($leilao);

$maiorValor = $leiloeiro->getMaiorValor();

// verifico saida do código testado -- assert -- then
$valorEsperado = 3705;

if ($maiorValor == $valorEsperado) {
    echo "TESTE OK";
} else {
    echo "TESTE FALHOU";
}