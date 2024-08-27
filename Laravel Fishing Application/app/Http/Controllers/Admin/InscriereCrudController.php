<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InscriereRequest;
use App\Models\Concurs;
use App\Models\Lac;
use App\Models\Mansa;
use App\Models\Sector;
use App\Models\Stand;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InscriereCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InscriereCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Inscriere::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inscriere');
        CRUD::setEntityNameStrings('inscriere', 'inscrierii');
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
            'attribute' => 'name'
        ]);

        CRUD::addColumn([
            'name' => 'pescar_id',
            'label' => 'Pescar',
            'type' => 'select',
            'entity' => 'pescarNume',
            'attribute' => 'name'
        ]);

        CRUD::addColumn([
            'name' => 'concurs_id',
            'label' => 'Concurs',
            'type' => 'select',
            'entity' => 'concursNume',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'mansa_id',
            'label' => 'Mansa',
            'type' => 'select',
            'entity' => 'mansaNume',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'stand_id',
            'label' => 'Stand',
            'type' => 'select',
            'entity' => 'standNume',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'lac_id',
            'label' => 'Lac',
            'type' => 'select',
            'entity' => 'lacNume',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'sector_Id',
            'label' => 'Sector',
            'type' => 'select',
            'entity' => 'sectorNume',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'label' => "Nume Trofeu",
            'type' => 'text',
            'name' => 'nume_trofeu'
        ]);

        CRUD::addColumn([
            'label' => "Puncte Penalizare",
            'type' => 'number',
            'name' => 'puncte_penalizare'
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
        CRUD::setValidation(InscriereRequest::class);
        CRUD::field('created_by')->type('hidden')->default(backpack_auth()->user()->id);

        CRUD::field('nume_trofeu')->type('text');
        CRUD::field('puncte_penalizare')->type('number');

        CRUD::addField([
            'label'     => 'Pescar',
            'type'      => 'select',
            'name'      => 'pescar_id',
            'entity'    => 'pescari',
            'model'     => User::class,
            'attribute' => 'prenume'
        ]);

        CRUD::addField([
            'label'     => 'Concurs',
            'type'      => 'select',
            'name'      => 'concurs_id',
            'entity'    => 'concursuri',
            'model'     => Concurs::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Mansa',
            'type'      => 'select',
            'name'      => 'mansa_id',
            'entity'    => 'manse',
            'model'     => Mansa::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Stand',
            'type'      => 'select',
            'name'      => 'stand_id',
            'entity'    => 'standuri',
            'model'     => Stand::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Lac',
            'type'      => 'select',
            'name'      => 'lac_id',
            'entity'    => 'lacuri',
            'model'     => Lac::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Sector',
            'type'      => 'select',
            'name'      => 'sector_id',
            'entity'    => 'sectoare',
            'model'     => Sector::class,
            'attribute' => 'nume'
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
