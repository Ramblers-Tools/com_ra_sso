# RA SSO Login

Joomla OAuth 2.0 / OpenID Connect (OIDC) Single Sign-On plugin used to provide SSO login
for Ramblers Tools Joomla sites. Originally based on MiniOrange's free
`miniorange-joomla-oauth-client-free-plugin`, now maintained as a standalone,
Ramblers-branded product.

## Features
- OAuth 2.0 and OIDC login against a configured identity provider (e.g. Authentik, Azure AD,
  Keycloak, AWS Cognito).
- Authorization Code, Implicit, Refresh Token, Hybrid, and PKCE grant types.
- Attribute/role mapping from the IdP onto Joomla user profiles.
- Admin SSO login support alongside frontend login.

## Installation
1. Log in to the Joomla Administrator panel.
2. Navigate to **Extensions → Manage → Install**.
3. Upload the installer zip (from a [GitHub Release](https://github.com/Ramblers-Tools/ra-sso/releases)
   or built locally via `build-package.sh`).
4. Configure the OAuth/OIDC app under **Components → RA SSO Login**, using the authorize/token/
   userinfo/end-session endpoint URLs from your identity provider's discovery document (there is
   no auto-discovery — copy the individual endpoint URLs, not the discovery URL itself).
5. Use the **Test Configuration** button to verify the login flow before relying on it in production.

## Building a release
`build-package.sh <version> <output-zip-path>` assembles the installer package from the
extension folders in this repo. CI (`.github/workflows/beta.yml` / `release.yml`) runs this
automatically on pushes to `beta` and on version tags.

## License
GNU GPL v3 — see `LICENSE.txt`. Based on the original MiniOrange OAuth Client
(Copyright (C) 2015 miniOrange), modified and rebranded by East Cheshire Ramblers.
