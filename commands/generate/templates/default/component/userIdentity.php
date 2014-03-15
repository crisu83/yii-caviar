<?php
/**
 * @var string $namespace
 * @var string $className
 * @var string $baseClass
 */

return <<<EOD
<?php

namespace $namespace;

/**
 * UserIdentity represents the data needed to identity a user.
 */
class $className extends $baseClass
{
    /**
     * Authenticates a user.
     *
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        \$users = array(
            // username => password
            'demo' => 'demo',
            'admin' => 'admin',
        );
        if (!isset(\$users[\$this->username])) {
            \$this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif (\$users[\$this->username] !== \$this->password) {
            \$this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            \$this->errorCode = self::ERROR_NONE;
        }
        return !\$this->errorCode;
    }
}
EOD;
