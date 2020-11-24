<?php

namespace AkeneoTest\Pim\Enrichment\Integration\Product;

use Akeneo\Pim\Enrichment\Component\Product\Factory\Read\ValueCollectionFactory;
use Akeneo\Pim\Enrichment\Component\Product\Factory\WriteValueCollectionFactory;
use Akeneo\Pim\Enrichment\Component\Product\Model\Product;
use Akeneo\Pim\Enrichment\Component\Product\Model\ReadValueCollection;
use Akeneo\Pim\Enrichment\Component\Product\Model\WriteValueCollection;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\Integration\TestCase;
use Webmozart\Assert\Assert;

class ReadValueCollectionFactoryIntegration extends TestCase
{
    /**
     * @test
     * @dataProvider matrix
     */
    public function test_if_product_values_with_wrong_attribute_type_are_skipped(string $attributeCode, $value, bool $skipped): void
    {
        /** @var Product $product */
        $product = $this->get('pim_catalog.builder.product')->createProduct('my_product', 'familyA');
        $this->get('pim_catalog.saver.product')->save($product);

        $rawValues = $product->getRawValues();
        $rawValues[$attributeCode] = [
            '<all_channels>' => [
                '<all_locales>' => $value,
            ],
        ];

        $sql = <<<SQL
UPDATE pim_catalog_product SET raw_values = :raw_values
WHERE identifier = 'my_product';
SQL;
        $this->get('database_connection')->executeQuery($sql, [
            'raw_values' => json_encode($rawValues),
        ]);

        $this->get('pim_connector.doctrine.cache_clearer')->clear();
        /** @var Product $product */
        $product = $this->get('pim_catalog.repository.product')->findOneByIdentifier('my_product');
        $rawValues = $product->getRawValues();

        /** @var ValueCollectionFactory $readValueCollectionFactory */
        $readValueCollectionFactory = $this->get('akeneo.pim.enrichment.factory.read.value_collection');
        /** @var ReadValueCollection $readValueCollection */
        $readValueCollection = $readValueCollectionFactory->createFromStorageFormat($rawValues);

        Assert::eq($skipped, !in_array($attributeCode, $readValueCollection->getAttributeCodes()));
    }

    public function matrix(): array
    {
        return [
            ['a_text', 'some_text', false], // this attribute should not be skipped
            ['a_price', 'some_text', true],
            ['a_price', [
                'unit' => 'SQUARE_METER',
                'amount' => '10.0000',
                'family' => 'Area',
                'base_data' => '10',
                'base_unit' => 'SQUARE_METER',
            ], true],
            ['a_ref_data_multi_select', 'some_text', true],
            ['a_multi_select', 'some_text', true],
            ['a_file', 'some_text', true],
            ['a_text', [
                ['amount' => '10.00', 'currency' => 'EUR'],
                ['amount' => null, 'currency' => 'USB'],
            ], true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): Configuration
    {
        return $this->catalog->useTechnicalCatalog();
    }
}
