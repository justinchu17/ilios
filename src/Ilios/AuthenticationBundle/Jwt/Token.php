<?php

namespace Ilios\AuthenticationBundle\Jwt;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\HttpFoundation\Request;
use JWT as TokenLib;

use Ilios\CoreBundle\Entity\UserInterface;

class Token extends AbstractToken
{
    /**
     * The key we use to sign and validate
     * @var array
     */
    protected $key;

    /**
     * Our JWT token
     * @var array
     */
    protected $jwt;

    const PREPEND_KEY = 'ilios.jwt.key.';

    public function __construct($key)
    {
        //allow for 5 seconds of clock skew
        TokenLib::$leeway = 5;
        $this->key = self::PREPEND_KEY . $key;
    }

    public function setRequest(Request $request)
    {
        $jwt = false;

        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        $matches = [];
        // we always take the Authorization header over the query param
        if (preg_match('/^Token (.*)$/', $authorizationHeader, $matches)) {
            $jwt = $matches[1];
        }
        if ($jwt) {
            $decoded = TokenLib::decode($jwt, $this->key, array('HS256'));
            $this->jwt = (array) $decoded;
        }
    }


    public function getUserId()
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $user->getId();
        }

        return null;
    }

    public function getUserName()
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $user->getEmail();
        }

        return null;
    }

    /**
     * Check if our JWT token was found in the request
     * @return boolean
     */
    public function isValidJwtRequest()
    {
        if (is_array($this->jwt)) {
            if (!$this->jwt['iss'] === 'ilios') {
                return false;
            }
            if (!array_key_exists('user_id', $this->jwt)) {
                return false;
            }
            return true;
        }

        return false;
    }

    public function getCredentials()
    {
        return (int) $this->jwt['user_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            $this->jwt
        );
    }
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->jwt = unserialize($serialized);
        $this->setAuthenticated(false);
    }

    /**
     * Creates and returns a fresh JSON Web Token (JWT).
     * @return string the encoded token.
     * @throws \Exception
     * @todo rename to createJwt() or the likes. [ST 2015-07-28]
     */
    public function getJwt()
    {
        if (! $this->getUser() instanceof UserInterface) {
            throw new \Exception('Can not build a JWT, we have no user');
        }

        $interval = new \DateInterval("PT8H"); // default 8 hrs TTL

        // if we have a token on file
        // then use the delta between its issuing and expiration date
        // as the TTL value for the new token.
        // TODO: this feels kludgy - side-effects much? revisit. [ST 2015/07/28]
        if (is_array($this->jwt)) {
            $iat = new \DateTime();
            $iat->setTimestamp($this->jwt['iat']);
            $exp = new \DateTime();
            $exp->setTimestamp($this->jwt['exp']);
            $interval = $iat->diff($exp);
        }

        $now = new \DateTime();
        $time = $now->getTimestamp();
        $expires = new \Datetime();
        $expires->setTimestamp($time);
        $expires->add($interval);

        $arr = array(
            'iss' => 'ilios',
            'aud' => 'ilios',
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'user_id' => $this->getUserId()
        );

        return TokenLib::encode($arr, $this->key);
    }
}