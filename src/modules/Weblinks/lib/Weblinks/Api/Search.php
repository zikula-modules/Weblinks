<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Api_Search extends Zikula_AbstractApi {

    /**
    * Search plugin info
    **/
    public function info()
    {
        return array('title' => 'Weblinks', 'functions' => array('Weblinks' => 'search'));
    }

    /**
    * Search form component
    **/
    public function options($args)
    {
        if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ)) {
            // Create output object - this object will store all of our output so that
            // we can return it easily when required
            $render = Zikula_View::getInstance('Weblinks', false);
            $render->assign('active',(isset($args['active']) && isset($args['active']['Weblinks'])) || (!isset($args['active'])));
            return $render->fetch('search/options.tpl');
        }

        return '';
    }

    /**
    * Search plugin main function
    **/
    public function search($args)
    {


        if (!SecurityUtil::checkPermission( 'Weblinks::', '::', ACCESS_READ)) {
            return true;
        }

        ModUtil::dbInfoLoad('Search');
        $dbtable = DBUtil::getTables();
        $linkstable = $dbtable['links_links'];
        $linkscolumn = $dbtable['links_links_column'];
        $searchTable = $dbtable['search_result'];
        $searchColumn = $dbtable['search_result_column'];

        $where = search_construct_where($args,
                                        array($linkscolumn['title'],
                                            $linkscolumn['description']),
                                            null);

        $sessionId = session_id();

        // define the permission filter to apply
        $permFilter = array();
        $permFilter[] = array('realm'            => 0,
                            'component_left'   => 'Weblinks',
                            'component_middle' => '',
                            'component_right'  => 'Category',
                            'instance_left'    => 'title',
                            'instance_middle'  => '',
                            'instance_right'   => 'cat_id',
                            'level'            => ACCESS_READ);

        // get the result set
        $links = DBUtil::selectObjectArray('links_links', $where, 'lid', 1, -1, '', $permFilter);
        if ($links === false) {
            return LogUtil::registerError($this->__('Error! Could not load any link.'));
        }

        $insertSql = "INSERT INTO $searchTable ($searchColumn[title],
                                                $searchColumn[text],
                                                $searchColumn[extra],
                                                $searchColumn[module],
                                                $searchColumn[created],
                                                $searchColumn[session]) VALUES ";

        foreach ($links as $link)
        {
            $sql = $insertSql . '('
                    . '\'' . DataUtil::formatForStore($link['title']) . '\', '
                    . '\'' . DataUtil::formatForStore($link['description']) . '\', '
                    . '\'' . DataUtil::formatForStore($link['lid']) . '\', '
                    . '\'' . 'Weblinks' . '\', '
                    . '\'' . DataUtil::formatForStore($link['date']) . '\', '
                    . '\'' . DataUtil::formatForStore($sessionId) . '\')';
            $insertResult = DBUtil::executeSQL($sql);
            if (!$insertResult) {
                return LogUtil::registerError($this->__('Error! Could not load any link.'));
            }
        }

        return true;
    }


    /**
    * Do last minute access checking and assign URL to items
    *
    * Access checking is ignored since access check has
    * already been done. But we do add a URL to the found user
    */
    public function search_check(&$args)
    {
        $datarow = $args['datarow'];
        $linkId = $datarow['extra'];

        $datarow['url'] = ModUtil::url('Weblinks', 'user', 'visit', array('lid' => $linkId));

        return true;
    }
}