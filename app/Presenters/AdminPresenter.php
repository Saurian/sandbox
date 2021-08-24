<?php

namespace App\Presenters;

use App\Controls\DescriptionGridControl;
use App\Controls\DescriptionGridControlFactory;
use App\Controls\EquationGridControl;
use App\Controls\EquationGridControlFactory;
use App\Forms\DescriptionFormFactory;
use App\Forms\EquationFormFactory;
use App\Model\Repository\DescriptionRepository;
use App\Model\Repository\EquationRepository;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class AdminPresenter
 * správa produktů
 *
 * @package App\Presenters
 *
 *
 */
class AdminPresenter extends BasePresenter
{

    /** @var EquationRepository @inject */
    public EquationRepository $equationRepository;

    /** @var DescriptionRepository @inject */
    public DescriptionRepository $descriptionRepository;




    /*
     * controls
     */

    /** @var EquationGridControlFactory @inject */
    public EquationGridControlFactory $equationGridControlFactory;

    /** @var DescriptionGridControlFactory @inject */
    public DescriptionGridControlFactory $descriptionGridControlFactory;



    /** @var EquationFormFactory @inject */
    public EquationFormFactory $equationFormFactory;

    /** @var DescriptionFormFactory @inject */
    public DescriptionFormFactory $descriptionFormFactory;



    /*
     * signals
     */




    /*
     * actions
     * ___________________________________________________________________________
     */


    /**
     * @param int|null $id
     * @throws Nette\Application\AbortException
     */
    public function actionEditEquation(int $id = null): void {
        $entity = null;
        if ($id) {
            if (!$entity = $this->equationRepository->getSelection()->get($id)) {
                $this->flashMessage("Rovnice nenalezena", "warning");
                $this->redirect("default");
            }
        }

        $this->template->entity = $entity;
    }

    public function actionEditDescription($id): void {
        $entity = null;
        if ($id) {
            if (!$entity = $this->descriptionRepository->getSelection()->get($id)) {
                $this->flashMessage("poznámka nenalezena", "warning");
                $this->redirect("default");
            }
        }

        $this->template->entity = $entity;
    }






    /*
     * components
     * ___________________________________________________________________________
     */


    /**
     * Formulář editace projektu
     * komponenta nemá vlastní šablonu
     *
     * @return Form
     */
    protected function createComponentEquationForm(): Form
    {
        $form = $this->equationFormFactory
            ->create($this->template->entity, function (Nette\Database\Table\ActiveRow $entity, bool $isNewEntity) {
                if ($isNewEntity) {
                    $this->flashMessage("Nová rovnice vytvořena", "success");

                    /*
                     * pokud je nová entita, rovnou přesměrujeme na editaci
                     */
                    $this->redirect('editEquation', $entity->id);

                } else {
                    $this->flashMessage("Rovnice [id: $entity->id] upravena", "success");
                    $this->ajaxRedirect("this");
                }

            }, function (string $message) {
                // error event
                if ($this->isAjax()) $this->redrawControl("form");
            });

        return $form;
    }


    /**
     * Formulář typu projektu
     * komponenta nemá vlastní šablonu
     *
     * @return Form
     * @throws Nette\Application\AbortException
     */
    protected function createComponentDescriptionForm() {
        $form = $this->descriptionFormFactory
            ->create($this->template->entity, function (Nette\Database\Table\ActiveRow $entity, bool $isNewEntity) {
                if ($isNewEntity) {
                    $this->flashMessage("Nová poznámka vytvořena", "success");

                    /*
                     * pokud je nová entita, rovnou přesměrujeme na editaci
                     */
                    $this->redirect('editDescription', $entity->id);

                } else {
                    $this->flashMessage("poznámka [id: $entity->id] upravena", "success");
                    $this->ajaxRedirect("this");
                }

            }, function (string $message) {
                // error event
                if ($this->isAjax()) $this->redrawControl("form");
            });

        return $form;
    }



    /**
     * komponenta s vlastní šablonou
     *
     * @return EquationGridControl
     */
    protected function createComponentEquationGridControl(): EquationGridControl {
        return $this->equationGridControlFactory->create('editEquation');
    }


    /**
     * @return DescriptionGridControl
     */
    protected function createComponentDescriptionGridControl(): DescriptionGridControl
    {
        return $this->descriptionGridControlFactory->create('editDescription');
    }





    /*
     * getters / setters
     * ___________________________________________________________________________
     */


}