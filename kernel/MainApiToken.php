<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

/**
 * POE which implements API tokens functiuonality.
 * 
 * @since 1.9.11
 * 
 * @final
 */
final class MainApiToken extends MainCluster
{

    /**
     * @since 1.9.16
     */
    protected function init(): self
    {

        $this
            ->apiTokenGet()
            ->apiTokenRemove();
        
        return $this;

    }

    /**
     * Get API token.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function apiTokenGet() : self
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

        return $this;

    }

    /**
     * Remove exist token.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function apiTokenRemove() : self
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

        return $this;

    }

}
