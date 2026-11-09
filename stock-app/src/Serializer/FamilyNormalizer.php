<?php

namespace App\Serializer;

use App\Entity\Family;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class FamilyNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Family $object */
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_counts']) && $context['with_counts']) {
            $data['sub_families_count'] = $object->getSubFamilles()->count();
            $data['articles_count'] = $object->getArticles()->count();
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Family;
    }
}
