<?php
/**
 * mds PimPrint
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license    https://pimprint.mds.eu/license GPLv3
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Template\Concrete;

use Mds\PimPrint\CoreBundle\InDesign\Template\AbstractTemplate;

/**
 * Class A3PortraitTemplate
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Template
 */
class A3PortraitTemplate extends AbstractTemplate
{
    /**
     * A3 portrait page width in mm.
     *
     * @var float
     */
    const PAGE_WIDTH = 297;

    /**
     * A3 portrait page height in mm.
     *
     * @var int
     */
    const PAGE_HEIGHT = 420;
}
