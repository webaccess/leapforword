<?php
/**
 * class.DashboardReport.php
 *  
 */

  class DashboardReportClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'DashboardReport' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }

    function getFieldsForPageSetup()
    {
    }

    function updateFieldsForPageSetup()
    {
    }

  }
?>