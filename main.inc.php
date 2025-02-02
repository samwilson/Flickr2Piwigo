<?php
/*
Plugin Name: Flickr2Piwigo
Version: 1.5.3
Description: Import pictures from your Flickr account
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(__DIR__) !== 'flickr2piwigo')
{
  add_event_handler('init', 'flickr_error');
  function flickr_error()
  {
    global $page;
    $page['errors'][] = 'Flickr2Piwigo folder name is incorrect, uninstall the plugin and rename it to "flickr2piwigo"';
  }
  return;
}

global $conf;

define('FLICKR2PIWIGO', 'flickr2piwigo');
define('FLICKR_PATH', PHPWG_PLUGINS_PATH.'flickr2piwigo/');
define('FLICKR_ADMIN', get_root_url().'admin.php?page=plugin-flickr2piwigo');
define(
  'FLICKR_FS_CACHE',
  realpath(PHPWG_ROOT_PATH).'/'.$conf['data_location'].'flickr_cache/'
);

include_once(FLICKR_PATH.'include/ws_functions.inc.php');


$conf['flickr2piwigo'] = safe_unserialize($conf['flickr2piwigo']);


add_event_handler('ws_add_methods', 'flickr_add_ws_method');

if (defined('IN_ADMIN'))
{
  add_event_handler('get_batch_manager_prefilters', 'flickr_add_batch_manager_prefilters');
  add_event_handler('perform_batch_manager_prefilters', 'flickr_perform_batch_manager_prefilters', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

  function flickr_add_batch_manager_prefilters($prefilters)
  {
    load_language('plugin.lang', FLICKR_PATH);
    $prefilters[] = [
      'ID' => 'flickr',
      'NAME' => l10n('Imported from Flickr'),
    ];
    return $prefilters;
  }

  function flickr_perform_batch_manager_prefilters($filter_sets, $prefilter)
  {
    if ($prefilter == 'flickr')
    {
      $query = '
  SELECT id
    FROM '.IMAGES_TABLE.'
    WHERE file LIKE "flickr-%"
  ;';
      $filter_sets[] = array_from_query($query, 'id');
    }

    return $filter_sets;
  }
}
