<?php
	$GLOBALS['phpgw_info']['server']['no_jscombine'] = true;
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.template_portico');

	if (!isset($GLOBALS['phpgw_info']['server']['site_title']))
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$cache_refresh_token = '';
	if (!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
	{
		$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
	}

	$config_frontend = CreateObject('phpgwapi.config', $app)->read();

	$tracker_id		 = !empty($config_frontend['tracker_id']) ? $config_frontend['tracker_id'] : '';
	$tracker_code1	 = <<<JS
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
JS;
	$tracker_code2	 = <<<JS
		try
		{
			var pageTracker = _gat._getTracker("{$tracker_id}");
			pageTracker._trackPageview();
		}
		catch(err)
		{
//			alert(err);
		}
JS;

	if ($tracker_id)
	{
		$GLOBALS['phpgw']->js->add_code('', $tracker_code1);
		$GLOBALS['phpgw']->js->add_code('', $tracker_code2);
	}

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');


	$stylesheets = array();


	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/bootstrap.min.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css";
	$stylesheets[]	 = "https://fonts.googleapis.com/css?family=Work+Sans";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/custom.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/normalize.css";

	if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
		$stylesheets[]	 = "/{$app}/templates/bookingfrontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

	foreach ($stylesheets as $stylesheet)
	{
		if (file_exists(PHPGW_SERVER_ROOT . $stylesheet))
		{
			$GLOBALS['phpgw']->template->set_var('stylesheet_uri', $webserver_url . $stylesheet . $cache_refresh_token);
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	if (!empty($GLOBALS['phpgw_info']['server']['logo_url']))
	{
		$logoimg = $GLOBALS['phpgw_info']['server']['logo_url'];
		$GLOBALS['phpgw']->template->set_var('logoimg', $webserver_url . $logoimg);
	}
	if (!empty($GLOBALS['phpgw_info']['server']['logo_title']))
	{
		$logo_title = $GLOBALS['phpgw_info']['server']['logo_title'];
	}
	else
	{
		$logo_title = 'Logo';
	}
	$GLOBALS['phpgw']->template->set_var('logo_title', $logo_title);

	//loads jquery
	phpgwapi_jquery::load_widget('core');

	$javascripts	 = array();
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/popper.min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/bootstrap.min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout-min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout.validation.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/aui-min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/jquery.autocompleter.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/common.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/nb-NO.js";
	$javascripts[]	 = "/phpgwapi/js/dateformat/dateformat.js";


	foreach ($javascripts as $javascript)
	{
		if (file_exists(PHPGW_SERVER_ROOT . $javascript))
		{
			$GLOBALS['phpgw']->template->set_var('javascript_uri', $webserver_url . $javascript . $cache_refresh_token);
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}

	$config = CreateObject('phpgwapi.config', 'booking')->read();

	$bodoc	 = CreateObject('booking.bodocumentation');
	$manual	 = $bodoc->so->getFrontendDoc();

	$menuaction	 = phpgw::get_var('menuaction', 'GET');
	$id			 = phpgw::get_var('id', 'GET');
	if (strpos($menuaction, 'organization'))
	{
		$boorganization	 = CreateObject('booking.boorganization');
		$metainfo		 = $boorganization->so->get_metainfo($id);
		$description	 = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords		 = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'group'))
	{
		$bogroup	 = CreateObject('booking.bogroup');
		$metainfo	 = $bogroup->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['organization'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'building'))
	{
		$bobuilding	 = CreateObject('booking.bobuilding');
		$metainfo	 = $bobuilding->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'resource'))
	{
		$boresource	 = CreateObject('booking.boresource');
		$metainfo	 = $boresource->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['building'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	if ($keywords != '')
	{
		$keywords = '<meta name="keywords" content="' . $keywords . '">';
	}
	else
	{
		$keywords = '<meta name="keywords" content="phpGroupWare">';
	}
	if (!empty($description))
	{
		$description = '<meta name="description" content="' . $description . '">';
	}
	else
	{
		$description = '<meta name="description" content="phpGroupWare">';
	}
	if (!empty($config['metatag_author']))
	{
		$author = '<meta name="author" content="' . $config['metatag_author'] . '">';
	}
	else
	{
		$author = '<meta name="author" content="phpGroupWare http://www.phpgroupware.org">';
	}
	if (!empty($config['metatag_robots']))
	{
		$robots = '<meta name="robots" content="' . $config['metatag_robots'] . '">';
	}
	else
	{
		$robots = '<meta name="robots" content="none">';
	}
	if (!empty($config_frontend['site_title']))
	{
		$site_title = $config_frontend['site_title'];
	}
	else
	{
		$site_title = $GLOBALS['phpgw_info']['server']['site_title'];
	}

	if (!$footer_info = $config_frontend['footer_info'])
	{
		$footer_info = 'footer info settes i bookingfrontend config';
	}

	phpgwapi_cache::session_set('phpgwapi', 'footer_info', $footer_info);

	//$test = $GLOBALS['phpgw']->common->get_on_events();
	$test	 = str_replace('window.onload = function()', '$(document).ready(function()', $test);
	$test	 = str_replace("\n}\n", "\n})\n", $test);

	$tpl_vars = array
		(
		'css'					 => $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
		'javascript'			 => $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
		'img_icon'				 => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'			 => $site_title,
		'str_base_url'			 => $GLOBALS['phpgw']->link('/', array(), true),
		'dateformat_backend'	 => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
		'site_url'				 => $GLOBALS['phpgw']->link("/{$app}/", array()),
		'webserver_url'			 => $webserver_url,
		'win_on_events'			 => $test,
		'userlang'				 => $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'],
		'metainfo_author'		 => $author,
		'metainfo_keywords'		 => $keywords,
		'metainfo_description'	 => $description,
		'metainfo_robots'		 => $robots,
		'lbl_search'			 => lang('Search'),
		'placeholder_search'	 => lang('Search building, resource, organization'),
		'logofile'				 => $logofile_frontend,
		'header_search_class'	 => 'hidden'//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
	);

	$tpl_vars['manual_text'] = lang('manual');
	$tpl_vars['manual_url']	 = $manual;
//	$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );
//	_debug_array($user);

	$bouser	 = CreateObject('bookingfrontend.bouser');
	$org	 = CreateObject('bookingfrontend.uiorganization');

	if ($bouser->is_logged_in())
	{
		$orgs = phpgwapi_cache::session_get($bouser->get_module(), $bouser::ORGARRAY_SESSION_KEY);

		$session_org_id = phpgw::get_var('session_org_id', 'int', 'GET');

		function get_ids_from_array( $org )
		{
			return $org['orgnumber'];
		}
		if ($session_org_id && in_array($session_org_id, array_map("get_ids_from_array", $orgs)))
		{
			try
			{
				$org_number = createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($session_org_id);
				if ($org_number)
				{
					$bouser->change_org($org_number);
				}
			}
			catch (sfValidatorError $e)
			{
				$session_org_id = -1;
			}
		}

		if ($bouser->orgname == '000000000')
		{
			$tpl_vars['login_text_org']	 = lang('SSN not registred');
			$tpl_vars['login_text']		 = lang('Logout');
			$tpl_vars['org_url']		 = '#';
		}
		else
		{
			$tpl_vars['login_text_org']	 = $bouser->orgname;
			$tpl_vars['login_text']		 = lang('Logout');
			$tpl_vars['org_url']		 = $GLOBALS['phpgw']->link("/{$app}/", array('menuaction' => 'bookingfrontend.uiorganization.show',
				'id' => $org->get_orgid($bouser->orgnr)));
		}
		$tpl_vars['login_text']	 = $bouser->orgnr . ' :: ' . lang('Logout');
		$tpl_vars['login_url']	 = 'logout.php';
	}
	else
	{
		$tpl_vars['login_text_org']	 = '';
		$tpl_vars['org_url']		 = '#';
		$tpl_vars['login_text']		 = lang('Login');
		$tpl_vars['login_url']		 = 'login.php?after=' . urlencode($_SERVER['QUERY_STRING']);
		$login_parameter			 = !empty($config_frontend['login_parameter']) ? $config_frontend['login_parameter'] : '';
		$custom_login_url			 = !empty($config_frontend['custom_login_url']) ? $config_frontend['custom_login_url'] : '';
		if ($login_parameter)
		{
			$login_parameter		 = ltrim($login_parameter, '&');
			$tpl_vars['login_url']	 .= "&{$login_parameter}";
		}
		if ($custom_login_url)
		{
			$tpl_vars['login_url'] = $custom_login_url;
		}
	}
	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
