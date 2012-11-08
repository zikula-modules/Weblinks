<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Version extends Zikula_AbstractVersion
{

    public function getMetaData()
    {
        $meta = array();
        $meta['name'] = 'Weblinks';
        $meta['oldnames'] = array('Web_Links');
        $meta['displayname'] = $this->__('Weblinks');
        $meta['description'] = $this->__('Weblinks Module');
        //! this defines the module's url
        $meta['url'] = $this->__('weblinks');
        $meta['version'] = '3.0.0';
        $meta['securityschema'] = array('Weblinks::Category' => 'Category name::Category ID',
            'Weblinks::Link' => '::');
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['core_max'] = '1.3.99'; // doesn't work with 1.4.0 (yet)
        return $meta;
    }

}