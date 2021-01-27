<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Traits\Randomizer;
use EPStatistics\Exceptions\TokensException;
use wpdb;

class Tokens {

    use Randomizer;

    protected $wpdb;
    protected $dbname;
    protected $table;

    public function __construct(wpdb $wpdb, string $dbname = '')
    {
        
        $this->wpdb = $wpdb;

        if (empty($dbname)) $this->dbname = DB_NAME;
        else $this->dbname = $dbname;

        $this->table = 'epstatistics_api_tokens';

        $this->createTable();

    }

    /**
     * Create a tokens table.
     * 
     * @return void
     * 
     * @throws TokensException
     */
    public function createTable() : void
    {

        if ($this->wpdb->query(
            "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.$this->table."` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT NOT NULL,
                `token` CHAR(128) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `token` (`token`)
            )
            COLLATE='utf8mb4_unicode_ci'
            AUTO_INCREMENT=0"
        ) === false) throw new TokensException(
            'Creating table faulire.',
            -40
        );

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
     * @throws TokensException
     */
    public function tokenGet(int $user_id) : string
    {

        $result = '';

        if ($user_id < 1) throw new TokensException(
            'Invalid user ID.',
            -41
        );

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM ".$this->dbname.".".$this->wpdb->prefix.$this->table." AS t
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
     * @throws TokensException
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
     * @throws TokenException
     */
    public function tokenGetGenerate(int $user_id) : string
    {

        if ($user_id < 1) throw new TokensException(
            'Invalid user ID.',
            -41
        );

        $token = $this->tokenGet($user_id);

        if (empty($token)) {

            do {

                $token = $this->generateRandomString(128);
    
            } while (!$this->tokenCheckUnique($token));
    
            if (!$this->tokenInsert($user_id, $token)) throw new TokensException(
                'Inserting token into DB failure.',
                -44
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
     * @throws TokensException
     */
    public function tokenDeleteByUser(int $user_id) : bool
    {

        if ($user_id < 1) throw new TokensException(
            'Invalid user ID.',
            -41
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
     * @throws TokensException
     */
    protected function tokenInsert(int $user_id, string $token) : bool
    {

        if ($user_id < 1) throw new TokensException(
            'Invalid user ID.',
            -41
        );

        if (empty($token)) throw new TokensException(
            'Token cannot be empty.',
            -43
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
     */
    protected function tokenSelect(string $token) : array
    {

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM ".$this->dbname.".".$this->wpdb->prefix.$this->table." AS t
                    WHERE t.token = %s",
                $token
            ),
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new TokensException(
            'Token selecting in DB failure.',
            -45
        );

    }

}
