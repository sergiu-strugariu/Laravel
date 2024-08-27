<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AlocareStandRequest;
use App\Models\AlocareStand;
use App\Models\Concurs;
use App\Models\Lac;
use App\Models\Sector;
use App\Models\Stand;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use PhpParser\Node\Expr\AssignOp\Concat;

/**
 * Class AlocareStandCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AlocareStandCrudController extends CrudController
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
        CRUD::setModel(\App\Models\AlocareStand::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/alocare-stand');
        CRUD::setEntityNameStrings('alocare stand', 'alocare stands');
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
            'name' => 'stand_id',
            'label' => 'Stand',
            'type' => 'select',
            'entity' => 'stand',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'pescar_id',
            'label' => 'Pescar',
            'type' => 'select',
            'entity' => 'pescar',
            'attribute' => 'prenume'
        ]);
        
        CRUD::addColumn([
            'name' => 'concurs_id',
            'label' => 'Concurs',
            'type' => 'select',
            'entity' => 'concurs',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'sector_id',
            'label' => 'Sector',
            'type' => 'select',
            'entity' => 'sector',
            'attribute' => 'nume'
        ]);

        CRUD::addColumn([
            'name' => 'lac_id',
            'label' => 'Lac',
            'type' => 'select',
            'entity' => 'lac',
            'attribute' => 'nume'
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
        CRUD::setValidation(AlocareStandRequest::class);
        CRUD::field('created_by')->type('hidden')->default(backpack_auth()->user()->id);

        CRUD::addField([
            'label'     => 'Stand',
            'type'      => 'select',
            'name'      => 'stand_id',
            'entity'    => 'stand',
            'model'     => Stand::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Pescar',
            'type'      => 'select',
            'name'      => 'pescar_id',
            'entity'    => 'pescar',
            'model'     => User::class,
            'attribute' => 'prenume'
        ]);

        CRUD::addField([
            'label'     => 'Sector',
            'type'      => 'select',
            'name'      => 'sector_id',
            'entity'    => 'sector',
            'model'     => Sector::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Concurs',
            'type'      => 'select',
            'name'      => 'concurs_id',
            'entity'    => 'concurs',
            'model'     => Concurs::class,
            'attribute' => 'nume'
        ]);

        CRUD::addField([
            'label'     => 'Lac',
            'type'      => 'select',
            'name'      => 'lac_id',
            'entity'    => 'lac',
            'model'     => Lac::class,
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
