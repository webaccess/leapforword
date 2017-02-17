<?php
require_once ("classes/interfaces/dashletInterface.php");


class dashletPartnerInformation implements DashletInterface
{
  const version = '1.0';

  private $role;
  private $note;

  public static function getAdditionalFields($className)
  {
    $additionalFields = array();

    ///////
    $cnn = Propel::getConnection("rbac");
    $stmt = $cnn->createStatement();

    $arrayRole = array();

    $sql = "SELECT ROL_CODE
            FROM  RBAC_ROLES
            WHERE  ROL_SYSTEM = '00000000000000000000000000000002' AND ROL_STATUS = 1
            ORDER BY ROL_CODE ASC";
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();

      $arrayRole[] = array($row["ROL_CODE"], $row["ROL_CODE"]);
    }

    ///////
    $storeRole = new stdclass();
    $storeRole->xtype = "arraystore";
    $storeRole->idIndex = 0;
    $storeRole->fields = array("value", "text");
    $storeRole->data = $arrayRole;

    ///////
    $cboRole = new stdclass();
    $cboRole->xtype = "combo";
    $cboRole->name = "DAS_ROLE";

    $cboRole->valueField = "value";
    $cboRole->displayField = "text";
    $cboRole->value = $arrayRole[0][0];
    $cboRole->store = $storeRole;

    $cboRole->triggerAction = "all";
    $cboRole->mode = "local";
    $cboRole->editable = false;

    $cboRole->width = 320;
    $cboRole->fieldLabel = "Role";
    $additionalFields[] = $cboRole;

    ///////
    $txtNote = new stdclass();
    $txtNote->xtype = "textfield";
    $txtNote->name = "DAS_NOTE";
    $txtNote->fieldLabel = "Note";
    $txtNote->width = 320;
    $txtNote->value = null;
    $additionalFields[] = $txtNote;

    ///////
    return ($additionalFields);
  }

  public static function getXTemplate($className)
  {
    return "<iframe src=\"{" . "page" . "}?DAS_INS_UID={" . "id" . "}\" width=\"{" . "width" . "}\" height=\"400\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
   //$this->role = $config["DAS_ROLE"];
    //$this->note = $config["DAS_NOTE"];
  }

  public function render($val= null , $width = 300)
  {
    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();

     if(!empty($_POST)) {
	
		$val = $_POST['s_val'];
	}

    $arrayUser = array();

   /*$sql = "SELECT USR.USR_USERNAME, USR.USR_FIRSTNAME, USR.USR_LASTNAME, USR.USR_STATUS
            FROM  USERS AS USR
            WHERE  USR.USR_ROLE = '" . $this->role . "'
            ORDER BY USR.USR_USERNAME ASC"; */
     
   //$sql = "SELECT PARTNER_NAME, TYPE_OF_PARTNER, ADDRESS, DATE_OF_MEETING, SUMMARY_OF_MEETING, CONTACT_NUMBER, CONTACT_PERSON, EMAIL_ID FROM PMT_PARTNERINFO WHERE Partner_name LIKE '$val%' ORDER BY app_number DESC LIMIT 1";

    $sql = "SELECT DISTINCT(PARTNER_NAME) as PARTNER_NAME  FROM PMT_PARTNERINFO WHERE Partner_name LIKE '$val%' ORDER BY app_number DESC";

    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();

      //$arrayUser[] = array("userName" => $row["USR_USERNAME"], "fullName" => $row["USR_FIRSTNAME"] . " " . $row["USR_LASTNAME"], "status" => $row["USR_STATUS"]);

     // $arrayUser[] = array("partnername" => $row["PARTNER_NAME"], "partnertype" => $row["TYPE_OF_PARTNER"] , "address" => $row["ADDRESS"], "meetingdate" => $row["DATE_OF_MEETING"], "summary" => $row["SUMMARY_OF_MEETING"], "cnumber" => $row["CONTACT_NUMBER"], "cperson" => $row["CONTACT_PERSON"], "email" => $row["EMAIL_ID"] );

     $arrayUser[] = array("partnername" => $row["PARTNER_NAME"]);
  
    }
    
    ///////
    $dashletView = new dashletPartnerInformationView($arrayUser, $this->note);
    $dashletView->templatePrint();
  }



public function renderDetails($val= null , $width = 300)
  {

    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();


if(!empty($_POST)) {
$val = $_POST['d_val'];
}

    $arrayUser = array();

    $sql = "SELECT USR.USR_USERNAME, USR.USR_FIRSTNAME, USR.USR_LASTNAME, USR.USR_STATUS
            FROM   USERS AS USR
            WHERE  USR.USR_ROLE = '" . $this->role . "'
            ORDER BY USR.USR_USERNAME ASC";


   $sql = "SELECT PARTNER_NAME, TYPE_OF_PARTNER, ADDRESS, DATE_OF_MEETING, SUMMARY_OF_MEETING, CONTACT_NUMBER, CONTACT_PERSON, EMAIL_ID FROM PMT_PARTNERINFO WHERE Partner_name LIKE '%$val%' ORDER BY app_number DESC LIMIT 1";

        $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();
  
     $arrayUser[] = array("partnername" => $row["PARTNER_NAME"], "partnertype" => $row["TYPE_OF_PARTNER"] , "address" => $row["ADDRESS"], "meetingdate" => $row["DATE_OF_MEETING"], "summary" => $row["SUMMARY_OF_MEETING"], "cnumber" => $row["CONTACT_NUMBER"], "cperson" => $row["CONTACT_PERSON"], "email" => $row["EMAIL_ID"] );

    }

    ///////
    $dashletView = new dashletPartnerInformationViewDetails($arrayUser, $this->note);
    $dashletView->templatePrintD();
  }


}





class dashletPartnerInformationView extends Smarty
{
  private $smarty;

  private $user;
  private $note;

  public function __construct($u, $n)
  {
    $this->user = $u;
    $this->note = $n;

    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . "smarty/configs";
    $this->smarty->caching      = false;
    $this->smarty->templateFile = PATH_PLUGINS . "PartnerInformation" . PATH_SEP . "views" . PATH_SEP . "dashletPartnerInformation.html";
  }

  public function templateRender()
  {
    $this->smarty->assign("user", $this->user);
    $this->smarty->assign("note", $this->note);
    $this->smarty->assign("item", $this->user);
    return ($this->smarty->fetch($this->smarty->templateFile));
  }

  public function templatePrint()
  {
    echo $this->templateRender();
    exit(0);
  }
}

class dashletPartnerInformationViewDetails extends Smarty
{
  private $smarty;

  private $user;
  private $note;

  public function __construct($u, $n)
  {
    $this->user = $u;
    $this->note = $n;

    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . "smarty/configs";
    $this->smarty->caching      = false;
   $this->smarty->templateFileD = PATH_PLUGINS . "PartnerInformation" . PATH_SEP . "views" . PATH_SEP . "dashletPartnerInfo.html";
  }

  public function templateRenderD()
  {
    $this->smarty->assign("user", $this->user);
    $this->smarty->assign("note", $this->note);

    return ($this->smarty->fetch($this->smarty->templateFileD));
  }

  public function templatePrintD()
  {
    echo $this->templateRenderD();
    exit(0);
  }
}



?>
