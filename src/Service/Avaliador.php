<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;

class Avaliador
{
    private float $maiorValor = -INF;
    private float $menorValor = INF;
    /** @var Lance[] */
    private array $maioresLances;

    public function avalia(Leilao $leilao): void
    {
        if ($leilao->getFinalizado()) {
            throw new \DomainException('Leilão não pode ser avaliado após ser finalizado!');
        }

        $lances = $leilao->getLances();

        if (empty($lances)) {
            throw new \DomainException('Não é possível avaliar leilão vazio!');
        }

        foreach ($lances as $lance) {
            if ($lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();                
            } 
            
            if ($lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }
        }

        usort($lances, fn(Lance $l1, Lance $l2) => $l2->getValor() - $l1->getValor());
        
        $this->maioresLances = array_slice($lances, 0, 3);
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    public function getMaioresLances(): array
    {
        return $this->maioresLances;
    }
}