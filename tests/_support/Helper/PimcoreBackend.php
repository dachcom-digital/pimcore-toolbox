<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Pimcore\Model\User;
use Pimcore\Tests\Util\TestHelper;
use Pimcore\Tool\Authentication;

use Pimcore\Model\Document;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PimcoreBackend extends Module\REST
{
    /**
     * @var User[]
     */
    protected $users = [];

    /**
     * @var array
     */
    protected $globalParams = [];

    /**
     * @inheritDoc
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->initializeUser('dachcom_test', true);
    }

    /**
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        parent::_before($test);

        TestHelper::cleanUpTree(Document::getById(1), 'document');
    }

    /**
     * @param $username
     */
    public function amLoggedInAs($username)
    {
        try {
            $pimcoreModule = $this->getModule('\\' . PimcoreBundle::class);
        } catch (\Exception $e) {
            $this->debug('error while getting module: ' . $e->getMessage());
            return;
        }

        $pimcoreUser = $this->getUser($username);

        /** @var Session $session */
        $session = $pimcoreModule->getContainer()->get('session');

        $firewallName = 'admin';
        $firewallContext = 'admin';

        $user = new \Pimcore\Bundle\AdminBundle\Security\User\User($pimcoreUser);
        $token = new UsernamePasswordToken($user, null, $firewallName, $pimcoreUser->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        \Pimcore\Tool\Session::useSession(function (AttributeBagInterface $adminSession) use ($pimcoreUser) {
            \Pimcore\Tool\Session::regenerateId();
            $adminSession->set('user', $pimcoreUser);
        });

        $cookie = new Cookie($session->getName(), $session->getId());

        /** @var \Codeception\Lib\Connector\Symfony $client */
        $client = $pimcoreModule->client;
        $client->getCookieJar()->set($cookie);

    }

    /**
     * @param       $snippetName
     * @param array $elements
     *
     */
    public function haveASnippet($snippetName, $elements = [])
    {
        $document = new Document\Snippet();
        $document->setModule('ToolboxBundle');
        $document->setController('Snippet');
        $document->setAction('teaser');
        $document->setType('snippet');
        $document->setElements($elements);
        $document->setParentId(1);
        $document->setUserOwner(1);
        $document->setUserModification(1);
        $document->setCreationDate(time());
        $document->setKey($snippetName);
        $document->setPublished(true);

        try {
            $document->save();
        } catch (\Exception $e) {
            $this->debug('error while creating snippet: ' . $e->getMessage());
        }

    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function getUser($username = 'dachcom_test')
    {
        if (isset($this->users[$username])) {
            return $this->users[$username];
        }

        throw new \InvalidArgumentException(sprintf('User %s does not exist', $username));
    }

    /**
     * @param string $username
     * @param bool   $admin
     *
     * @return null|User|User\AbstractUser
     */
    protected function initializeUser($username = 'dachcom_test', $admin = true)
    {
        if (!TestHelper::supportsDbTests()) {
            $this->debug(sprintf('[PIMCORE BACKEND] Not initializing user %s as DB is not connected', $username));
            return null;
        } else {
            $this->debug(sprintf('[PIMCORE BACKEND] Initializing user %s', $username));
        }

        $password = $username;

        /** @var User $user */
        $user = User::getByName($username);

        if (!$user) {
            $this->debug(sprintf('[PIMCORE BACKEND] Creating user %s', $username));

            $pass = null;

            try {
                $pass = Authentication::getPasswordHash($username, $password);
            } catch (\Exception $e) {
                // fail silently.
            }

            $user = User::create([
                'parentId' => 0,
                'username' => $username,
                'password' => $pass,
                'active'   => true,
                'admin'    => $admin
            ]);
        }

        $this->users[$user->getName()] = $user;

        return $user;
    }
}
