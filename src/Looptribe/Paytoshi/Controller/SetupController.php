<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Setup\Configurator;
use Looptribe\Paytoshi\Setup\Diagnostics;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    public function __construct(
        TemplatingEngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        Diagnostics $diagnostics,
        Configurator $configurator,
        $dbConfig
    ) {
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->diagnostics = $diagnostics;
        $this->configurator = $configurator;
        $this->dbConfig = $dbConfig;
    }

    public function startAction()
    {
        $requirementsChecker = $this->diagnostics->checkRequirements();

        return $this->templating->render('admin/setup_requirements.html.twig', array(
            'checker' => $requirementsChecker,
        ));
    }

    public function checkRewriteAction(Request $request)
    {
        $result = $this->diagnostics->checkRewrite($request->getUri());
        return new JsonResponse(array('result' => $result), $result ? 200 : 400);
    }

    public function checkPostTagsAction(Request $request)
    {
        $data = $request->request->get('data');
        return new JsonResponse(array('result' => $data));
    }

    public function setupAction()
    {
        $requirementsChecker = $this->diagnostics->checkRequirements();

        if ($requirementsChecker->hasFailedRequirements())
        {
            return $this->templating->render('admin/setup_requirements.html.twig', array(
                'checker' => $requirementsChecker,
            ));
        }

        $isConfigWritable = $this->diagnostics->isConfigWritable();

        return $this->templating->render('admin/setup.html.twig', array(
            'isConfigWritable' => $isConfigWritable,
            'checker' => $requirementsChecker,
            'config' => array(
                'database' => $this->dbConfig,
            ),
        ));
    }

    public function saveAction(Request $request)
    {
        if ($this->diagnostics->isConfigWritable() === true) {
            $config = array(
                'database' => array(
                    'name' => $request->request->get('dbName'),
                    'host' => $request->request->get('dbHost'),
                    'username' => $request->request->get('dbUser'),
                    'password' => $request->request->get('dbPass'),
                )
            );

            $this->configurator->saveConfig($config);
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
