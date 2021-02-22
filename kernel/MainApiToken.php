<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

class MainApiToken extends MainCluster
{

    /**
     * Get API token.
     * 
     * @return void
     */
    public function apiTokenGet() : void
    {

        add_action('wp_loaded', function() {

            $user_id = get_current_user_id();

            if ($user_id !== 0) {

                $tokens = new Tokens($this->wpdb);

                if ($tokens->userGetByToken(
                        (string)$_COOKIE['eps_api_token']
                    ) === 0) {

                    setcookie(
                        'eps_api_token',
                        $tokens->tokenGetGenerate($user_id),
                        0,
                        '/'
                    );

                }

            }

        });

    }

    /**
     * Remove exist token.
     * 
     * @return void
     */
    public function apiTokenRemove() : void
    {

        add_action('clear_auth_cookie', function() {

            $user_id = get_current_user_id();

            $tokens = new Tokens($this->wpdb);

            if ($user_id !== 0) $tokens->tokenDeleteByUser($user_id);

            if (!empty($_COOKIE['eps_api_token'])) {

                $tokens->tokenDelete((string)$_COOKIE['eps_api_token']);

                setcookie('eps_api_token', '', 0, '/');

            }

        });

    }

}
