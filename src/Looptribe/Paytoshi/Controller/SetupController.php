<?php

namespace Looptribe\Paytoshi\Controller;

use Looptribe\Paytoshi\Model\SetupDiagnostics;
use Looptribe\Paytoshi\Templating\TemplatingEngineInterface;

class SetupController
{
    /** @var TemplatingEngineInterface */
    private $templating;

    /** @var SetupDiagnostics */
    private $diagnostics;

    public function __construct(TemplatingEngineInterface $templating, SetupDiagnostics $diagnostics)
    {
        $this->templating = $templating;
        $this->diagnostics = $diagnostics;
    }

    public function startAction()
    {
        $dbException = false;
        try {
            $this->diagnostics->checkDatabase();
        }
        catch (\Exception $ex) {
            $dbException = $ex;
        }

        return $this->templating->render('default/setup.html.twig', array(
            'dbException' => $dbException,
            'errors' => $dbException,
        ));
    }

    public function action()
    {

    }
}
