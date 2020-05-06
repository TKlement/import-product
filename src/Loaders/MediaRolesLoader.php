<?php

/**
 * TechDivision\Import\Product\Loaders\MediaRolesLoader
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 */

namespace TechDivision\Import\Product\Loaders;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for media roles.
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @link      https://www.techdivision.com
 */
class MediaRolesLoader implements LoaderInterface
{

    /**
     * The media roles array (default: ['base', 'small', 'thumbnail', 'swatch']).
     *
     * @var array
     */
    protected $mediaRoles = array();

    /**
     * The import processor.
     *
     * @var ImportProcessorInterface
     */
    protected $importProcessor;

    /**
     * ImageMediaRolesLoader constructor
     *
     * @param ImportProcessorInterface $importProcessor The import processor.
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {
        $this->importProcessor = $importProcessor;
        $this->mediaRoles = $this->createMediaRoles();
    }

    /**
     * @return \ArrayAccess|void|array
     */
    public function load()
    {
        return $this->mediaRoles;
    }

    /**
     * Creates media roles from available image types.
     *
     * @return array
     */
    public function createMediaRoles()
    {

        // initialize default values
        $mediaRoles = array();

        // derive media roles form image types
        foreach ($this->importProcessor->getImageTypes() as $imageColumnName => $imageLabelColumnName) {
            // create the role based prefix for the image columns
            $role = str_replace('_image', null, $imageColumnName);

            // initialize the values for the corresponding media role
            $mediaRoles[$role] = array(
                ColumnKeys::IMAGE_PATH        => $imageColumnName,
                ColumnKeys::IMAGE_LABEL       => $imageLabelColumnName,
                ColumnKeys::IMAGE_POSITION    => sprintf('%s_image_position', $role)
            );
        }

        return $mediaRoles;
    }
}
