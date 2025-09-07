# embo-simple-snippets

A collection of small WordPress tweaks implemented in the `Snippets` class.

## Getting Started

- Read this README to understand the available hooks.
- Upload the plugin folder to your `wp-content/plugins` directory and activate it in WordPress.

## Structure

- `embo-simple-snippets.php` – loads the class and runs `register()`.
- `snippets.php` – `Snippets` class with all hooks.
- `assets/js/remove-h2-slash.js` – cleans stray slash wrappers from `<h2>` headings on the client side.

## Contents of snippets.php

- `wp_head` – prepends a decorative slash to `<h2>` headings except for specific IDs.
- `wp_enqueue_scripts` – enqueues a script that removes legacy slash wrappers from headings.
- `wp_head` – injects CSS styling for the comment form on pages and posts.
- `comment_form_fields` – reorders fields, removes the URL field and localizes labels.
- `comment_form_defaults` – removes default notes and adds a placeholder to the textarea.
- `comment_form_before` / `comment_form` – buffers and replaces the heading and submit button labels for the comment form.
- `wp_footer` – adds `multipart/form-data` attributes and scripts for avatar/image uploads.
- `wp_footer` (page `vidguki`) – changes the “Leave a Review” button text and color.
- `comment_form_after_fields` / `comment_form_logged_in_after` – outputs avatar and image upload inputs.
- `comment_post` – processes uploaded files and stores them as attachments.
- `get_avatar` – shows the uploaded avatar in comments.
- `comment_text` – displays the uploaded image underneath the comment text.
