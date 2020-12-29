<?php

/**
 * TechDivision\Import\Product\Observers\UrlKeyObserver
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

namespace TechDivision\Import\Product\Observers;

use Zend\Filter\FilterInterface;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\UrlKeyUtilInterface;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Observer that extracts the URL key from the product name and adds a two new columns
 * with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlKeyObserver extends AbstractProductImportObserver
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * The URL key utility instance.
     *
     * @var \TechDivision\Import\Utils\UrlKeyUtilInterface
     */
    protected $urlKeyUtil;

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * Initialize the observer with the passed product bunch processor and filter instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor   The product bunch processor instance
     * @param \Zend\Filter\FilterInterface                                         $convertLiteralUrlFilter The URL filter instance
     * @param \TechDivision\Import\Utils\UrlKeyUtilInterface                       $urlKeyUtil              The URL key utility instance
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        FilterInterface $convertLiteralUrlFilter,
        UrlKeyUtilInterface $urlKeyUtil
    ) {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
        $this->urlKeyUtil = $urlKeyUtil;
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     * @throws \Exception Is thrown, if either column "url_key" or "name" have a value set
     */
    protected function process()
    {

        // initialize the URL key and the product
        $urlKey = null;
        $product = array();

        // prepare the store view code
        $this->getSubject()->prepareStoreViewCode();

        // set the entity ID for the product with the passed SKU
        if ($product = $this->loadProduct($sku = $this->getValue(ColumnKeys::SKU))) {
            $this->setIds($product);
        } else {
            $this->setIds(array());
        }

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } else {
            // query whether or not the existing product `url_key` should be re-created from the product name
            if ($product && !$this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::UPDATE_URL_KEY_FROM_NAME, true)) {
                // if the product already exists and NO re-creation from the product name has to
                // be done, load the original `url_key`from the product and use that to proceed
                $urlKey = $this->loadUrlKey($this->getSubject(), $this->getPrimaryKey());
            }

            // try to load the value from column `name` if URL key is still
            // empty, because we need it to process the the rewrites later on
            if ($urlKey === null || $urlKey === '' && $this->hasValue(ColumnKeys::NAME)) {
                $urlKey = $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME));
            }
        }

        // stop processing, if no URL key is available
        if ($urlKey === null || $urlKey === '') {
            // throw an exception, that the URL key can not be
            // initialized and we're in the default store view
            if ($this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
                throw new \Exception(sprintf('Can\'t initialize the URL key for product "%s" because columns "url_key" or "name" have a value set for default store view', $sku));
            }
            // stop processing, because we're in a store
            // view row and a URL key is not mandatory
            return;
        }

        // try to load the URL paths of the categories found in the column
        // `categories, if not available, simply make the URL key unique
        if (sizeof($urlPaths = $this->getUrlPaths()) === 0) {
            $urlKey = $this->makeUnique($this->getSubject(), $urlKey);
        } else {
            // otherwise iterate over the found URL
            // paths and try to find a unique URL key
            for ($i = 0; $i < sizeof($urlPaths); $i++) {
                // try to make the URL key unique for the given URL path
                $proposedUrlKey = $this->makeUnique($this->getSubject(), $urlKey, $urlPaths[$i]);
                // if the URL key is NOT the same as the passed one or with the parent URL path
                // it can NOT be used, so we've to persist it temporarily and try it again for
                // all the other URL paths until we found one that works with every URL path
                if ($urlKey !== $proposedUrlKey) {
                    // temporarily persist the URL key
                    $urlKey = $proposedUrlKey;
                    // reset the counter and restart the
                    // iteration with the first URL path
                    $i = 0;
                }
            }
        }

        // set the unique URL key for further processing
        $this->setValue(ColumnKeys::URL_KEY, $urlKey);
    }

    /**
     * Extract's the category from the comma separeted list of categories from
     * the column `categories` and return's an array with their URL paths.
     *
     * @return string[] Array with the URL paths of the categories found in column `categories`
     * @todo The list has to be exended by the URL paths of the parent categories that has the anchor flag been acitvated
     */
    protected function getUrlPaths()
    {

        // initialize the array for the URL paths of the cateogries
        $urlPaths = array();

        // extract the categories from the column `categories`
        $categories = $this->getValue(ColumnKeys::CATEGORIES, array(), array($this, 'explode'));

        // iterate of the found categories, load
        // their URL path and add it the array
        foreach ($categories as $path) {
            $category = $this->getCategoryByPath($path);
            $urlPaths[] = $category[MemberNames::URL_PATH];
        }

        // return the array with the categories URL paths
        return $urlPaths;
    }

    /**
     * Temporarily persist's the IDs of the passed product.
     *
     * @param array $product The product to temporarily persist the IDs for
     *
     * @return void
     */
    protected function setIds(array $product)
    {
        $this->setLastEntityId(isset($product[MemberNames::ENTITY_ID]) ? $product[MemberNames::ENTITY_ID] : null);
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
    }

    /**
     * Return's the PK to of the product.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        $this->getSubject()->getLastEntityId();
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    protected function loadProduct($sku)
    {
        return $this->getProductBunchProcessor()->loadProduct($sku);
    }

    /**
     * Load's and return's the url_key with the passed primary ID.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject      The subject to load the URL key
     * @param int                                                       $primaryKeyId The ID from product
     *
     * @return string|null url_key or null
     */
    protected function loadUrlKey(UrlKeyAwareSubjectInterface $subject, $primaryKeyId)
    {
        return $this->getUrlKeyUtil()->loadUrlKey($subject, $primaryKeyId);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Returns the URL key utility instance.
     *
     * @return \TechDivision\Import\Utils\UrlKeyUtilInterface The URL key utility instance
     */
    protected function getUrlKeyUtil()
    {
        return $this->urlKeyUtil;
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject The subject to make the URL key unique for
     * @param string                                                    $urlKey  The URL key to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUnique(UrlKeyAwareSubjectInterface $subject, $urlKey)
    {
        return $this->getUrlKeyUtil()->makeUnique($subject, $urlKey);
    }
}
