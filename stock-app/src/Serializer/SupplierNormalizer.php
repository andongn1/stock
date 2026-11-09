<?php

namespace App\Serializer;

use App\Entity\Supplier;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class SupplierNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Supplier $object */
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'contact_info' => $object->getContactInfo(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_counts']) && $context['with_counts']) {
            $data['price_histories_count'] = $object->getPriceHistories()->count();
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Supplier;
    }
}
