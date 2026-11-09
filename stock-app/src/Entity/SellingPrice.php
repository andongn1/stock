<?php

namespace App\Entity;

use App\Enum\SaleType;
use App\Repository\SellingPriceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellingPriceRepository::class)]
#[ORM\Table(name: 'selling_prices')]
class SellingPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'sellingPrices')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Article $article = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $priceTtc;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $saleType = 'per_unit';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private string $quantity = '1';

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getPriceTtc(): string
    {
        return $this->priceTtc;
    }

    public function setPriceTtc(string $priceTtc): self
    {
        $this->priceTtc = $priceTtc;
        return $this;
    }

    public function getSaleType(): string
    {
        return $this->saleType;
    }

    public function setSaleType(string $saleType): self
    {
        $this->saleType = $saleType;
        return $this;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSaleTypeLabel(): string
    {
        $saleType = SaleType::tryFrom($this->saleType);
        if ($saleType) {
            return $saleType->label();
        }
        return ucfirst(str_replace('_', ' ', $this->saleType));
    }

    public function getFormattedPrice(): string
    {
        return number_format((float)$this->priceTtc, 2) . ' ' . $this->currency;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
