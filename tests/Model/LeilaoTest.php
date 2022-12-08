<?php 

namespace Alura\Leilao\Tests\Model;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function testLeilaoNaoDeveReceberMaisCincoLancesPorUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor cinco lances no mesmo leilão!');

        $leilao = new Leilao('Telcado Red Dragon');
        $maria = new Usuario('Maria');
        $joaoSantoCristo = new Usuario('Joao Santo Cristo');

        $leilao->recebeLance(new Lance($joaoSantoCristo, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joaoSantoCristo, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joaoSantoCristo, 3000));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joaoSantoCristo, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joaoSantoCristo, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));
        
        $leilao->recebeLance(new Lance($joaoSantoCristo, 6000));
    }

    public function testLeiaoNaoDeveReceberLancesSeguidosDoMesmoUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor dois lances seguidos!');

        $leilao = new Leilao('PC pika');
        $ana = new Usuario('ana');

        $leilao->recebeLance(new Lance($ana, 1500));
        $leilao->recebeLance(new Lance($ana, 1700));
    }

    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(Leilao $leilao, int $qtdLances, array $valores)
    {
        self::assertCount($qtdLances, $leilao->getLances());

        foreach ($valores as $key => $valor) {
            self::assertEquals($valor, $leilao->getLances()[$key]->getValor());            
        }
    }

    public function geraLances()
    {
        $leilao = new Leilao('foda-se');
        $leilao2 = new Leilao('Foda-se com F maísculo');

        $maria = new Usuario('maria');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($jorge, 1800));

        $leilao2->recebeLance(new Lance($jorge, 2000));

        return [
            '2-lances' => [$leilao, 2, [1500,1800]],
            '1-lance' => [$leilao2, 1, [2000]]
        ];
    }
}