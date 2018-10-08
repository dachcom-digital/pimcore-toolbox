<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Lib\ModuleContainer;
use Pimcore\Event\TestEvents;
use Pimcore\Model\User;
use Pimcore\Tests\Helper\Pimcore;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class PimcoreBundle extends Pimcore
{
    const PIMCORE_ADMIN_CSRF_TOKEN_NAME = 'MOCK_CSRF_TOKEN';

    /**
     * @var Cookie
     */
    protected $sessionSnapShot;

    /**
     * @inheritDoc
     */
    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        $this->config = array_merge($this->config, [
            // set specific configuration file for container
            'configuration_file' => null
        ]);

        parent::__construct($moduleContainer, $config);
    }

    public function _initialize()
    {
        $this->sessionSnapShot = [];
        $isNew = \Pimcore::getKernel() === null;

        parent::_initialize();

        if ($isNew === true) {
            return;
        }

        $this->initializeKernel();

    }

    /**
     * @param string $url
     * @param array  $params
     */
    public function sendTokenAjaxPostRequest(string $url, array $params = [])
    {
        $params['csrfToken'] = self::PIMCORE_ADMIN_CSRF_TOKEN_NAME;
        self::sendAjaxPostRequest($url, $params);
    }

    /**
     * Actor Function to login into Pimcore Backend
     *
     * @param $username
     */
    public function amLoggedInAs($username)
    {
        $firewallName = 'admin';

        try {
            /** @var PimcoreUser $pimcoreUserModule */
            $pimcoreUserModule = $this->getModule('\\' . PimcoreUser::class);
        } catch (ModuleException $pimcoreUserModule) {
            $this->debug('[PIMCORE BUNDLE MODULE] could not load pimcore user module');
            return;
        }

        $pimcoreUser = $pimcoreUserModule->getUser($username);

        if (!$pimcoreUser instanceof User) {
            $this->debug(sprintf('[PIMCORE BUNDLE MODULE] could not fetch user %s.', $username));
            return;
        }

        /** @var Session $session */
        $session = $this->getContainer()->get('session');

        /** @var \Codeception\Lib\Connector\Symfony $client */
        $client = $this->client;

        $client->getCookieJar()->clear();

        $user = new \Pimcore\Bundle\AdminBundle\Security\User\User($pimcoreUser);
        $token = new UsernamePasswordToken($user, null, $firewallName, $pimcoreUser->getRoles());
        $this->getContainer()->get('security.token_storage')->setToken($token);

        \Pimcore\Tool\Session::useSession(function (AttributeBagInterface $adminSession) use ($pimcoreUser) {
            \Pimcore\Tool\Session::regenerateId();
            $adminSession->set('user', $pimcoreUser);
            $adminSession->set('csrfToken', self::PIMCORE_ADMIN_CSRF_TOKEN_NAME);
        });

        // allow re-usage of session in same cest.
        if (!empty($this->sessionSnapShot)) {
            $cookie = $this->sessionSnapShot;
        } else {
            $cookie = new Cookie($session->getName(), $session->getId());
            $this->sessionSnapShot = $cookie;
        }

        $client->getCookieJar()->set($cookie);

    }

    /**
     * Initialize the kernel (see parent Pimcore module)
     */
    protected function initializeKernel()
    {
        $maxNestingLevel = 200; // Symfony may have very long nesting level
        $xdebugMaxLevelKey = 'xdebug.max_nesting_level';
        if (ini_get($xdebugMaxLevelKey) < $maxNestingLevel) {
            ini_set($xdebugMaxLevelKey, $maxNestingLevel);
        }

        if ($this->config['configuration_file'] !== null) {
            putenv('DACHCOM_BUNDLE_CONFIG_FILE=' . $this->config['configuration_file']);
        } else {
            putenv('DACHCOM_BUNDLE_CONFIG_FILE');
        }

        //touch cache container to force refresh
        $fileSystem = new Filesystem();
        $this->kernel = require __DIR__ . '/../../kernelBuilder.php';

        $fileSystem->remove($this->kernel->getCacheDir());
        $fileSystem->mkdir($this->kernel->getCacheDir());

        $this->kernel->boot();

        $this->setupPimcoreDirectories();

        if ($this->config['cache_router'] === true) {
            $this->persistService('router', true);
        }

        // dispatch kernel booted event - will be used from services which need to reset state between tests
        $this->kernel->getContainer()->get('event_dispatcher')->dispatch(TestEvents::KERNEL_BOOTED);
    }
}
