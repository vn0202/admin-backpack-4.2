<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Post;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class CategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Category::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/category');
        CRUD::setEntityNameStrings('category', 'categories');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
        CRUD::column('title');
        CRUD::column('slug');
        $this->crud->addColumn(
            [
                'name'=>'category',
                'label'=>'Parent category',
                'type'=>'relationship',
            ]
        );
        $this->crud->query->withCount('posts'); // this will add a tags_count column to the results
        $this->crud->addColumn([
            'name'      => 'posts_count', // name of relationship method in the model
            'type'      => 'text',
            'label'     => 'Total post', // Table column heading
            'suffix'    => ' post', // to show "123 tags" instead of "123"
            'wrapper'=>[
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('post')."?category=".$entry->id;
                },
            ]
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
        CRUD::setValidation(CategoryRequest::class);
        CRUD::field('title');
        $this->crud->addField(
            [
                'name'=>'category',
                'label'=>'Parent category',
                'type'=>'relationship',
            ]
        );


Category::creating(function ($entry){
    $entry->user_id = backpack_auth()->user()->id;
});
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateCategoryRequest::class);

        CRUD::field('title');
        $this->crud->addField(
            [
                'name'=>'id',
                'type'=>'number',
                'label'=>'',
                'attributes' => [
                    'hidden' => 'hidden',
                ], // change the HTML attri
            ]
        );
        $this->crud->addField(
            [
                'label'     => "Category",
                'type'      => 'select',
                'name'      => 'category_id', // the db column for the foreign key
                'entity'    => 'category',

                'model'     => "App\Models\Category", // related model
                'attribute' => 'title', // foreign key attribute that is shown to user
            ]
        );
        Category::updating(function ($entry)
        {
            $entry->slug = Str::of($this->crud->getRequest()->title)->slug('-');
        });

    }
}
