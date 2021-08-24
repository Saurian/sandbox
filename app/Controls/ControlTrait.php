<?php

declare(strict_types=1);

namespace App\Controls;

trait ControlTrait
{

    /**
     * simple ajax redirect
     *
     * @param string $uri
     * @param array $snippets
     */
    public function ajaxRedirect(string $uri = 'this', array $snippets = array()): void
    {
        if ($this->presenter->isAjax())
            if (empty($snippets)) {
                $this->redrawControl();

            } else {
                foreach ($snippets as $snippet) {
                    $this->redrawControl($snippet);
                }
            }

        else
            $this->redirect($uri);

    }


}