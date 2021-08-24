<?php

namespace App\Forms;

use App\Model\Repository\EquationRepository;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class EquationFormFactory
{

    private FormFactory $factory;

    private EquationRepository $repository;


    /**
     * ProjectFormFactory constructor.
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory, EquationRepository $equationRepository)
    {
        $this->factory    = $factory;
        $this->repository = $equationRepository;
    }


    public function create(?ActiveRow $entity, callable $onSuccess, callable $onError): Form
    {
        $form = $this->factory->create();
        $form->addHidden("id");
        $form->addText("name", "Název")
             ->addRule(Form::FILLED)
             ->addRule(Form::MAX_LENGTH, null, 255)
             ->setHtmlAttribute("class", "form-control");

        $form->addText("turn_scene", "Otáčecí scéna")
             ->addRule(Form::FILLED)
             ->addRule(Form::MAX_LENGTH, null, 255)
             ->setHtmlAttribute("class", "form-control");

        $form->addText("value", "Hodnota")
             ->addRule(Form::FILLED)
             ->addRule(Form::MAX_LENGTH, null, 255)
             ->setHtmlAttribute("class", "form-control");

        $form->addText("strong", "Síla")
             ->addRule(Form::FILLED)
             ->addRule(Form::MAX_LENGTH, null, 255)
             ->setHtmlAttribute("class", "form-control");


        $form->addSubmit('send', 'Odeslat');

        if ($entity) {
            $form->setDefaults([
                    "id"         => $entity->id,
                    "name"       => $entity->name,
                    "value"      => $entity->value,
                    "strong"     => $entity->strong,
                    "turn_scene" => $entity->turn_scene,
                ]
            );
        }

        $this->factory::bs3Rendered($form);
        $form->getElementPrototype()->class = "_ajax form-horizontal";

        $form->onSuccess[] = function (Form $form, $values) use ($entity, $onSuccess, $onError): void {

            try {
                $id          = $values->id;
                $isNewEntity = (bool)!$id;
                unset($values["id"]);

                if ($id) {
                    $this->repository->getSelection()->where(["id" => $id])->update($values);

                } else {
                    $values["inserted"] = new \DateTime();
                    $entity             = $this->repository->getSelection()->insert($values);
                }

                $onSuccess($entity, $isNewEntity);

            } catch (\Nette\Database\DriverException $exception) {
                $form->addError($exception->getMessage());
                $onError($exception->getMessage());
            }
        };

        return $form;
    }


}