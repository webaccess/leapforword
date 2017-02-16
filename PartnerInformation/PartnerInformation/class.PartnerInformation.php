<?php
/**
 * class.PartnerInformation.php
 *  
 */

  class PartnerInformationClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'PartnerInformation' . PATH_SEPARATOR .
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
