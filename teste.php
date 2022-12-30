<?php

require_once 'vendor/autoload.php';

use Alura\Leilao\Helper\EntityManagerCreator;
use Alura\Leilao\Model\Leilao;

$entityManager = EntityManagerCreator::createEntityManager();

// $leilao = new Leilao('Oi');

// $entityManager->persist($leilao);
// $entityManager->flush();

/** @var Leilao */
$busca = $entityManager->find(Leilao::class, 17);

var_dump($busca); 