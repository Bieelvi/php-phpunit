<?php

namespace Alura\Leilao\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'leiloes')]
class Leilao
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id;
    #[ORM\Column(type: 'string')]
    private string $descricao;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dataInicio;
    #[ORM\Column(type: 'boolean')]
    private bool $finalizado = false;
    /** @var Lance[] */
    private array $lances;

    public function __construct(string $descricao, \DateTimeInterface $dataInicio = null, ?int $id = null)
    {
        $this->descricao = $descricao;
        $this->finalizado = false;
        $this->dataInicio = $dataInicio ?? new \DateTimeImmutable();
        $this->id = $id;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance): void
    {
        if ($this->finalizado) {
            throw new \DomainException('Leilão não pode receber propostas após ser finalizado!');
        }

        if (!empty($this->getLances()) && $this->lanceSeguidoUsuario($lance)) {
            throw new \DomainException('Usuário não pode propor dois lances seguidos!');
        }

        if ($this->qtdLancesPorUsuario($lance) >= 5) {
            throw new \DomainException('Usuário não pode propor cinco lances no mesmo leilão!');
        }

        $this->lances[] = $lance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getDataInicio(): \DateTimeInterface
    {
        return $this->dataInicio;
    }

    public function finaliza(): void
    {
        $this->finalizado = true;
    }

    public function getFinalizado(): bool
    {
        return $this->finalizado;
    }

    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTime();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    private function lanceSeguidoUsuario(Lance $lance): bool
    {
        $utlimoLance = $this->getLances()[array_key_last($this->getLances())];
        return $lance->getUsuario() == $utlimoLance->getUsuario();
    }

    private function qtdLancesPorUsuario(Lance $lance): int
    {
        return array_reduce(
            $this->getLances(), 
            function (int $acumulador, Lance $lanceAtual) use ($lance) {
                if ($lanceAtual->getUsuario() == $lance->getUsuario()) 
                    return $acumulador + 1;
                else 
                    return $acumulador;
            }, 
            0
        );
    }
}
