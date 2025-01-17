<?php
/*
 * system_user_settings.php
 *
 * part of pfSense (https://www.pfsense.org)
 * Copyright (c) 2004-2013 BSD Perimeter
 * Copyright (c) 2013-2016 Electric Sheep Fencing
 * Copyright (c) 2014-2025 Rubicon Communications, LLC (Netgate)
 * All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

##|+PRIV
##|*IDENT=page-system-user-settings
##|*NAME=System: User Settings
##|*DESCR=Allow access to the 'System: User Settings' page.
##|*MATCH=system_user_settings.php*
##|-PRIV

 require_once("auth.inc");
 require_once("guiconfig.inc");

$pgtitle = array(gettext("System"), gettext("User Settings"));

if (isset($_SESSION['Username']) && isset($userindex[$_SESSION['Username']]) && is_numericint($userindex[$_SESSION['Username']])) {
	$id = $userindex[$_SESSION['Username']];
	$this_user = config_get_path("system/user/{$id}");
}
if ($this_user) {
	$pconfig['webguicss'] = $this_user['webguicss'];
	$pconfig['webguifixedmenu'] = $this_user['webguifixedmenu'];
	$pconfig['webguihostnamemenu'] = $this_user['webguihostnamemenu'];
	$pconfig['dashboardcolumns'] = $this_user['dashboardcolumns'];
	$pconfig['interfacessort'] = isset($this_user['interfacessort']);
	$pconfig['dashboardavailablewidgetspanel'] = isset($this_user['dashboardavailablewidgetspanel']);
	$pconfig['systemlogsfilterpanel'] = isset($this_user['systemlogsfilterpanel']);
	$pconfig['systemlogsmanagelogpanel'] = isset($this_user['systemlogsmanagelogpanel']);
	$pconfig['statusmonitoringsettingspanel'] = isset($this_user['statusmonitoringsettingspanel']);
	$pconfig['webguileftcolumnhyper'] = isset($this_user['webguileftcolumnhyper']);
	$pconfig['disablealiaspopupdetail'] = isset($this_user['disablealiaspopupdetail']);
	$pconfig['pagenamefirst'] = isset($this_user['pagenamefirst']);
} else {
	echo gettext("The settings cannot be managed for a non-local user.");
	include("foot.inc");
	exit;
}

if (isset($_POST['save'])) {
	unset($input_errors);
	/* input validation */

	$reqdfields = explode(" ", "webguicss dashboardcolumns");
	$reqdfieldsn = array(gettext("Theme"), gettext("Dashboard Columns"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	validate_webguicss_field($input_errors, $_POST['webguicss']);
	validate_webguifixedmenu_field($input_errors, $_POST['webguifixedmenu']);
	validate_webguihostnamemenu_field($input_errors, $_POST['webguihostnamemenu']);
	validate_dashboardcolumns_field($input_errors, $_POST['dashboardcolumns']);

	$userent = config_get_path("system/user/{$id}");

	if (!$input_errors) {
		$pconfig['webguicss'] = $userent['webguicss'] = $_POST['webguicss'];

		if ($_POST['webguifixedmenu']) {
			$pconfig['webguifixedmenu'] = $userent['webguifixedmenu'] = $_POST['webguifixedmenu'];
		} else {
			$pconfig['webguifixedmenu'] = "";
			unset($userent['webguifixedmenu']);
		}

		if ($_POST['webguihostnamemenu']) {
			$pconfig['webguihostnamemenu'] = $userent['webguihostnamemenu'] = $_POST['webguihostnamemenu'];
		} else {
			$pconfig['webguihostnamemenu'] = "";
			unset($userent['webguihostnamemenu']);
		}

		$pconfig['dashboardcolumns'] = $userent['dashboardcolumns'] = $_POST['dashboardcolumns'];

		if ($_POST['interfacessort']) {
			$pconfig['interfacessort'] = $userent['interfacessort'] = true;
		} else {
			$pconfig['interfacessort'] = false;
			unset($userent['interfacessort']);
		}

		if ($_POST['dashboardavailablewidgetspanel']) {
			$pconfig['dashboardavailablewidgetspanel'] = $userent['dashboardavailablewidgetspanel'] = true;
		} else {
			$pconfig['dashboardavailablewidgetspanel'] = false;
			unset($userent['dashboardavailablewidgetspanel']);
		}

		if ($_POST['systemlogsfilterpanel']) {
			$pconfig['systemlogsfilterpanel'] = $userent['systemlogsfilterpanel'] = true;
		} else {
			$pconfig['systemlogsfilterpanel'] = false;
			unset($userent['systemlogsfilterpanel']);
		}

		if ($_POST['systemlogsmanagelogpanel']) {
			$pconfig['systemlogsmanagelogpanel'] = $userent['systemlogsmanagelogpanel'] = true;
		} else {
			$pconfig['systemlogsmanagelogpanel'] = false;
			unset($userent['systemlogsmanagelogpanel']);
		}

		if ($_POST['statusmonitoringsettingspanel']) {
			$pconfig['statusmonitoringsettingspanel'] = $userent['statusmonitoringsettingspanel'] = true;
		} else {
			$pconfig['statusmonitoringsettingspanel'] = false;
			unset($userent['statusmonitoringsettingspanel']);
		}

		if ($_POST['webguileftcolumnhyper']) {
			$pconfig['webguileftcolumnhyper'] = $userent['webguileftcolumnhyper'] = true;
		} else {
			$pconfig['webguileftcolumnhyper'] = false;
			unset($userent['webguileftcolumnhyper']);
		}

		if ($_POST['disablealiaspopupdetail']) {
			$pconfig['disablealiaspopupdetail'] = $userent['disablealiaspopupdetail'] = true;
		} else {
			$pconfig['disablealiaspopupdetail'] = false;
			unset($userent['disablealiaspopupdetail']);
		}

		if ($_POST['pagenamefirst']) {
			$pconfig['pagenamefirst'] = $userent['pagenamefirst'] = true;
		} else {
			$pconfig['pagenamefirst'] = false;
			unset($userent['pagenamefirst']);
		}

		config_set_path("system/user/{$id}", $userent);
		$savemsg = sprintf(gettext("User settings successfully changed for user %s."), $_SESSION['Username']);
		write_config($savemsg);
	}
}

include("head.inc");

if ($input_errors) {
	print_input_errors($input_errors);
}

if ($savemsg) {
	print_info_box($savemsg, 'success');
}

$form = new Form();

$section = new Form_Section('User Settings for ' . $_SESSION['Username']);

gen_user_settings_fields($section, $pconfig);

$form->add($section);
print($form);
$csswarning = sprintf(gettext("%sUser-created themes are unsupported, use at your own risk."), "<br />");
?>
<script type="text/javascript">
//<![CDATA[
events.push(function() {

	// Handle displaying a warning message if a user-created theme is selected.
	function setThemeWarning() {
		if ($('#webguicss').val().startsWith("pfSense")) {
			$('#csstxt').html("").addClass("text-default");
		} else {
			$('#csstxt').html("<?=$csswarning?>").addClass("text-danger");
		}
	}

	$('#webguicss').change(function() {
		setThemeWarning();
	});

	// ---------- On initial page load ------------------------------------------------------------
	setThemeWarning();

});
//]]>
</script>
<?php
include("foot.inc");
