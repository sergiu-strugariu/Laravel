<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CantarRequest;
use App\Models\Concurs;
use App\Models\Lac;
use App\Models\Stand;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CantarCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CantarCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Cantar::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/cantar');
        CRUD::setEntityNameStrings('cantar', 'cantarire');
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
            'name' => 'stand_id',
            'label' => 'Stand',
            'type' => 'select',
            'entity' => 'stand',
            'attribute' => 'nume'
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
            'name' => 'cantitate',
            'label' => 'Cantitate',
            'type' => 'number',
            'suffix' => ' KG',
            'decimals' => 2,
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
        CRUD::setValidation(CantarRequest::class);
        
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
            'name' => 'cantitate',
            'label' => 'Cantitate',
            'type' => 'number',
            'attributes' => [
                "step" => "any"
            ],
            'suffix'     => "KG",
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
