<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.ra_sso_error_redirect
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class PlgSystemRa_sso_error_redirect extends CMSPlugin
{
	protected $app;
	private $watchingCallback = false;

	public function onAfterInitialise()
	{
		if (!$this->app) {
			$this->app = Factory::getApplication();
		}

		if (!$this->isSsoCallback()) {
			return;
		}

		$this->watchingCallback = true;
		ob_start();

		register_shutdown_function(array($this, 'renderOnAutoCreationError'));
	}

	public function onAfterRender()
	{
		if (!$this->watchingCallback) {
			return;
		}

		$this->replaceAutoCreationErrorBody($this->app->getBody());
	}

	public function renderOnAutoCreationError()
	{
		if (!$this->watchingCallback) {
			return;
		}

		$body = '';

		if (ob_get_level() > 0) {
			$body = ob_get_contents();
		}

		$this->replaceAutoCreationErrorBody($body);
	}

	private function replaceAutoCreationErrorBody($body)
	{
		if (!$this->isAutoCreationErrorPage($body)) {
			return;
		}

		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		$this->renderAccessErrorPage();
	}

	private function isSsoCallback()
	{
		$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$path = trim((string) parse_url($requestUri, PHP_URL_PATH), '/');
		$callbackPaths = array(
			'v1/ra-sso-login',
			'v1/ra_sso',
		);

		foreach ($callbackPaths as $callbackPath) {
			if (substr($path, -strlen($callbackPath)) === $callbackPath) {
				return true;
			}
		}

		return false;
	}

	private function isAutoCreationErrorPage($body)
	{
		return strpos($body, 'User Auto-Creation Not Available in Current Plugin Version') !== false
			|| strpos($body, 'The plan could not create a new user during the login attempt') !== false;
	}

	private function renderAccessErrorPage()
	{
		if (!headers_sent()) {
			http_response_code(403);
			header('Content-Type: text/html; charset=utf-8');
		}

		$siteName = trim((string) $this->app->get('sitename', 'this Ramblers group'));

		if ($siteName === '') {
			$siteName = 'this Ramblers group';
		}

		$siteName = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');
		$homeUrl = htmlspecialchars(Uri::root(), ENT_QUOTES, 'UTF-8');

		echo '<!DOCTYPE html>
<html lang="en-GB">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Unable to sign you in</title>
	<style>
		:root {
			color-scheme: light;
			--ink: #1c2f49;
			--muted: #50617a;
			--panel: #ffffff;
			--page: #f4f7fb;
			--border: #ccd8e8;
			--accent: #244d7f;
		}

		body {
			margin: 0;
			background: var(--page);
			color: var(--ink);
			font-family: Arial, Helvetica, sans-serif;
			font-size: 17px;
			line-height: 1.55;
		}

		main {
			box-sizing: border-box;
			width: min(860px, calc(100% - 32px));
			margin: 48px auto;
			padding: 36px;
			background: var(--panel);
			border: 1px solid var(--border);
			border-radius: 8px;
			box-shadow: 0 8px 24px rgba(28, 47, 73, 0.08);
		}

		h1 {
			margin: 0 0 18px;
			font-size: 32px;
			line-height: 1.2;
			color: var(--accent);
		}

		h2 {
			margin: 28px 0 10px;
			font-size: 21px;
			line-height: 1.25;
		}

		p {
			margin: 0 0 16px;
		}

		ul {
			margin: 0 0 18px 24px;
			padding: 0;
		}

		li {
			margin: 7px 0;
		}

		.notice {
			margin-top: 24px;
			color: var(--muted);
		}

		.actions {
			margin-top: 26px;
		}

		.button {
			display: inline-block;
			padding: 10px 18px;
			background: var(--accent);
			color: #ffffff;
			border-radius: 4px;
			text-decoration: none;
			font-weight: 700;
		}

		.button:focus,
		.button:hover {
			background: #18375f;
			color: #ffffff;
			text-decoration: none;
		}

		@media (max-width: 560px) {
			main {
				margin: 18px auto;
				padding: 24px;
			}

			h1 {
				font-size: 27px;
			}
		}
	</style>
</head>
<body>
	<main>
		<h1>Unable to sign you in</h1>

		<p>We&rsquo;re sorry, but we couldn&rsquo;t find your Ramblers membership details in our website system.</p>

		<p>This can happen for a couple of reasons:</p>

		<ul>
			<li>You are a new Ramblers member and our website has not yet been updated with your username.</li>
			<li>You are a member of another Ramblers group and are not currently listed in the ' . $siteName . ' website system.</li>
		</ul>

		<h2>What to do next</h2>

		<p>If you believe you should have access, please contact the webmaster and include:</p>

		<ul>
			<li>Your full name</li>
			<li>Your Ramblers membership number, if known</li>
			<li>The email address you used to sign in</li>
			<li>Your Ramblers group, if different from ' . $siteName . '</li>
		</ul>

		<p>Once we have checked your details, we&rsquo;ll update the website system where appropriate and let you know when you can try signing in again.</p>

		<p class="actions"><a class="button" href="' . $homeUrl . '">Return to website</a></p>

		<p class="notice">Thank you for your patience.</p>
	</main>
</body>
</html>';
		exit;
	}
}
