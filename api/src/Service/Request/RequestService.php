<?php

namespace App\Service\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestService
{
    public static function getField(Request $request, string $fieldName, bool $isRequired = true, bool $isArray = false): mixed
    {
        $requestData = \json_decode($request->getContent(), true);

        if ($isArray) {
            $arrayData = self::arrayFlatten($requestData);

            $isFound = array_key_exists($fieldName, $arrayData);
            if ($isFound) {
                return $arrayData[$fieldName];
            }

            if ($isRequired) {
                throw new BadRequestHttpException(\sprintf('Missing field %s', $fieldName));
            }

            return null;
        }

        if (\array_key_exists($fieldName, $requestData)) {
            return $requestData[$fieldName];
        }

        if ($isRequired) {
            throw new BadRequestHttpException(\sprintf('Missing field %s', $fieldName));
        }

        return null;
    }

    public static function arrayFlatten(array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $return = \array_merge($return, self::arrayFlatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}