<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 18:37
 */

namespace DG\SymfonyCert\Service\Security\UserProvider;


use DG\SymfonyCert\Entity\ApiUser;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiUserProvider implements UserProviderInterface
{
    const DIC_NAME = 'security.api_user_provider';

    /**
     * @var ApiUser[]
     */
    private $users;

    public function addUser(array $data)
    {
        $user = new ApiUser($data['name'], $data['key'], $data['roles']);
        $this->users[$user->getUsername()] = $user;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if (!isset($this->users[$username])) {
            $exception =  new UsernameNotFoundException(sprintf('User "%s" does not exist', $username));
            $exception->setUsername($username);
            throw $exception;
        }

        return $this->users[$username];
    }

    /**
     * @param $key
     *
     * @throws BadCredentialsException if the user is not found
     * @return ApiUser
     */
    public function loadUserByKey($key)
    {
        foreach ($this->users as &$user) {
            if ($user->getApiKey() === $key) {
                return $user;
            }
        }

        throw new BadCredentialsException();
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!($user instanceof ApiUser)) {
            throw new UnsupportedUserException(sprintf('"%s" instance is not supported', get_class($user)));
        }

        return $this->users[$user->getUsername()];
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class == 'DG\SymfonyCert\Entity\ApiUser';
    }

} 