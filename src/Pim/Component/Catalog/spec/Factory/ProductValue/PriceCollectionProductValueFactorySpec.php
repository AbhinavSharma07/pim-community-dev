<?php

namespace spec\Pim\Component\Catalog\Factory\ProductValue;

use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Exception\InvalidArgumentException;
use Pim\Component\Catalog\Factory\PriceFactory;
use Pim\Component\Catalog\Factory\ProductValue\PriceCollectionProductValueFactory;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\PriceCollection;
use Pim\Component\Catalog\Model\ProductPriceInterface;
use Pim\Component\Catalog\Model\ProductValue;
use Prophecy\Argument;

class PriceCollectionProductValueFactorySpec extends ObjectBehavior
{
    function let(PriceFactory $priceFactory)
    {
        $this->beConstructedWith($priceFactory, ProductValue::class, 'pim_catalog_price_collection', $priceFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PriceCollectionProductValueFactory::class);
    }

    function it_supports_price_collection_attribute_type()
    {
        $this->supports('foo')->shouldReturn(false);
        $this->supports('pim_catalog_price_collection')->shouldReturn(true);
    }

    function it_creates_a_empty_price_collection_product_value(
        $priceFactory,
        AttributeInterface $attribute
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $priceFactory->createPrice(Argument::cetera())->shouldNotBeCalled();

        $productValue = $this->create(
            $attribute,
            null,
            null,
            []
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('price_collection_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_localizable_and_scopable_empty_price_collection_product_value(
        $priceFactory,
        AttributeInterface $attribute
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $priceFactory->createPrice(Argument::cetera())->shouldNotBeCalled();

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            []
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('price_collection_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_price_collection_product_value(
        $priceFactory,
        AttributeInterface $attribute,
        ProductPriceInterface $priceEUR,
        ProductPriceInterface $priceUSD
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $priceFactory->createPrice(42, 'EUR')->willReturn($priceEUR);
        $priceFactory->createPrice(63, 'USD')->willReturn($priceUSD);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            [['amount' => 42, 'currency' => 'EUR'], ['amount' => 63, 'currency' => 'USD']]
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('price_collection_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHavePrices();
    }

    function it_creates_a_localizable_and_scopable_price_collection_product_value(
        $priceFactory,
        AttributeInterface $attribute,
        ProductPriceInterface $priceEUR,
        ProductPriceInterface $priceUSD
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $priceFactory->createPrice(42, 'EUR')->willReturn($priceEUR);
        $priceFactory->createPrice(63, 'USD')->willReturn($priceUSD);

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            [['amount' => 42, 'currency' => 'EUR'], ['amount' => 63, 'currency' => 'USD']]
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('price_collection_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldHavePrices();
    }

    function it_throws_an_exception_if_provided_data_is_not_an_array(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidArgumentException::arrayExpected(
            'price_collection_attribute',
            'prices collection',
            'factory',
            'string'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', 'foobar']);
    }

    function it_throws_an_exception_if_provided_data_is_not_an_array_of_array(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidArgumentException::arrayOfArraysExpected(
            'price_collection_attribute',
            'prices collection',
            'factory',
            'array of string'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', ['foobar']]);
    }

    function it_throws_an_exception_if_provided_data_does_not_contains_an_amount(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidArgumentException::arrayKeyExpected(
            'price_collection_attribute',
            'amount',
            'prices collection',
            'factory',
            'foo, currency'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', [['foo' => 42, 'currency' => 'EUR']]]);
    }

    function it_throws_an_exception_if_provided_data_does_not_contains_a_currency(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('price_collection_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_price_collection');
        $attribute->getBackendType()->willReturn('prices');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $exception = InvalidArgumentException::arrayKeyExpected(
            'price_collection_attribute',
            'currency',
            'prices collection',
            'factory',
            'amount, bar'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', [['amount' => 42, 'bar' => 'EUR']]]);
    }

    public function getMatchers()
    {
        return [
            'haveAttribute' => function ($subject, $attributeCode) {
                return $subject->getAttribute()->getCode() === $attributeCode;
            },
            'beLocalizable' => function ($subject) {
                return null !== $subject->getLocale();
            },
            'haveLocale'    => function ($subject, $localeCode) {
                return $localeCode === $subject->getLocale();
            },
            'beScopable'    => function ($subject) {
                return null !== $subject->getScope();
            },
            'haveChannel'   => function ($subject, $channelCode) {
                return $channelCode === $subject->getScope();
            },
            'beEmpty'       => function ($subject) {
                return $subject->getData() instanceof PriceCollection && [] === $subject->getData()->toArray();
            },
            'havePrices'    => function ($subject) {
                return $subject->getData() instanceof PriceCollection && [] !== $subject->getData()->toArray();
            },
        ];
    }
}
