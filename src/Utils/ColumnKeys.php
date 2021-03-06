<?php

/**
 * TechDivision\Import\Product\Utils\ColumnKeys
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
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ColumnKeys extends \TechDivision\Import\Utils\ColumnKeys
{

    /**
     * Name for the column 'sku'.
     *
     * @var string
     */
    const SKU = 'sku';

    /**
     * Name for the column 'categories'.
     *
     * @var string
     */
    const CATEGORIES = 'categories';

    /**
     * Name for the column 'categories_position'.
     *
     * @var string
     */
    const CATEGORIES_POSITION = 'categories_position';

    /**
     * Name for the column 'product_websites'.
     *
     * @var string
     */
    const PRODUCT_WEBSITES = 'product_websites';

    /**
     * Name for the column 'created_at'.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Name for the column 'updated_at'.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Name for the column 'qty'.
     *
     * @var string
     */
    const QTY = 'qty';

    /**
     * Name for the column 'is_in_stock'.
     *
     * @var string
     */
    const IS_IN_STOCK = 'is_in_stock';

    /**
     * Name for the column 'quantity_and_stock_status'.
     *
     * @var string
     */
    const QUANTITY_AND_STOCK_STATUS = 'quantity_and_stock_status';

    /**
     * Name for the column 'website_id'.
     *
     * @var string
     */
    const WEBSITE_ID = 'website_id';

    /**
     * Name for the column 'additional_attributes'.
     *
     * @var string
     */
    const ADDITIONAL_ATTRIBUTES = 'additional_attributes';

    /**
     * Name for the column 'attribute_set_code'.
     *
     * @var string
     */
    const ATTRIBUTE_SET_CODE = 'attribute_set_code';

    /**
     * Name for the column 'url_key'.
     *
     * @var string
     */
    const URL_KEY = 'url_key';

    /**
     * Name for the column 'name'.
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * Name for the column 'visibility'.
     *
     * @var string
     */
    const VISIBILITY = 'visibility';

    /**
     * Name for the column 'additional_images'.
     *
     * @var string
     */
    const ADDITIONAL_IMAGES = 'additional_images';

    /**
     * Name for the column 'image_path'.
     *
     * @var string
     */
    const IMAGE_PATH = 'image_path';

    /**
     * Name for the column 'image_label'.
     *
     * @var string
     */
    const IMAGE_LABEL = 'image_label';

    /**
     * Name for the column 'image_position'.
     *
     * @var string
     */
    const IMAGE_POSITION = 'image_position';

    /**
     * Name for the column 'image_disabled'.
     *
     * @var string
     */
    const IMAGE_DISABLED = 'image_disabled';

    /**
     * Name for the "virtual" column 'position' (this is a temporary
     * solution till techdivision/import#179 as been implemented).
     *
     * @var string
     * @todo https://github.com/techdivision/import/issues/179
     */
    const POSITION = 'position';
}
