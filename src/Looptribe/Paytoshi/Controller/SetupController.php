<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Model\Configurator;
use Looptribe\Paytoshi\Model\SetupDiagnostics;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;

class SetupController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var SetupDiagnostics */
    private $diagnostics;

    /** @var Configurator */
    private $configurator;

    public function __construct(
        TemplatingEngineInterface $templating,
        SetupDiagnostics $diagnostics,
        Configurator $configurator
    ) {
        $this->templating = $templating;
        $this->diagnostics = $diagnostics;
        $this->configurator = $configurator;
    }

    public function startAction()
    {
        $dbException = false;
        try {
            $this->diagnostics->checkDatabase();
        } catch (\Exception $ex) {
            $dbException = $ex;
        }

        return $this->templating->render('admin/setup.html.twig', array(
            'dbException' => $dbException,
            'errors' => $dbException,
        ));
    }

    public function action()
    {
        $results = $this->configurator->setup();

        return $this->templating->render('admin/setup_completed.html.twig', array(
            'results' => $results,
        ));
    }
}
