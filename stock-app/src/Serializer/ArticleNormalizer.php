<?php

namespace App\Serializer;

use App\Entity\Article;
use App\Entity\PriceHistory;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ArticleNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Article $object */
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'brand' => $object->getBrand(),
            'family_id' => $object->getFamily()?->getId(),
            'sub_family_id' => $object->getSubFamily()?->getId(),
            'unit_id' => $object->getUnit()?->getId(),
            'packaging_id' => $object->getPackaging()?->getId(),
            'packaging_unit_quantity' => $object->getPackagingUnitQuantity(),
            'vat_id' => $object->getVat()?->getId(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_relations']) && $context['with_relations']) {
            if ($object->getFamily()) {
                $data['family'] = [
                    'id' => $object->getFamily()->getId(),
                    'name' => $object->getFamily()->getName(),
                ];
            }
            if ($object->getSubFamily()) {
                $data['sub_family'] = [
                    'id' => $object->getSubFamily()->getId(),
                    'name' => $object->getSubFamily()->getName(),
                ];
            }
            if ($object->getUnit()) {
                $data['unit'] = [
                    'id' => $object->getUnit()->getId(),
                    'name' => $object->getUnit()->getName(),
                ];
            }
            if ($object->getPackaging()) {
                $data['packaging'] = [
                    'id' => $object->getPackaging()->getId(),
                    'name' => $object->getPackaging()->getName(),
                ];
            }
            if ($object->getVat()) {
                $data['vat'] = [
                    'id' => $object->getVat()->getId(),
                    'rate' => $object->getVat()->getRate(),
                    'label' => $object->getVat()->getLabel(),
                ];
            }
        }

        // Current price
        $currentPrice = $this->getCurrentPrice($object);
        if ($currentPrice) {
            $data['current_price'] = [
                'price' => $currentPrice->getPrice(),
                'currency' => $currentPrice->getCurrency(),
                'supplier' => $currentPrice->getSupplier()->getName(),
                'valid_from' => $currentPrice->getValidFrom()->format('Y-m-d'),
            ];
        } else {
            $data['current_price'] = null;
        }

        if (isset($context['with_counts']) && $context['with_counts']) {
            $data['price_histories_count'] = $object->getPriceHistories()->count();
        }

        if (isset($context['with_price_histories']) && $context['with_price_histories'] && $object->getPriceHistories()->count() > 0) {
            $data['price_histories'] = array_map(function(PriceHistory $ph) {
                return [
                    'id' => $ph->getId(),
                    'article_id' => $ph->getArticle()->getId(),
                    'supplier_id' => $ph->getSupplier()->getId(),
                    'price' => $ph->getPrice(),
                    'currency' => $ph->getCurrency(),
                    'price_type' => $ph->getPriceType()?->value ?? $ph->getPriceType(),
                    'price_type_label' => $ph->getPriceTypeLabel(),
                    'unit_price' => $ph->getUnitPrice(),
                    'packaging_price' => $ph->getPackagingPrice(),
                    'valid_from' => $ph->getValidFrom()->format('Y-m-d'),
                    'valid_to' => $ph->getValidTo()?->format('Y-m-d'),
                    'article' => [
                        'id' => $ph->getArticle()->getId(),
                        'name' => $ph->getArticle()->getName(),
                        'brand' => $ph->getArticle()->getBrand(),
                        'packaging_unit_quantity' => $ph->getArticle()->getPackagingUnitQuantity(),
                        'unit' => $ph->getArticle()->getUnit() ? [
                            'id' => $ph->getArticle()->getUnit()->getId(),
                            'name' => $ph->getArticle()->getUnit()->getName(),
                        ] : null,
                        'packaging' => $ph->getArticle()->getPackaging() ? [
                            'id' => $ph->getArticle()->getPackaging()->getId(),
                            'name' => $ph->getArticle()->getPackaging()->getName(),
                        ] : null,
                    ],
                    'supplier' => [
                        'id' => $ph->getSupplier()->getId(),
                        'name' => $ph->getSupplier()->getName(),
                    ],
                    'created_at' => $ph->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $ph->getUpdatedAt()->format('Y-m-d H:i:s'),
                ];
            }, $object->getPriceHistories()->toArray());
        }

        return $data;
    }

    private function getCurrentPrice(Article $article): ?PriceHistory
    {
        $now = new \DateTime();
        foreach ($article->getPriceHistories() as $ph) {
            if ($ph->getValidFrom() <= $now && 
                ($ph->getValidTo() === null || $ph->getValidTo() >= $now)) {
                return $ph;
            }
        }
        return null;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Article;
    }
}
