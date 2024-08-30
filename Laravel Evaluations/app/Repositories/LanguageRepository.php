<?php
/**
 * Repository to handle data flow for Language model
 */

namespace App\Repositories;

use App\Models\Language;


class LanguageRepository implements LanguageInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * LanguageRepository constructor.
     *
     * @param \App\Models\Language $model
     */

    public function __construct(Language $model)
    {
        $this->model = $model;
    }

    /**
     * Get all languages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get language by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Language
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new language.
     *
     * @param array $attributes
     *
     * @return \App\Models\Language
     */
    public function create(array $attributes)
    {
        $language = $this->model->where('name', $attributes['name'])->first();
        if ($language) {
            return null;
        }

        return $this->model->create($attributes);
    }

    /**
     * Update an language.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Language
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an language.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Get language by name.
     *
     * @param string $name
     *
     * @return \App\Models\Language
     */
    public function getByName($name)
    {
        return $this->model->where(['name' => $name])->first();
    }

}