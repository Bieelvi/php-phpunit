<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Avaliador;

class AvaliadorTest extends TestCase
{
    private Avaliador $leiloeiro;

    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    public function leilaoOrdemCrescente()
    {
        $leilao = new Leilao('Computador capenga 2019');
        $maria = new Usuario('maria');
        $james = new Usuario('james');
        $silva = new Usuario('silva');
        $gabs = new Usuario('gabs');
        $katinha = new Usuario('katinha');
        $karol = new Usuario('karol');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($maria, 1555));
        $leilao->recebeLance(new Lance($gabs, 1556));
        $leilao->recebeLance(new Lance($katinha, 4100));
        $leilao->recebeLance(new Lance($silva, 5450));
        $leilao->recebeLance(new Lance($james, 6666));
        $leilao->recebeLance(new Lance($maria, 7845));
        $leilao->recebeLance(new Lance($karol, 7852));
        $leilao->recebeLance(new Lance($jorge, 10555));
    
        return [ "ordem-crescente" => [$leilao] ];
    }

    public function leilaoOrdemDecrescente()
    {
        $leilao = new Leilao('Computador capenga 2019');
        $maria = new Usuario('maria');
        $james = new Usuario('james');
        $silva = new Usuario('silva');
        $gabs = new Usuario('gabs');
        $katinha = new Usuario('katinha');
        $karol = new Usuario('karol');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($jorge, 10555));
        $leilao->recebeLance(new Lance($karol, 7852));
        $leilao->recebeLance(new Lance($maria, 7845));
        $leilao->recebeLance(new Lance($james, 6666));
        $leilao->recebeLance(new Lance($silva, 5450));
        $leilao->recebeLance(new Lance($katinha, 4100));
        $leilao->recebeLance(new Lance($gabs, 1556));
        $leilao->recebeLance(new Lance($maria, 1555));

        return [ "ordem-decrescente" => [$leilao] ];
    }

    public function leilaoOrdemAleatoria()
    {
        $leilao = new Leilao('Computador capenga 2019');
        $maria = new Usuario('maria');
        $james = new Usuario('james');
        $silva = new Usuario('silva');
        $gabs = new Usuario('gabs');
        $katinha = new Usuario('katinha');
        $karol = new Usuario('karol');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($maria, 1555));
        $leilao->recebeLance(new Lance($james, 6666));
        $leilao->recebeLance(new Lance($silva, 5450));
        $leilao->recebeLance(new Lance($gabs, 1556));
        $leilao->recebeLance(new Lance($maria, 7845));
        $leilao->recebeLance(new Lance($katinha, 4100));
        $leilao->recebeLance(new Lance($karol, 7852));
        $leilao->recebeLance(new Lance($jorge, 10555));

        return [ "ordem-aleatoria" => [$leilao] ];
    }

    /**
     * @dataProvider leilaoOrdemCrescente
     * @dataProvider leilaoOrdemDecrescente
     * @dataProvider leilaoOrdemAleatoria
     */
    public function testAvaliadorDeveEncontrarMaiorValor(Leilao $leilao)
    {        // executo o c??digo a ser testado -- act -- when
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        // verifico saida do c??digo testado -- assert -- then

        self::assertEquals(10555, $maiorValor);
    }

    /**
     * @dataProvider leilaoOrdemCrescente
     * @dataProvider leilaoOrdemDecrescente
     * @dataProvider leilaoOrdemAleatoria
     */
    public function testAvaliadorDeveEncontrarMenorValor(Leilao $leilao)
    {
        // executo o c??digo a ser testado -- act -- when
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        // verifico saida do c??digo testado -- assert -- then

        self::assertEquals(1555, $menorValor);
    }

    /**
     * @dataProvider leilaoOrdemCrescente
     * @dataProvider leilaoOrdemDecrescente
     * @dataProvider leilaoOrdemAleatoria
     */
    public function testeAvaliadorDeveEncontrarTresMaioresLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresLances = $this->leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(10555, $maioresLances[0]->getValor());
        self::assertEquals(7852, $maioresLances[1]->getValor());
        self::assertEquals(7845, $maioresLances[2]->getValor());
    }

    public function testLeilaoNaoPodeSerAvaliadoQuandoVazio()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('N??o ?? poss??vel avaliar leil??o vazio!');

        $leilao = new Leilao('Fucas Preto');

        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeReceberPropostasAporFinalizado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leil??o n??o pode receber propostas ap??s ser finalizado!');
    
        $leilao = new Leilao('Fusca Preto, voc?? ?? feito de a??o');
        $karol = new Usuario('karol');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($karol, 1500));
        $leilao->recebeLance(new Lance($jorge, 2000));

        $leilao->finaliza();        
        
        $leilao->recebeLance(new Lance($karol, 2500));
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leil??o n??o pode ser avaliado ap??s ser finalizado!');
    
        $leilao = new Leilao('Fusca Preto, voc?? ?? feito de a??o');
        $karol = new Usuario('karol');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($karol, 1500));
        $leilao->recebeLance(new Lance($jorge, 2000));
        $leilao->finaliza();
        
        $this->leiloeiro->avalia($leilao); 
    }
}
