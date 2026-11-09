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

    #[ORM\Column(type: Types::STRING, length: 20, enumType: SaleType::class)]
    private SaleType $saleType = SaleType::PER_UNIT;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, options: ['default' => '1.0000'])]
    private string $quantity = '1.0000';

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'EUR'])]
    private string $currency = 'EUR';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;
        return $this;
    }

    public function getPriceTtc(): string
    {
        return $this->priceTtc;
    }

    public function setPriceTtc(string $priceTtc): static
    {
        $this->priceTtc = $priceTtc;
        return $this;
    }

    public function getSaleType(): SaleType
    {
        return $this->saleType;
    }

    public function setSaleType(SaleType $saleType): static
    {
        $this->saleType = $saleType;
        return $this;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get the formatted sale type label.
     */
    public function getSaleTypeLabel(): string
    {
        return $this->saleType->label();
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->priceTtc, 2) . ' ' . $this->currency;
    }
}
