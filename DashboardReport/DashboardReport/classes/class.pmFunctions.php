<?php
/**
 * class.DashboardReport.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// DashboardReport PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function DashboardReport_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function DashboardReport_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
