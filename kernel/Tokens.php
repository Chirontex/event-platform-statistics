<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Traits\Randomizer;
use EPStatistics\Exceptions\TokensException;
use wpdb;

class Tokens extends Storage {

    use Randomizer;

    public function __construct(wpdb $wpdb)
    {

        $this->table = 'epstatistics_api_tokens';

        $this->fields = [
            'user_id' => 'BIGINT NOT NULL',
            'token' => 'CHAR(128) NOT NULL'
        ];

        $this->indexes = [
            'token' => 'UNIQUE INDEX'
        ];

        parent::__construct($wpdb);

    }

    /**
     * Return token by user ID.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @return string
     * If token not exist, the method will return an empty string.
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    public function tokenGet(int $user_id) : string
    {

        $result = '';

        if ($user_id < 1) throw new TokensException(
            TokensException::INVALID_USER_ID_MESSAGE,
            TokensException::INVALID_USER_ID_CODE
        );

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.$this->table."` AS t
                    WHERE t.user_id = %d",
                $user_id
            ),
            ARRAY_A
        );

        if (is_array($select) &&
            !empty($select)) $result = $select[0]['token'];

        return $result;

    }

    /**
     * Check token for uniqueness.
     * 
     * @param string $token
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    public function tokenCheckUnique(string $token) : bool
    {

        return empty($this->tokenSelect($token));

    }

    /**
     * Generate and/or return user's token.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @return string
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    public function tokenGetGenerate(int $user_id) : string
    {

        if ($user_id < 1) throw new TokensException(
            TokensException::INVALID_USER_ID_MESSAGE,
            TokensException::INVALID_USER_ID_CODE
        );

        $token = $this->tokenGet($user_id);

        if (empty($token)) {

            do {

                $token = $this->generateRandomString(128);
    
            } while (!$this->tokenCheckUnique($token));
    
            if (!$this->tokenInsert($user_id, $token)) throw new TokensException(
                TokensException::INSERTING_TOKEN_FAILURE_MESSAGE,
                TokensException::INSERTING_TOKEN_FAILURE_CODE
            );

        }

        return $token;

    }

    /**
     * Delete an existing token.
     * 
     * @param string $token
     * 
     * @return bool
     */
    public function tokenDelete(string $token) : bool
    {

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table,
            ['token' => $token],
            ['%s']
        );

        return !empty($delete);

    }

    /**
     * Delete tokens of certain user.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    public function tokenDeleteByUser(int $user_id) : bool
    {

        if ($user_id < 1) throw new TokensException(
            TokensException::INVALID_USER_ID_MESSAGE,
            TokensException::INVALID_USER_ID_CODE
        );

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table,
            ['user_id' => $user_id],
            ['%d']
        );

        return !empty($delete);

    }
    
    /**
     * Return user ID by token.
     * 
     * @param string $token
     * 
     * @return int
     */
    public function userGetByToken(string $token) : int
    {

        $select = $this->tokenSelect($token);

        if (empty($select)) return 0;
        else return $select[0]['user_id'];

    }

    /**
     * Inserting token into DB table.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @param string $token
     * Token cannot be empty.
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    protected function tokenInsert(int $user_id, string $token) : bool
    {

        if ($user_id < 1) throw new TokensException(
            TokensException::INVALID_USER_ID_MESSAGE,
            TokensException::INVALID_USER_ID_CODE
        );

        if (empty($token)) throw new TokensException(
            TokensException::TOKEN_CANNOT_BE_EMPTY_MESSAGE,
            TokensException::TOKEN_CANNOT_BE_EMPTY_CODE
        );

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'user_id' => $user_id,
                'token' => $token
            ],
            ['%d', '%s']
        ) === false) return false;
        else return true;

    }

    /**
     * Selecting token in DB.
     * 
     * @param string $token
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\TokensException
     */
    protected function tokenSelect(string $token) : array
    {

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.$this->table."` AS t
                    WHERE t.token = %s",
                $token
            ),
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new TokensException(
            TokensException::SELECTING_TOKEN_FAILURE_MESSAGE,
            TokensException::SELECTING_TOKEN_FAILURE_CODE
        );

    }

}
