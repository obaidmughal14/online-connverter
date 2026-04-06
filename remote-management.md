# Remote Theme Management (Git Updater)

Remote management works with **MainWP**, **ManageWP**, **InfiniteWP**, **iThemes Sync**, and similar tools when they are configured with this site’s URL and credentials, alongside Git Updater’s REST API.

The [Git Remote Updater](https://git-updater.com/git-remote-updater/) plugin was created to simplify remote management of Git Updater–supported plugins and themes. You need the **Site URL** and **REST API key** for Git Remote Updater settings (or other tools: MainWP, ManageWP, InfiniteWP, iThemes Sync, custom webhooks).

> See the [Git Updater Knowledge Base](https://git-updater.com/knowledge-base/) for the full list of attributes and advanced usage.

---

## Site & API Key

| Setting | Value |
|--------|--------|
| **Site URL** | https://convert.devigontech.com |
| **REST API key** | `55feeb189dc3d5ef1b8ba55944c13f59` |

Use the **Site URL** and **REST API key** in your remote management tool (for example, in the settings for Git Remote Updater or a custom webhook integration).

> **Security:** Anyone with this API key can trigger theme/plugin updates on the site. Do not expose it in client-side code. If this repository is ever made public, move the key into a secure secrets manager and remove it from version control.

---

## REST API Endpoints

Base path: `https://convert.devigontech.com/wp-json/git-updater/v1/`  
All endpoints require the query parameter: `key=55feeb189dc3d5ef1b8ba55944c13f59`

### Update (webhook / remote update)

**Endpoint base:**  
`https://convert.devigontech.com/wp-json/git-updater/v1/update/?key=55feeb189dc3d5ef1b8ba55944c13f59`

Append standard Git Updater query parameters such as:

- `theme=` – the theme slug to update
- `branch=`, `tag=`, `committish=` – which version/branch to deploy
- `override=` – whether to override the stored branch/tag

Example (curl, updating this theme by slug — use the **theme directory name** under `wp-content/themes/`, e.g. `toolverse`):

```bash
curl "https://convert.devigontech.com/wp-json/git-updater/v1/update/?key=55feeb189dc3d5ef1b8ba55944c13f59&theme=toolverse"
```

### Reset branch

Use if the theme is stuck on a deleted branch and Git Updater can’t connect.

**Endpoint base:**  
`https://convert.devigontech.com/wp-json/git-updater/v1/reset-branch/?key=55feeb189dc3d5ef1b8ba55944c13f59`

Example (reset this theme’s branch — ensure the `theme=` value matches the theme directory slug):

```bash
curl "https://convert.devigontech.com/wp-json/git-updater/v1/reset-branch/?key=55feeb189dc3d5ef1b8ba55944c13f59&theme=toolverse"
```

---

## References

- [Git Updater Knowledge Base](https://git-updater.com/knowledge-base/)
- [Required Headers](https://git-updater.com/knowledge-base/required-headers/)
- [Remote Management – REST API Endpoints](https://git-updater.com/knowledge-base/remote-management-restful-endpoints/)
- [Versions & Branches](https://git-updater.com/knowledge-base/versions-branches/)
- [Git Remote Updater plugin](https://git-updater.com/git-remote-updater/)
