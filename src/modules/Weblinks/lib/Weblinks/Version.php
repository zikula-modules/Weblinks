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
        return $meta;
    }

}