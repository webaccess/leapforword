<?php
require_once ("classes/interfaces/dashletInterface.php");





class dashletDashboardReport implements DashletInterface
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
            FROM   RBAC_ROLES
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
    return "<iframe src=\"{" . "page" . "}?DAS_INS_UID={" . "id" . "}\" width=\"{" . "width" . "}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->role = $config["DAS_ROLE"];
    $this->note = $config["DAS_NOTE"];
  }

  public function render($width = 300)
  {
    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();

    $arrayUser = array();

    $sql = "SELECT USR.USR_USERNAME, USR.USR_FIRSTNAME, USR.USR_LASTNAME, USR.USR_STATUS
            FROM   USERS AS USR
            WHERE  USR.USR_ROLE = '" . $this->role . "'
            ORDER BY USR.USR_USERNAME ASC";
			
	$sql = "SELECT *, u.APP_UID as APP_UID, CASE
WHEN u.APP_STATUS = 'TO_DO' THEN 'To Do'
WHEN u.APP_STATUS = 'DRAFT' THEN 'Draft'
WHEN u.APP_STATUS = 'COMPLETED' THEN 'Completed'
ELSE 'Maybe'
END AS APP_STATUS  , u.PARTNER_NAME as PARTNER_NAME FROM 
(SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS,  a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER,  pfm.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_FINAL_MAPPING pfm ON a.app_uid = pfm.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, ppo.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_PARTNER_ONBOARDING ppo ON a.app_uid = ppo.app_uid 
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pptf.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_PARTNER_TRAINING_FORM pptf ON a.app_uid = pptf.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, ppi.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_PROCESS_INFORMATION ppi ON a.app_uid = ppi.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pfl1.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_FOUND_LEVEL_1 pfl1 ON a.app_uid = pfl1.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pfl2.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_FOUND_LEVEL_2 pfl2 ON a.app_uid = pfl2.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pfl3.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_FOUND_LEVEL_3 pfl3 ON a.app_uid = pfl3.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pfl4.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_FOUND_LEVEL_4 pfl4 ON a.app_uid = pfl4.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgfm.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_FINAL_MAP pgfm ON a.app_uid = pgfm.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr1.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_1 pgr1 ON a.app_uid = pgr1.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr2.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_2 pgr2 ON a.app_uid = pgr2.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr3.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_3 pgr3 ON a.app_uid = pgr3.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr4.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_4 pgr4 ON a.app_uid = pgr4.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr5.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_5 pgr5 ON a.app_uid = pgr5.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr6.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_6 pgr6 ON a.app_uid = pgr6.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgr7.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_REPORT_7 pgr7 ON a.app_uid = pgr7.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgt8.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLC_TEST_8 pgt8 ON a.app_uid = pgt8.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pgfm.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_FINAL_MAPPING pgfm ON a.app_uid = pgfm.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr1.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_1 pglr1 ON a.app_uid = pglr1.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr2.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_2 pglr2 ON a.app_uid = pglr2.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr3.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_3 pglr3 ON a.app_uid = pglr3.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr4.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_4 pglr4 ON a.app_uid = pglr4.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr5.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_5 pglr5 ON a.app_uid = pglr5.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr6.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_6 pglr6 ON a.app_uid = pglr6.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pglr7.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_GLR_REPORT_7 pglr7 ON a.app_uid = pglr7.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pir.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_INTERI_REPORT pir ON a.app_uid = pir.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocfr.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_FINAL_REPORT plocfr ON a.app_uid = plocfr.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr1.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_1 plocr1 ON a.app_uid = plocr1.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr2.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_2 plocr2 ON a.app_uid = plocr2.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr3.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_3 plocr3 ON a.app_uid = plocr3.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr4.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_4 plocr4 ON a.app_uid = plocr4.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr5.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_5 plocr5 ON a.app_uid = plocr5.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr6.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_6 plocr6 ON a.app_uid = plocr6.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr7.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_7 plocr7 ON a.app_uid = plocr7.app_uid
UNION ALL
SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, plocr8.partner_name FROM APP_CACHE_VIEW a 
JOIN PMT_LEVEL_ONE_COMP_REPORT_8 plocr8 ON a.app_uid = plocr8.app_uid
-- UNION ALL
-- SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, pm.partner_name FROM APP_CACHE_VIEW a 
-- JOIN PMT_MAIN pm ON a.app_uid = pm.app_uid
-- UNION ALL
-- SELECT a.app_uid, a.app_number,a.app_status,a.del_THREAD_STATUS, a.APP_TITLE, a.APP_PRO_TITLE,a.APP_TAS_TITLE,a.APP_CURRENT_USER, ptc.partner_name FROM APP_CACHE_VIEW a 
-- JOIN PMT_TRACKCERTIFICATIONRESULT ptc ON a.app_uid = ptc.app_uid

)
AS u 
WHERE u.app_status != 'completed' AND u.del_THREAD_STATUS = 'OPEN'  ORDER BY u.APP_NUMBER DESC";		
			
			
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();

      // $arrayUser[] = array("userName" => $row["USR_USERNAME"], "fullName" => $row["USR_FIRSTNAME"] . " " . $row["USR_LASTNAME"], "status" => $row["USR_STATUS"]);
	  
	  $arrayUser[] = array("UID" => $row["APP_UID"], "STATUS" => $row["APP_STATUS"] ,"PARTNERNAME" => $row["PARTNER_NAME"] , "SrNo" => $row["APP_TITLE"], "PROCESSTITLE" => $row["APP_PRO_TITLE"], "TASKTITLE" => $row["APP_TAS_TITLE"], "ASSIGNEDTO" => $row ["APP_CURRENT_USER"] );
    }

    ///////
    $dashletView = new dashletDashboardReportView($arrayUser, $this->note);
    $dashletView->templatePrint();
  }
}





class dashletDashboardReportView extends Smarty
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
    $this->smarty->templateFile = PATH_PLUGINS . "DashboardReport" . PATH_SEP . "views" . PATH_SEP . "dashletDashboardReport.html";
  }

  public function templateRender()
  {
    $this->smarty->assign("user", $this->user);
    $this->smarty->assign("note", $this->note);

    return ($this->smarty->fetch($this->smarty->templateFile));
  }

  public function templatePrint()
  {
    echo $this->templateRender();
    exit(0);
  }
}
?>