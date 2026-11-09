<?php

namespace App\Serializer;

use App\Entity\Packaging;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class PackagingNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Packaging $object */
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_counts']) && $context['with_counts']) {
            $data['articles_count'] = $object->getArticles()->count();
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Packaging;
    }
}
