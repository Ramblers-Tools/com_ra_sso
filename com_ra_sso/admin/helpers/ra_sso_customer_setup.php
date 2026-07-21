<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_ra_sso
 *
 * @author    East Cheshire Ramblers
 * @copyright Copyright (C) 2026 East Cheshire Ramblers. Based on original work Copyright (C) 2015 miniOrange.
 * @license   GNU General Public License version 3; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

require_once JPATH_ADMINISTRATOR . '/components/com_ra_sso/helpers/ra_sso_utility.php';

class RaSsoCustomer
{
    
    public $email;
    public $phone;
    public $customerKey;
    public $transactionId;
    
    public static function getAccountDetails()
    {
        $db = self::getDBObject();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ra_sso_customer'));
        $query->where($db->quoteName('id')." = 1");

        $db->setQuery($query);
        $result=$db->loadAssoc();
        return $result;
    }

    private static function getDBObject()
    {
        $app = Factory::getApplication();

        if (method_exists($app, 'getDatabase')) {
            return $app->getDatabase();
        }
        return Factory::getDbo();
    }
    
    public static function getConfigurationDetails()
    {
        $db = self::getDBObject();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ra_sso_config'));
        $query->where($db->quoteName('id')." = 1");

        $db->setQuery($query);
        $result=$db->loadAssoc();
        return $result;
    }
    
