<?php

/**
 * Put ALL your tiny tweaks here, but inside an OOP shell.
 * Define class Snippets with a register() method and add hooks there.
 * Comments must be in English for consistency.
 */

namespace Embo\Snippets;

use WP_Comment;

class Snippets {

    /**
     * IDs of H2 elements that should not receive the decorative slash.
     * Extend this list if more exclusions are required.
     *
     * @var string[]
     */
    private array $excluded_h2_ids = ['reply-title'];

    /**
     * Register all your hooks/filters here.
     * Paste your add_action/add_filter lines inside this method.
     */

    public function register(): void {
        // CODES
        // ===== Slash before all H2 headings =====
        // Add slash via CSS and strip old inline wrappers via JS.
        add_action('wp_head', [$this, 'add_heading_slash_style']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_remove_h2_slash_script']);

        // ===== UI styles for comment form (Pages and Posts) =====
        add_action('wp_head', function () {
            if (is_admin() || ! ( is_page() || is_single() )) return;
            ?>
            <style id="embo-comments-ui">
            /* Layout */
            .comment-respond,.comment-respond .comment-form{width:100%;max-width:none;}
            .comment-respond .comment-form{
                display:grid;
                grid-template-columns:repeat(12,1fr);
                gap:16px;
            }
            .comment-respond .logged-in-as{grid-row:1;grid-column:1/-1;margin:0;}
            .comment-respond .comment-form-author{grid-row:1;grid-column:1/7;margin:0;}
            .comment-respond .comment-form-email{grid-row:1;grid-column:7/13;margin:0;}
            .comment-respond .comment-form-comment,
            .comment-respond #comment-textarea{grid-row:2;grid-column:1/-1;margin:0;}
            .comment-respond .comment-form-embo-avatar{grid-row:3;grid-column:1/5;margin:8px 0 0 0;}
            .comment-respond .comment-form-embo-image{grid-row:3;grid-column:5/9;margin:8px 0 0 0;}
            .comment-respond p.form-submit{grid-row:3;grid-column:1/-1;margin:8px 0 0 0;justify-self:end;}
            .comment-respond .comment-form-embo-avatar ~ p.form-submit,
            .comment-respond .comment-form-embo-image  ~ p.form-submit{grid-column:9/13;}
            .comment-respond .comment-form-cookies-consent{grid-row:4;grid-column:1/-1;margin:0;}

            .comment-text{overflow:hidden;}

            /* Hide text labels — placeholders are used instead */
            .comment-respond .comment-form-author>label,
            .comment-respond .comment-form-email>label,
            .comment-respond .comment-form-comment>label{position:absolute;clip:rect(1px,1px,1px,1px);height:1px;width:1px;overflow:hidden;}

            /* Input fields as light gray capsules */
            .comment-respond input#author,
            .comment-respond input#email{
                width:100%;border:0;border-radius:24px;padding:12px 18px;
                background:#e3e3e3;color:#1a1a1a;box-shadow:inset 0 0 0 2px rgba(0,0,0,.05);
            }
            .comment-respond textarea#comment{
                width:100%;max-width:100% !important;border:0;border-radius:20px;padding:14px 18px;min-height:180px;
                background:#e3e3e3;color:#1a1a1a;box-shadow:inset 0 0 0 2px rgba(0,0,0,.05);
            }
            .comment-respond input#author:focus,
            .comment-respond input#email:focus,
            .comment-respond textarea#comment:focus{outline:none;box-shadow:0 0 0 3px rgba(33,77,4,.35);}
            .comment-respond ::placeholder{color:#6a6a6a;opacity:1;}

            /* File upload buttons */
            .comment-respond .comment-form-embo-avatar input[type=file],
            .comment-respond .comment-form-embo-image  input[type=file]{position:absolute;left:-9999px;}
            .comment-respond .comment-form-embo-avatar label,
            .comment-respond .comment-form-embo-image  label{
                display:inline-flex;align-items:center;gap:10px;
                padding:10px 14px 10px 12px;border-radius:9999px;
                cursor:pointer;user-select:none;line-height:1;white-space:nowrap;
            }
            .comment-respond .comment-form-embo-avatar label::after,
            .comment-respond .comment-form-embo-image  label::after{
                content:"";padding:5px 24px;flex:0 0 28px;height:28px;border-radius:9999px;display:inline-block;
                background-position:center;background-repeat:no-repeat;background-size:18px 18px;
            }
            /* Icon for avatar upload */
            .comment-respond .comment-form-embo-avatar label::after{
                background-image:url('<?php echo esc_url( plugins_url('assets/img/icons/icon-ava.svg', __FILE__) ); ?>');
            }
            /* Icon for comment image upload */
            .comment-respond .comment-form-embo-image label::after{
                background-image:url('<?php echo esc_url( plugins_url('assets/img/icons/icon-img.svg', __FILE__) ); ?>');
            }
            .comment-respond .comment-form-embo-avatar label::after,
            .comment-respond .comment-form-embo-image label::after{background-color:#454545;}
            .comment-respond .comment-form-embo-avatar.has-file label::after,
            .comment-respond .comment-form-embo-image.has-file label::after{background-color:#265207;}

            /* Responsive adjustments for label wrapping */
            @media (max-width:1020px) and (min-width:768px){
                .comment-respond .comment-form-embo-avatar label,
                .comment-respond .comment-form-embo-image  label{white-space:normal;}
            }

            /* Submit button */
            .comment-respond p.form-submit input[type=submit]{background:#2e5f0b;color:#fff;border:0;border-radius:9999px;padding:12px 28px;font-weight:600;cursor:pointer;}
            .comment-respond p.form-submit input[type=submit]:hover{background:#265207;}

            /* Mobile layout */
            @media (max-width:768px){
                .comment-respond .comment-form{grid-template-columns:1fr;}
                .comment-respond .comment-form-author,
                .comment-respond .comment-form-email,
                .comment-respond .comment-form-embo-avatar,
                .comment-respond .comment-form-embo-image,
                .comment-respond p.form-submit{grid-column:1;grid-row:auto;}
                .comment-respond p.form-submit{justify-self:stretch;}
                .comment-respond p.form-submit input[type=submit]{width:100%;}
            }
            </style>
            <?php
        });

        // ===== Comment form tweaks for Pages and Posts =====

        /**
         * Move name/email above textarea, remove 'url' field, localize labels/placeholders (Pages and Posts).
         */
        add_filter('comment_form_fields', function(array $fields) {
            if ( is_admin() || ! ( is_page() || is_single() ) ) return $fields;

            // Remove Website field
            unset($fields['url']);

            // Rebuild author/email with labels & placeholders
            $author_placeholder = esc_attr__( 'Ваше ім\'я', 'embo-simple-snippets' );
            $email_placeholder  = esc_attr__( 'Ваш email', 'embo-simple-snippets' );

            if ( isset($fields['author']) ) {
                $fields['author'] = sprintf(
                    '<p class="comment-form-author"><label for="author">%s%s</label><input id="author" name="author" type="text" value="%s" size="30" maxlength="245" required="required" placeholder="%s" /></p>',
                    esc_html__( 'Name', 'embo-simple-snippets' ),
                    ' <span class="required">*</span>',
                    esc_attr( wp_get_current_commenter()['comment_author'] ?? '' ),
                    $author_placeholder
                );
            }
            if ( isset($fields['email']) ) {
                $fields['email'] = sprintf(
                    '<p class="comment-form-email"><label for="email">%s%s</label><input id="email" name="email" type="email" value="%s" size="30" maxlength="100" aria-describedby="email-notes" required="required" placeholder="%s" /></p>',
                    esc_html__( 'Email', 'embo-simple-snippets' ),
                    ' <span class="required">*</span>',
                    esc_attr( wp_get_current_commenter()['comment_author_email'] ?? '' ),
                    $email_placeholder
                );
            }

            // Auto-check and hide cookie consent field
            if ( isset( $fields['cookies'] ) ) {
                $fields['cookies'] = str_replace(
                    '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox"',
                    '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" checked="checked"',
                    $fields['cookies']
                );
                $fields['cookies'] = str_replace(
                    '<p class="comment-form-cookies-consent">',
                    '<p class="comment-form-cookies-consent" style="display:none;">',
                    $fields['cookies']
                );
            }

            // Reorder: author, email, comment (others keep order if present)
            $order = array('author','email','comment');
            $ordered = array();
            foreach ($order as $k) {
                if (isset($fields[$k])) {
                    $ordered[$k] = $fields[$k];
                    unset($fields[$k]);
                }
            }
            return $ordered + $fields;
        });

        /**
         * Change default comment form labels, textarea placeholder (Pages and Posts).
         */
        add_filter('comment_form_defaults', function(array $dfl) {
            if ( is_admin() || ! ( is_page() || is_single() ) ) return $dfl;

            $dfl['comment_notes_before'] = '';
            $dfl['comment_notes_after']  = '';

            // Replace textarea with placeholder text
            $placeholder = esc_attr__( 'Напишіть свій відгук тут…', 'embo-simple-snippets' );
            $dfl['comment_field'] = sprintf(
                '<p class="comment-form-comment"><label for="comment">%s%s</label><textarea id="comment" name="comment" cols="45" rows="8" required="required" placeholder="%s"></textarea></p>',
                esc_html__( 'Comment', 'embo-simple-snippets' ),
                ' <span class="required">*</span>',
                $placeholder
            );
            return $dfl;
        });

        // ===== Replace comment form labels when theme overrides defaults =====
        add_action('comment_form_before', [$this, 'start_comment_form_buffer']);
        add_action('comment_form', [$this, 'replace_comment_form_labels'], 999);

        /**
         * Ensure comment form can upload files (multipart) — inject attribute via footer script (Pages only).
         */
        add_action('wp_footer', function () {
            if ( is_admin() || ! is_page() ) return;
            ?>
            <script>
            (function(){
            var f = document.getElementById('commentform');
            if (f) { f.setAttribute('enctype','multipart/form-data'); f.setAttribute('encoding','multipart/form-data'); }
            ['embo_comment_avatar','embo_comment_image'].forEach(function(id){
                var input = document.getElementById(id);
                if(!input) return;
                var wrap = input.parentNode;
                var toggle = function(){
                    if(input.files && input.files.length){ wrap.classList.add('has-file'); }
                    else { wrap.classList.remove('has-file'); }
                };
                input.addEventListener('change', toggle);
                toggle();
            });
            })();
            </script>
            <?php
        });

        add_action('wp_footer', function () {
            if ( ! is_page('vidguki') ) return;
            ?>
            <script>
            document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('a,button').forEach(function(el){if(el.textContent.trim()==='Залишити коментар'){el.textContent='Залишити відгук';el.style.color='#b5b5b5';}})});
            </script>
            <?php
        });

        /**
         * Add avatar & image upload fields right after visible fields (both for guests and logged-in, Pages only).
         */
        $__embo_render_upload_fields = function () {
            if ( is_admin() || ! is_page() ) return;
            $avatarLabel = esc_html__( 'Прикріпити аватарку', 'embo-simple-snippets' );
            $imageLabel  = esc_html__( 'Завантажити фото результату роботи', 'embo-simple-snippets' );
            echo '<p class="comment-form-embo-avatar"><label for="embo_comment_avatar">'.$avatarLabel.'</label> ';
            echo '<input id="embo_comment_avatar" name="embo_comment_avatar" type="file" accept="image/*" /></p>';
            echo '<p class="comment-form-embo-image"><label for="embo_comment_image">'.$imageLabel.'</label> ';
            echo '<input id="embo_comment_image" name="embo_comment_image" type="file" accept="image/*" /></p>';
        };
        add_action('comment_form_after_fields', $__embo_render_upload_fields);
        add_action('comment_form_logged_in_after', $__embo_render_upload_fields);

        /**
         * Handle file uploads on comment save: create attachments, resize, save comment meta (Pages only).
         */
        add_action('comment_post', function (int $comment_id, $approved) {
            // Limit to Pages only
            $c = get_comment( $comment_id );
            if ( ! $c ) return;
            $post_type = get_post_type( $c->comment_post_ID );
            if ( $post_type !== 'page' ) return;

            // Allowed MIME types
            $mimes = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png'          => 'image/png',
                'gif'          => 'image/gif',
                'webp'         => 'image/webp',
            );

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            // Helper: upload, optionally resize, and attach; returns attachment ID or 0
            $handle_upload = function(string $field, int $max_w, int $max_h, bool $crop) use ($mimes) : int {
                if ( empty($_FILES[$field]) || !empty($_FILES[$field]['error']) || empty($_FILES[$field]['name']) ) return 0;

                // Basic size guard: 8MB hard cap (tweak if needed)
                if ( ! empty($_FILES[$field]['size']) && $_FILES[$field]['size'] > 8 * 1024 * 1024 ) return 0;

                add_filter('upload_mimes', function($existing) use ($mimes){ return $mimes + $existing; });
                $overrides = array( 'test_form' => false, 'mimes' => $mimes );
                $file = wp_handle_upload( $_FILES[$field], $overrides );
                if ( empty($file) || ! empty($file['error']) ) return 0;

                $file_path = $file['file'];
                $filetype  = wp_check_filetype( $file_path );

                // Resize original file
                $editor = wp_get_image_editor( $file_path );
                if ( ! is_wp_error( $editor ) ) {
                    $editor->resize( $max_w, $max_h, $crop );
                    $editor->save( $file_path );
                }

                // Check for an existing attachment with the same hash to avoid duplicates
                $hash = md5_file( $file_path );
                $existing = get_posts( array(
                    'post_type'      => 'attachment',
                    'post_status'    => 'inherit',
                    'posts_per_page' => 1,
                    'meta_key'       => 'embo_file_hash',
                    'meta_value'     => $hash,
                    'fields'         => 'ids',
                ) );
                if ( $existing ) {
                    @unlink( $file_path );
                    return (int) $existing[0];
                }

                // Create attachment
                $attachment_id = wp_insert_attachment( array(
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => sanitize_file_name( basename( $file_path ) ),
                    'post_status'    => 'inherit',
                    'post_content'   => '',
                ), $file_path );

                if ( is_wp_error( $attachment_id ) ) return 0;

                $attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
                wp_update_attachment_metadata( $attachment_id, $attach_data );
                update_post_meta( $attachment_id, 'embo_file_hash', $hash );

                return (int) $attachment_id;
            };

            // Avatar: hard cap to 256×256 (crop=true)
            $avatar_id = $handle_upload( 'embo_comment_avatar', 256, 256, true );
            if ( $avatar_id ) {
                add_comment_meta( $comment_id, 'embo_comment_avatar_id', $avatar_id, true );
            }

            // Comment image: max 1000×1000 (no crop)
            $image_id = $handle_upload( 'embo_comment_image', 1000, 1000, false );
            if ( $image_id ) {
                add_comment_meta( $comment_id, 'embo_comment_image_id', $image_id, true );
            }
        }, 10, 2);

        /**
         * Replace avatar in comments with uploaded one (if present; Pages only).
         */
        add_filter('get_avatar', function($avatar, $id_or_email, $size, $default, $alt, $args) {
            // Only intercept for comment objects / comment IDs
            $comment_id = 0;
            if ( is_object($id_or_email) && isset($id_or_email->comment_ID) ) {
                $comment_id = (int) $id_or_email->comment_ID;
            } elseif ( is_numeric($id_or_email) ) {
                $comment_id = (int) $id_or_email;
            }

            if ( ! $comment_id ) return $avatar;

            $c = get_comment( $comment_id );
            if ( ! $c || get_post_type($c->comment_post_ID) !== 'page' ) return $avatar;

            $aid = (int) get_comment_meta( $comment_id, 'embo_comment_avatar_id', true );
            if ( $aid ) {
                // Respect requested size
                $img = wp_get_attachment_image( $aid, array( (int)$size, (int)$size ), false, array(
                    'class' => ( isset($args['class']) ? $args['class'].' ' : '' ) . 'avatar avatar-embo',
                    'alt'   => $alt ?: esc_attr__( 'Comment avatar', 'embo-simple-snippets' ),
                    'loading' => 'lazy',
                ));
                if ( $img ) return $img;
            }
            return $avatar;
        }, 10, 6);

        /**
         * Display uploaded image centered below the comment text (Pages only).
         */
        add_filter('comment_text', function($text, $comment) {
            if ( ! $comment instanceof WP_Comment ) return $text;
            if ( get_post_type($comment->comment_post_ID) !== 'page' ) return $text;

            $aid = (int) get_comment_meta( $comment->comment_ID, 'embo_comment_image_id', true );
            if ( $aid ) {
                $img = wp_get_attachment_image( $aid, [500, 500], false, array(
                    'class'   => 'comment-image-embo',
                    'loading' => 'lazy',
                    'style'   => 'width:100%;max-width:500px;height:auto;display:block;margin:12px auto 0;',
                ));
                if ( $img ) {
                    $text .= '<div class="comment-image-wrapper" style="text-align:center;">'.$img.'</div>';
                }
            }
            return $text;
        }, 10, 2);
    }

    /**
     * Begin buffering of the comment form to allow later modifications.
     */
    public function start_comment_form_buffer(): void {
        if (is_admin() || ! ( is_page() || is_single() )) {
            return;
        }
        ob_start();
    }

    /**
     * Replace comment form labels captured in the buffer and output the result.
     */
    public function replace_comment_form_labels(): void {
        if (is_admin() || ! ( is_page() || is_single() )) {
            return;
        }
        $html = ob_get_clean();
        if ($html === false) {
            return;
        }
        // Replace submit button label first to avoid altering the heading.
        $html = str_replace('value="Залишити коментар"', 'value="Опублікувати"', $html);
        $html = str_replace('value="Залишити Коментар"', 'value="Опублікувати"', $html);
        // Replace heading text regardless of casing.
        $html = str_replace(['>Залишити коментар<', '>Залишити Коментар<'], '>Залишити відгук<', $html);

        // Localize placeholder text and logout link.
        $html = str_replace(
            [
                'placeholder="Your name"',
                'placeholder="Your email"',
                'Log out »'
            ],
            [
                'placeholder="Ваше ім\'я"',
                'placeholder="Ваш email"',
                'Вийти'
            ],
            $html
        );
        echo $html;
    }

    /**
     * Output CSS rule that prepends a green slash to H2 headings.
     * Excludes IDs listed in $excluded_h2_ids.
     */
    public function add_heading_slash_style(): void {
        if (is_admin()) {
            return;
        }
        $selector = 'h2';
        foreach ($this->excluded_h2_ids as $id) {
            $selector .= ':not(#' . esc_attr($id) . ')';
        }
        echo '<style id="embo-h2-slash">' . $selector . '::before{content:"\\002F\\00A0";color:#305600;}</style>';
    }

    /**
     * Enqueue client-side script to remove legacy slash spans from H2 headings.
     */
    public function enqueue_remove_h2_slash_script(): void {
        if (is_admin()) {
            return;
        }
        wp_enqueue_script(
            'embo-remove-h2-slash',
            plugins_url('assets/js/remove-h2-slash.js', __FILE__),
            [],
            null,
            true
        );
    }
}
