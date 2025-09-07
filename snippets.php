<?php

/**
 * Put ALL your tiny tweaks here, but inside an OOP shell.
 * Define class Snippets with a register() method and add hooks there.
 * Comments must be in English for consistency.
 */

namespace Embo\Snippets;

class Snippets {

    /**
     * Register all your hooks/filters here.
     * Paste your add_action/add_filter lines inside this method.
     */

    public function register(): void {
        // CODES
        // ===== hide bbPress revision line (comment in if needed) =====
        add_filter( 'bbp_get_reply_revision_log', '__return_empty_string', 999 );
        add_filter( 'bbp_get_topic_revision_log', '__return_empty_string', 999 );
}