<?php

namespace Akeneo\Pim\tests\Common\Api\Product;

use Akeneo\Pim\Pagination\PageInterface;

class ListProductApiIntegration extends AbstractProductApiTestCase
{
    /**
     * @group common
     */
    public function testSearchHavingNoResults()
    {
        $api = $this->createClient()->getProductApi();
        $products = $api->listPerPage(10, true, [
            'search'  => [
                'name' => [
                    [
                        'operator' => '=',
                        'value'    => 'No name',
                        'locale'   => 'en_US',
                    ]
                ]
            ]
        ]);

        $this->assertInstanceOf(PageInterface::class, $products);
        $this->assertSame(0, $products->getCount());
        $this->assertEmpty($products->getItems());
    }

    /**
     * @group common
     * @expectedException \Akeneo\Pim\Exception\UnprocessableEntityHttpException
     */
    public function testSearchFailedWithInvalidOperator()
    {
        $api = $this->createClient()->getProductApi();
        $api->listPerPage(10, true, [
            'search'  => [
                'family' => [
                    [
                        'operator' => '=',
                        'value'    => 'Invalid operator for Family',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @group common
     */
    public function testAllWithSelectedAttributes()
    {
        $baseUri = $this->getConfiguration()['api']['baseUri'];
        $api = $this->createClient()->getProductApi();
        $products = $api->all(1, ['attributes' => 'name,color']);

        $expectedProduct = $this->sanitizeProductData([
            '_links'        => [
                'self' => [
                    'href' => $baseUri . '/api/rest/v1/products/big_boot',
                ],
            ],
            'identifier'    => 'big_boot',
            'family'        => 'boots',
            'groups'        => [
                'similar_boots',
            ],
            'variant_group' => null,
            'categories'    => [
                'summer_collection',
                'winter_boots',
                'winter_collection',
            ],
            'enabled'       => true,
            'values'        => [
                'color' => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => 'black',
                    ],
                ],
                'name'  => [
                    [
                        'locale' => 'en_US',
                        'scope'  => null,
                        'data'   => 'Big boot !',
                    ],
                ],
            ],
            'created'       => '2017-06-26T07:33:09+00:00',
            'updated'       => '2017-06-26T07:33:09+00:00',
            'associations'  => [
                'X_SELL' => [
                    'groups'   => [],
                    'products' => [
                        'small_boot',
                        'medium_boot',
                    ],
                ],
            ],
        ]);

        $actualProduct = $this->sanitizeProductData($products->current());

        $this->assertSameContent($expectedProduct, $actualProduct);
    }

    /**
     * @group common
     */
    public function testAllWithSelectedLocales()
    {
        $baseUri = $this->getConfiguration()['api']['baseUri'];
        $api = $this->createClient()->getProductApi();
        $products = $api->all(10, [
            'locales' => 'fr_FR',
            'search'  => [
                'categories' => [
                    [
                        'operator' => 'IN',
                        'value'    => ['sandals'],
                    ]
                ]
            ]
        ]);

        $expectedProduct = $this->sanitizeProductData([
            '_links'        => [
                'self' => [
                    'href' => $baseUri . '/api/rest/v1/products/dance_shoe',
                ],
            ],
            'identifier'    => 'dance_shoe',
            'family'        => 'sandals',
            'groups'        => [],
            'variant_group' => null,
            'categories'    => [
                'sandals',
            ],
            'enabled'       => true,
            'values'        => [
                'color'           => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => 'greem',
                    ],
                ],
                'description'     => [
                    [
                        'locale' => 'fr_FR',
                        'scope'  => 'ecommerce',
                        'data'   => 'Dansez toute la nuit !',
                    ],
                ],
                'destocking_date' => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => '2017-06-30T00:00:00+00:00',
                    ],
                ],
                'manufacturer'    => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => 'Converse',
                    ],
                ],
                'name'            => [
                    [
                        'locale' => 'fr_FR',
                        'scope'  => null,
                        'data'   => 'Chaussure de dance',
                    ],
                ],
                'side_view'       => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => '0/4/1/a/041a1570a3cefa2c79872c433f60c2e6301afd60_Akeneo_logo.png',
                        '_links' => [
                            'download' => [
                                'href' => $baseUri . '/api/rest/v1/media-files/0/4/1/a/041a1570a3cefa2c79872c433f60c2e6301afd60_Akeneo_logo.png/download',
                            ],
                        ],
                    ],
                ],
                'size'            => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => '40',
                    ],
                ],
                'price'           => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => [
                            [
                                'amount'   => '90.50',
                                'currency' => 'USD',
                            ],
                            [
                                'amount'   => '99.49',
                                'currency' => 'EUR',
                            ],
                        ],
                    ],
                ],
            ],
            'created'       => '2017-06-26T07:33:09+00:00',
            'updated'       => '2017-06-26T07:33:09+00:00',
            'associations'  => [],
        ]);

        $actualProduct = $this->sanitizeProductData(iterator_to_array($products)[0]);

        $this->assertSameContent($expectedProduct, $actualProduct);
    }

    /**
     * @group common
     */
    public function testAllWithSelectedScope()
    {
        $baseUri = $this->getConfiguration()['api']['baseUri'];
        $api = $this->createClient()->getProductApi();
        $products = $api->all(10, [
            'scope' => 'mobile',
            'search'  => [
                'family' => [
                    [
                        'operator' => 'IN',
                        'value'    => ['sneakers'],
                    ]
                ]
            ]
        ]);

        $expectedProduct = $this->sanitizeProductData([
            '_links'        => [
                'self' => [
                    'href' => $baseUri . '/api/rest/v1/products/black_sneakers',
                ],
            ],
            'identifier'    => 'black_sneakers',
            'family'        => 'sneakers',
            'groups'        => [],
            'variant_group' => null,
            'categories'    => [
                'summer_collection',
                'winter_collection',
            ],
            'enabled'       => true,
            'values'        => [
                'color'              => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => 'black',
                    ],
                ],
                'manufacturer'       => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => 'Converse',
                    ],
                ],
                'name'               => [
                    [
                        'locale' => 'en_US',
                        'scope'  => null,
                        'data'   => 'Black sneakers',
                    ],
                ],
                'side_view'          => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => '3/d/8/9/3d89680c85a835b5b0a5bd0e7dd2515b55a4b657_Ziggy_certification.jpg',
                        '_links' => [
                            'download' => [
                                'href' => $baseUri . '/api/rest/v1/media-files/3/d/8/9/3d89680c85a835b5b0a5bd0e7dd2515b55a4b657_Ziggy_certification.jpg/download',
                            ],
                        ],
                    ],
                ],
                'size'               => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => '41',
                    ],
                ],
                'weather_conditions' => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => [
                            'dry',
                            'wet',
                        ],
                    ],
                ],
                'length'             => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => [
                            'amount' => 14,
                            'unit'   => 'CENTIMETER',
                        ],
                    ],
                ],
                'price'              => [
                    [
                        'locale' => null,
                        'scope'  => null,
                        'data'   => [
                            [
                                'amount'   => '40.00',
                                'currency' => 'EUR',
                            ],
                            [
                                'amount'   => '42.00',
                                'currency' => 'USD',
                            ],
                        ],
                    ],
                ],
            ],
            'created'       => '2017-06-26T07:33:09+00:00',
            'updated'       => '2017-06-26T07:33:09+00:00',
            'associations'  => [],
        ]);

        $actualProduct = $this->sanitizeProductData(iterator_to_array($products)[0]);

        $this->assertSameContent($expectedProduct, $actualProduct);
    }
}
