<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StandRequest;
use App\Models\Lac;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StandCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StandCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Stand::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/stand');
        CRUD::setEntityNameStrings('stand', 'standuri');
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
            'attribute' => 'prenume',
        ]);

        CRUD::addColumn([
            'label' => 'Nume',
            'type' => 'text',
            'name' => 'nume',
        ]);

        CRUD::addColumn([
            'name' => 'lac_id',
            'label' => 'Nume Lac',
            'type' => 'select',
            'entity' => 'lac',
            'attribute' => 'nume',
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
        CRUD::setValidation(StandRequest::class);
        CRUD::field('created_by')->type('hidden')->default(backpack_auth()->user()->id);

        CRUD::field('nume')->type('text');

        CRUD::addField([
            'label'     => 'Lacuri',
            'type'      => 'select',
            'name'      => 'lac_id',
            'entity'    => 'lac',
            'model'     => Lac::class,
            'attribute' => 'nume',
            'allows_null' => false,
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
