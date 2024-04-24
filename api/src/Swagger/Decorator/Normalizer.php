<?php

namespace App\Swagger\Decorator;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Normalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;
    private string $endpoint;

    public function __construct(NormalizerInterface $decorated, string $endpoint)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->endpoint = $endpoint;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if ($object instanceof User) {
            $data = $this->decorated->normalize($object, $format, $context);

            if (null !== $avatar = $object->getAvatar()) {
                $data['avatar'] = sprintf('%s%s', $this->endpoint, $avatar);
            }

            return $data;
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}