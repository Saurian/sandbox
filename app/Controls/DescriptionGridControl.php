<?php


namespace App\Controls;

use App\Model\Repository\DescriptionRepository;
use Nette;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

interface DescriptionGridControlFactory
{
    public function create(string $editLinkUri): DescriptionGridControl;
}

/**
 * Class EquationGridControl
 *
 * @package App\Controls
 */
class DescriptionGridControl extends Control
{
    use ControlTrait;

     private DescriptionRepository $repository;

    /** @var string uri edit name [Admin:edit ...] */
    private string $editLinkUri;


    /**
     * DescriptionGridControl constructor.
     *
     * @param DescriptionRepository $equationRepository
     * @param string $editLinkUri
     */
    public function __construct(DescriptionRepository $equationRepository, string $editLinkUri)
    {
        $this->editLinkUri = $editLinkUri;
        $this->repository  = $equationRepository;
    }


    public function render(): void
    {
        $this->template->render(__DIR__ . '/descriptionGridControl.latte');
    }


    /**
     * smazání typu projektu
     *
     * @param int $id
     * @throws Nette\Application\AbortException
     */
    public function handleDelete(int $id)
    {
        if (!$id || !$entity = $this->repository->getSelection()->get($id)) {
            $this->flashMessage("poznámka [$id] nenalezena", "warning");
            $this->redirect("this");

        } else {
            $name = $entity->name;
            $this->repository->getSelection()->where(["id" => $id])->delete();
            $this->flashMessage("poznámka `$name` smazána", "success");

            /** @var DataGrid $grid */
            $grid = $this['grid'];
            $grid->reload();

            $this->ajaxRedirect();
        }
    }

    protected function createComponentGrid(): DataGrid
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->repository->getSelection());

        $grid->addColumnNumber("id", "Id")
             ->setFitContent(true);

        $grid->addColumnText("inserted", "Vložena");

        $grid->addColumnText("text", "Název")
            ->setRenderer(function (Nette\Database\Table\ActiveRow $row) {
                return Nette\Utils\Strings::truncate($row->text, 16);
            });


        $grid->addAction("edit", "Edit")
             ->setRenderer(function (Nette\Database\Table\ActiveRow $row) {
                 $html = (new Nette\Utils\Html())->create('a', ['href' => $this->presenter->link($this->editLinkUri, ['id' => $row->id])]);

                 $icon = Nette\Utils\Html::el('span')->setAttribute('class','fa fa-edit');
                 $html->addHtml($icon);

                 $html->setAttribute('class', 'btn btn-xs btn-default');
                 $html->addText(' Edit');

                 return $html;
             });

        $grid->addAction("delete", "Delete")
             ->setIcon('trash')
             ->setClass("ajax btn btn-xs btn-danger")
             ->setConfirmation(new StringConfirmation("Opravdu chcete smazat poznámku `%s`?", "id"));

        $grid->addToolbarButton("editEquation", "Nová rovnice")
             ->setRenderer(function () {
                 $html = (new Nette\Utils\Html())->create('a', ['href' => $this->presenter->link($this->editLinkUri)]);

                 $html->setAttribute('class', 'btn btn-xs btn-default');
                 $html->setText('Nová poznámka');

                 return $html;
             });

        return $grid;
    }


}