<?php

/**
 * TechDivision\Import\Product\Repositories\StockStatusRepository
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

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Repositories\AbstractRepository;

/**
 * Repository implementation to load stock status data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StockStatusRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing stock status.
     *
     * @var \PDOStatement
     */
    protected $stockStatusStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->stockStatusStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::STOCK_STATUS));
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock status to load
     * @param integer $websiteId The website ID of the stock status to load
     * @param integer $stockId   The stock ID of the stock status to load
     *
     * @return array The stock status
     */
    public function findOneByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId)
    {

        // prepare the params
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::WEBSITE_ID => $websiteId,
            MemberNames::STOCK_ID   => $stockId
        );

        // load and return the stock status with the passed product/website/stock ID
        $this->stockStatusStmt->execute($params);
        return $this->stockStatusStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
