<?php

namespace App\Entity;

use App\Enum\PriceType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'family_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Family $family = null;

    #[ORM\ManyToOne(targetEntity: SubFamily::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'sub_family_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?SubFamily $subFamily = null;

    #[ORM\ManyToOne(targetEntity: Unit::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'unit_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(targetEntity: Packaging::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'packaging_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Packaging $packaging = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $packagingUnitQuantity = null;

    #[ORM\ManyToOne(targetEntity: Vat::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'vat_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Vat $vat = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: PriceHistory::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $priceHistories;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: SellingPrice::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $sellingPrices;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Inventory::class, orphanRemoval: true)]
    private Collection $inventories;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Entry::class, orphanRemoval: true)]
    private Collection $entries;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->priceHistories = new ArrayCollection();
        $this->sellingPrices = new ArrayCollection();
        $this->inventories = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): static
    {
        $this->family = $family;
        return $this;
    }

    public function getSubFamily(): ?SubFamily
    {
        return $this->subFamily;
    }

    public function setSubFamily(?SubFamily $subFamily): static
    {
        $this->subFamily = $subFamily;
        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;
        return $this;
    }

    public function getPackaging(): ?Packaging
    {
        return $this->packaging;
    }

    public function setPackaging(?Packaging $packaging): static
    {
        $this->packaging = $packaging;
        return $this;
    }

    public function getPackagingUnitQuantity(): ?string
    {
        return $this->packagingUnitQuantity;
    }

    public function setPackagingUnitQuantity(?string $packagingUnitQuantity): static
    {
        $this->packagingUnitQuantity = $packagingUnitQuantity;
        return $this;
    }

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): static
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return Collection<int, PriceHistory>
     */
    public function getPriceHistories(): Collection
    {
        return $this->priceHistories;
    }

    public function addPriceHistory(PriceHistory $priceHistory): static
    {
        if (!$this->priceHistories->contains($priceHistory)) {
            $this->priceHistories->add($priceHistory);
            $priceHistory->setArticle($this);
        }
        return $this;
    }

    public function removePriceHistory(PriceHistory $priceHistory): static
    {
        if ($this->priceHistories->removeElement($priceHistory)) {
            if ($priceHistory->getArticle() === $this) {
                $priceHistory->setArticle(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, SellingPrice>
     */
    public function getSellingPrices(): Collection
    {
        return $this->sellingPrices;
    }

    public function addSellingPrice(SellingPrice $sellingPrice): static
    {
        if (!$this->sellingPrices->contains($sellingPrice)) {
            $this->sellingPrices->add($sellingPrice);
            $sellingPrice->setArticle($this);
        }
        return $this;
    }

    public function removeSellingPrice(SellingPrice $sellingPrice): static
    {
        if ($this->sellingPrices->removeElement($sellingPrice)) {
            if ($sellingPrice->getArticle() === $this) {
                $sellingPrice->setArticle(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Inventory>
     */
    public function getInventories(): Collection
    {
        return $this->inventories;
    }

    public function addInventory(Inventory $inventory): static
    {
        if (!$this->inventories->contains($inventory)) {
            $this->inventories->add($inventory);
            $inventory->setArticle($this);
        }
        return $this;
    }

    public function removeInventory(Inventory $inventory): static
    {
        if ($this->inventories->removeElement($inventory)) {
            if ($inventory->getArticle() === $this) {
                $inventory->setArticle(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setArticle($this);
        }
        return $this;
    }

    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            if ($entry->getArticle() === $this) {
                $entry->setArticle(null);
            }
        }
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
     * Get the current price (most recent by valid_from date).
     */
    public function getCurrentPrice(): ?PriceHistory
    {
        $prices = $this->priceHistories->toArray();
        usort($prices, function ($a, $b) {
            $dateA = $a->getValidFrom() ?: new \DateTime();
            $dateB = $b->getValidFrom() ?: new \DateTime();
            if ($dateA == $dateB) {
                return $b->getId() <=> $a->getId();
            }
            return $dateB <=> $dateA;
        });
        return $prices[0] ?? null;
    }

    /**
     * Get the current selling price.
     */
    public function getCurrentSellingPrice(): ?SellingPrice
    {
        $prices = $this->sellingPrices->toArray();
        usort($prices, function ($a, $b) {
            return $b->getId() <=> $a->getId();
        });
        return $prices[0] ?? null;
    }

    /**
     * Get the formatted packaging unit info (e.g., "10 kg", "250 pieces").
     */
    public function getPackagingUnitInfo(): string
    {
        if ($this->packagingUnitQuantity && $this->unit) {
            return $this->packagingUnitQuantity . ' ' . $this->unit->getName();
        }
        return '';
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
