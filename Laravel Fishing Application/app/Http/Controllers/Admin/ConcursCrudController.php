<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ConcursRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ConcursCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ConcursCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Concurs::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/concurs');
        CRUD::setEntityNameStrings('concurs', 'concursuri');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'created_by',
            'label' => 'Creat de',
            'type' => 'select',
            'entity' => 'createdBy',
            'attribute' => 'prenume'
        ]);

        CRUD::addColumn([
            'label' => "Nume",
            'type' => 'text',
            'name' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'organizator_id',
            'label' => 'Organizator',
            'type' => 'select',
            'entity' => 'user',
            'attribute' => 'prenume'
        ]);

        CRUD::addColumn([
            'label' => "Descriere",
            'type' => 'text',
            'name' => 'descriere'
        ]);

        CRUD::addColumn([
            'label' => "Regulament",
            'type' => 'text',
            'name' => 'regulament'
        ]);

        CRUD::addColumn([
            'name'      => 'poza',
            'label'     => 'Poza',
            'type'      => 'image',
            'height'    => '30px',
            'width'     => '30px',
            'withFiles' => true
        ]);

        CRUD::addColumn([
            'name'  => 'start',
            'label' => 'Start Concurs',
            'type'  => 'datetime'
        ]);

        CRUD::addColumn([
            'name'  => 'stop',
            'label' => 'Stop Concurs',
            'type'  => 'datetime'
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

        CRUD::setValidation(ConcursRequest::class);
        CRUD::field('created_by')->type('hidden')->default(backpack_auth()->user()->id);

        CRUD::field('nume')->type('text');
        CRUD::addField([
            'label'     => 'Organizator',
            'type'      => 'select',
            'name'      => 'organizator_id',
            'entity'    => 'user',
            'model'     => User::class,
            'attribute' => 'prenume'
        ]);

        CRUD::field('descriere')->type('textarea');
        CRUD::field('regulament')->type('textarea');

        CRUD::addField([
            'name'      => 'poza',
            'label'     => 'Poza',
            'type'      => 'upload'
        ]);

        CRUD::addField([
            'name'  => 'start',
            'label' => 'Start Concurs',
            'type'  => 'datetime'
        ]);

        CRUD::addField([
            'name'  => 'stop',
            'label' => 'Stop Concurs',
            'type'  => 'datetime'
        ]);

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
