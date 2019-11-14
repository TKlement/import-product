<?php

/**
 * TechDivision\Import\Product\Utils\CacheKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * A utility class that contains the cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheKeys extends \TechDivision\Import\Utils\CacheKeys
{

    /**
     * The cache key for products.
     *
     * @var string
     */
    const PRODUCT = 'product';

    /**
     * The cache key for product varchar attributes.
     *
     * @var string
     */
    const PRODUCT_VARCHAR = 'product_varchar';

    /**
     * The cache key for product integer attributes.
     *
     * @var string
     */
    const PRODUCT_INT = 'product_int';

    /**
     * The cache key for product datetime attribute.
     *
     * @var string
     */
    const PRODUCT_DATETIME = 'product_datetime';

    /**
     * The cache key for product decimal attribute.
     *
     * @var string
     */
    const PRODUCT_DECIMAL = 'product_decimal';

    /**
     * The cache key for product text attribute.
     *
     * @var string
     */
    const PRODUCT_TEXT = 'product_text';

    /**
     * Initializes the instance with the passed cache key.
     *
     * @param string $cacheKey The cache key use
     */
    public function __construct($cacheKey, array $cacheKeys = array())
    {

        // merge the passed cache keys with the one from this class
        $mergedCacheKeys = array_merge(
            array(
                CacheKeys::PRODUCT,
                CacheKeys::PRODUCT_VARCHAR,
                CacheKeys::PRODUCT_INT,
                CacheKeys::PRODUCT_DATETIME,
                CacheKeys::PRODUCT_DECIMAL,
                CacheKeys::PRODUCT_TEXT
            ),
            $cacheKeys
        );

        // pass them to the parent instance
        parent::__construct($cacheKey, $mergedCacheKeys);
    }
}