    function getAppJason()
    {
        return '{	
        "azure": {
            "label":"Azure AD", "type":"oauth", "image":"azure.png", "scope": "openid email profile", "authorize": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize", "token": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token", "userinfo":"https://graph.microsoft.com/beta/me", "logo_class":"fa fa-windowslive"
        },
        "cognito": {
            "label":"AWS Cognito", "type":"oauth", "image":"cognito.png", "scope": "openid", "authorize": "https://{domain}/oauth2/authorize", "token": "https://{domain}/oauth2/token", "userinfo": "https://{domain}/oauth2/userInfo", "logo_class":"fa fa-amazon"
        },
        "whmcs": {
            "label":"WHMCS", "type":"oauth", "image":"whmcs.png", "scope": "openid profile email", "authorize": "https://{domain}/oauth/authorize.php", "token": "https://{domain}/oauth/token.php", "userinfo": "https://{domain}/oauth/userinfo.php?access_token=", "logo_class":"fa fa-lock"
        },
        "slack": {
            "label":"Slack", "type":"oauth", "image":"slack.png", "scope": "users.profile:read", "authorize": "https://slack.com/oauth/authorize", "token": "https://slack.com/api/oauth.access", "userinfo": "https://slack.com/api/users.profile.get", "logo_class":"fa fa-slack"
        },
        "discord": {
            "label":"Discord", "type":"oauth", "image":"discord.png", "scope": "identify email", "authorize": "https://discordapp.com/api/oauth2/authorize", "token": "https://discordapp.com/api/oauth2/token", "userinfo": "https://discordapp.com/api/users/@me", "logo_class":"fa fa-lock"
        },
        "invisioncommunity": {
            "label":"Invision Community", "type":"oauth", "image":"invis.png", "scope": "email", "authorize": "{domain}/oauth/authorize/", "token": "https://{domain}/oauth/token/", "userinfo": "https://{domain}/oauth/me", "logo_class":"fa fa-lock"
        },
        "bitrix24": {
            "label":"Bitrix24", "type":"oauth", "image":"bitrix24.png", "scope": "user", "authorize": "https://{accountid}.bitrix24.com/oauth/authorize", "token": "https://{accountid}.bitrix24.com/oauth/token", "userinfo": "https://{accountid}.bitrix24.com/rest/user.current.json?auth=", "logo_class":"fa fa-clock-o"
        },
        "wso2": {
            "label":"WSO2", "type":"oauth", "image":"wso2.png", "scope": "openid", "authorize": "https://{domain}/wso2/oauth2/authorize", "token": "https://{domain}/wso2/oauth2/token", "userinfo": "https://{domain}/wso2/oauth2/userinfo", "logo_class":"fa fa-lock"
        },
        "gapps": {
            "label":"Google", "type":"oauth", "image":"google.png", "scope": "email", "authorize": "https://accounts.google.com/o/oauth2/auth", "token": "https://www.googleapis.com/oauth2/v4/token", "userinfo": "https://www.googleapis.com/oauth2/v1/userinfo", "logo_class":"fa fa-google-plus"
        },
        "fbapps": {
            "label":"Facebook", "type":"oauth", "image":"facebook.png", "scope": "public_profile email", "authorize": "https://www.facebook.com/dialog/oauth", "token": "https://graph.facebook.com/v2.8/oauth/access_token", "userinfo": "https://graph.facebook.com/me/?fields=id,name,email,age_range,first_name,gender,last_name,link", "logo_class":"fa fa-facebook"
        },
        "gluu": {
            "label":"Gluu Server", "type":"oauth", "image":"gluu.png", "scope": "openid", "authorize": "http://{domain}/oxauth/restv1/authorize", "token": "http://{domain}/oxauth/restv1/token", "userinfo": "http:///{domain}/oxauth/restv1/userinfo", "logo_class":"fa fa-lock"
        },
        "linkedin": {
            "label":"LinkedIn", "type":"oauth", "image":"linkedin.png", "scope": "openid email profile", "authorize": "https://www.linkedin.com/oauth/v2/authorization", "token": "https://www.linkedin.com/oauth/v2/accessToken", "userinfo": "https://api.linkedin.com/v2/me", "logo_class":"fa fa-linkedin-square"
        },
        "strava": {
            "label":"Strava", "type":"oauth", "image":"strava.png", "scope": "public", "authorize": "https://www.strava.com/oauth/authorize", "token": "https://www.strava.com/oauth/token", "userinfo": "https://www.strava.com/api/v3/athlete", "logo_class":"fa fa-lock"
        },
        "fitbit": {
            "label":"FitBit", "type":"oauth", "image":"fitbit.png", "scope": "profile", "authorize": "https://www.fitbit.com/oauth2/authorize", "token": "https://api.fitbit.com/oauth2/token", "userinfo": "https://www.fitbit.com/1/user", "logo_class":"fa fa-lock"
        },
        "box": {
            "label":"Box", "type":"oauth", "image":"box.png", "scope": "root_readwrite", "authorize": "https://account.box.com/api/oauth2/authorize", "token": "https://api.box.com/oauth2/token", "userinfo": "https://api.box.com/2.0/users/me", "logo_class":"fa fa-lock"
        },
        "github": {
            "label":"GitHub", "type":"oauth", "image":"github.png", "scope": "user repo", "authorize": "https://github.com/login/oauth/authorize", "token": "https://github.com/login/oauth/access_token", "userinfo": "https://api.github.com/user", "logo_class":"fa fa-github"
        },
        "gitlab": {
            "label":"GitLab", "type":"oauth", "image":"gitlab.png", "scope": "read_user", "authorize": "https://gitlab.com/oauth/authorize", "token": "http://gitlab.com/oauth/token", "userinfo": "https://gitlab.com/api/v4/user", "logo_class":"fa fa-gitlab"
        },
        "clever": {
            "label":"Clever", "type":"oauth", "image":"clever.png", "scope": "read:students read:teachers read:user_id", "authorize": "https://clever.com/oauth/authorize", "token": "https://clever.com/oauth/tokens", "userinfo": "https://api.clever.com/v1.1/me", "logo_class":"fa fa-lock"
        },
        "salesforce": {
            "label":"Salesforce", "type":"oauth", "image":"salesforce.png", "scope": "email", "authorize": "https://login.salesforce.com/services/oauth2/authorize", "token": "https://login.salesforce.com/services/oauth2/token", "userinfo": "https://login.salesforce.com/services/oauth2/userinfo", "logo_class":"fa fa-lock"
        },
        "reddit": {
            "label":"Reddit", "type":"oauth", "image":"reddit.png", "scope": "identity", "authorize": "https://www.reddit.com/api/v1/authorize", "token": "https://www.reddit.com/api/v1/access_token", "userinfo": "https://www.reddit.com/api/v1/me", "logo_class":"fa fa-reddit"
        },
        "spotify": {
            "label":"Spotify", "type":"oauth", "image":"spotify.png", "scope": "user-read-private user-read-email", "authorize": "https://accounts.spotify.com/authorize", "token": "https://accounts.spotify.com/api/token", "userinfo": "https://api.spotify.com/v1/me", "logo_class":"fa fa-spotify"
        },
        "eveonlinenew": {
            "label":"Eve Online", "type":"oauth", "image":"eveonline.png", "scope": "publicData", "authorize": "https://login.eveonline.com/oauth/authorize", "token": "https://login.eveonline.com/oauth/token", "userinfo": "https://esi.evetech.net/verify", "logo_class":"fa fa-lock"
        },
        "pinterest": {
            "label":"Pinterest", "type":"oauth", "image":"pinterest.png", "scope": "read_public", "authorize": "https://api.pinterest.com/oauth/", "token": "https://api.pinterest.com/v1/oauth/token", "userinfo": "https://api.pinterest.com/v1/me/", "logo_class":"fa fa-pinterest"
        },
        "vimeo": {
            "label":"Vimeo", "type":"oauth", "image":"vimeo.png", "scope": "public", "authorize": "https://api.vimeo.com/oauth/authorize", "token": "https://api.vimeo.com/oauth/access_token", "userinfo": "https://api.vimeo.com/me", "logo_class":"fa fa-vimeo"
        },
        "dailymotion": {
            "label":"Dailymotion", "type":"oauth", "image":"dailymotion.png", "scope": "email", "authorize": "https://www.dailymotion.com/oauth/authorize", "token": "https://api.dailymotion.com/oauth/token", "userinfo": "https://api.dailymotion.com/user/me?fields=id,username,email,first_name,last_name", "logo_class":"fa fa-lock"
        },
        "autodesk": {
            "label":"Autodesk", "type":"oauth", "image":"autodesk.png", "scope": "user:read user-profile:read", "authorize": "https://developer.api.autodesk.com/authentication/v1/authorize", "token": "https://developer.api.autodesk.com/authentication/v1/gettoken", "userinfo": "https://developer.api.autodesk.com/userprofile/v1/users/@me", "logo_class":"fa fa-lock"
        },
        "zendesk": {
            "label":"Zendesk", "type":"oauth", "image":"zendesk.png", "scope": "read write", "authorize": "https://{domain}/oauth/authorizations/new", "token": "https://{domain}/oauth/tokens", "userinfo": "https://{domain}/api/v2/users", "logo_class":"fa fa-lock"
        },
        "laravel": {
            "label":"Laravel", "type":"oauth", "image":"laravel.png", "scope": "", "authorize": "http://{domain}/oauth/authorize", "token": "http://{domain}/oauth/token", "userinfo": "http://{domain}}/api/user/get", "logo_class":"fa fa-lock"
        },
        "identityserver": {
            "label":"Identity Server", "type":"oauth", "image":"identityserver.png", "scope": "openid", "authorize": "https://{domain}/connect/authorize", "token": "https://{domain}/connect/token", "userinfo": "https://{domain}/connect/introspect", "logo_class":"fa fa-lock"
        },
        "nextcloud": {
            "label":"Nextcloud", "type":"oauth", "image":"nextcloud.png", "scope": "user:read:email", "authorize": "https://{domain}/index.php/apps/oauth2/authorize", "token": "https://{domain}/index.php/apps/oauth2/api/v1/token", "userinfo": "https://{domain}/ocs/v2.php/cloud/user?format=json", "logo_class":"fa fa-lock"
        },
        "twitch": {
            "label":"Twitch", "type":"oauth", "image":"twitch.png", "scope": "Analytics:read:extensions", "authorize": "https://id.twitch.tv/oauth2/authorize", "token": "https://id.twitch.tv/oauth2/token", "userinfo": "https://id.twitch.tv/oauth2/userinfo", "logo_class":"fa fa-lock"
        },
        "wildApricot": {
            "label":"Wild Apricot", "type":"oauth", "image":"wildApricot.png", "scope": "auto", "authorize": "https://{domain}/sys/login/OAuthLogin", "token": "https://oauth.wildapricot.org/auth/token", "userinfo": "https://api.wildapricot.org/v2.1/accounts/{accountid}/contacts/me", "logo_class":"fa fa-lock"
        },
        "connect2id": {
            "label":"Connect2id", "type":"oauth", "image":"connect2id.png", "scope": "openid", "authorize": "https://c2id.com/login", "token": "https://{domain}/token", "userinfo": "https://{domain}/userinfo", "logo_class":"fa fa-lock"
        },
        "Amazon": {
            "label":"Amazon", "type":"oauth", "image":"cognito.png", "scope": "profile", "authorize": "https://www.amazon.com/ap/oa", "token": "https://api.amazon.com/auth/o2/token", "userinfo": "https://api.amazon.com/user/profile", "logo_class":"fa fa-lock"
        },
        "Office 365": {
            "label":"Office 365", "type":"oauth", "image":"microsoft.webp", "scope": "openid email profile", "authorize": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize", "token": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token", "userinfo": "https://graph.microsoft.com/beta/me", "logo_class":"fa fa-lock"
        },
        "Instagram": {
            "label":"Instagram", "type":"oauth", "image":"instagram.png", "scope": "user_profile user_media", "authorize": "https://api.instagram.com/oauth/authorize", "token": "https://api.instagram.com/oauth/access_token", "userinfo": "https://graph.instagram.com/me?fields=id,username&access_token=", "logo_class":"fa fa-lock"
        },
        "Line":{
            "label":"Line", "type":"oauth", "image":"line.webp", "scope": "profile openid email", "authorize": "https://access.line.me/oauth2/v2.1/authorize", "token": "https://api.line.me/oauth2/v2.1/token", "userinfo": "https://api.line.me/v2/profile", "logo_class":"fa fa-lock"
        },
        "PingFederate": {
            "label":"PingFederate", "type":"oauth", "image":"ping.webp", "scope": "openid", "authorize": "https://{domain}/as/authorization.oauth2", "token": "https://{domain}/as/token.oauth2", "userinfo": "https://{domain}/idp/userinfo.oauth2", "logo_class":"fa fa-lock"
        },
        "OpenAthens": {
            "label":"OpenAthens", "type":"oauth", "image":"openathens.webp", "scope": "openid", "authorize": "https://sp.openathens.net/oauth2/authorize", "token": "https://sp.openathens.net/oauth2/token", "userinfo": "https://sp.openathens.net/oauth2/userInfo", "logo_class":"fa fa-lock"
        },
        "Intuit": {
            "label":"Intuit", "type":"oauth", "image":"intuit.webp", "scope": "openid email profile", "authorize": "https://appcenter.intuit.com/connect/oauth2", "token": "https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer", "userinfo": "https://accounts.platform.intuit.com/v1/openid_connect/userinfo", "logo_class":"fa fa-lock"
        },
        "Twitter": {
            "label":"Twitter", "type":"oauth", "image":"twitter-logo.webp", "scope": "email", "authorize": "https://api.twitter.com/oauth/authorize", "token": "https://api.twitter.com/oauth2/token", "userinfo": "https://api.twitter.com/1.1/users/show.json?screen_name=here-comes-twitter-screen-name", "logo_class":"fa fa-lock"
        },
        "WordPress": {
            "label":"WordPress", "type":"oauth", "image":"wordpress.png", "scope": "profile openid email custom", "authorize": "http://{site_base_url}/wp-json/moserver/authorize", "token": "http://{site_base_url}/wp-json/moserver/token", "userinfo": "http://{site_base_url}/wp-json/moserver/resource", "logo_class":"fa fa-lock"
        },
        "Subscribestar": {
            "label":"Subscribestar", "type":"oauth", "image":"Subscriberstar-logo.png", "scope": "user.read user.email.read", "authorize": "https://www.subscribestar.com/oauth2/authorize", "token": "https://www.subscribestar.com/oauth2/token", "userinfo": "https://www.subscribestar.com/api/graphql/v1?query={user{name,email}}", "logo_class":"fa fa-lock"
        },
        "Classlink": {
            "label":"Classlink", "type":"oauth", "image":"classlink.webp", "scope": "email profile oneroster full", "authorize": "https://launchpad.classlink.com/oauth2/v2/auth", "token": "https://launchpad.classlink.com/oauth2/v2/token", "userinfo": "https://nodeapi.classlink.com/v2/my/info", "logo_class":"fa fa-lock"
        },
        "HP": {
            "label":"HP", "type":"oauth", "image":"hp-logo.webp", "scope": "read", "authorize": "https://{hp_domain}/v1/oauth/authorize", "token": "https://{hp_domain}/v1/oauth/token", "userinfo": "https://{hp_domain}/v1/userinfo", "logo_class":"fa fa-lock"
        },
        "Basecamp": {
            "label":"Basecamp", "type":"oauth", "image":"basecamp-logo.webp", "scope": "openid", "authorize": "https://launchpad.37signals.com/authorization/new?type=web_server", "token": "https://launchpad.37signals.com/authorization/token?type=web_server", "userinfo": "https://launchpad.37signals.com/authorization.json", "logo_class":"fa fa-lock"
        },
        "ServiceNow": {
            "label":"ServiceNow", "type":"oauth", "image":"servicenow-logo.webp", "scope": "email profile", "authorize": "https://{your-servicenow-domain}/oauth_auth.do", "token": "https://{your-servicenow-domain}/oauth_token.do", "userinfo": "https://{your-servicenow-domain}/{base-api-path}?access_token=", "logo_class":"fa fa-lock"
        },
        "IMIS": {
            "label":"IMIS", "type":"oauth", "image":"imis-logo.webp", "scope": "openid", "authorize": "https://{your-imis-domain}/sso-pages/Aurora-SSO-Redirect.aspx", "token": "https://{your-imis-domain}/token", "userinfo": "https://{your-imis-domain}/api/iqa?queryname=$/Bearer_Info_Aurora", "logo_class":"fa fa-lock"
        },
		"Canvas": {
			"label":"Canvas", "type":"oauth", "image":"canvas-logo.webp", "scope": "openid profile", "authorize": "https://{your-site-url}/login/oauth2/auth", "token": "https://{your-site-url}/login/oauth2/token", "userinfo": "https://{your-site-url}/login/v2.1/users/self", "logo_class":"fa fa-lock"
		},
        "azureb2c": {
            "label":"Azure B2C", "type":"openidconnect", "image":"azure.png", "scope": "openid email", "authorize": "https://{tenant}.b2clogin.com/{tenant}.onmicrosoft.com/{policy}/oauth2/v2.0/authorize", "token": "https://{tenant}.b2clogin.com/{tenant}.onmicrosoft.com/{policy}/oauth2/v2.0/token", "userinfo": "", "logo_class":"fa fa-windowslive"
        },
        "adfs": {
            "label":"ADFS", "type":"openidconnect", "image":"adfs.png", "scope": "openid", "authorize": "https://{domain}/adfs/oauth2/authorize/", "token": "https://{domain}/adfs/oauth2/token/", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-windowslive"
        },
        "keycloak": {
            "label":"keycloak", "type":"openidconnect", "image":"keycloak.png", "scope": "openid", "authorize": "{domain}realms/{realm}/protocol/openid-connect/auth", "token": "{domain}realms/{realm}/protocol/openid-connect/token", "userinfo": "{domain}realms/{realm}/protocol/openid-connect/userinfo", "logo_class":"fa fa-lock"
        },
        "okta": {
            "label":"Okta", "type":"openidconnect", "image":"okta.png", "scope": "openid email profile", "authorize": "https://{domain}/oauth2/default/v1/authorize", "token": "https://{domain}/oauth2/default/v1/token", "userinfo": "", "logo_class":"fa fa-lock"
        },
        "onelogin": {
            "label":"OneLogin", "type":"openidconnect", "image":"onelogin.png", "scope": "openid", "authorize": "https://{domain}/oidc/auth", "token": "https://{domain}/oidc/token", "userinfo": "", "logo_class":"fa fa-lock"
        },
        "paypal": {
            "label":"PayPal", "type":"openidconnect", "image":"paypal.png", "scope": "openid", "authorize": "https://www.paypal.com/signin/authorize", "token": "https://api.paypal.com/v1/oauth2/token", "userinfo": "", "logo_class":"fa fa-paypal"
        },
        "swiss-rx-login": {
            "label":"Swiss RX Login", "type":"openidconnect", "image":"swiss-rx-login.png", "scope": "anonymous", "authorize": "https://www.swiss-rx-login.ch/oauth/authorize", "token": "https://swiss-rx-login.ch/oauth/token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
        },
        "yahoo": {
            "label":"Yahoo", "type":"openidconnect", "image":"yahoo.png", "scope": "openid", "authorize": "https://api.login.yahoo.com/oauth2/request_auth", "token": "https://api.login.yahoo.com/oauth2/get_token", "userinfo": "", "logo_class":"fa fa-yahoo"
        },
        "orcid": {
            "label":"ORCID", "type":"openidconnect", "image":"orcid.png", "scope": "openid", "authorize": "https://orcid.org/oauth/authorize", "token": "https://orcid.org/oauth/token", "userinfo": "", "logo_class":"fa fa-lock"
        },
        "diaspora": {
            "label":"Diaspora", "type":"openidconnect", "image":"diaspora.png", "scope": "openid", "authorize": "https://{domain}/api/openid_connect/authorizations/new", "token": "https://{domain}/api/openid_connect/access_tokens", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
        },
        "MineCraft": {
			"label":"MineCraft", "type":"openidconnect", "image":"minecraft-logo.webp", "scope": "openid", "authorize": "https://login.live.com/oauth20_authorize.srf", "token": "https://login.live.com/oauth20_token.srf", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
		},
        "Freja EID": {
            "label":"Freja EID", "type":"openidconnect", "image":"frejaeid-logo.webp", "scope": "openid profile email", "authorize": "https://oidc.prod.frejaeid.com/oidc/authorize", "token": "https://oidc.prod.frejaeid.com/oidc/token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
        },
        "Elvanto": {
            "label":"Elvanto", "type":"openidconnect", "image":"elvanto-logo.webp", "scope": "ManagePeople", "authorize": "https://api.elvanto.com/oauth?", "token": "https://api.elvanto.com/oauth/token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
        },
        "UNA": {
            "label":"UNA", "type":"openidconnect", "image":"una-logo.webp", "scope": "basic", "authorize": "https://{site-url}.una.io/oauth2/authorize?", "token": "https://{site-url}.una.io/oauth2/access_token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
        },
		"Ticketmaster": {
			"label":"Ticketmaster", "type":"openidconnect", "image":"ticketmaster-logo.webp", "scope": "openid email", "authorize": "https://auth.ticketmaster.com/as/authorization.oauth2", "token": "https://auth.ticketmaster.com/as/token.oauth2", "userinfo": "", "logo_class":"fa fa-lock"
		},
		"Mindbody": {
			"label":"Mindbody", "type":"openidconnect", "image":"mindbody-logo.webp", "scope": "email profile openid", "authorize": "https://signin.mindbodyonline.com/connect/authorize", "token": "https://signin.mindbodyonline.com/connect/token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
		},
		"iGov": {
			"label":"iGov", "type":"openidconnect", "image":"iGov-logo.webp", "scope": "openid profile", "authorize": "https://idp.government.gov/oidc/authorization", "token": "https://idp.government.gov/token", "userinfo": "", "logo_class":"fa fa-lock"
		},
		"LearnWorlds": {
			"label":"LearnWorlds", "type":"openidconnect", "image":"learnworlds-logo.webp", "scope": "openid profile", "authorize": "https://api.learnworlds.com/oauth", "token": "https://api.learnworlds.com/oauth2/access_token", "userinfo": "", "guide":"https://github.com/Ramblers-Tools/ra-sso/issues", "logo_class":"fa fa-lock"
		},
        "other": {
            "label":"Custom OAuth", "type":"oauth", "image":"customapp.png", "scope": "", "authorize": "", "token": "", "userinfo": "", "logo_class":"fa fa-lock"
        },
        "openidconnect": {
            "label":"Custom OpenID Connect App", "type":"openidconnect", "image":"customapp.png", "scope": "", "authorize": "", "token": "", "userinfo": "", "logo_class":"fa fa-lock"
        }
        }';
    }

    function getAppData()
    {
        return '{
			"azure": {
				"0":"both","1":"Tenant"
			},
			"azureb2c": {
				"0":"both","1":"Tenant,Policy"
			},
			"cognito": {
				"0":"both","1": "Domain"
			},
			"adfs": {
				"0":"both","1":"Domain"
			},
			"whmcs": {
				"0":"both","1":"Domain"
			},
			"keycloak": {
				"0":"both","1":"Domain,Realm"
			},
			"invisioncommunity": {
				"0":"both","1":"Domain"
			},
			"bitrix24": {
				"0":"both","1":"Domain"
			},
			"wso2": {
				"0":"both","1":"Domain"
			},
			"okta": {
				"0":"header","1":"Domain"
			},
			"onelogin": {
				"0":"both","1":"Domain"
			},
			"gluu": {
				"0":"both","1": "Domain" 
			},
			"zendesk": {
				"0":"both","1":"Domain"
			},
			"laravel": {
				"0":"both","1":"Domain"
			},
			"identityserver": {
				"0":"both","1":"Domain"
			},
			"nextcloud": {
				"0":"both","1":"Domain"
			},
			"wildApricot": {
				"0":"both","1":"Domain,AccountId"
			},
			"connect2id": {
				"0":"both","1":"Domain"
			},
			"diaspora": {
				"0":"both","1":"Domain" 
			},
			"Office 365": {
				"0":"both","1":"Tenant" 
			},
			"PingFederate": {
				"0":"both","1":"Domain"
			},
			"HP": {
				"0":"both","1":"Domain"
			},
			"Neon CRM": {
				"0":"both","1":"Domain"
			},
			"Canvas": {
				"0":"both","1":"Domain"
			},
			"UNA": {
				"0":"both","1":"Domain"
			},
			"OpenedX": {
				"0":"both","1":"Domain"
			},
			"ServiceNow": {
				"0":"both","1":"Domain"
			},
			"WordPress": {
				"0":"both","1":"Domain"
			},
			"MemberClicks": {
				"0":"both","1":"Domain"
			},
			"IMIS": {
				"0":"both","1":"Domain"
			}
		}';
    }

    function getCountryCodes()
    {
        return '[
            { "country_name": "Afghanistan", "country_id": "AF", "country_code": "+93" },
            { "country_name": "Albania", "country_id": "AL", "country_code": "+355" },
            { "country_name": "Algeria", "country_id": "DZ", "country_code": "+213" },
            { "country_name": "Andorra", "country_id": "AD", "country_code": "+376" },
            { "country_name": "Angola", "country_id": "AO", "country_code": "+244" },
            { "country_name": "Argentina", "country_id": "AR", "country_code": "+54" },
            { "country_name": "Armenia", "country_id": "AM", "country_code": "+374" },
            { "country_name": "Australia", "country_id": "AU", "country_code": "+61" },
            { "country_name": "Austria", "country_id": "AT", "country_code": "+43" },
            { "country_name": "Azerbaijan", "country_id": "AZ", "country_code": "+994" },       
            { "country_name": "Bahamas", "country_id": "BS", "country_code": "+1" },
            { "country_name": "Bahrain", "country_id": "BH", "country_code": "+973" },
            { "country_name": "Bangladesh", "country_id": "BD", "country_code": "+880" },
            { "country_name": "Belarus", "country_id": "BY", "country_code": "+375" },
            { "country_name": "Belgium", "country_id": "BE", "country_code": "+32" },
            { "country_name": "Belize", "country_id": "BZ", "country_code": "+501" },
            { "country_name": "Benin", "country_id": "BJ", "country_code": "+229" },
            { "country_name": "Bhutan", "country_id": "BT", "country_code": "+975" },
            { "country_name": "Bolivia", "country_id": "BO", "country_code": "+591" },
            { "country_name": "Bosnia and Herzegovina", "country_id": "BA", "country_code": "+387" },
            { "country_name": "Botswana", "country_id": "BW", "country_code": "+267" },
            { "country_name": "Brazil", "country_id": "BR", "country_code": "+55" },
            { "country_name": "Brunei", "country_id": "BN", "country_code": "+673" },
            { "country_name": "Bulgaria", "country_id": "BG", "country_code": "+359" },     
            { "country_name": "Cambodia", "country_id": "KH", "country_code": "+855" },
            { "country_name": "Cameroon", "country_id": "CM", "country_code": "+237" },
            { "country_name": "Canada", "country_id": "CA", "country_code": "+1" },
            { "country_name": "Chile", "country_id": "CL", "country_code": "+56" },
            { "country_name": "China", "country_id": "CN", "country_code": "+86" },
            { "country_name": "Colombia", "country_id": "CO", "country_code": "+57" },
            { "country_name": "Costa Rica", "country_id": "CR", "country_code": "+506" },
            { "country_name": "Croatia", "country_id": "HR", "country_code": "+385" },
            { "country_name": "Cuba", "country_id": "CU", "country_code": "+53" },
            { "country_name": "Cyprus", "country_id": "CY", "country_code": "+357" },
            { "country_name": "Czech Republic", "country_id": "CZ", "country_code": "+420" },       
            { "country_name": "Denmark", "country_id": "DK", "country_code": "+45" },
            { "country_name": "Dominican Republic", "country_id": "DO", "country_code": "+1" },     
            { "country_name": "Ecuador", "country_id": "EC", "country_code": "+593" },
            { "country_name": "Egypt", "country_id": "EG", "country_code": "+20" },
            { "country_name": "El Salvador", "country_id": "SV", "country_code": "+503" },
            { "country_name": "Estonia", "country_id": "EE", "country_code": "+372" },
            { "country_name": "Ethiopia", "country_id": "ET", "country_code": "+251" },     
            { "country_name": "Finland", "country_id": "FI", "country_code": "+358" },
            { "country_name": "France", "country_id": "FR", "country_code": "+33" },        
            { "country_name": "Georgia", "country_id": "GE", "country_code": "+995" },
            { "country_name": "Germany", "country_id": "DE", "country_code": "+49" },
            { "country_name": "Ghana", "country_id": "GH", "country_code": "+233" },
            { "country_name": "Greece", "country_id": "GR", "country_code": "+30" },        
            { "country_name": "Hungary", "country_id": "HU", "country_code": "+36" },       
            { "country_name": "Iceland", "country_id": "IS", "country_code": "+354" },
            { "country_name": "India", "country_id": "IN", "country_code": "+91" },
            { "country_name": "Indonesia", "country_id": "ID", "country_code": "+62" },
            { "country_name": "Iran", "country_id": "IR", "country_code": "+98" },
            { "country_name": "Iraq", "country_id": "IQ", "country_code": "+964" },
            { "country_name": "Ireland", "country_id": "IE", "country_code": "+353" },
            { "country_name": "Israel", "country_id": "IL", "country_code": "+972" },
            { "country_name": "Italy", "country_id": "IT", "country_code": "+39" },     
            { "country_name": "Japan", "country_id": "JP", "country_code": "+81" },     
            { "country_name": "Kenya", "country_id": "KE", "country_code": "+254" },
            { "country_name": "Kuwait", "country_id": "KW", "country_code": "+965" },       
            { "country_name": "Malaysia", "country_id": "MY", "country_code": "+60" },
            { "country_name": "Mexico", "country_id": "MX", "country_code": "+52" },
            { "country_name": "Nepal", "country_id": "NP", "country_code": "+977" },
            { "country_name": "Netherlands", "country_id": "NL", "country_code": "+31" },
            { "country_name": "New Zealand", "country_id": "NZ", "country_code": "+64" },
            { "country_name": "Nigeria", "country_id": "NG", "country_code": "+234" },
            { "country_name": "Norway", "country_id": "NO", "country_code": "+47" },        
            { "country_name": "Pakistan", "country_id": "PK", "country_code": "+92" },
            { "country_name": "Philippines", "country_id": "PH", "country_code": "+63" },
            { "country_name": "Poland", "country_id": "PL", "country_code": "+48" },
            { "country_name": "Portugal", "country_id": "PT", "country_code": "+351" },     
            { "country_name": "Qatar", "country_id": "QA", "country_code": "+974" },        
            { "country_name": "Romania", "country_id": "RO", "country_code": "+40" },
            { "country_name": "Russia", "country_id": "RU", "country_code": "+7" },     
            { "country_name": "Saudi Arabia", "country_id": "SA", "country_code": "+966" },
            { "country_name": "Singapore", "country_id": "SG", "country_code": "+65" },
            { "country_name": "South Africa", "country_id": "ZA", "country_code": "+27" },
            { "country_name": "South Korea", "country_id": "KR", "country_code": "+82" },
            { "country_name": "Spain", "country_id": "ES", "country_code": "+34" },
            { "country_name": "Sri Lanka", "country_id": "LK", "country_code": "+94" },
            { "country_name": "Sweden", "country_id": "SE", "country_code": "+46" },
            { "country_name": "Switzerland", "country_id": "CH", "country_code": "+41" },       
            { "country_name": "Thailand", "country_id": "TH", "country_code": "+66" },
            { "country_name": "Turkey", "country_id": "TR", "country_code": "+90" },        
            { "country_name": "Ukraine", "country_id": "UA", "country_code": "+380" },
            { "country_name": "United Arab Emirates", "country_id": "AE", "country_code": "+971" },
            { "country_name": "United Kingdom", "country_id": "GB", "country_code": "+44" },
            { "country_name": "United States", "country_id": "US", "country_code": "+1" },      
            { "country_name": "Vietnam", "country_id": "VN", "country_code": "+84" },
            { "country_name": "Zimbabwe", "country_id": "ZW", "country_code": "+263" }
        ]';

    }
}?>
