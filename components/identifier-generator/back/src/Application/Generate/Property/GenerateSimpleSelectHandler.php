<?php

declare(strict_types=1);

namespace Akeneo\Pim\Automation\IdentifierGenerator\Application\Generate\Property;

use Akeneo\Pim\Automation\IdentifierGenerator\Application\Exception\UnableToTruncateException;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\IdentifierGenerator;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\ProductProjection;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Property\Process;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Property\PropertyInterface;
use Akeneo\Pim\Automation\IdentifierGenerator\Domain\Model\Property\SimpleSelectProperty;
use Webmozart\Assert\Assert;

/**
 * @copyright 2023 Akeneo SAS (https://www.akeneo.com)
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class GenerateSimpleSelectHandler implements GeneratePropertyHandlerInterface
{
    public function getPropertyClass(): string
    {
        return SimpleSelectProperty::class;
    }

    public function __invoke(
        PropertyInterface $simpleSelectProperty,
        IdentifierGenerator $identifierGenerator,
        ProductProjection $productProjection,
        string $prefix
    ): string {
        Assert::isInstanceOf($simpleSelectProperty, SimpleSelectProperty::class);
        $normalizedData = $simpleSelectProperty->normalize();
        $value = $productProjection->value(
            $normalizedData['attributeCode'],
            $normalizedData['locale'] ?? null,
            $normalizedData['scope'] ?? null,
        );
        Assert::string($value);

        switch ($simpleSelectProperty->process()->type()) {
            case Process::PROCESS_TYPE_TRUNCATE:
                Assert::integer($simpleSelectProperty->process()->value());
                if ($simpleSelectProperty->process()->operator() === Process::PROCESS_OPERATOR_EQ) {
                    try {
                        Assert::minLength($value, $simpleSelectProperty->process()->value());
                    } catch (\InvalidArgumentException) {
                        throw new UnableToTruncateException(
                            \sprintf('%s%s', $prefix, $value),
                            $identifierGenerator->target()->asString(),
                            $value
                        );
                    }
                }

                return \substr($value, 0, $simpleSelectProperty->process()->value());
            case Process::PROCESS_TYPE_NO:
                return $value;
            default:
                throw new \InvalidArgumentException('Not implemented yet');
        }
    }
}