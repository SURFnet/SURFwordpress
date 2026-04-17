# WP SURF Theme

WP SURF Theme is an open source WordPress theme developed and maintained by SURF.  
It provides a structured foundation with custom post types, curated block patterns and a performance-focused architecture.

This theme is intended for SURF-related implementations and projects that require a controlled, extensible WordPress setup.

---

## Features

Out of the box, the theme includes:

- Custom post types and taxonomies tailored to SURF use cases
- Block editor (Gutenberg) support
- Reusable block patterns
- Custom page templates
- Accessibility-friendly markup
- Performance-focused configuration
- Structured architecture with namespaced PHP code

---

## Requirements

- WordPress 6.x or higher
- PHP 8.2 or higher
- A modern browser

---

## Installation

### Via WordPress admin

1. Download the latest release as a `.zip` file from the [GitHub Releases](https://github.com/SURFnet/SURFwordpress/releases) page
2. Go to **Appearance → Themes**
3. Click **Add New → Upload Theme**
4. Upload the zip file and activate

### Via FTP or local development

1. Extract the theme folder
2. Place it in `wp-content/themes/`
3. Activate the theme via **Appearance → Themes**

---

## Updates

This theme includes a built-in update checker that connects to GitHub Releases.  
When a new release is published, WordPress will notify you in the admin dashboard — just like a theme from the WordPress.org repository.

No additional plugin is required for updates to work.

### GitHub API rate limiting

The update checker uses the GitHub API to check for new releases. GitHub allows:

- **60 requests per hour** for unauthenticated requests (per server IP)
- **5,000 requests per hour** when authenticating with a Personal Access Token (PAT)

For most production sites, 60 requests per hour is sufficient — the check only runs when the WordPress admin dashboard is visited.

For **development and staging environments**, where the dashboard is visited more frequently and updates are tested regularly, it is recommended to configure a PAT.

### Configuring a Personal Access Token (optional)

1. Go to **GitHub → Settings → Developer settings → Personal access tokens → Fine-grained tokens**
2. Set the resource owner to the `SURFnet` organisation
3. Limit repository access to this repository only
4. Under permissions, set **Contents → Read-only**
5. Generate the token and add it to your environment:
```env
GH_PAT=github_pat_xxxxxxxxxxxxxxxx
```

> **Note:** A PAT is tied to a GitHub user account, not an organisation. For long-term stability, consider generating the token from a dedicated machine user account rather than a personal account.

---

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `GH_PAT` | No | GitHub Personal Access Token. Raises the API rate limit from 60 to 5,000 requests per hour. Recommended for development and staging environments. |

---

## Getting Started

After activating the theme:

1. Configure **Settings → Reading**
2. Review available page templates
3. Explore the included block patterns

Depending on the project setup, additional configuration may be required.

---

## Architecture Notes

This theme includes custom post types and taxonomies.

It is not intended to be a generic, switchable theme.  
Switching to another theme may impact content presentation and functionality.

The theme follows a structured architecture with namespaced PHP classes and centralized hook registration. Business logic is intentionally separated from templates wherever possible.

---

## Customization

Customization options include:

- Site Editor (Global Styles)
- Block-level configuration
- Theme options and structured PHP extensions

---

## Accessibility

Accessibility is considered throughout the theme:

- Semantic HTML
- Keyboard navigability
- Accessible markup patterns
- Sensible default color contrast

Final accessibility compliance depends on content and implementation choices.

---

## Distribution

This theme is open source and licensed under GPLv2 or later.  
It is **not** distributed via the WordPress.org theme repository.

Releases are distributed via GitHub.  
Updates are managed through the built-in release workflow described above.

---

## Support

This theme is provided as open source software.

- Bug reports can be submitted via [GitHub Issues](https://github.com/SURFnet/SURFwordpress/issues)
- Security issues must be reported privately (see `SECURITY.md`)
- Feature requests may be considered
- No SLA or guaranteed support is provided

---

## License

WP SURF Theme is licensed under the GNU General Public License v2 or later.

You are free to:

- Use the theme
- Modify it
- Distribute it

Any derivative work must remain licensed under the GPL.
