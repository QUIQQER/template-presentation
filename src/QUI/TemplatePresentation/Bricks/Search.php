<?php

/**
 * This file contains QUI\TemplatePresentation\Bricks\Search
 */

namespace QUI\TemplatePresentation\Bricks;

use QUI;

/**
 * Class Search
 *
 * @package namerobot/template-namingservice
 */
class Search extends QUI\Control
{
    /**
     * constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        // default options
        $this->setAttributes(array(
            'class'    => 'tplPresentation-brick-search',
            'quiClass' => 'package/quiqqer/search/bin/controls/Search'

        ));

        parent::__construct($attributes);
    }

    /**
     * Return the inner body of the element
     * Can be overwritten
     *
     * @return String
     */
    public function getBody()
    {
        QUI::getPackage('quiqqer/search');
        $Engine = QUI::getTemplateManager()->getEngine();

        $Engine->assign(array(
            'this' => $this
        ));

        $this->addCSSFile(dirname(__FILE__) . '/Search.css');
        return $Engine->fetch(dirname(__FILE__) . '/Search.html');
    }
}
