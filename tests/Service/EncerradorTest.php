<?php 

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private Encerrador $encerrador;
    private MockObject $enviadorEmail;
    private Leilao $leilaoPczao;
    private Leilao $leilaoPczinho;

    protected function setUp(): void
    {
        $this->leilaoPczao = new Leilao('Pc TOP!', new \DateTimeImmutable('8 days ago'));
        $this->leilaoPczinho = new Leilao('Pc Méh!', new \DateTimeImmutable('15 days ago'));
        $leiloes = [$this->leilaoPczao, $this->leilaoPczinho];

        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)->getMock();
        $leilaoDao->method('recuperarNaoFinalizados')->willReturn($leiloes);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$leiloes[0]], 
                [$leiloes[1]]
            );

        $this->enviadorEmail = $this->getMockBuilder(EnviadorEmail::class)->getMock();

        /** @var EnviadorEmail */
        $enviadorEmail = $this->enviadorEmail;
        /** @var LeilaoDao */
        $leilaoDao = $leilaoDao;
        $this->encerrador = new Encerrador($leilaoDao, $enviadorEmail);
    }

    public function testLeiloesTemMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        self::assertCount(2, [$this->leilaoPczao, $this->leilaoPczinho]);
        self::assertTrue($this->leilaoPczao->getFinalizado());
        self::assertTrue($this->leilaoPczinho->getFinalizado());
    }

    public function testDeveContinuarOProecessamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('Erro ao enviar email');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }

    public function testSoDeveSerEnviadoLelaoPorEmailAposFinalizado()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                self::assertTrue($leilao->getFinalizado());
            });

        $this->encerrador->encerra();
    }
}