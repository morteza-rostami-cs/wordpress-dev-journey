Perfect 👌 — you’re thinking exactly like a **developer**, not just a user.

Let’s now turn **Milestone 1** into a _developer-focused exploration plan_ — a full list of **atomic learning tasks** that give you deep knowledge of how WordPress works **out of the box**, preparing you to later **extend it with code** (themes, plugins, CPTs, etc).

---

# 🧱 Milestone 1 – Explore Default WordPress (Developer-Oriented)

**Goal:**
Explore every _admin feature and default content type_ in WordPress to understand how you can later interact with it programmatically.

---

## 🧩 SECTION 1: Core Content System (Posts, Pages, Categories, Tags)

### 🔹 Tasks

1. **Posts vs Pages**

   - Create a few sample posts and pages.
   - Observe differences: posts appear in the blog index; pages are standalone.
   - View database (optional): note how both are stored in `wp_posts` with different `post_type`.

2. **Categories & Tags**

   - Create new categories and tags.
   - Assign posts to them.
   - Visit `/category/slug` and `/tag/slug` on the frontend to see how archive templates are used.
   - Observe database tables: `wp_terms`, `wp_term_taxonomy`, and `wp_term_relationships`.

3. **Featured Images**

   - Add featured images to posts and pages.
   - Inspect HTML and note where they appear.
   - Find the function call in the theme: `the_post_thumbnail()`.

4. **Post Metadata**

   - Explore “Custom Fields” (enable via Screen Options).
   - Add a few key-value pairs and confirm they’re saved in `wp_postmeta`.
   - Note: This will later help when you add custom data fields in plugins or custom post types.

---

## 🧭 SECTION 2: Admin Interface & Menus

### 🔹 Tasks

5. **Dashboard Overview**

   - Understand the default widgets (At a Glance, Quick Draft, Activity).
   - Later you can hook into this with `wp_add_dashboard_widget()`.

6. **Posts, Media, Pages, Comments**

   - Explore each menu section; note how columns and filters work.
   - Learn that each section corresponds to a custom post type (e.g., Comments → `wp_comments`).
   - You can later add custom post types and custom columns.

7. **Appearance → Menus**

   - Create a custom menu.
   - Assign it to a location.
   - Inspect theme template for `wp_nav_menu()` usage.
   - Note how menus are stored in `wp_terms`.

8. **Appearance → Widgets**

   - Check which widget areas (sidebars) exist.
   - Add/remove widgets and inspect front-end changes.
   - Learn the concept of `register_sidebar()` and `dynamic_sidebar()`.

9. **Appearance → Customize**

   - Open the Customizer (if supported by the theme).
   - Change site title, logo, colors, etc.
   - Understand that developers can later add controls using `customize_register` hook.

10. **Settings Menus**

    - Explore Reading → “Your homepage displays” (posts page vs static page).
    - Understand how this connects to `front-page.php` and `home.php` in the theme hierarchy.
    - Explore Permalinks → how URL structure affects routing.

---

## ⚙️ SECTION 3: Theme & Template Hierarchy

### 🔹 Tasks

11. **Template Hierarchy Mapping**

    - Visit:

      - `/` (index)
      - `/sample-post/` (single post)
      - `/sample-page/` (page)
      - `/category/...` (archive)
      - `/tag/...`
      - `/404`

    - Use Query Monitor plugin (optional) to see which template is loaded.
    - Document how WordPress picks template files (index.php → fallback chain).

12. **The Loop**

    - Edit a theme file (temporarily) and inspect where `have_posts()` and `the_post()` appear.
    - Understand how the loop iterates through query results.
    - Learn difference between **main query** vs **custom query** (`WP_Query`).

13. **Theme Functions**

    - Open `functions.php` of the theme.
    - Note what’s defined (menus, thumbnails, widgets).
    - Learn that this is where developers hook and enqueue scripts/styles.

---

## 🔐 SECTION 4: Users, Roles, and Capabilities

### 🔹 Tasks

14. **Explore User Roles**

    - Create users with roles: Administrator, Editor, Author, Contributor, Subscriber.
    - Test what each can access in `/wp-admin`.
    - Note: These roles are defined in `wp_roles` and can be customized with code (`add_role()`).

15. **User Meta**

    - Edit your user profile and note fields like nickname, website, biographical info.
    - Add a custom field using a plugin or code later (future milestone).
    - Understand that these values are stored in `wp_usermeta`.

---

## 📦 SECTION 5: Plugins & Extensibility

### 🔹 Tasks

16. **Installed Plugins**

    - Review built-in ones (Akismet, Hello Dolly).
    - Activate/deactivate each.
    - Note plugin folder structure (`wp-content/plugins/hello.php`).
    - Check how they hook into WordPress (e.g., `add_action`, `add_filter`).

17. **Plugin Settings Pages**

    - Observe plugins that add their own admin menus (like Akismet).
    - Learn concept: `add_menu_page()` and `add_submenu_page()` — you’ll use these to make custom admin sections later.

---

## 🧱 SECTION 6: Content Storage & Database

### 🔹 Tasks

18. **Database Tables Overview**

    - Explore tables in phpMyAdmin:

      - `wp_posts`, `wp_postmeta`, `wp_terms`, `wp_users`, `wp_comments`, `wp_options`.

    - Note how posts, pages, menus, and widgets all map back here.

19. **Options Table**

    - Explore `wp_options`.
    - Change a setting (e.g., site title) and watch it update here.
    - Understand that you can later use `get_option()` and `update_option()` in PHP.

---

## 🧠 SECTION 7: Developer Mindset

### 🔹 Tasks

20. **Document Each Concept**

    - For each major concept (Posts, Menus, Widgets, Custom Fields, Template Hierarchy), create a section in a `MILESTONE_1.md` file describing:

      - What it does
      - Where it lives (folder, database table)
      - Which function controls it (e.g., `register_post_type`, `register_nav_menu`)
      - Screenshot or sample admin page

21. **Summarize Developer Entry Points**

    - By the end, list where a developer can extend WordPress:

      - Hooks (actions, filters)
      - Functions.php (theme)
      - Plugin main file (custom features)
      - Template hierarchy overrides

---

✅ **End Result:**
By completing all 21 atomic tasks, you’ll know **exactly how every piece of WordPress’s admin and frontend is connected** — from database to theme templates.
That’s the foundation every professional WordPress developer builds on before writing their first plugin or custom theme.

---

Would you like me to turn this list into a **checklist GitHub Issue template** (so you can paste it directly into GitHub and tick items off as you complete them)?
