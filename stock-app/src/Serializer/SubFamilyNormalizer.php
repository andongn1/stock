<?php

namespace App\Serializer;

use App\Entity\SubFamily;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class SubFamilyNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var SubFamily $object */
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'family_id' => $object->getFamily()?->getId(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if (isset($context['with_relations']) && $context['with_relations']) {
            $data['family'] = $this->normalizer->normalize($object->getFamily(), $format, [
                'groups' => ['family_simple']
            ]);
        }

        if (isset($context['with_counts']) && $context['with_counts']) {
            $data['articles_count'] = $object->getArticles()->count();
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof SubFamily;
    }
}
