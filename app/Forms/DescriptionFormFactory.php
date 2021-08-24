<?php

namespace App\Forms;

use App\Model\Repository\DescriptionRepository;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class DescriptionFormFactory
{

    private FormFactory $factory;

    private DescriptionRepository $descriptionRepository;


    /**
     * ProjectFormFactory constructor.
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory, DescriptionRepository $descriptionRepository)
    {
        $this->factory               = $factory;
        $this->descriptionRepository = $descriptionRepository;
    }


    public function create(?ActiveRow $entity, callable $onSuccess, callable $onError): Form
    {
        $form = $this->factory->create();
        $form->addHidden("id");
        $form->addTextArea("text", "Popis")
             ->addRule(Form::FILLED)
             ->addRule(Form::MAX_LENGTH, null, 255)
             ->setHtmlAttribute("class", "form-control");

        $form->addSubmit('send', 'Odeslat');

        if ($entity) {
            $form->setDefaults([
                    "id"   => $entity->id,
                    "text" => $entity->text,
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
                    $this->descriptionRepository->getSelection()->where(["id" => $id])->update($values);

                } else {
                    $values["inserted"] = new \DateTime();
                    $entity             = $this->descriptionRepository->getSelection()->insert($values);
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