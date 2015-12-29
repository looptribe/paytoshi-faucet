<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Setup\Configurator;
use Looptribe\Paytoshi\Setup\Diagnostics;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;

class SetupController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Diagnostics */
    private $diagnostics;

    /** @var Configurator */
    private $configurator;

    /** @var array */
    private $dbConfig;

    private $configPath;

    public function __construct(
        TemplatingEngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        Diagnostics $diagnostics,
        Configurator $configurator,
        $dbConfig,
        $configPath
    ) {
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->diagnostics = $diagnostics;
        $this->configurator = $configurator;
        $this->dbConfig = $dbConfig;
        $this->configPath = $configPath;
    }

    public function startAction()
    {
        return $this->templating->render('admin/setup.html.twig', array(
            'isConfigWritable' => $this->diagnostics->isConfigWritable(),
            'config' => array(
                'database' => $this->dbConfig,
            ),
        ));
    }

    public function saveAction(Request $request)
    {
        $config = array(
            'database' => array(
                'name' => $request->request->get('dbName'),
                'host' => $request->request->get('dbHost'),
                'username' => $request->request->get('dbUser'),
                'password' => $request->request->get('dbPass'),
            )
        );

        $yml = Yaml::dump($config);

        if (file_put_contents($this->configPath, $yml) === false) {
            throw new \RuntimeException(sprintf('Cannot write configuration file "%s".', $this->configPath));
        }

        return new RedirectResponse($this->urlGenerator->generate('setup_complete'));
    }

    public function completeAction()
    {
        $results = $this->configurator->setup();

        return $this->templating->render('admin/setup_completed.html.twig', array(
            'results' => $results,
        ));
    }

    public function checkAction(Request $request)
    {
        $results = array(
            'ok' => true,
            'errors' => array(),
        );

        $config = $request->request->all();

        $results['errors']['db'] = false;
        try {
            $this->diagnostics->checkDatabase($config['database']);
        } catch (\Exception $ex) {
            $results['errors']['db'] = $ex->getMessage();
            $results['ok'] = false;
        }

        return new JsonResponse($results);
    }
}
