<?php

namespace App\Entity;

use App\Enum\PriceType;
use App\Repository\PriceHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceHistoryRepository::class)]
#[ORM\Table(name: 'price_histories')]
class PriceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'priceHistories')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Article $article = null;

    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'priceHistories')]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Supplier $supplier = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: Types::STRING, length: 3, options: ['default' => 'EUR'])]
    private string $currency = 'EUR';

    #[ORM\Column(type: Types::STRING, length: 20, enumType: PriceType::class)]
    private PriceType $priceType = PriceType::PER_UNIT;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validTo = null;

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

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
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

    public function getPriceType(): PriceType
    {
        return $this->priceType;
    }

    public function setPriceType(PriceType $priceType): static
    {
        $this->priceType = $priceType;
        return $this;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTimeInterface $validFrom): static
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): static
    {
        $this->validTo = $validTo;
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
     * Get the unit price based on the price type.
     */
    public function getUnitPrice(): float
    {
        if ($this->priceType === PriceType::PER_UNIT) {
            return (float) $this->price;
        }

        // Price is per packaging, calculate unit price
        if ($this->article && $this->article->getPackagingUnitQuantity() > 0) {
            return (float) ($this->price / $this->article->getPackagingUnitQuantity());
        }

        return (float) $this->price;
    }

    /**
     * Get the packaging price based on the price type.
     */
    public function getPackagingPrice(): float
    {
        if ($this->priceType === PriceType::PER_PACKAGING) {
            return (float) $this->price;
        }

        // Price is per unit, calculate packaging price
        if ($this->article && $this->article->getPackagingUnitQuantity() > 0) {
            return (float) ($this->price * $this->article->getPackagingUnitQuantity());
        }

        return (float) $this->price;
    }

    /**
     * Get the formatted price type label.
     */
    public function getPriceTypeLabel(): string
    {
        return $this->priceType->label();
    }
}
