Perfect 👏 — that’s exactly how a real dev workflow should look: **atomic, code-driven, and progressive**.
We’ll structure this like a _WordPress developer bootcamp_, where each task gives you a small, concrete win.

---

# 🧩 Project: WP Movie Library

**Goal:** Build a custom plugin that registers movies, adds genres, custom fields, frontend UI, admin settings, AJAX, and REST integration.

---

## 🔰 PHASE 1 — Plugin Setup & Custom Post Type

1. **Create plugin folder and main file**
2. **Add plugin header and basic activation/deactivation hooks**
3. **Register the “movie” custom post type**
4. **Add support for title, editor, thumbnail, excerpt, and comments**
5. **Test CPT in admin (add a few movies manually)**
6. **Register custom taxonomy “Genre” for movies**
7. **Add hierarchical = false (like tags) and show in admin filters**
8. **Test adding genres and assigning them to movies**

---

## 🧱 PHASE 2 — Custom Meta Fields (Movie Details)

9. **Add meta box: “Movie Details” (rating, release year)**
10. **Save meta fields when movie is saved**
11. **Display meta values in the movie edit screen**
12. **Show movie rating and year in the movie list table**
13. **Add sorting/filtering by rating in admin list**

---

## ⚙️ PHASE 3 — Admin Settings Page

14. **Create a “Movie Settings” admin menu under Settings**
15. **Register plugin options (e.g., default genre)**
16. **Build HTML form with WordPress Settings API**
17. **Save and retrieve settings with `get_option()`**
18. **Display current setting on plugin admin page**

---

## 🧩 PHASE 4 — Frontend Display (Template & Shortcode)

19. **Create `/templates/movies-list.php`**
20. **Write a custom WP_Query loop to show all movies**
21. **Create a `[movie_list]` shortcode**
22. **Add attributes like `[movie_list genre="action" limit="5"]`**
23. **Test shortcode inside a normal page**
24. **Style it lightly with CSS (optional)**

---

## 🎨 PHASE 5 — Widget & Sidebar Integration

25. **Create a custom widget: “Featured Movie”**
26. **Add widget settings (title + movie ID)**
27. **Render selected movie in sidebar**
28. **Use template part for consistency**

---

## ⚡ PHASE 6 — AJAX & REST API Integration

29. **Add AJAX handler for liking a movie**
30. **Enqueue JS, localize nonce + AJAX URL**
31. **Handle POST request and update meta “likes”**
32. **Show updated like count dynamically**
33. **Create REST endpoint `/wp-json/movies/v1/latest`**
34. **Return last 5 movies with genre + rating**

---

## 🧑‍💻 PHASE 7 — Roles, Capabilities, and Permissions

35. **Create “Movie Editor” role with limited capabilities**
36. **Assign custom capabilities to movie post type**
37. **Hide other post types for this role**
38. **Test by creating a user with this role**

---

## 🌍 PHASE 8 — Extras & Polish

39. **Add translation support (`__()` and `load_plugin_textdomain`)**
40. **Add plugin icon and readme.txt**
41. **Add uninstall hook to clean up options/meta**
42. **Refactor into clean folders (`includes/`, `templates/`, etc.)**
43. **Final test: create, display, like, and fetch movies**
44. **Push to GitHub (add `.gitignore`, README, screenshots)**

---

✅ **End Result:**
A fully working, production-grade plugin with:

- Admin interface
- Frontend template
- Database integration
- REST + AJAX features
- Clean, modular PHP structure

---

Would you like me to now give you the **code + instructions for Task 1** — “Create plugin folder and main file”?
That’s our first atomic step — we’ll go line by line from there.
