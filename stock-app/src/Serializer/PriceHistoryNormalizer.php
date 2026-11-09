<?php

namespace App\Serializer;

use App\Entity\PriceHistory;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class PriceHistoryNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var PriceHistory $object */
        $data = [
            'id' => $object->getId(),
            'article_id' => $object->getArticle()?->getId(),
            'supplier_id' => $object->getSupplier()?->getId(),
            'price' => $object->getPrice(),
            'currency' => $object->getCurrency(),
            'price_type' => $object->getPriceType()?->value ?? $object->getPriceType(),
            'price_type_label' => $object->getPriceTypeLabel(),
            'unit_price' => $object->getUnitPrice(),
            'packaging_price' => $object->getPackagingPrice(),
            'valid_from' => $object->getValidFrom()->format('Y-m-d'),
            'valid_to' => $object->getValidTo()?->format('Y-m-d'),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_relations']) && $context['with_relations']) {
            if ($object->getArticle()) {
                $article = $object->getArticle();
                $data['article'] = [
                    'id' => $article->getId(),
                    'name' => $article->getName(),
                    'brand' => $article->getBrand(),
                    'packaging_unit_quantity' => $article->getPackagingUnitQuantity(),
                    'unit' => $article->getUnit() ? [
                        'id' => $article->getUnit()->getId(),
                        'name' => $article->getUnit()->getName(),
                    ] : null,
                    'packaging' => $article->getPackaging() ? [
                        'id' => $article->getPackaging()->getId(),
                        'name' => $article->getPackaging()->getName(),
                    ] : null,
                ];
            }
            if ($object->getSupplier()) {
                $data['supplier'] = [
                    'id' => $object->getSupplier()->getId(),
                    'name' => $object->getSupplier()->getName(),
                ];
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof PriceHistory;
    }
}
