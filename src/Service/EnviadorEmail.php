<?php 

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    public function notificarTerminoLeilao(Leilao $leilao): void
    {
        $enviado = mail(
            'email@gmail.com', 
            'Leilão finalizado', 
            "O Leilão para {$leilao->getDescricao()}"
        );

        if (!$enviado) 
            throw new \DomainException('Erro ao enviar email');
    }
}