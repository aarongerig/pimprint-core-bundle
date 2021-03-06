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

namespace Mds\PimPrint\CoreBundle\Service;

use Pimcore\Http\RequestHelper;

/**
 * Class PluginParameters
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class PluginParameters
{
    /**
     * Parameter value to start on left page of a print bow.
     *
     * @var int
     */
    const START_ALIGNMENT_LEFT = 400;

    /**
     * Parameter value to start on right page of a print bow.
     *
     * @var int
     */
    const START_ALIGNMENT_RIGHT = 401;

    /**
     * Update mode value "All elements (position and content)"
     *
     * @var int
     */
    const UPDATE_ALL_POSITION_CONTENT = 501;

    /**
     * Update mode value "All elements (only content)"
     *
     * @var int
     */
    const UPDATE_ALL_CONTENT = 502;

    /**
     * Update mode value "Selected elements (position and content)"
     *
     * Important:
     * This update mode only works in absolute positioned layouts.
     * Layouts using relative position or CheckNewPage commands are not absolute positioned.
     *
     * @var int
     */
    const UPDATE_SELECTED_POSITION_CONTENT = 511;

    /**
     * Update mode value "Selected elements (only content)"
     *
     * @var int
     */
    const UPDATE_SELECTED_CONTENT = 512;

    /**
     * Plugin param publication ident.
     *
     * @var string
     */
    const PARAM_PUBLICATION = 'publicationIdent';

    /**
     * Plugin param render language.
     *
     * @var string
     */
    const PARAM_LANGUAGE = 'renderLanguage';

    /**
     * Plugin param startAlignment.
     *
     * @var string
     */
    const PARAM_START_ALIGNMENT = 'startAlignment';

    /**
     * Plugin param update mode.
     *
     * @var string
     */
    const PARAM_UPDATE_MODE = 'updateMode';

    /**
     * Plugin param start page.
     *
     * @var string
     */
    const PARAM_PAGE_START = 'pageStart';

    /**
     * Plugin param end page.
     *
     * @var string
     */
    const PARAM_PAGE_END = 'pageEnd';

    /**
     * Plugin param element list.
     *
     * @var string
     */
    const PARAM_ELEMENT_LIST = 'elementList';

    /**
     * Pimcore request helper.
     *
     * @var RequestHelper
     */
    protected $requestHelper;

    /**
     * JsonRequestDecoder instance.
     *
     * @var JsonRequestDecoder
     */
    protected $jsonRequestDecoder;

    /**
     * Parameter definition to load from request.
     * Boolean value indicates if param a required parameter.
     *
     * @var array
     */
    protected $paramDefinition = [
        self::PARAM_PUBLICATION     => ['required' => true, 'default' => null],
        self::PARAM_LANGUAGE        => ['required' => true, 'default' => null],
        self::PARAM_UPDATE_MODE     => ['required' => false, 'default' => self::UPDATE_ALL_POSITION_CONTENT],
        self::PARAM_START_ALIGNMENT => ['required' => false, 'default' => self::START_ALIGNMENT_LEFT],
        self::PARAM_PAGE_START      => ['required' => false, 'default' => 1],
        self::PARAM_PAGE_END        => ['required' => false, 'default' => false],
        self::PARAM_ELEMENT_LIST    => ['required' => false, 'default' => []],
    ];

    /**
     * Loaded params from request.
     *
     * @var array
     */
    protected $params = [];

    /**
     * PluginParams constructor.
     *
     * @param RequestHelper      $requestHelper
     * @param JsonRequestDecoder $jsonRequestDecoder
     */
    public function __construct(RequestHelper $requestHelper, JsonRequestDecoder $jsonRequestDecoder)
    {
        $this->requestHelper = $requestHelper;
        $this->jsonRequestDecoder = $jsonRequestDecoder;
    }

    /**
     * Returns plugin $param. Use class constants to identify param.
     *
     * @param string $param
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function get(string $param)
    {
        if (empty($this->params)) {
            $this->load();
        }
        if (false === isset($this->params[$param])) {
            throw new \Exception(sprintf("Undefined plugin parameter '%s'.", $param));
        }

        return $this->params[$param];
    }

    /**
     * Returns true of PluginParam option is set to start on left page.
     *
     * @return bool
     * @throws \Exception
     */
    public function isStartOnLeftPage()
    {
        return PluginParameters::START_ALIGNMENT_LEFT == $this->get(PluginParameters::PARAM_START_ALIGNMENT);
    }

    /**
     * Returns true of PluginParam option is set to start on right page.
     *
     * @return bool
     * @throws \Exception
     */
    public function isStartOnRightPage()
    {
        return !$this->isStartOnLeftPage();
    }

    /**
     * Returns true if current update mode is for selected elements.
     *
     * @return bool
     * @throws \Exception
     */
    public function isUpdateModeSelected()
    {
        $mode = $this->get(self::PARAM_UPDATE_MODE);
        if (self::UPDATE_ALL_POSITION_CONTENT == $mode || self::UPDATE_ALL_CONTENT == $mode) {
            return false;
        }

        return true;
    }

    /**
     * Loads params from InDesign plugin from current request.
     * Throws an exception if required params are missing.
     *
     * @throws \Exception
     */
    protected function load()
    {
        $request = $this->requestHelper->getRequest();
        $this->jsonRequestDecoder->decode($request);

        foreach ($this->paramDefinition as $param => $definition) {
            $required = $definition['required'];
            $default = $definition['default'];
            $value = $request->get($param);
            if (true === $required && true === empty($value)) {
                throw new \Exception(sprintf("Required plugin parameter '%s' not found in request.", $param));
            }
            if (true === empty($value)) {
                $value = $default;
            }

            $this->params[$param] = $value;
        }
    }
}
