<?php


namespace App\Controls;

use App\Model\Repository\EquationRepository;
use Nette;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

interface EquationGridControlFactory
{
    public function create(string $editLinkUri): EquationGridControl;
}

/**
 * Class EquationGridControl
 *
 * @package App\Controls
 */
class EquationGridControl extends Control
{
    use ControlTrait;

    private EquationRepository $repository;

    /** @var string uri edit name [Admin:edit ...] */
    private string $editLinkUri;


    /**
     * EquationGridControl constructor.
     *
     * @param EquationRepository $equationRepository
     * @param string $editLinkUri
     */
    public function __construct(EquationRepository $equationRepository, string $editLinkUri)
    {
        $this->editLinkUri = $editLinkUri;
        $this->repository  = $equationRepository;
    }


    public function render()
    {
        $this->template->render(__DIR__ . '/equationGridControl.latte');
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
            $this->flashMessage("rovnice [$id] nenalezena", "warning");
            $this->redirect("this");

        } else {
            $name = $entity->name;
            $this->repository->getSelection()->where(["id" => $id])->delete();
            $this->flashMessage("rovnice `$name` smazána", "success");

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

        $grid->addColumnText("name", "Název");

        $grid->addColumnText("value", "Hodnota");

        $grid->addColumnText("strong", "Síla");
        $grid->addColumnText("turn_scene", "Scéna pro otočku");

        $grid->addColumnText("inserted", "Vložena");

        $grid->addAction("edit", "Edit")
             ->setRenderer(function (Nette\Database\Table\ActiveRow $row) {
                 $html = (new Nette\Utils\Html())->create('a', ['href' => $this->presenter->link($this->editLinkUri, ['id' => $row->id])]);

                 $icon = Nette\Utils\Html::el('span')->setAttribute('class', 'fa fa-edit');
                 $html->addHtml($icon);

                 $html->setAttribute('class', 'btn btn-xs btn-default');
                 $html->addText(' Edit');

                 return $html;
             });

        $grid->addAction("delete", "Delete")
             ->setIcon('trash')
             ->setClass("ajax btn btn-xs btn-danger")
             ->setConfirmation(new StringConfirmation("Opravdu chcete smazat rovnici `%s`?", "name"));

        $grid->addToolbarButton("edit", "Nová rovnice")
             ->setRenderer(function () {
                 $html = (new Nette\Utils\Html())->create('a', ['href' => $this->presenter->link($this->editLinkUri)]);

                 $html->setAttribute('class', 'btn btn-xs btn-default');
                 $html->setText('Nová rovnice');

                 return $html;
             });

        return $grid;
    }


}