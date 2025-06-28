<?php

namespace App\Models;

class Sale extends BaseModel
{
    protected $table_name = 'sales';
    protected $primary_key = 'id_sale';

    public function all()
    {
        return $this->getAll(1, 1000);
    }

    public function find($id)
    {
        return $this->getById($id);
    }

    public function createSale($data)
    {
        $id = $this->create($data);
        if ($id) {
            return $this->getById($id);
        }
        return false;
    }

    public function updateSale($id, $data)
    {
        if ($this->update($id, $data)) {
            return $this->getById($id);
        }
        return false;
    }

    public function deleteSale($id)
    {
        return $this->delete($id);
    }
}