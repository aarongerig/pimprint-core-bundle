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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\GoToPage;
use Mds\PimPrint\CoreBundle\InDesign\Command\OpenDocument;
use Mds\PimPrint\CoreBundle\InDesign\Command\RemoveEmptyLayers;
use Mds\PimPrint\CoreBundle\Project\AbstractProject;

/**
 * Trait RenderingTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait RenderingTrait
{
    /**
     * Generates PimPrint commands to build a publication in InDesign.
     *
     * @return array
     * @throws \Exception
     */
    public function run(): array
    {
        $this->buildPublication();

        return $this->getCommandQueue()
                    ->getCommands();
    }

    /**
     * Convenience method that initializes renderMode, opens InDesign template and jumps to first page.
     *
     * @param bool $openFirstPage
     *
     * @throws \Exception
     */
    protected function startRendering($openFirstPage = true)
    {
        $this->initRenderMode()
             ->initInDesignDocument();

        if ($openFirstPage) {
            $this->addCommand(new GoToPage());
        }
    }

    /**
     * Convenience method that ends the rendering process by sending a RemoveEmptyLayers command.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function stopRendering(): AbstractProject
    {
        $this->addCommand(new RemoveEmptyLayers());

        /* @var AbstractProject $this */
        return $this;
    }

    /**
     * Sets PHP settings for generation mode.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function initRenderMode(): AbstractProject
    {
        set_time_limit(
            $this->config()
                 ->offsetGet('php_time_limit', self::DEFAULT_TIME_LIMIT)
        );
        ini_set(
            'memory_limit',
            $this->config()
                 ->offsetGet('php_memory_limit', self::DEFAULT_MEMORY_LIMIT)
        );

        /* @var AbstractProject $this */
        return $this;
    }

    /**
     * Opens a new InDesign document and loads the indd.template parameter template file.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function initInDesignDocument(): AbstractProject
    {
        $template = $this->getTemplate();

        //Declares current open InDesign document as target document to generate publication in.
        $this->addCommand(new OpenDocument(OpenDocument::TYPE_USECURRENT))
            //opens the InDesign template document.
             ->addCommand(new OpenDocument(OpenDocument::TYPE_TEMPLATE, '0', $template));

        /* @var AbstractProject $this */
        return $this;
    }
}