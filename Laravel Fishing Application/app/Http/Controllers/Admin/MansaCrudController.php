<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MansaRequest;
use App\Models\Concurs;
use App\Models\Lac;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MansaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MansaCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Mansa::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mansa');
        CRUD::setEntityNameStrings('mansa', 'manse');
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
            'label' => 'Nume',
            'type' => 'text',
            'name' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'concurs_id',
            'label' => 'Concurs',
            'type' => 'select',
            'entity' => 'concurs',
            'attribute' => 'nume'
        ]);
        CRUD::addColumn([
            'name' => 'lac_id',
            'label' => 'Lac',
            'type' => 'select',
            'entity' => 'lac',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'label' => 'Status Mansa',
            'type' => 'text',
            'name' => 'status_mansa'
        ]);

        CRUD::addColumn([
            'label' => 'Participanti',
            'type' => 'number',
            'name' => 'participanti'
        ]);

        CRUD::addColumn([
            'label' => 'Participanti Maximi',
            'type' => 'number',
            'name' => 'participanti_max'
        ]);

        CRUD::addColumn([
            'name'  => 'start_mansa',
            'label' => 'Start Mansa',
            'type'  => 'datetime'
        ]);

        CRUD::addColumn([
            'name'  => 'stop_mansa',
            'label' => 'Stop Mansa',
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
        CRUD::setValidation(MansaRequest::class);
        CRUD::field('created_by')->type('hidden')->default(backpack_auth()->user()->id);

        CRUD::field('nume')->type('text');

        CRUD::addField([
            'label'     => 'Concurs',
            'type'      => 'select',
            'name'      => 'concurs_id',
            'entity'    => 'concurs',
            'model'     => Concurs::class,
            'attribute' => 'nume',
        ]);

        CRUD::addField([
            'label'     => 'Lacuri',
            'type'      => 'select',
            'name'      => 'lac_id',
            'entity'    => 'lac',
            'model'     => Lac::class,
            'attribute' => 'nume',
        ]);

        CRUD::addField([
            'name'        => 'status_mansa',
            'label'       => 'Status',
            'type'        => 'select_from_array',
            'options'     => [
                'Status 1' => 'Status 1',
                'Status 2' => 'Status 2',
                'Status 3' => 'Status 3',
                'Status 4' => 'Status 4'
            ],
            'allows_null' => false,
            'default'     => 1,
        ]);

        CRUD::addField([
            'name'  => 'start_mansa',
            'label' => 'Start Mansa',
            'type'  => 'datetime'
        ]);

        CRUD::addField([
            'name'  => 'stop_mansa',
            'label' => 'Stop Mansa',
            'type'  => 'datetime'
        ]);

//        CRUD::field('participanti')->type('number');
        CRUD::field('participanti_max')->type('number');
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
